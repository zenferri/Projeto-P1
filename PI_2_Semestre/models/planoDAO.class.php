<?php
require_once __DIR__ . "/conexao.class.php";
require_once __DIR__ . "/plano.class.php";

class PlanoDAO extends Conexao
{
    public function __construct()
    {
        parent::__construct();
    }

    public function buscarAtivos()
    {
        return $this->buscar("WHERE situacao = 'ativo' AND deletado_em IS NULL ORDER BY preco_mensal");
    }

    public function buscarTodos()
    {
        return $this->buscar(
            "WHERE deletado_em IS NULL ORDER BY FIELD(situacao, 'ativo', 'inativo', 'suspenso'), preco_mensal"
        );
    }

    /** Lista para o painel admin (inclui metadados e soft delete). */
    public function listarParaAdmin(): array
    {
        if (!$this->possuiConexao()) {
            return [];
        }

        try {
            $sql = "SELECT id_plano, nome, slug, preco_mensal, vcpu, ram_mb, storage_gb,
                           banda_mbps, situacao, deletado_em, criado_em
                      FROM planos_vps
                  ORDER BY deletado_em IS NULL DESC, FIELD(situacao, 'ativo', 'inativo', 'suspenso'), preco_mensal";
            return $this->db->query($sql)->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function buscarRegistroPorId(int $idPlano): ?object
    {
        if (!$this->possuiConexao() || $idPlano <= 0) {
            return null;
        }

        try {
            $stm = $this->db->prepare("SELECT * FROM planos_vps WHERE id_plano = ? LIMIT 1");
            $stm->execute([$idPlano]);
            return $stm->fetch() ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function buscarPorCodigo(string $codigo)
    {
        return $this->buscarPorSlug($codigo);
    }

    public function buscarPorSlug(string $slug)
    {
        if (!$this->possuiConexao()) {
            return null;
        }

        $sql = "SELECT * FROM planos_vps
                 WHERE slug = ? AND situacao = 'ativo' AND deletado_em IS NULL
                 LIMIT 1";

        try {
            $stm = $this->db->prepare($sql);
            $stm->execute([strtolower($slug)]);
            $dado = $stm->fetch();
            return $dado ? $this->montarPlano($dado) : null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function salvarAdmin(array $dados): array
    {
        if (!$this->possuiConexao()) {
            return ["ok" => false, "msg" => "Banco indisponivel."];
        }

        $id = (int) ($dados["id_plano"] ?? 0);
        $nome = trim($dados["nome"] ?? "");
        $slug = strtolower(trim($dados["slug"] ?? ""));
        $descricao = trim($dados["descricao"] ?? "");
        $vcpu = (int) ($dados["vcpu"] ?? 0);
        $ramMb = (int) ($dados["ram_mb"] ?? 0);
        $storageGb = (int) ($dados["storage_gb"] ?? 0);
        $banda = (int) ($dados["banda_mbps"] ?? 100);
        $preco = (float) str_replace(",", ".", (string) ($dados["preco_mensal"] ?? "0"));
        $situacao = trim($dados["situacao"] ?? "ativo");

        if ($nome === "" || $slug === "" || $vcpu <= 0 || $ramMb <= 0 || $storageGb <= 0 || $preco <= 0) {
            return ["ok" => false, "msg" => "Preencha nome, slug, recursos e preco validos."];
        }

        $situacoes = ["ativo", "inativo", "suspenso"];
        if (!in_array($situacao, $situacoes, true)) {
            return ["ok" => false, "msg" => "Situacao invalida."];
        }

        try {
            if ($id > 0) {
                $stm = $this->db->prepare(
                    "UPDATE planos_vps
                        SET nome = ?, slug = ?, descricao = ?, vcpu = ?, ram_mb = ?, storage_gb = ?,
                            banda_mbps = ?, preco_mensal = ?, situacao = ?, alterado_em = NOW(), deletado_em = NULL
                      WHERE id_plano = ?"
                );
                $stm->execute([
                    $nome, $slug, $descricao !== "" ? $descricao : null,
                    $vcpu, $ramMb, $storageGb, $banda, $preco, $situacao, $id,
                ]);
                return ["ok" => true, "msg" => "Plano #{$id} atualizado.", "id" => $id];
            }

            $stm = $this->db->prepare(
                "INSERT INTO planos_vps
                    (nome, slug, descricao, vcpu, ram_mb, storage_gb, banda_mbps, preco_mensal, situacao)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stm->execute([
                $nome, $slug, $descricao !== "" ? $descricao : null,
                $vcpu, $ramMb, $storageGb, $banda, $preco, $situacao,
            ]);
            $novoId = (int) $this->db->lastInsertId();
            return ["ok" => true, "msg" => "Plano #{$novoId} criado.", "id" => $novoId];
        } catch (PDOException $e) {
            if ((int) $e->errorInfo[1] === 1062) {
                return ["ok" => false, "msg" => "Slug ja existe. Escolha outro identificador."];
            }
            return ["ok" => false, "msg" => "Nao foi possivel salvar o plano."];
        }
    }

    public function softDelete(int $idPlano): bool
    {
        if (!$this->possuiConexao() || $idPlano <= 0) {
            return false;
        }

        try {
            $stm = $this->db->prepare(
                "UPDATE planos_vps
                    SET situacao = 'inativo', deletado_em = NOW(), alterado_em = NOW()
                  WHERE id_plano = ? AND deletado_em IS NULL"
            );
            $stm->execute([$idPlano]);
            return $stm->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    private function buscar(string $complementoSql)
    {
        if (!$this->possuiConexao()) {
            return [];
        }

        try {
            $stm = $this->db->prepare("SELECT * FROM planos_vps {$complementoSql}");
            $stm->execute();
            $planos = [];

            foreach ($stm->fetchAll() as $dado) {
                $planos[] = $this->montarPlano($dado);
            }

            return $planos;
        } catch (PDOException $e) {
            return [];
        }
    }

    private function montarPlano($dado)
    {
        return new Plano(
            (int) $dado->id_plano,
            $dado->slug,
            $dado->nome,
            (float) $dado->preco_mensal,
            (int) $dado->vcpu,
            (int) $dado->ram_mb,
            (int) $dado->storage_gb,
            (int) $dado->banda_mbps,
            $dado->situacao
        );
    }
}
