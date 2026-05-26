DROP DATABASE IF EXISTS singularys;

CREATE DATABASE IF NOT EXISTS singularys
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE singularys;

CREATE TABLE IF NOT EXISTS usuarios (
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
    ultimo_login_em DATETIME NULL,
    status ENUM('ativo', 'inativo', 'bloqueado') DEFAULT 'ativo',
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    alterado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deletado_em DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS papeis (
    id_papel BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO papeis (nome)
VALUES
    ('admin'),
    ('cliente'),
    ('operador');

CREATE TABLE IF NOT EXISTS usuarios_papeis (
    usuario_id BIGINT UNSIGNED NOT NULL,
    papel_id BIGINT UNSIGNED NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    alterado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deletado_em DATETIME NULL,
    CONSTRAINT fk_usuario_papel_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id_usuario),
    CONSTRAINT fk_usuario_papel_papel
        FOREIGN KEY (papel_id) REFERENCES papeis(id_papel)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS planos (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pedidos (
    id_pedido BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    plano_id BIGINT UNSIGNED NOT NULL,
    status ENUM(
        'pendente',
        'aguardando pagamento',
        'pago',
        'provisionando',
        'concluido',
        'cancelado',
        'falhou'
    ) DEFAULT 'pendente',
    valor_total DECIMAL(10,2) NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    alterado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deletado_em DATETIME NULL,
    CONSTRAINT fk_pedido_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id_usuario),
    CONSTRAINT fk_pedido_plano
        FOREIGN KEY (plano_id) REFERENCES planos(id_plano)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS assinaturas_mensais (
    id_assinatura_mensal BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    plano_id BIGINT UNSIGNED NOT NULL,
    pedido_id BIGINT UNSIGNED NOT NULL,
    data_inicio DATE NULL,
    data_vencimento DATE NULL,
    data_cancelamento DATE NULL,
    status ENUM(
        'ativa',
        'suspensa',
        'cancelada',
        'encerrada'
    ) DEFAULT 'ativa',
    CONSTRAINT fk_assinatura_mensal_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id_usuario),
    CONSTRAINT fk_assinatura_mensal_plano
        FOREIGN KEY (plano_id) REFERENCES planos(id_plano),
    CONSTRAINT fk_assinatura_mensal_pedido
        FOREIGN KEY (pedido_id) REFERENCES pedidos(id_pedido)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS faturas_mensais (
    id_fatura_mensal BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    assinatura_mensal_id BIGINT UNSIGNED NOT NULL,
    mes_competencia DATE NULL,
    data_emissao DATE NULL,
    data_vencimento DATE NULL,
    valor_cobrado DECIMAL(10,2) NOT NULL,
    status ENUM(
        'aberta',
        'paga',
        'vencida',
        'cancelada'
    ) DEFAULT 'aberta',
    CONSTRAINT fk_fatura_mensal_assinatura_mensal
        FOREIGN KEY (assinatura_mensal_id)
        REFERENCES assinaturas_mensais(id_assinatura_mensal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pagamentos (
    id_pagamento BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id BIGINT UNSIGNED NOT NULL,
    fatura_mensal_id BIGINT UNSIGNED NOT NULL,
    gateway VARCHAR(50) NOT NULL,
    gateway_transacao_id VARCHAR(150) UNIQUE,
    idempotency_key VARCHAR(100) UNIQUE,
    status ENUM(
        'pendente',
        'confirmado',
        'rejeitado',
        'estornado'
    ) NOT NULL,
    valor_pago DECIMAL(10,2) NOT NULL,
    data_pagamento DATETIME NULL,
    forma_pagamento ENUM(
        'cartão',
        'Pix',
        'boleto'
    ) NOT NULL,
    recorrencia_meses INT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    alterado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deletado_em DATETIME NULL,
    CONSTRAINT fk_pagamento_pedido
        FOREIGN KEY (pedido_id) REFERENCES pedidos(id_pedido),
    CONSTRAINT fk_pagamento_fatura_mensal
        FOREIGN KEY (fatura_mensal_id)
        REFERENCES faturas_mensais(id_fatura_mensal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS provisionamentos (
    id_provisionamento BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    template_utilizado VARCHAR(255) NOT NULL,
    cloud_init VARCHAR(255) NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    alterado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deletado_em DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS fila_tarefas (
    id_tarefa BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(100) NOT NULL,
    referencia_id BIGINT UNSIGNED,
    payload JSON,
    status ENUM(
        'pendente',
        'processando',
        'concluido',
        'falhou'
    ) DEFAULT 'pendente',
    tentativas INT DEFAULT 0,
    erro TEXT,
    processado_em DATETIME NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    alterado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deletado_em DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS maquinas_virtuais (
    id_maquina_virtual BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    pedido_id BIGINT UNSIGNED NOT NULL UNIQUE,
    plano_id BIGINT UNSIGNED NOT NULL,
    proxmox_node VARCHAR(255),
    vmid VARCHAR(255),
    hostname VARCHAR(255) NOT NULL,
    ipv4 VARCHAR(255),
    status ENUM(
        'provisionando',
        'ativa',
        'suspensa',
        'encerrada',
        'falha'
    ) DEFAULT 'provisionando',
    cpu INT NOT NULL,
    memoria_ram INT NOT NULL,
    armazenamento_gb INT NOT NULL,
    template_utilizado VARCHAR(255),
    provisionada_em DATETIME NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    alterado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deletado_em DATETIME NULL,
    CONSTRAINT fk_vm_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id_usuario),
    CONSTRAINT fk_vm_pedido
        FOREIGN KEY (pedido_id) REFERENCES pedidos(id_pedido),
    CONSTRAINT fk_vm_plano
        FOREIGN KEY (plano_id) REFERENCES planos(id_plano)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS credenciais (
    id_credencial BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    maquina_virtual_id BIGINT UNSIGNED NOT NULL UNIQUE,
    usuario_inicial VARCHAR(100) NOT NULL,
    chave_ssh TEXT,
    senha_temporaria BOOLEAN DEFAULT FALSE,
    expira_em DATETIME NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    alterado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deletado_em DATETIME NULL,
    CONSTRAINT fk_credencial_vm
        FOREIGN KEY (maquina_virtual_id)
        REFERENCES maquinas_virtuais(id_maquina_virtual)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS eventos_provisionamento (
    id_evento BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    maquina_virtual_id BIGINT UNSIGNED NOT NULL,
    tipo_evento VARCHAR(255) NOT NULL,
    status ENUM(
        'em execução',
        'sucesso',
        'falha'
    ) NOT NULL,
    mensagem TEXT,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    alterado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deletado_em DATETIME NULL,
    CONSTRAINT fk_evento_vm
        FOREIGN KEY (maquina_virtual_id)
        REFERENCES maquinas_virtuais(id_maquina_virtual)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
