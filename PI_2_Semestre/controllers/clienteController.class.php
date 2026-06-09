<?php
require_once "models/conexao.class.php";
require_once "models/usuario.class.php";
require_once "models/cliente.class.php";
require_once "models/endereco.class.php";
require_once "models/plano.class.php";
require_once "models/planoDAO.class.php";
require_once "models/clienteDAO.class.php";
require_once "models/usuarioDAO.class.php";
require_once "models/telefoneHelper.class.php";
require_once "models/carrinhoSessao.class.php";

class ClienteController
{
    public function cadastrar()
    {
        if (($_GET["intencao"] ?? "") === "cadastro") {
            CarrinhoSessao::limpar();
        }

        $contextoPlano = $this->resolverContextoPlano();
        $planoSelecionado = $contextoPlano["plano"];
        $exibirPlanoContratacao = $contextoPlano["exibir_plano"];
        $codigoPlano = $contextoPlano["slug"];

        if ($this->usuarioJaCadastrado()) {
            $this->redirecionarUsuarioCadastrado();
        }

        $msg = [];
        $dados = $_POST ?: [];
        $tipoCliente = strtoupper(trim($_GET["tipo"] ?? $_POST["tipo_cliente"] ?? ""));

        if ($_POST) {
            $this->processarCadastro(
                $msg,
                $dados,
                $tipoCliente,
                $codigoPlano,
                $exibirPlanoContratacao
            );
            if (count($msg) > 0) {
                $this->renderizarFormularioCadastro(
                    $tipoCliente,
                    $planoSelecionado,
                    $exibirPlanoContratacao,
                    $msg,
                    $dados
                );
                return;
            }
            return;
        }

        if ($tipoCliente !== "PF" && $tipoCliente !== "PJ") {
            require_once "views/cadastro_escolha.php";
            return;
        }

        $this->renderizarFormularioCadastro(
            $tipoCliente,
            $planoSelecionado,
            $exibirPlanoContratacao,
            $msg,
            $dados
        );
    }

    private function resolverContextoPlano(): array
    {
        $slugUrl = strtolower(trim($_GET["plano"] ?? ""));
        $slugPost = strtolower(trim($_POST["codigo_plano"] ?? ""));
        $slugCarrinho = CarrinhoSessao::obterSlug() ?? "";

        if ($slugUrl !== "") {
            CarrinhoSessao::definirPlano($slugUrl);
            $slug = $slugUrl;
        } elseif ($slugPost !== "") {
            CarrinhoSessao::definirPlano($slugPost);
            $slug = $slugPost;
        } elseif ($slugCarrinho !== "") {
            $slug = $slugCarrinho;
        } else {
            $slug = "";
        }

        $plano = $slug !== "" ? $this->resolverPlano($slug) : null;

        return [
            "slug" => $slug,
            "plano" => $plano,
            "exibir_plano" => $plano instanceof Plano,
        ];
    }

    private function usuarioJaCadastrado(): bool
    {
        return !empty($_SESSION["usuario_id"]) && !empty($_SESSION["conta_id"]);
    }

    private function redirecionarUsuarioCadastrado(): void
    {
        if (CarrinhoSessao::possuiItem()) {
            header("Location:index.php?controle=pedidoController&metodo=checkout");
            exit;
        }

        header("Location:index.php?controle=dashboardController&metodo=cliente");
        exit;
    }

    private function resolverPlano(string $slug): ?Plano
    {
        $planoDAO = new PlanoDAO();
        $plano = $planoDAO->buscarPorSlug($slug);

        return $plano ?: Plano::buscarNoCatalogoInicial($slug);
    }

    private function processarCadastro(
        array &$msg,
        array $dados,
        string $tipoCliente,
        string $codigoPlano,
        bool $exibirPlanoContratacao
    ): void {
        $email = trim($dados["email"] ?? "");
        $senha = $dados["senha"] ?? "";
        $senhaConfirmacao = $dados["senha_confirmacao"] ?? "";

        if ($tipoCliente !== "PF" && $tipoCliente !== "PJ") {
            $msg["tipo_cliente"] = "Tipo de cadastro invalido.";
            return;
        }

        $camposObrigatorios = [
            "email" => "Informe o e-mail de acesso.",
            "senha" => "Crie uma senha.",
            "logradouro" => "Informe o endereco.",
            "numero" => "Informe o numero.",
            "bairro" => "Informe o bairro.",
            "cidade" => "Informe a cidade.",
            "estado" => "Informe o estado.",
            "cep" => "Informe o CEP.",
            "ddd" => "Informe o DDD.",
            "numero_telefone" => "Informe o numero de telefone.",
        ];

        foreach ($camposObrigatorios as $campo => $mensagem) {
            if (trim($dados[$campo] ?? "") === "") {
                $msg[$campo] = $mensagem;
            }
        }

        if ($tipoCliente === "PF") {
            foreach (["nome", "sobrenome", "cpf", "data_nascimento"] as $campoPF) {
                if (trim($dados[$campoPF] ?? "") === "") {
                    $msg[$campoPF] = "Campo obrigatorio para pessoa fisica.";
                }
            }
        } else {
            if (trim($dados["razao_social"] ?? "") === "") {
                $msg["razao_social"] = "Informe a razao social.";
            }
            if (trim($dados["cnpj"] ?? "") === "") {
                $msg["cnpj"] = "Informe o CNPJ.";
            }
            if (trim($dados["representante_nome"] ?? "") === "") {
                $msg["representante_nome"] = "Informe o nome do representante legal.";
            }
            if (trim($dados["representante_cpf"] ?? "") === "") {
                $msg["representante_cpf"] = "Informe o CPF do representante legal.";
            }
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg["email"] = "Informe um e-mail valido.";
        }

        if (strlen($senha) < 8) {
            $msg["senha"] = "A senha deve ter no minimo 8 caracteres.";
        }

        if ($senha !== $senhaConfirmacao) {
            $msg["senha_confirmacao"] = "As senhas nao conferem.";
        }

        if (!isset($dados["aceite_termos"])) {
            $msg["aceite_termos"] = "Aceite os termos para continuar.";
        }

        $usuarioDAO = new UsuarioDAO();
        if ($email !== "" && $usuarioDAO->buscarPorEmail($email)) {
            $msg["email"] = "Este e-mail ja possui conta.";
        }

        $codigoPais = trim($dados["codigo_pais"] ?? "55") ?: "55";
        $validacaoTel = TelefoneHelper::validarBrasil(
            (string) ($dados["ddd"] ?? ""),
            (string) ($dados["numero_telefone"] ?? "")
        );
        if (!$validacaoTel["ok"]) {
            $msg["numero_telefone"] = $validacaoTel["msg"];
        }

        if (count($msg) > 0) {
            return;
        }

        $nomeExibicao = $tipoCliente === "PF"
            ? trim(($dados["nome"] ?? "") . " " . ($dados["sobrenome"] ?? ""))
            : trim($dados["razao_social"] ?? "");

        $usuario = new Usuario(
            email: strtolower($email),
            situacao: "ativo",
            nomeExibicao: $nomeExibicao
        );

        $cliente = new Cliente(
            tipoCliente: $tipoCliente,
            nome: trim($dados["nome"] ?? ""),
            sobrenome: trim($dados["sobrenome"] ?? ""),
            cpf: preg_replace("/\D/", "", $dados["cpf"] ?? ""),
            dataNascimento: $dados["data_nascimento"] ?? "",
            nomeEmpresarial: trim($dados["razao_social"] ?? ""),
            nomeFantasia: trim($dados["nome_fantasia"] ?? ""),
            cnpj: preg_replace("/\D/", "", $dados["cnpj"] ?? "")
        );

        $endereco = new Endereco(
            logradouro: trim($dados["logradouro"] ?? ""),
            numero: trim($dados["numero"] ?? ""),
            complemento: trim($dados["complemento"] ?? ""),
            bairro: trim($dados["bairro"] ?? ""),
            cidade: trim($dados["cidade"] ?? ""),
            estado: trim($dados["estado"] ?? ""),
            cep: preg_replace("/\D/", "", $dados["cep"] ?? "")
        );

        $representanteNome = trim($dados["representante_nome"] ?? "");
        $representanteCpf = preg_replace("/\D/", "", $dados["representante_cpf"] ?? "");

        $clienteDAO = new ClienteDAO();
        $resultado = $clienteDAO->cadastrarComUsuario(
            $cliente,
            $endereco,
            $usuario,
            $senha,
            $representanteNome,
            $representanteCpf,
            $codigoPais,
            $validacaoTel["ddd"],
            $validacaoTel["numero"],
            $validacaoTel["tipo"]
        );

        if (is_array($resultado) && isset($resultado["conta_id"], $resultado["usuario_id"])) {
            $_SESSION["usuario_id"] = (int) $resultado["usuario_id"];
            $_SESSION["usuario_nome"] = $nomeExibicao;
            $_SESSION["usuario_papel"] = "titular";
            $_SESSION["conta_id"] = (int) $resultado["conta_id"];

            if ($exibirPlanoContratacao && $codigoPlano !== "") {
                CarrinhoSessao::definirPlano($codigoPlano);
                header("Location:index.php?controle=pedidoController&metodo=checkout");
                exit;
            }

            CarrinhoSessao::limpar();
            $_SESSION["flash_sucesso"] = "Cadastro concluido com sucesso. Escolha um produto para contratar.";
            header("Location:index.php?controle=dashboardController&metodo=cliente");
            exit;
        }

        $msg["geral"] = is_string($resultado)
            ? $resultado
            : "Nao foi possivel concluir o cadastro.";
    }

    private function renderizarFormularioCadastro(
        string $tipoCliente,
        ?Plano $planoSelecionado,
        bool $exibirPlanoContratacao,
        array $msg,
        array $dados
    ): void {
        if ($tipoCliente === "PJ") {
            require_once "views/cadastro_pj.php";
            return;
        }

        if ($tipoCliente === "PF") {
            require_once "views/cadastro_pf.php";
            return;
        }

        require_once "views/cadastro_escolha.php";
    }
}
