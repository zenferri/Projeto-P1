<?php
require_once "models/conexao.class.php";
require_once "models/sessaoHelper.class.php";
require_once "models/maquinaVirtualDAO.class.php";
require_once "models/provisionamentoDAO.class.php";
require_once "models/telefoneDAO.class.php";
require_once "models/telefoneHelper.class.php";

class DashboardController
{
    public function cliente()
    {
        SessaoHelper::exigirContaCliente();

        $contaId = SessaoHelper::contaEmSessao();
        $maquinaVirtualDAO = new MaquinaVirtualDAO();
        $provisionamentoDAO = new ProvisionamentoDAO();
        $telefoneDAO = new TelefoneDAO();

        $maquinasVirtuais = $maquinaVirtualDAO->buscarPorConta($contaId);
        $telefones = $telefoneDAO->listarPorConta($contaId);
        $provisionamentos = $provisionamentoDAO->buscarRecentesPorConta($contaId);

        if (!is_array($maquinasVirtuais)) {
            $maquinasVirtuais = [];
        }
        if (!is_array($provisionamentos)) {
            $provisionamentos = [];
        }

        $flashSucesso = $_SESSION["flash_sucesso"] ?? "";
        unset($_SESSION["flash_sucesso"]);

        require_once "views/dashboard.php";
    }
}
