<?php
class ContaDAO extends Conexao
{
    public function __construct()
    {
        parent::__construct();
    }

    public function buscarPorId(int $contaId): ?object
    {
        if (!$this->possuiConexao() || $contaId <= 0) {
            return null;
        }

        $sql = "SELECT c.id_conta, c.tipo_pessoa, c.situacao,
                       pf.nome AS pf_nome, pf.cpf, pf.data_nascimento, pf.usuario_id AS pf_usuario_id,
                       pj.nome_empresarial, pj.nome_fantasia, pj.cnpj,
                       pj.representante_nome, pj.representante_cpf
                  FROM contas c
             LEFT JOIN pessoas_fisicas pf ON pf.conta_id = c.id_conta
             LEFT JOIN pessoas_juridicas pj ON pj.conta_id = c.id_conta
                 WHERE c.id_conta = ?
                 LIMIT 1";

        try {
            $stm = $this->db->prepare($sql);
            $stm->execute([$contaId]);
            return $stm->fetch() ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function usuarioTemAcessoConta(int $usuarioId, int $contaId): bool
    {
        if (!$this->possuiConexao()) {
            return false;
        }

        $stm = $this->db->prepare(
            "SELECT 1 FROM usuarios_contas
              WHERE usuario_id = ? AND conta_id = ? AND situacao = 'ativo'
              LIMIT 1"
        );
        $stm->execute([$usuarioId, $contaId]);
        if ($stm->fetch()) {
            return true;
        }

        $stmPf = $this->db->prepare(
            "SELECT 1 FROM pessoas_fisicas WHERE usuario_id = ? AND conta_id = ? LIMIT 1"
        );
        $stmPf->execute([$usuarioId, $contaId]);

        return (bool) $stmPf->fetch();
    }

    public function atualizarPf(int $contaId, string $nome, string $cpf, string $dataNascimento): bool
    {
        if (!$this->possuiConexao()) {
            return false;
        }

        try {
            $stm = $this->db->prepare(
                "UPDATE pessoas_fisicas
                    SET nome = ?, cpf = ?, data_nascimento = ?, alterado_em = NOW()
                  WHERE conta_id = ?"
            );
            $stm->execute([$nome, $cpf, $dataNascimento, $contaId]);
            return $stm->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function atualizarPj(
        int $contaId,
        string $nomeEmpresarial,
        ?string $nomeFantasia,
        string $cnpj,
        string $representanteNome,
        string $representanteCpf
    ): bool {
        if (!$this->possuiConexao()) {
            return false;
        }

        try {
            $stm = $this->db->prepare(
                "UPDATE pessoas_juridicas
                    SET nome_empresarial = ?, nome_fantasia = ?, cnpj = ?,
                        representante_nome = ?, representante_cpf = ?, alterado_em = NOW()
                  WHERE conta_id = ?"
            );
            $stm->execute([
                $nomeEmpresarial,
                $nomeFantasia ?: null,
                $cnpj,
                $representanteNome,
                $representanteCpf,
                $contaId,
            ]);
            return $stm->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}
