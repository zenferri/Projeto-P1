<?php
require_once "models/conexao.class.php";
require_once "models/sessaoHelper.class.php";
require_once "models/maquinaVirtualDAO.class.php";

class MaquinaVirtualController
{
    public function salvarHostname()
    {
        SessaoHelper::exigirContaCliente();
        if (!$_POST) {
            header("Location:index.php?controle=dashboardController&metodo=cliente");
            exit;
        }

        $contaId = SessaoHelper::contaEmSessao();
        $maquinaId = (int) ($_POST["maquina_id"] ?? 0);
        $hostname = trim($_POST["hostname"] ?? "");
        $dao = new MaquinaVirtualDAO();

        if ($maquinaId > 0 && $dao->atualizarHostname($maquinaId, $contaId, $hostname)) {
            $_SESSION["flash_sucesso"] = "Hostname da VM #{$maquinaId} atualizado.";
        } else {
            $_SESSION["flash_sucesso"] = "Nao foi possivel atualizar o hostname (apenas VMs aguardando configuracao).";
        }

        header("Location:index.php?controle=dashboardController&metodo=cliente");
        exit;
    }

    public function excluir()
    {
        SessaoHelper::exigirContaCliente();
        if (!$_POST) {
            header("Location:index.php?controle=dashboardController&metodo=cliente");
            exit;
        }

        $contaId = SessaoHelper::contaEmSessao();
        $maquinaId = (int) ($_POST["maquina_id"] ?? 0);
        $dao = new MaquinaVirtualDAO();

        if ($maquinaId > 0 && $dao->marcarDestruida($maquinaId, $contaId)) {
            $_SESSION["flash_sucesso"] = "VM #{$maquinaId} marcada como destruida. O registro permanece no historico da conta.";
        } else {
            $_SESSION["flash_sucesso"] = "Nao foi possivel remover a VM.";
        }

        header("Location:index.php?controle=dashboardController&metodo=cliente");
        exit;
    }
}
