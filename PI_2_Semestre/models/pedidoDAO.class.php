<?php
class PedidoDAO extends Conexao
{
    public function __construct()
    {
        parent::__construct();
    }

    public function criarPedidoAprovado(Pedido $pedido, Plano $plano, Pagamento $pagamento)
    {
        if (!$this->possuiConexao()) {
            return $this->mensagemBancoIndisponivel();
        }

        if ($pagamento->getIdEndereco() <= 0) {
            return "Selecione um endereco de faturamento.";
        }

        try {
            $this->db->beginTransaction();

            $stmPedido = $this->db->prepare(
                "INSERT INTO pedidos (conta_id, plano_id, valor_total, situacao)
                 VALUES (?, ?, ?, 'pago')"
            );
            $stmPedido->execute([
                $pedido->getIdConta(),
                $plano->getIdPlano(),
                $pedido->getValorTotal(),
            ]);
            $pedidoId = (int) $this->db->lastInsertId();

            $stmPagamento = $this->db->prepare(
                "INSERT INTO pagamentos
                    (pedido_id, endereco_id, valor, gateway, forma_pagamento, situacao, identificador_transacao, confirmado_em)
                 VALUES (?, ?, ?, ?, ?, 'confirmado', ?, NOW())"
            );
            $stmPagamento->execute([
                $pedidoId,
                $pagamento->getIdEndereco(),
                $pagamento->getValor(),
                $pagamento->getGateway(),
                $this->normalizarFormaPagamento($pagamento->getFormaPagamento()),
                "SIM-" . date("YmdHis") . "-" . $pedidoId,
            ]);

            $stmVm = $this->db->prepare(
                "INSERT INTO maquinas_virtuais (pedido_id, estado)
                 VALUES (?, 'aguardando_configuracao')"
            );
            $stmVm->execute([$pedidoId]);
            $maquinaVirtualId = (int) $this->db->lastInsertId();

            $stmSolicitacao = $this->db->prepare(
                "INSERT INTO provisionamento_solicitacoes (maquina_virtual_id, situacao)
                 VALUES (?, 'rascunho')"
            );
            $stmSolicitacao->execute([$maquinaVirtualId]);
            $solicitacaoId = (int) $this->db->lastInsertId();

            $stmEvento = $this->db->prepare(
                "INSERT INTO eventos_provisionamento (solicitacao_id, situacao, mensagem)
                 VALUES (?, 'pendente', 'Pedido pago e enviado para fila operacional.')"
            );
            $stmEvento->execute([$solicitacaoId]);

            $this->db->commit();
            return [
                "pedido_id" => $pedidoId,
                "maquina_virtual_id" => $maquinaVirtualId,
                "solicitacao_id" => $solicitacaoId,
            ];
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            return "Problema ao criar o pedido.";
        }
    }

    public function buscarPorConta(int $contaId)
    {
        if (!$this->possuiConexao()) {
            return [];
        }

        $sql = "SELECT p.id_pedido, p.situacao, p.valor_total, p.criado_em,
                       pl.nome AS plano_nome
                  FROM pedidos p
                  JOIN planos_vps pl ON pl.id_plano = p.plano_id
                 WHERE p.conta_id = ?
              ORDER BY p.criado_em DESC";

        try {
            $stm = $this->db->prepare($sql);
            $stm->execute([$contaId]);
            return $stm->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    private function normalizarFormaPagamento(string $metodo): string
    {
        $mapa = [
            "pix" => "pix",
            "cartao" => "cartao",
            "simulado" => "simulado",
        ];

        return $mapa[$metodo] ?? "simulado";
    }
}
