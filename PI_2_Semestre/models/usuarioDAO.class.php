<?php
class UsuarioDAO extends Conexao
{
    public function __construct()
    {
        parent::__construct();
    }

    public function buscarPorEmail(string $email)
    {
        return $this->buscarUsuarioPorEmail($this->normalizarEmail($email));
    }

    public function autenticar(string $email, string $senha, ?int $contaIdInformada = null)
    {
        if (!$this->possuiConexao()) {
            return null;
        }

        $email = $this->normalizarEmail($email);
        if ($email === "" || $senha === "") {
            return null;
        }

        $usuario = $this->buscarUsuarioPorEmail($email);
        if (!$usuario) {
            return null;
        }

        $hash = (string) ($usuario->senha_hash ?? "");
        if ($hash === "" || !password_verify($senha, $hash)) {
            return null;
        }

        return $this->carregarContextoLogin((int) $usuario->id_usuario, $contaIdInformada);
    }

    public function registrarUltimoLogin(int $usuarioId): void
    {
        if (!$this->possuiConexao() || $usuarioId <= 0) {
            return;
        }

        try {
            $stm = $this->db->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id_usuario = ?");
            $stm->execute([$usuarioId]);
        } catch (PDOException $e) {
        }
    }

    public function listarContasAcessiveis(int $usuarioId): array
    {
        if (!$this->possuiConexao() || $usuarioId <= 0) {
            return [];
        }

        $sql = "SELECT c.id_conta, c.tipo_pessoa, c.situacao,
                       p.nome_papel,
                       COALESCE(pf.nome, pj.nome_empresarial, CONCAT('Conta #', c.id_conta)) AS nome_exibicao
                  FROM usuarios_contas uc
                  JOIN contas c ON c.id_conta = uc.conta_id
                  JOIN papeis p ON p.id_papel = uc.papel_id
             LEFT JOIN pessoas_fisicas pf ON pf.conta_id = c.id_conta
             LEFT JOIN pessoas_juridicas pj ON pj.conta_id = c.id_conta
                 WHERE uc.usuario_id = ?
                   AND uc.situacao = 'ativo'
                   AND uc.conta_id IS NOT NULL
              ORDER BY c.id_conta ASC";

        try {
            $stm = $this->db->prepare($sql);
            $stm->execute([$usuarioId]);
            return $stm->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    private function buscarUsuarioPorEmail(string $email)
    {
        if (!$this->possuiConexao() || $email === "") {
            return null;
        }

        try {
            $stm = $this->db->prepare(
                "SELECT id_usuario, email, senha_hash, situacao
                   FROM usuarios WHERE email = ? LIMIT 1"
            );
            $stm->execute([$email]);
            return $stm->fetch() ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    private function carregarContextoLogin(int $usuarioId, ?int $contaIdInformada)
    {
        $admin = $this->buscarVinculoAdministrador($usuarioId);
        if ($admin) {
            return $admin;
        }

        if ($contaIdInformada !== null && $contaIdInformada > 0) {
            return $this->buscarVinculoConta($usuarioId, $contaIdInformada);
        }

        return $this->buscarContaPadraoUsuario($usuarioId);
    }

    private function buscarVinculoAdministrador(int $usuarioId)
    {
        $sql = "SELECT u.id_usuario, u.email, u.situacao,
                       NULL AS conta_id,
                       p.nome_papel,
                       u.email AS nome_exibicao
                  FROM usuarios u
                  JOIN usuarios_contas uc ON uc.usuario_id = u.id_usuario AND uc.situacao = 'ativo'
                  JOIN papeis p ON p.id_papel = uc.papel_id
                 WHERE u.id_usuario = ?
                   AND p.nome_papel = 'administrador'
                   AND p.escopo = 'plataforma'
                 LIMIT 1";

        try {
            $stm = $this->db->prepare($sql);
            $stm->execute([$usuarioId]);
            return $stm->fetch() ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    private function buscarVinculoConta(int $usuarioId, int $contaId)
    {
        $sql = "SELECT u.id_usuario, u.email, u.situacao,
                       uc.conta_id,
                       p.nome_papel,
                       COALESCE(pf.nome, pj.nome_empresarial, u.email) AS nome_exibicao
                  FROM usuarios u
                  JOIN usuarios_contas uc ON uc.usuario_id = u.id_usuario AND uc.situacao = 'ativo'
                  JOIN papeis p ON p.id_papel = uc.papel_id
                  JOIN contas c ON c.id_conta = uc.conta_id
             LEFT JOIN pessoas_fisicas pf ON pf.conta_id = c.id_conta
             LEFT JOIN pessoas_juridicas pj ON pj.conta_id = c.id_conta
                 WHERE u.id_usuario = ?
                   AND uc.conta_id = ?
                 LIMIT 1";

        try {
            $stm = $this->db->prepare($sql);
            $stm->execute([$usuarioId, $contaId]);
            $row = $stm->fetch();
            if ($row) {
                return $row;
            }

            return $this->buscarContaPorPfTitular($usuarioId, $contaId);
        } catch (PDOException $e) {
            return null;
        }
    }

    private function buscarContaPadraoUsuario(int $usuarioId)
    {
        $sql = "SELECT u.id_usuario, u.email, u.situacao,
                       c.id_conta AS conta_id,
                       COALESCE(p.nome_papel, 'titular') AS nome_papel,
                       COALESCE(pf.nome, pj.nome_empresarial, u.email) AS nome_exibicao
                  FROM usuarios u
             LEFT JOIN pessoas_fisicas pf ON pf.usuario_id = u.id_usuario
             LEFT JOIN contas c ON c.id_conta = pf.conta_id
             LEFT JOIN usuarios_contas uc ON uc.usuario_id = u.id_usuario
                   AND uc.conta_id = c.id_conta AND uc.situacao = 'ativo'
             LEFT JOIN papeis p ON p.id_papel = uc.papel_id
             LEFT JOIN pessoas_juridicas pj ON pj.conta_id = c.id_conta
                 WHERE u.id_usuario = ?
                 ORDER BY pf.id_pessoa_fisica ASC, uc.id_usuario_conta ASC
                 LIMIT 1";

        try {
            $stm = $this->db->prepare($sql);
            $stm->execute([$usuarioId]);
            $row = $stm->fetch();
            if ($row && !empty($row->conta_id)) {
                return $row;
            }

            return $this->buscarPrimeiroVinculoConta($usuarioId);
        } catch (PDOException $e) {
            return null;
        }
    }

    private function buscarContaPorPfTitular(int $usuarioId, int $contaId)
    {
        $sql = "SELECT u.id_usuario, u.email, u.situacao,
                       pf.conta_id,
                       'titular' AS nome_papel,
                       pf.nome AS nome_exibicao
                  FROM usuarios u
                  JOIN pessoas_fisicas pf ON pf.usuario_id = u.id_usuario AND pf.conta_id = ?
                 WHERE u.id_usuario = ?
                 LIMIT 1";

        try {
            $stm = $this->db->prepare($sql);
            $stm->execute([$contaId, $usuarioId]);
            return $stm->fetch() ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    private function buscarPrimeiroVinculoConta(int $usuarioId)
    {
        $sql = "SELECT u.id_usuario, u.email, u.situacao,
                       uc.conta_id,
                       p.nome_papel,
                       COALESCE(pf.nome, pj.nome_empresarial, u.email) AS nome_exibicao
                  FROM usuarios u
                  JOIN usuarios_contas uc ON uc.usuario_id = u.id_usuario AND uc.situacao = 'ativo'
                  JOIN papeis p ON p.id_papel = uc.papel_id
                  JOIN contas c ON c.id_conta = uc.conta_id
             LEFT JOIN pessoas_fisicas pf ON pf.conta_id = c.id_conta
             LEFT JOIN pessoas_juridicas pj ON pj.conta_id = c.id_conta
                 WHERE u.id_usuario = ?
                   AND uc.conta_id IS NOT NULL
                 ORDER BY uc.id_usuario_conta ASC
                 LIMIT 1";

        try {
            $stm = $this->db->prepare($sql);
            $stm->execute([$usuarioId]);
            return $stm->fetch() ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    private function normalizarEmail(string $email): string
    {
        return strtolower(trim($email));
    }
}
