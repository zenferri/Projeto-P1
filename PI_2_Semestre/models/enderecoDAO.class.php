<?php
require_once __DIR__ . "/conexao.class.php";

class EnderecoDAO extends Conexao
{
    public function __construct()
    {
        parent::__construct();
    }

    public function inserirEVincularConta(int $contaId, Endereco $endereco): ?int
    {
        if (!$this->possuiConexao() || $contaId <= 0) {
            return null;
        }

        try {
            $stmEndereco = $this->db->prepare(
                "INSERT INTO enderecos (cep, logradouro, numero, complemento, bairro, cidade, estado, pais)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmEndereco->execute([
                $endereco->getCep(),
                $endereco->getLogradouro(),
                $endereco->getNumero(),
                $endereco->getComplemento() ?: null,
                $endereco->getBairro(),
                $endereco->getCidade(),
                strtoupper($endereco->getEstado()) ?: null,
                $endereco->getPais(),
            ]);
            $enderecoId = (int) $this->db->lastInsertId();

            $stmVinculo = $this->db->prepare(
                "INSERT INTO contas_enderecos (conta_id, endereco_id, situacao) VALUES (?, ?, 'ativo')"
            );
            $stmVinculo->execute([$contaId, $enderecoId]);

            return $enderecoId;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function listarAtivosPorConta(int $contaId): array
    {
        if (!$this->possuiConexao() || $contaId <= 0) {
            return [];
        }

        $sql = "SELECT e.id_endereco, e.cep, e.logradouro, e.numero, e.complemento,
                       e.bairro, e.cidade, e.estado, e.pais, ce.situacao AS vinculo_situacao
                  FROM contas_enderecos ce
                  JOIN enderecos e ON e.id_endereco = ce.endereco_id
                 WHERE ce.conta_id = ?
                   AND ce.situacao = 'ativo'
              ORDER BY ce.criado_em ASC";

        try {
            $stm = $this->db->prepare($sql);
            $stm->execute([$contaId]);
            return $stm->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function enderecoPertenceConta(int $contaId, int $enderecoId): bool
    {
        if (!$this->possuiConexao() || $contaId <= 0 || $enderecoId <= 0) {
            return false;
        }

        $stm = $this->db->prepare(
            "SELECT 1 FROM contas_enderecos
              WHERE conta_id = ? AND endereco_id = ? AND situacao = 'ativo'
              LIMIT 1"
        );
        $stm->execute([$contaId, $enderecoId]);

        return (bool) $stm->fetch();
    }

    public function desativarVinculo(int $contaId, int $enderecoId): bool
    {
        if (!$this->possuiConexao()) {
            return false;
        }

        try {
            $stm = $this->db->prepare(
                "UPDATE contas_enderecos
                    SET situacao = 'desativado', desativado_em = NOW()
                  WHERE conta_id = ? AND endereco_id = ? AND situacao = 'ativo'"
            );
            $stm->execute([$contaId, $enderecoId]);
            return $stm->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}
