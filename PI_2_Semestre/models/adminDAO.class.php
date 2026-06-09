<?php
require_once __DIR__ . "/conexao.class.php";

class AdminDAO extends Conexao
{
    public function __construct()
    {
        parent::__construct();
    }

    public function resumoPainel(): array
    {
        if (!$this->possuiConexao()) {
            return [];
        }

        try {
            return [
                "usuarios" => (int) $this->db->query("SELECT COUNT(*) AS t FROM usuarios")->fetch()->t,
                "contas" => (int) $this->db->query("SELECT COUNT(*) AS t FROM contas")->fetch()->t,
                "pedidos" => (int) $this->db->query("SELECT COUNT(*) AS t FROM pedidos")->fetch()->t,
                "vms" => (int) $this->db->query("SELECT COUNT(*) AS t FROM maquinas_virtuais")->fetch()->t,
            ];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function listarContas(int $usuarioSessaoId = 0): array
    {
        if (!$this->possuiConexao()) {
            return [];
        }

        $sql = "SELECT c.id_conta, c.tipo_pessoa, c.situacao, c.criado_em,
                       COALESCE(pf.nome, pj.nome_empresarial) AS titular_nome,
                       COALESCE(pf.cpf, pj.cnpj) AS documento,
                       CASE
                           WHEN pf.usuario_id = :uid_pf THEN 1
                           WHEN EXISTS (
                               SELECT 1 FROM usuarios_contas uc_me
                                WHERE uc_me.conta_id = c.id_conta
                                  AND uc_me.usuario_id = :uid_uc
                                  AND uc_me.situacao = 'ativo'
                           ) THEN 1
                           ELSE 0
                       END AS eh_conta_propria,
                       CASE
                           WHEN EXISTS (
                               SELECT 1
                                 FROM pessoas_fisicas pf_adm
                                 JOIN usuarios_contas uc_adm
                                   ON uc_adm.usuario_id = pf_adm.usuario_id
                                  AND uc_adm.conta_id IS NULL
                                  AND uc_adm.situacao = 'ativo'
                                 JOIN papeis p_adm ON p_adm.id_papel = uc_adm.papel_id
                                WHERE pf_adm.conta_id = c.id_conta
                                  AND p_adm.nome_papel = 'administrador'
                                  AND p_adm.escopo = 'plataforma'
                           ) THEN 1
                           ELSE 0
                       END AS tem_titular_administrador
                  FROM contas c
             LEFT JOIN pessoas_fisicas pf ON pf.conta_id = c.id_conta
             LEFT JOIN pessoas_juridicas pj ON pj.conta_id = c.id_conta
              ORDER BY c.id_conta DESC";

        try {
            $stm = $this->db->prepare($sql);
            $stm->execute([
                "uid_pf" => $usuarioSessaoId,
                "uid_uc" => $usuarioSessaoId,
            ]);
            return $stm->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function listarUsuarios(int $usuarioSessaoId = 0): array
    {
        if (!$this->possuiConexao()) {
            return [];
        }

        $sql = "SELECT u.id_usuario, u.email, u.situacao, u.ultimo_login,
                       GROUP_CONCAT(DISTINCT CONCAT(p.nome_papel, IFNULL(CONCAT(' #', uc.conta_id), '')) SEPARATOR ', ') AS papeis,
                       MAX(CASE
                           WHEN p.nome_papel = 'administrador' AND p.escopo = 'plataforma' AND uc.conta_id IS NULL
                           THEN 1 ELSE 0
                       END) AS eh_administrador,
                       CASE WHEN u.id_usuario = ? THEN 1 ELSE 0 END AS eh_proprio_usuario
                  FROM usuarios u
             LEFT JOIN usuarios_contas uc ON uc.usuario_id = u.id_usuario AND uc.situacao = 'ativo'
             LEFT JOIN papeis p ON p.id_papel = uc.papel_id
              GROUP BY u.id_usuario, u.email, u.situacao, u.ultimo_login
              ORDER BY u.id_usuario DESC";

        try {
            $stm = $this->db->prepare($sql);
            $stm->execute([$usuarioSessaoId]);
            return $stm->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function usuarioEhAdministradorPlataforma(int $usuarioId): bool
    {
        if (!$this->possuiConexao() || $usuarioId <= 0) {
            return false;
        }

        try {
            $stm = $this->db->prepare(
                "SELECT 1
                   FROM usuarios_contas uc
                   JOIN papeis p ON p.id_papel = uc.papel_id
                  WHERE uc.usuario_id = ?
                    AND uc.conta_id IS NULL
                    AND uc.situacao = 'ativo'
                    AND p.nome_papel = 'administrador'
                    AND p.escopo = 'plataforma'
                  LIMIT 1"
            );
            $stm->execute([$usuarioId]);
            return (bool) $stm->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function contaPertenceAoUsuario(int $contaId, int $usuarioId): bool
    {
        if (!$this->possuiConexao() || $contaId <= 0 || $usuarioId <= 0) {
            return false;
        }

        try {
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
        } catch (PDOException $e) {
            return false;
        }
    }

    public function contaPossuiTitularAdministrador(int $contaId): bool
    {
        if (!$this->possuiConexao() || $contaId <= 0) {
            return false;
        }

        try {
            $stm = $this->db->prepare(
                "SELECT 1
                   FROM pessoas_fisicas pf
                   JOIN usuarios_contas uc ON uc.usuario_id = pf.usuario_id
                    AND uc.conta_id IS NULL
                    AND uc.situacao = 'ativo'
                   JOIN papeis p ON p.id_papel = uc.papel_id
                  WHERE pf.conta_id = ?
                    AND p.nome_papel = 'administrador'
                    AND p.escopo = 'plataforma'
                  LIMIT 1"
            );
            $stm->execute([$contaId]);
            return (bool) $stm->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function podeAlterarSituacaoUsuario(int $usuarioAlvoId, int $usuarioSessaoId): bool
    {
        if ($usuarioAlvoId <= 0) {
            return false;
        }
        if ($usuarioAlvoId === $usuarioSessaoId) {
            return false;
        }
        if ($this->usuarioEhAdministradorPlataforma($usuarioAlvoId)) {
            return false;
        }

        return true;
    }

    public function podeAlterarSituacaoConta(int $contaId, int $usuarioSessaoId): bool
    {
        if ($contaId <= 0) {
            return false;
        }
        if ($this->contaPertenceAoUsuario($contaId, $usuarioSessaoId)) {
            return false;
        }
        if ($this->contaPossuiTitularAdministrador($contaId)) {
            return false;
        }

        return true;
    }

    public function listarPedidos(): array
    {
        if (!$this->possuiConexao()) {
            return [];
        }

        $sql = "SELECT p.id_pedido, p.situacao, p.valor_total, p.criado_em,
                       pl.nome AS plano_nome,
                       COALESCE(pf.nome, pj.nome_empresarial, CONCAT('Conta #', c.id_conta)) AS conta_nome
                  FROM pedidos p
                  JOIN contas c ON c.id_conta = p.conta_id
                  JOIN planos_vps pl ON pl.id_plano = p.plano_id
             LEFT JOIN pessoas_fisicas pf ON pf.conta_id = c.id_conta
             LEFT JOIN pessoas_juridicas pj ON pj.conta_id = c.id_conta
              ORDER BY p.criado_em DESC";

        try {
            return $this->db->query($sql)->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function listarPagamentos(): array
    {
        if (!$this->possuiConexao()) {
            return [];
        }

        $sql = "SELECT pg.id_pagamento, pg.pedido_id, pg.valor, pg.situacao, pg.forma_pagamento, pg.confirmado_em,
                       e.logradouro, e.numero, e.cidade, e.estado
                  FROM pagamentos pg
                  JOIN enderecos e ON e.id_endereco = pg.endereco_id
              ORDER BY pg.criado_em DESC";

        try {
            return $this->db->query($sql)->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function atualizarSituacaoConta(int $contaId, string $situacao, int $usuarioSessaoId = 0): bool
    {
        if (!$this->possuiConexao()) {
            return false;
        }

        if (!$this->podeAlterarSituacaoConta($contaId, $usuarioSessaoId)) {
            return false;
        }

        $permitidas = ["ativa", "inativa", "bloqueada"];
        if (!in_array($situacao, $permitidas, true)) {
            return false;
        }

        try {
            $stm = $this->db->prepare("UPDATE contas SET situacao = ?, alterado_em = NOW() WHERE id_conta = ?");
            $stm->execute([$situacao, $contaId]);
            return $stm->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function atualizarSituacaoUsuario(int $usuarioId, string $situacao, int $usuarioSessaoId = 0): bool
    {
        if (!$this->possuiConexao()) {
            return false;
        }

        if (!$this->podeAlterarSituacaoUsuario($usuarioId, $usuarioSessaoId)) {
            return false;
        }

        $permitidas = [
            "pendente_verificacao_email",
            "cadastro_incompleto",
            "ativo",
            "inativo",
            "bloqueado",
            "expirado",
        ];
        if (!in_array($situacao, $permitidas, true)) {
            return false;
        }

        try {
            $stm = $this->db->prepare(
                "UPDATE usuarios SET situacao = ?, alterado_em = NOW() WHERE id_usuario = ?"
            );
            $stm->execute([$situacao, $usuarioId]);
            return $stm->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function atualizarSituacaoPedido(int $pedidoId, string $situacao): bool
    {
        if (!$this->possuiConexao()) {
            return false;
        }

        $permitidas = ["criado", "aguardando_pagamento", "pago", "cancelado", "falhou"];
        if (!in_array($situacao, $permitidas, true)) {
            return false;
        }

        try {
            $stm = $this->db->prepare(
                "UPDATE pedidos SET situacao = ?, alterado_em = NOW() WHERE id_pedido = ?"
            );
            $stm->execute([$situacao, $pedidoId]);
            return $stm->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function atualizarSituacaoPagamento(int $pagamentoId, string $situacao): bool
    {
        if (!$this->possuiConexao()) {
            return false;
        }

        $permitidas = ["pendente", "confirmado", "recusado", "cancelado"];
        if (!in_array($situacao, $permitidas, true)) {
            return false;
        }

        try {
            $stm = $this->db->prepare(
                "UPDATE pagamentos SET situacao = ?, alterado_em = NOW() WHERE id_pagamento = ?"
            );
            $stm->execute([$situacao, $pagamentoId]);
            return $stm->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}
