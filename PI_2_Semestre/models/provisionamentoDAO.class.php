<?php
class ProvisionamentoDAO extends Conexao
{
    public function __construct()
    {
        parent::__construct();
    }

    public function buscarRecentesPorConta(int $contaId)
    {
        if (!$this->possuiConexao() || $contaId <= 0) {
            return [];
        }

        $sql = "SELECT ps.id_solicitacao, mv.hostname, ps.situacao,
                       pl.nome AS plano,
                       ev.situacao AS situacao_evento, ev.mensagem, ev.criado_em AS evento_em
                  FROM provisionamento_solicitacoes ps
                  JOIN maquinas_virtuais mv ON mv.id_maquina_virtual = ps.maquina_virtual_id
                  JOIN pedidos pe ON pe.id_pedido = mv.pedido_id
                  JOIN planos_vps pl ON pl.id_plano = pe.plano_id
             LEFT JOIN eventos_provisionamento ev ON ev.id_evento = (
                       SELECT e2.id_evento
                         FROM eventos_provisionamento e2
                        WHERE e2.solicitacao_id = ps.id_solicitacao
                     ORDER BY e2.criado_em DESC
                        LIMIT 1
                   )
                 WHERE pe.conta_id = ?
              ORDER BY ps.criado_em DESC
                 LIMIT 10";

        try {
            $stm = $this->db->prepare($sql);
            $stm->execute([$contaId]);
            return $stm->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}
