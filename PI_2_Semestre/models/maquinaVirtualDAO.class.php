<?php
class MaquinaVirtualDAO extends Conexao
{
    public function __construct()
    {
        parent::__construct();
    }

    public function buscarPorConta(int $contaId)
    {
        if (!$this->possuiConexao() || $contaId <= 0) {
            return [];
        }

        $sql = "SELECT mv.id_maquina_virtual, mv.hostname, mv.estado, mv.ipv4_publico,
                       mv.provisionada_em, mv.vcpu_aplicado, mv.ram_mb_aplicado,
                       COALESCE(sl.nome, 'A definir') AS sabor_linux,
                       COALESCE(sl.distribuicao, '') AS distribuicao,
                       COALESCE(sl.versao, '') AS versao,
                       pl.nome AS plano, pl.vcpu, pl.ram_mb, pl.storage_gb
                  FROM maquinas_virtuais mv
                  JOIN pedidos pe ON pe.id_pedido = mv.pedido_id
                  JOIN planos_vps pl ON pl.id_plano = pe.plano_id
             LEFT JOIN sabores_linux sl ON sl.id_sabor_linux = mv.sabor_linux_id
                 WHERE pe.conta_id = ?
              ORDER BY mv.criado_em DESC";

        try {
            $stm = $this->db->prepare($sql);
            $stm->execute([$contaId]);
            return $stm->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function buscarPorIdEConta(int $maquinaId, int $contaId): ?object
    {
        if (!$this->possuiConexao() || $maquinaId <= 0 || $contaId <= 0) {
            return null;
        }

        $sql = "SELECT mv.id_maquina_virtual, mv.hostname, mv.estado, mv.pedido_id
                  FROM maquinas_virtuais mv
                  JOIN pedidos pe ON pe.id_pedido = mv.pedido_id
                 WHERE mv.id_maquina_virtual = ? AND pe.conta_id = ?
                 LIMIT 1";

        try {
            $stm = $this->db->prepare($sql);
            $stm->execute([$maquinaId, $contaId]);
            $row = $stm->fetch();
            return $row ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function atualizarHostname(int $maquinaId, int $contaId, string $hostname): bool
    {
        if (!$this->possuiConexao() || $maquinaId <= 0 || $contaId <= 0) {
            return false;
        }

        $hostname = trim($hostname);
        if ($hostname === "" || strlen($hostname) > 255) {
            return false;
        }

        $vm = $this->buscarPorIdEConta($maquinaId, $contaId);
        if (!$vm || $vm->estado !== "aguardando_configuracao") {
            return false;
        }

        try {
            $stm = $this->db->prepare(
                "UPDATE maquinas_virtuais
                    SET hostname = ?, alterado_em = NOW()
                  WHERE id_maquina_virtual = ?
                    AND estado = 'aguardando_configuracao'"
            );
            $stm->execute([$hostname, $maquinaId]);
            return $stm->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function marcarDestruida(int $maquinaId, int $contaId): bool
    {
        if (!$this->possuiConexao() || $maquinaId <= 0 || $contaId <= 0) {
            return false;
        }

        $vm = $this->buscarPorIdEConta($maquinaId, $contaId);
        if (!$vm || $vm->estado === "destruida") {
            return false;
        }

        try {
            $stm = $this->db->prepare(
                "UPDATE maquinas_virtuais
                    SET estado = 'destruida', alterado_em = NOW()
                  WHERE id_maquina_virtual = ?
                    AND estado <> 'destruida'"
            );
            $stm->execute([$maquinaId]);
            return $stm->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}
