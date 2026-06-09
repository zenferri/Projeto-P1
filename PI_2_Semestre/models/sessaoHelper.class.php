<?php
require_once __DIR__ . "/carrinhoSessao.class.php";

class SessaoHelper
{
    public static function usuarioLogado(): bool
    {
        return !empty($_SESSION["usuario_id"]);
    }

    public static function isAdministrador(): bool
    {
        return ($_SESSION["usuario_papel"] ?? "") === "administrador";
    }

    public static function contaEmSessao(): int
    {
        return (int) ($_SESSION["conta_id"] ?? 0);
    }

    public static function exigirLogin(): void
    {
        if (!self::usuarioLogado()) {
            header("Location:index.php?controle=usuarioController&metodo=login");
            exit;
        }
    }

    public static function exigirAdministrador(): void
    {
        self::exigirLogin();
        if (!self::isAdministrador()) {
            http_response_code(403);
            $mensagemErro = "Acesso restrito ao administrador da plataforma.";
            require_once "views/erro.php";
            exit;
        }
    }

    public static function exigirContaCliente(): void
    {
        self::exigirLogin();
        if (self::isAdministrador()) {
            header("Location:index.php?controle=adminController&metodo=painel");
            exit;
        }
        if (self::contaEmSessao() <= 0) {
            header("Location:index.php?controle=clienteController&metodo=cadastrar");
            exit;
        }
    }

    public static function redirecionarPosLogin(): string
    {
        if (self::isAdministrador()) {
            return "index.php?controle=adminController&metodo=painel";
        }
        if (self::contaEmSessao() <= 0) {
            return "index.php?controle=clienteController&metodo=cadastrar";
        }
        if (CarrinhoSessao::possuiItem()) {
            return "index.php?controle=pedidoController&metodo=checkout";
        }
        return "index.php?controle=dashboardController&metodo=cliente";
    }
}
