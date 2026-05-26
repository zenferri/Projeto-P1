<?php

class PlanModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->initializeTable();
    }

    private function initializeTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS planos (
            id_plano BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            descricao TEXT,
            cpu INT NOT NULL,
            memoria_ram INT NOT NULL,
            armazenamento_gb INT NOT NULL,
            preco_mensal DECIMAL(10,2) NOT NULL,
            status ENUM('ativo', 'inativo') DEFAULT 'ativo',
            criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
            alterado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deletado_em DATETIME NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $this->db->exec($sql);
        $this->seedDefaultPlans();
    }

    private function seedDefaultPlans(): void
    {
        $stmt = $this->db->query('SELECT COUNT(*) AS count FROM planos');
        $count = (int)$stmt->fetchColumn();

        if ($count === 0) {
            $plans = [
                ['nome' => 'Essencial', 'descricao' => 'Plano de entrada para pequenos projetos e sites leves.', 'cpu' => 1, 'memoria_ram' => 2, 'armazenamento_gb' => 20, 'preco_mensal' => 29.90],
                ['nome' => 'Avançado', 'descricao' => 'Configuração equilibrada para aplicações em crescimento.', 'cpu' => 2, 'memoria_ram' => 4, 'armazenamento_gb' => 40, 'preco_mensal' => 39.90],
                ['nome' => 'Desempenho', 'descricao' => 'Servidor de alto desempenho para cargas críticas e produção.', 'cpu' => 8, 'memoria_ram' => 16, 'armazenamento_gb' => 100, 'preco_mensal' => 69.90],
            ];

            $sql = 'INSERT INTO planos (nome, descricao, cpu, memoria_ram, armazenamento_gb, preco_mensal) VALUES (:nome, :descricao, :cpu, :memoria_ram, :armazenamento_gb, :preco_mensal)';
            $stmt = $this->db->prepare($sql);

            foreach ($plans as $plan) {
                $stmt->execute($plan);
            }
        }
    }

    public function getPlans(): array
    {
        $stmt = $this->db->query('SELECT id_plano, nome, descricao, cpu, memoria_ram, armazenamento_gb, preco_mensal FROM planos WHERE status = "ativo" ORDER BY id_plano');
        $plans = $stmt->fetchAll();

        return array_map(function ($plan) {
            $slug = strtolower(preg_replace('/[^a-z0-9]+/', '', $plan['nome']));
            return [
                'id_plano' => (int)$plan['id_plano'],
                'code' => $slug,
                'title' => $plan['nome'],
                'description' => $plan['descricao'],
                'cpu' => $plan['cpu'] . ' vCPU',
                'ram' => $plan['memoria_ram'] . ' GB RAM',
                'storage' => $plan['armazenamento_gb'] . ' GB SSD',
                'price' => (float)$plan['preco_mensal'],
            ];
        }, $plans);
    }

    public function getPlan(string $code): ?array
    {
        $plans = $this->getPlans();

        foreach ($plans as $plan) {
            if ($plan['code'] === $code) {
                return $plan;
            }
        }

        return null;
    }
}
