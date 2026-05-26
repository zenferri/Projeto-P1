<?php

class AuthController extends BaseController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function register(): void
    {
        $data = array_map('trim', $_POST);
        $email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $tipoUsuario = ($data['tipoCadastro'] ?? 'pf') === 'pj' ? 'juridica' : 'fisica';

        if (!$email) {
            $_SESSION['flash_error'] = 'Preencha um e-mail válido.';
            $this->redirect('/cadastro');
        }

        if (($data['senha'] ?? '') !== ($data['senhaConfirmacao'] ?? '')) {
            $_SESSION['flash_error'] = 'As senhas não coincidem.';
            $this->redirect('/cadastro');
        }

        if ($tipoUsuario === 'fisica' && empty($data['cpf'])) {
            $_SESSION['flash_error'] = 'CPF é obrigatório para pessoa física.';
            $this->redirect('/cadastro');
        }

        if ($tipoUsuario === 'juridica' && empty($data['cnpj'])) {
            $_SESSION['flash_error'] = 'CNPJ é obrigatório para pessoa jurídica.';
            $this->redirect('/cadastro');
        }

        $address = trim(($data['endereco'] ?? '') . ' ' . ($data['numero'] ?? '') . ' ' . ($data['complemento'] ?? ''));
        $userData = [
            'tipo_usuario' => $tipoUsuario,
            'nome' => $data['nome'] ?? '',
            'email' => $email,
            'telefone' => $data['telefone'] ?? '',
            'senha_hash' => password_hash($data['senha'], PASSWORD_DEFAULT),
            'cpf' => $tipoUsuario === 'fisica' ? ($data['cpf'] ?? null) : null,
            'cnpj' => $tipoUsuario === 'juridica' ? ($data['cnpj'] ?? null) : null,
            'data_nascimento' => $data['dataNascimento'] ?: null,
            'endereco' => $address ?: null,
        ];

        try {
            $user = $this->userModel->create($userData);
            $_SESSION['user'] = $user;
            $_SESSION['flash_success'] = 'Cadastro realizado com sucesso!';
            $this->redirect('/carrinho');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Erro ao salvar cadastro: ' . $e->getMessage();
            $this->redirect('/cadastro');
        }
    }

    public function login(): void
    {
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $_POST['senha'] ?? '';

        if (!$email || !$password) {
            $_SESSION['flash_error'] = 'Informe e-mail e senha.';
            $this->redirect('/cadastro');
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user || !password_verify($password, $user['senha_hash'] ?? '')) {
            $_SESSION['flash_error'] = 'E-mail ou senha incorretos.';
            $this->redirect('/cadastro');
        }

        $_SESSION['user'] = $user;
        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();
        $redirect = BASE_URL ?: '/';
        header('Location: ' . $redirect);
        exit;
    }
}
