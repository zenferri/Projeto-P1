<?php
require_once "models/conexao.class.php";
require_once "models/sessaoHelper.class.php";
require_once "models/adminDAO.class.php";
require_once "models/planoDAO.class.php";
require_once "models/plano.class.php";
require_once "models/contaDAO.class.php";

class AdminController
{
    private function flashRedirect(string $metodo, string $mensagem): void
    {
        $_SESSION["flash_admin"] = $mensagem;
        header("Location:index.php?controle=adminController&metodo={$metodo}");
        exit;
    }

    public function painel()
    {
        SessaoHelper::exigirAdministrador();
        $adminDAO = new AdminDAO();
        $resumo = $adminDAO->resumoPainel();
        require_once "views/admin/painel.php";
    }

    public function contas()
    {
        SessaoHelper::exigirAdministrador();
        $adminDAO = new AdminDAO();
        $usuarioSessaoId = (int) ($_SESSION["usuario_id"] ?? 0);
        $contas = $adminDAO->listarContas($usuarioSessaoId);
        $msg = $_SESSION["flash_admin"] ?? "";
        unset($_SESSION["flash_admin"]);
        require_once "views/admin/contas.php";
    }

    public function usuarios()
    {
        SessaoHelper::exigirAdministrador();
        $adminDAO = new AdminDAO();
        $usuarioSessaoId = (int) ($_SESSION["usuario_id"] ?? 0);
        $usuarios = $adminDAO->listarUsuarios($usuarioSessaoId);
        $msg = $_SESSION["flash_admin"] ?? "";
        unset($_SESSION["flash_admin"]);
        $situacoesUsuario = [
            "pendente_verificacao_email",
            "cadastro_incompleto",
            "ativo",
            "inativo",
            "bloqueado",
            "expirado",
        ];
        require_once "views/admin/usuarios.php";
    }

    public function pedidos()
    {
        SessaoHelper::exigirAdministrador();
        $adminDAO = new AdminDAO();
        $pedidos = $adminDAO->listarPedidos();
        $msg = $_SESSION["flash_admin"] ?? "";
        unset($_SESSION["flash_admin"]);
        $situacoesPedido = ["criado", "aguardando_pagamento", "pago", "cancelado", "falhou"];
        require_once "views/admin/pedidos.php";
    }

    public function pagamentos()
    {
        SessaoHelper::exigirAdministrador();
        $adminDAO = new AdminDAO();
        $pagamentos = $adminDAO->listarPagamentos();
        $msg = $_SESSION["flash_admin"] ?? "";
        unset($_SESSION["flash_admin"]);
        $situacoesPagamento = ["pendente", "confirmado", "recusado", "cancelado"];
        require_once "views/admin/pagamentos.php";
    }

    public function planos()
    {
        SessaoHelper::exigirAdministrador();
        $planoDAO = new PlanoDAO();
        $planos = $planoDAO->listarParaAdmin();
        $msg = $_SESSION["flash_admin"] ?? "";
        unset($_SESSION["flash_admin"]);
        require_once "views/admin/planos.php";
    }

    public function planoForm()
    {
        SessaoHelper::exigirAdministrador();
        $planoDAO = new PlanoDAO();
        $id = (int) ($_GET["id"] ?? 0);
        $plano = $id > 0 ? $planoDAO->buscarRegistroPorId($id) : null;
        $msg = [];
        require_once "views/admin/plano_form.php";
    }

    public function salvarPlano()
    {
        SessaoHelper::exigirAdministrador();
        if (!$_POST) {
            $this->flashRedirect("planos", "Requisicao invalida.");
        }

        $ramGb = (int) ($_POST["ram_gb"] ?? 0);
        if ($ramGb > 0) {
            $_POST["ram_mb"] = $ramGb * 1024;
        }

        $planoDAO = new PlanoDAO();
        $resultado = $planoDAO->salvarAdmin($_POST);
        if (empty($resultado["ok"])) {
            $_SESSION["flash_admin"] = $resultado["msg"] ?? "Erro ao salvar plano.";
            header("Location:index.php?controle=adminController&metodo=planoForm&id=" . (int) ($_POST["id_plano"] ?? 0));
            exit;
        }
        $this->flashRedirect("planos", $resultado["msg"]);
    }

    public function excluirPlano()
    {
        SessaoHelper::exigirAdministrador();
        $id = (int) ($_POST["id_plano"] ?? 0);
        $planoDAO = new PlanoDAO();
        if ($id > 0 && $planoDAO->softDelete($id)) {
            $this->flashRedirect("planos", "Plano #{$id} desativado (soft delete).");
        }
        $this->flashRedirect("planos", "Nao foi possivel desativar o plano.");
    }

    public function alterarConta()
    {
        SessaoHelper::exigirAdministrador();
        if (!$_POST) {
            header("Location:index.php?controle=adminController&metodo=contas");
            exit;
        }

        $contaId = (int) ($_POST["conta_id"] ?? 0);
        $situacao = trim($_POST["situacao"] ?? "");
        $adminDAO = new AdminDAO();
        $usuarioSessaoId = (int) ($_SESSION["usuario_id"] ?? 0);

        if ($contaId > 0 && $adminDAO->atualizarSituacaoConta($contaId, $situacao, $usuarioSessaoId)) {
            $this->flashRedirect("contas", "Conta #{$contaId} atualizada.");
        }
        $this->flashRedirect("contas", "Alteracao de situacao nao permitida para esta conta.");
    }

    public function salvarUsuario()
    {
        SessaoHelper::exigirAdministrador();
        $id = (int) ($_POST["usuario_id"] ?? 0);
        $situacao = trim($_POST["situacao"] ?? "");
        $adminDAO = new AdminDAO();
        $usuarioSessaoId = (int) ($_SESSION["usuario_id"] ?? 0);

        if ($id > 0 && $adminDAO->atualizarSituacaoUsuario($id, $situacao, $usuarioSessaoId)) {
            $this->flashRedirect("usuarios", "Usuario #{$id} atualizado.");
        }
        $this->flashRedirect("usuarios", "Alteracao de situacao nao permitida para este usuario.");
    }

    public function salvarPedido()
    {
        SessaoHelper::exigirAdministrador();
        $id = (int) ($_POST["pedido_id"] ?? 0);
        $situacao = trim($_POST["situacao"] ?? "");
        $adminDAO = new AdminDAO();

        if ($id > 0 && $adminDAO->atualizarSituacaoPedido($id, $situacao)) {
            $this->flashRedirect("pedidos", "Pedido #{$id} atualizado.");
        }
        $this->flashRedirect("pedidos", "Nao foi possivel atualizar o pedido.");
    }

    public function salvarPagamento()
    {
        SessaoHelper::exigirAdministrador();
        $id = (int) ($_POST["pagamento_id"] ?? 0);
        $situacao = trim($_POST["situacao"] ?? "");
        $adminDAO = new AdminDAO();

        if ($id > 0 && $adminDAO->atualizarSituacaoPagamento($id, $situacao)) {
            $this->flashRedirect("pagamentos", "Pagamento #{$id} atualizado.");
        }
        $this->flashRedirect("pagamentos", "Nao foi possivel atualizar o pagamento.");
    }
}
