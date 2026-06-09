<?php
require_once "models/conexao.class.php";
require_once "models/usuario.class.php";
require_once "models/usuarioDAO.class.php";
require_once "models/carrinhoSessao.class.php";
require_once "models/sessaoHelper.class.php";

class UsuarioController
{
    public function login()
    {
        $msg = ["email" => "", "senha" => "", "conta_id" => "", "geral" => ""];
        $email = "";
        $contaIdForm = "";
        $planoPendente = CarrinhoSessao::obterSlug();

        if ($_POST) {
            $email = trim($_POST["email"] ?? "");
            $senha = (string) ($_POST["senha"] ?? "");
            $contaIdForm = trim($_POST["conta_id"] ?? "");
            $contaIdInformada = $contaIdForm !== "" ? (int) $contaIdForm : null;

            if ($email === "") {
                $msg["email"] = "Informe o e-mail.";
            }
            if ($senha === "") {
                $msg["senha"] = "Informe a senha.";
            }

            if ($msg["email"] === "" && $msg["senha"] === "") {
                $usuarioDAO = new UsuarioDAO();
                $usuarioBanco = $usuarioDAO->buscarPorEmail($email);
                $situacoesPermitidas = ["ativo", "cadastro_incompleto"];

                if ($usuarioBanco && $usuarioBanco->situacao === "pendente_verificacao_email") {
                    $msg["geral"] = "Confirme seu e-mail antes de entrar.";
                } elseif ($usuarioBanco && in_array($usuarioBanco->situacao, $situacoesPermitidas, true)) {
                    $usuarioAutenticado = $usuarioDAO->autenticar($email, $senha, $contaIdInformada);

                    if ($usuarioAutenticado) {
                        session_regenerate_id(true);
                        $_SESSION["usuario_id"] = (int) $usuarioAutenticado->id_usuario;
                        $_SESSION["usuario_nome"] = $usuarioAutenticado->nome_exibicao;
                        $_SESSION["usuario_papel"] = $usuarioAutenticado->nome_papel ?? "titular";
                        $_SESSION["conta_id"] = isset($usuarioAutenticado->conta_id)
                            ? (int) $usuarioAutenticado->conta_id
                            : null;

                        $usuarioDAO->registrarUltimoLogin((int) $usuarioAutenticado->id_usuario);
                        $destino = SessaoHelper::redirecionarPosLogin();
                        session_write_close();
                        header("Location:{$destino}");
                        exit;
                    }

                    $msg["geral"] = "E-mail, senha ou conta invalidos.";
                } else {
                    $msg["geral"] = "E-mail ou senha invalidos.";
                }
            }
        }

        require_once "views/login.php";
    }

    public function logout()
    {
        CarrinhoSessao::limpar();
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), "", time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        session_destroy();

        header("Location:index.php");
        exit;
    }
}
