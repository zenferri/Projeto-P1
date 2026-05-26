<?php

class UserModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->initializeTable();
    }

    private function initializeTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS usuarios (
            id_usuario BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            tipo_usuario ENUM('fisica', 'juridica') NOT NULL,
            nome VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            senha_hash TEXT NOT NULL,
            telefone VARCHAR(20),
            cpf VARCHAR(14) UNIQUE,
            cnpj VARCHAR(18) UNIQUE,
            data_nascimento DATE NULL,
            endereco VARCHAR(255),
            status ENUM('ativo', 'inativo', 'bloqueado') DEFAULT 'ativo',
            criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $this->db->exec($sql);
    }

    public function create(array $data): array
    {
        $sql = 'INSERT INTO usuarios (tipo_usuario, nome, email, telefone, senha_hash, cpf, cnpj, data_nascimento, endereco, status) VALUES (:tipo_usuario, :nome, :email, :telefone, :senha_hash, :cpf, :cnpj, :data_nascimento, :endereco, :status)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':tipo_usuario' => $data['tipo_usuario'],
            ':nome' => $data['nome'],
            ':email' => $data['email'],
            ':telefone' => $data['telefone'],
            ':senha_hash' => $data['senha_hash'],
            ':cpf' => $data['cpf'],
            ':cnpj' => $data['cnpj'],
            ':data_nascimento' => $data['data_nascimento'],
            ':endereco' => $data['endereco'],
            ':status' => 'ativo',
        ]);

        $data['id_usuario'] = (int)$this->db->lastInsertId();
        return $data;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $result = $stmt->fetch();

        return $result ?: null;
    }
}
