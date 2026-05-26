<?php

class OrderModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->initializeTable();
    }

    private function initializeTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS pedidos (
            id_pedido BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            usuario_id BIGINT UNSIGNED NOT NULL,
            plano_id BIGINT UNSIGNED NOT NULL,
            status ENUM('pendente', 'aguardando pagamento', 'pago', 'provisionando', 'concluido', 'cancelado', 'falhou') DEFAULT 'pendente',
            valor_total DECIMAL(10,2) NOT NULL,
            criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
            alterado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deletado_em DATETIME NULL,
            CONSTRAINT fk_pedido_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id_usuario),
            CONSTRAINT fk_pedido_plano FOREIGN KEY (plano_id) REFERENCES planos(id_plano)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $this->db->exec($sql);
    }

    public function create(array $data): array
    {
        $sql = 'INSERT INTO pedidos (usuario_id, plano_id, status, valor_total, criado_em) VALUES (:usuario_id, :plano_id, :status, :valor_total, :criado_em)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $data['usuario_id'],
            ':plano_id' => $data['plano_id'],
            ':status' => $data['status'],
            ':valor_total' => $data['valor_total'],
            ':criado_em' => $data['criado_em'],
        ]);

        $data['id_pedido'] = (int)$this->db->lastInsertId();
        return $data;
    }
}
