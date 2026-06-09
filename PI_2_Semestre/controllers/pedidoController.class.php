<?php
require_once "models/conexao.class.php";
require_once "models/plano.class.php";
require_once "models/planoDAO.class.php";
require_once "models/pedido.class.php";
require_once "models/pagamento.class.php";
require_once "models/pedidoDAO.class.php";
require_once "models/enderecoDAO.class.php";
require_once "models/carrinhoSessao.class.php";
require_once "models/sessaoHelper.class.php";

class PedidoController
{
    public function checkout()
    {
        SessaoHelper::exigirContaCliente();

        $slugPlano = CarrinhoSessao::obterSlug()
            ?? strtolower(trim($_GET["plano"] ?? $_POST["codigo_plano"] ?? ""));

        if ($slugPlano !== "") {
            CarrinhoSessao::definirPlano($slugPlano);
        }

        $plano = $this->resolverPlano($slugPlano);
        if (!$plano) {
            CarrinhoSessao::limpar();
            header("Location:index.php?controle=planoController&metodo=listar");
            exit;
        }

        $contaId = SessaoHelper::contaEmSessao();
        $enderecoDAO = new EnderecoDAO();
        $enderecos = $enderecoDAO->listarAtivosPorConta($contaId);
        $msg = [];

        if ($_POST) {
            $metodoPagamento = $_POST["metodo_pagamento"] ?? "";
            $enderecoId = (int) ($_POST["endereco_id"] ?? 0);

            if ($metodoPagamento !== "simulado") {
                $msg["geral"] = "Nesta versao de demonstracao, selecione Pagamento Simulado para concluir a compra.";
            } elseif ($enderecoId <= 0) {
                $msg["geral"] = "Selecione o endereco de faturamento.";
            } elseif (!$enderecoDAO->enderecoPertenceConta($contaId, $enderecoId)) {
                $msg["geral"] = "Endereco invalido para esta conta.";
            } else {
                $pedidoDAO = new PedidoDAO();
                $pedido = new Pedido(
                    idConta: $contaId,
                    valorTotal: (float) $plano->getPrecoMensal()
                );
                $pagamento = new Pagamento(
                    idEndereco: $enderecoId,
                    formaPagamento: "simulado",
                    valor: (float) $plano->getPrecoMensal(),
                    gateway: "simulado"
                );

                $resultado = $pedidoDAO->criarPedidoAprovado($pedido, $plano, $pagamento);

                if (is_array($resultado) && isset($resultado["pedido_id"])) {
                    CarrinhoSessao::limpar();
                    $_SESSION["flash_sucesso"] =
                        "Pagamento confirmado! Sua VPS \"{$plano->getNome()}\" esta aguardando configuracao.";
                    header("Location:index.php?controle=dashboardController&metodo=cliente");
                    exit;
                }

                $msg["geral"] = is_string($resultado) ? $resultado : "Nao foi possivel gerar o pedido.";
            }
        }

        require_once "views/checkout.php";
    }

    public function listar()
    {
        SessaoHelper::exigirContaCliente();
        $pedidoDAO = new PedidoDAO();
        $pedidos = $pedidoDAO->buscarPorConta(SessaoHelper::contaEmSessao());
        if (!is_array($pedidos)) {
            $pedidos = [];
        }
        require_once "views/listar_pedidos.php";
    }

    private function resolverPlano(string $slug): ?Plano
    {
        if ($slug === "") {
            return null;
        }
        $planoDAO = new PlanoDAO();
        $plano = $planoDAO->buscarPorSlug($slug);
        return $plano ?: Plano::buscarNoCatalogoInicial($slug);
    }
}
