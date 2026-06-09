<?php
require_once __DIR__ . "/conexao.class.php";

class TelefoneDAO extends Conexao
{
    public function __construct()
    {
        parent::__construct();
    }

    public function inserirEVincularConta(
        int $contaId,
        string $codigoPais,
        string $ddd,
        string $numero,
        string $tipoTelefone
    ): ?int {
        if (!$this->possuiConexao() || $contaId <= 0) {
            return null;
        }

        try {
            $stmTel = $this->db->prepare(
                "INSERT INTO telefones (codigo_pais, ddd, numero, tipo_telefone)
                 VALUES (?, ?, ?, ?)"
            );
            $stmTel->execute([
                $codigoPais,
                $ddd !== "" ? $ddd : null,
                $numero,
                $tipoTelefone,
            ]);
            $telefoneId = (int) $this->db->lastInsertId();

            $stmVinculo = $this->db->prepare(
                "INSERT INTO contas_telefones (conta_id, telefone_id) VALUES (?, ?)"
            );
            $stmVinculo->execute([$contaId, $telefoneId]);

            return $telefoneId;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function listarPorConta(int $contaId): array
    {
        if (!$this->possuiConexao() || $contaId <= 0) {
            return [];
        }

        $sql = "SELECT t.id_telefone, t.codigo_pais, t.ddd, t.numero, t.tipo_telefone
                  FROM contas_telefones ct
                  JOIN telefones t ON t.id_telefone = ct.telefone_id
                 WHERE ct.conta_id = ?
              ORDER BY ct.criado_em ASC";

        try {
            $stm = $this->db->prepare($sql);
            $stm->execute([$contaId]);
            return $stm->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}
