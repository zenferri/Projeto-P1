-- Singularys - Schema para Sprint 1 (CRUD)
-- Versão: v7.7 Sprint 1 corrigida

-- Três domínios:
--   ADMINISTRATIVO  -> usuarios, contas, enderecos, contas_enderecos, telefones, papeis, permissoes
--   COMERCIAL       -> planos_vps, sabores_linux, pedidos, pagamentos
--   OPERACIONAL     -> maquinas_virtuais, provisionamento_solicitacoes, eventos_provisionamento

DROP DATABASE IF EXISTS singularys2;

CREATE DATABASE singularys2
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE singularys2;

SET NAMES utf8mb4;


-- DOMÍNIO ADMINISTRATIVO
-- usuarios, contas, enderecos, contas_enderecos, telefones, papeis, permissoes


-- Regra de negócio:
-- Cadastro inicial apenas com e-mail e senha.
-- Nome e demais dados cadastrais entram ao complementar PF ou PJ (na contratação ou antes).
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    email_verificado_em DATETIME NULL,
    situacao ENUM(
        'pendente_verificacao_email',
        'cadastro_incompleto',
        'ativo',
        'inativo',
        'bloqueado',
        'expirado'
    ) NOT NULL DEFAULT 'pendente_verificacao_email',
    ultimo_login DATETIME NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    alterado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_usuario_email (email)
);

CREATE TABLE IF NOT EXISTS tokens_verificacao_email (
    id_token BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    codigo_hash VARCHAR(255) NOT NULL COMMENT 'Hash do código de 6 dígitos',
    expira_em DATETIME NOT NULL COMMENT 'Validade: 20 minutos',
    utilizado_em DATETIME NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token_usuario_expira (usuario_id, expira_em),
    CONSTRAINT fk_token_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id_usuario)
);

-- Regra de negócio — fluxo criar conta:
--   1) Usuário informa e-mail + senha (tabela usuarios).
--   2) Cria-se id_conta e vínculo em usuarios_contas (quem registrou passa a operar a conta).
--   3) Escolhe PF ou PJ (tipo_pessoa) e preenche o formulário correspondente.
--   4) PF: pessoas_fisicas nesta conta = titular da conta; fim do cadastro.
--   5) PJ: pessoas_juridicas nesta conta = titular (CNPJ); nome e CPF do representante legal no formulário PJ.
-- Titular jurídico NUNCA é usuarios.id_usuario: PF titular = pessoas_fisicas; PJ titular = pessoas_juridicas.
-- E-mail de login permanece em usuarios; id_conta agrega PF ou PJ após o complemento.
CREATE TABLE IF NOT EXISTS contas (
    id_conta BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo_pessoa ENUM('pf', 'pj') NULL COMMENT 'Definido ao escolher o tipo no complemento; exclusivo PF ou PJ',
    situacao ENUM('ativa', 'inativa', 'bloqueada') NOT NULL DEFAULT 'ativa',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    alterado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_conta_tipo (tipo_pessoa)
);

-- Regra de negócio:
-- Conta PF: esta linha É o titular da conta (CPF). usuario_id liga o login de quem completou o cadastro PF.
CREATE TABLE IF NOT EXISTS pessoas_fisicas (
    id_pessoa_fisica BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conta_id BIGINT UNSIGNED NOT NULL,
    usuario_id BIGINT UNSIGNED NULL COMMENT 'Usuário do portal vinculado a esta PF (login/cadastro)',
    nome VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) NOT NULL,
    data_nascimento DATE NOT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    alterado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_pf_conta (conta_id),
    UNIQUE KEY uk_pf_cpf (cpf),
    INDEX idx_pf_usuario (usuario_id),
    CONSTRAINT fk_pf_conta
        FOREIGN KEY (conta_id) REFERENCES contas(id_conta),
    CONSTRAINT fk_pf_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id_usuario)
);

-- Regra de negócio:
-- Conta PJ: esta linha (CNPJ) É o titular da conta. Representante legal = nome e CPF informados no formulário PJ
-- (identificação do responsável legal; não titulariza a conta e não substitui a PJ).
-- Quem abre a conta no portal é usuarios + usuarios_contas; pode ser o mesmo CPF do representante sem ser titular da PJ.
-- A aplicação deve garantir contas.tipo_pessoa = pj e ausência de pessoas_fisicas para a mesma conta_id.
-- Para teste, representante_nome e representante_cpf podem ser NULL.
CREATE TABLE IF NOT EXISTS pessoas_juridicas (
    id_pessoa_juridica BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conta_id BIGINT UNSIGNED NOT NULL,
    nome_empresarial VARCHAR(255) NOT NULL,
    nome_fantasia VARCHAR(255) NULL,
    cnpj VARCHAR(25) NOT NULL COMMENT 'Campo amplo para eventual CNPJ alfanumérico de 18 caracteres',
    representante_nome VARCHAR(255) NOT NULL COMMENT 'Responsável legal da PJ (formulário de cadastro)',
    representante_cpf VARCHAR(14) NOT NULL COMMENT 'CPF do responsável legal; não confundir com titular da conta PJ',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    alterado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_pj_conta (conta_id),
    UNIQUE KEY uk_pj_cnpj (cnpj),
    CONSTRAINT fk_pj_conta
        FOREIGN KEY (conta_id) REFERENCES contas(id_conta)
);

-- Regra de negócio:
-- Endereço é entidade global (dados postais). N:N com contas via contas_enderecos.
-- Um mesmo endereço físico pode ser compartilhado por várias contas (ex.: sede de grupo econômico).
CREATE TABLE IF NOT EXISTS enderecos (
    id_endereco BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cep VARCHAR(10) NOT NULL,
    logradouro VARCHAR(255) NOT NULL,
    numero VARCHAR(6) NOT NULL,
    complemento VARCHAR(120) NULL,
    bairro VARCHAR(120) NOT NULL,
    cidade VARCHAR(120) NOT NULL,
    estado VARCHAR(2) NULL COMMENT 'UF brasileira; NULL para endereços internacionais',
    pais VARCHAR(80) NOT NULL DEFAULT 'Brasil',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    alterado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Regra de negócio:
-- N:N simplificado: uma conta pode ter vários endereços; um endereço pode pertencer a várias contas.
-- Endereço de faturamento não é papel na conta: é escolhido no pagamento (pagamentos.endereco_id).
-- Soft delete no vínculo (situacao desativado): some para o cliente, permanece para auditoria.
-- A aplicação deve validar: endereco_id do pagamento com vínculo ativo em contas_enderecos para a conta do pedido.
CREATE TABLE IF NOT EXISTS contas_enderecos (
    conta_id BIGINT UNSIGNED NOT NULL,
    endereco_id BIGINT UNSIGNED NOT NULL,
    situacao ENUM('ativo', 'desativado') NOT NULL DEFAULT 'ativo',
    desativado_em DATETIME NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    alterado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (conta_id, endereco_id),
    INDEX idx_ce_conta_ativo (conta_id, situacao),
    INDEX idx_ce_endereco (endereco_id),
    CONSTRAINT fk_ce_conta
        FOREIGN KEY (conta_id) REFERENCES contas(id_conta),
    CONSTRAINT fk_ce_endereco
        FOREIGN KEY (endereco_id) REFERENCES enderecos(id_endereco)
);

-- Regra de negócio:
-- Telefone global com suporte internacional (codigo_pais + DDD/região + número).
-- Tipo fixo/celular e formato validados no front; campos amplos para evolução futura dos dígitos.
-- N:N com contas via contas_telefones.
CREATE TABLE IF NOT EXISTS telefones (
    id_telefone BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_pais VARCHAR(5) NOT NULL DEFAULT '55' COMMENT 'Código telefônico do país (ex.: 55 Brasil, 1 EUA)',
    ddd CHAR(3) NULL COMMENT 'DDD/região; obrigatório para BR na validação do front; NULL em alguns países',
    numero VARCHAR(15) NOT NULL COMMENT 'Número sem código do país nem DDD',
    tipo_telefone ENUM('fixo', 'celular') NOT NULL COMMENT 'Definido e validado pelo front',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    alterado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ddd_normalizado CHAR(3) AS (COALESCE(ddd, '')) PERSISTENT,
    UNIQUE KEY uk_telefone_pais_numero (codigo_pais, ddd_normalizado, numero),
    INDEX idx_telefone_tipo (tipo_telefone),
    INDEX idx_telefone_pais (codigo_pais)
);

-- Regra de negócio:
-- Uma conta pode ter muitos telefones; um telefone pode pertencer a muitas contas.
CREATE TABLE IF NOT EXISTS contas_telefones (
    conta_id BIGINT UNSIGNED NOT NULL,
    telefone_id BIGINT UNSIGNED NOT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (conta_id, telefone_id),
    INDEX idx_ct_telefone (telefone_id),
    CONSTRAINT fk_ct_conta
        FOREIGN KEY (conta_id) REFERENCES contas(id_conta),
    CONSTRAINT fk_ct_telefone
        FOREIGN KEY (telefone_id) REFERENCES telefones(id_telefone)
);

-- Regra de negócio:
-- Papel administrador: concedido somente por INSERT direto no banco; o front nunca promove usuário a administrador.
-- Acesso ao painel global da plataforma, com poderes sobre todos os domínios e todas as contas.
-- escopo plataforma: administrador (conta_id NULL em usuarios_contas).
-- escopo titular: permissão de cadastro na conta em sessão (não é a entidade PF/PJ titular).
-- escopo operador: operador da conta; permissões operacionais na conta (VMs, pedidos, pagamentos); não altera cadastro.
CREATE TABLE IF NOT EXISTS papeis (
    id_papel BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome_papel VARCHAR(50) NOT NULL,
    descricao VARCHAR(255) NULL,
    escopo ENUM('plataforma', 'titular', 'operador') NOT NULL DEFAULT 'titular',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_papel_nome (nome_papel)
);

-- dominio admin: painel/cadastro na conta em sessão (titular) ou ações globais da plataforma (administrador).
-- dominio operacional: gestão de VMs, pedidos e pagamentos na conta em sessão; sem alterar cadastro da conta.
CREATE TABLE IF NOT EXISTS permissoes (
    id_permissao BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome_permissao VARCHAR(150) NOT NULL,
    descricao VARCHAR(255) NULL,
    dominio ENUM('admin', 'operacional') NOT NULL DEFAULT 'admin',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_permissao_nome (nome_permissao)
);

-- Tabela associativa N:N entre papéis e permissões.
CREATE TABLE IF NOT EXISTS papeis_permissoes (
    papel_id BIGINT UNSIGNED NOT NULL,
    permissao_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (papel_id, permissao_id),
    CONSTRAINT fk_pp_papel
        FOREIGN KEY (papel_id) REFERENCES papeis(id_papel),
    CONSTRAINT fk_pp_permissao
        FOREIGN KEY (permissao_id) REFERENCES permissoes(id_permissao)
);

-- Regra de negócio:
-- Quem acessa id_conta no login: vínculos desta tabela (papéis titular, operador; gestor/analista futuro).
-- Entidade titular (PF/PJ) está em pessoas_fisicas ou pessoas_juridicas, não em contas.
-- conta_id NULL: papel administrador (escopo plataforma), sem conta de cliente.
-- FUTURO: reforçar unicidade quando conta_id IS NULL (hoje UNIQUE permite duplicatas por NULL no MySQL/MariaDB).
-- Papéis futuros: gestor, analista — mesma mecânica, conta_id obrigatório.
CREATE TABLE IF NOT EXISTS usuarios_contas (
    id_usuario_conta BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    conta_id BIGINT UNSIGNED NULL,
    papel_id BIGINT UNSIGNED NOT NULL,
    situacao ENUM('ativo', 'inativo', 'revogado') NOT NULL DEFAULT 'ativo',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_usuario_conta_papel (usuario_id, conta_id, papel_id),
    INDEX idx_uc_usuario (usuario_id),
    INDEX idx_uc_conta (conta_id),
    INDEX idx_uc_papel (papel_id),
    CONSTRAINT fk_uc_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id_usuario),
    CONSTRAINT fk_uc_conta
        FOREIGN KEY (conta_id) REFERENCES contas(id_conta),
    CONSTRAINT fk_uc_papel
        FOREIGN KEY (papel_id) REFERENCES papeis(id_papel)
);


-- DOMÍNIO COMERCIAL
-- planos_vps, sabores_linux, pedidos, pagamentos


-- Regra de negócio:
-- Planos: administrador altera via painel global; soft delete (deletado_em) oculta do catálogo sem apagar histórico de pedidos.
CREATE TABLE IF NOT EXISTS planos_vps (
    id_plano BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    descricao TEXT NULL,
    vcpu INT UNSIGNED NOT NULL,
    ram_mb INT UNSIGNED NOT NULL,
    storage_gb INT UNSIGNED NOT NULL,
    banda_mbps INT UNSIGNED NOT NULL DEFAULT 100,
    preco_mensal DECIMAL(10,2) NOT NULL,
    situacao ENUM('ativo', 'inativo', 'suspenso') NOT NULL DEFAULT 'ativo',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    alterado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deletado_em DATETIME NULL COMMENT 'Soft delete; pedidos antigos mantêm FK ao plano',
    UNIQUE KEY uk_plano_slug (slug)
);

-- Regra de negócio:
-- Sabor Linux: apenas situacao ativo/inativo. Nunca deletado logicamente; inativo some para novas VMs.
CREATE TABLE IF NOT EXISTS sabores_linux (
    id_sabor_linux BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    distribuicao VARCHAR(80) NOT NULL,
    versao VARCHAR(100) NOT NULL,
    template_proxmox VARCHAR(255) NOT NULL,
    situacao ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    alterado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_sabor_slug (slug)
);

-- Regra de negócio:
-- Pedido = escolha de plano (hardware virtualizado) ao contratar. Sem sabor Linux no checkout.
-- Sabor, hostname e usuários Linux são definidos na dashboard após pagamento confirmado.
-- valor_total: snapshot comercial no momento do pedido (pode divergir de planos_vps.preco_mensal por promoção/ajuste).
CREATE TABLE IF NOT EXISTS pedidos (
    id_pedido BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conta_id BIGINT UNSIGNED NOT NULL,
    plano_id BIGINT UNSIGNED NOT NULL,
    valor_total DECIMAL(10,2) NOT NULL COMMENT 'Valor fechado no pedido; não recalculado automaticamente se o plano mudar',
    situacao ENUM(
        'criado',
        'aguardando_pagamento',
        'pago',
        'cancelado',
        'falhou'
    ) NOT NULL DEFAULT 'criado',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    alterado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_pedidos_conta (conta_id),
    INDEX idx_pedidos_situacao (situacao),
    CONSTRAINT fk_pedido_conta
        FOREIGN KEY (conta_id) REFERENCES contas(id_conta),
    CONSTRAINT fk_pedido_plano
        FOREIGN KEY (plano_id) REFERENCES planos_vps(id_plano)
);

-- Regra de negócio:
-- Gateway simulado no Sprint 1. Várias tentativas por pedido são permitidas.
-- valor: snapshot da tentativa; conferir com pedidos.valor_total na confirmação.
-- endereco_id: endereço de faturamento escolhido nesta tentativa (entre os vinculados à conta do pedido).
CREATE TABLE IF NOT EXISTS pagamentos (
    id_pagamento BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id BIGINT UNSIGNED NOT NULL,
    endereco_id BIGINT UNSIGNED NOT NULL COMMENT 'Endereço de faturamento desta tentativa de pagamento',
    valor DECIMAL(10,2) NOT NULL COMMENT 'Valor desta tentativa; conferir com pedidos.valor_total na confirmação',
    gateway VARCHAR(100) NOT NULL DEFAULT 'simulado',
    forma_pagamento ENUM('cartao', 'pix', 'boleto', 'simulado') NOT NULL DEFAULT 'simulado',
    situacao ENUM('pendente', 'confirmado', 'recusado', 'cancelado') NOT NULL DEFAULT 'pendente',
    identificador_transacao VARCHAR(255) NULL,
    confirmado_em DATETIME NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    alterado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_pagamento_pedido (pedido_id),
    INDEX idx_pagamento_endereco (endereco_id),
    INDEX idx_pagamentos_situacao (situacao),
    CONSTRAINT fk_pagamento_pedido
        FOREIGN KEY (pedido_id) REFERENCES pedidos(id_pedido),
    CONSTRAINT fk_pagamento_endereco
        FOREIGN KEY (endereco_id) REFERENCES enderecos(id_endereco)
);


-- DOMÍNIO OPERACIONAL
-- maquinas_virtuais, provisionamento_solicitacoes, eventos_provisionamento


-- Regra de negócio:
-- Após pagamento, cria-se 1 VM por pedido (uk_vm_pedido), estado aguardando_configuracao.
-- Na dashboard o usuário escolhe sabor Linux (somente sabores ativos), hostname e nomes de usuário Linux.
-- Senhas (usuário e root) vão ao cloud-init e NÃO são persistidas.
-- hostname: única fonte no banco é maquinas_virtuais.hostname (placeholder vm-pendente até confirmar).
-- sabor_linux_id NULL até a configuração; vcpu/ram/storage_aplicado podem espelhar o plano na confirmação.
CREATE TABLE IF NOT EXISTS maquinas_virtuais (
    id_maquina_virtual BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id BIGINT UNSIGNED NOT NULL,
    sabor_linux_id BIGINT UNSIGNED NULL COMMENT 'Preenchido após pagamento, na configuração da dashboard',
    hostname VARCHAR(255) NOT NULL DEFAULT 'vm-pendente' COMMENT 'Definido na dashboard; unicidade validada pela aplicação ao sair do placeholder',
    node_proxmox VARCHAR(255) NULL,
    vmid_proxmox INT UNSIGNED NULL,
    ipv4_publico VARCHAR(45) NULL,
    ipv4_privado VARCHAR(45) NULL,
    estado ENUM(
        'aguardando_configuracao',
        'aguardando_provisionamento',
        'em_criacao',
        'ativa',
        'destruida'
    ) NOT NULL DEFAULT 'aguardando_configuracao',
    vcpu_aplicado INT UNSIGNED NULL COMMENT 'Snapshot de hardware aplicado; tipicamente copiado do plano do pedido',
    ram_mb_aplicado INT UNSIGNED NULL,
    storage_gb_aplicado INT UNSIGNED NULL,
    provisionada_em DATETIME NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    alterado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_vm_pedido (pedido_id),
    UNIQUE KEY uk_vm_vmid_proxmox (vmid_proxmox),
    INDEX idx_vm_estado (estado),
    INDEX idx_vm_sabor (sabor_linux_id),
    CONSTRAINT fk_vm_pedido
        FOREIGN KEY (pedido_id) REFERENCES pedidos(id_pedido),
    CONSTRAINT fk_vm_sabor
        FOREIGN KEY (sabor_linux_id) REFERENCES sabores_linux(id_sabor_linux)
);

-- Regra de negócio:
-- Uma solicitação por VM (uk_solicitacao_vm). Registra método de acesso e nome do usuário inicial para o cloud-init.
-- Não armazena hostname (ver maquinas_virtuais) nem senhas/chaves.
-- Fluxo: rascunho -> confirmado -> enviado (credenciais descartadas da memória) -> concluido.
CREATE TABLE IF NOT EXISTS provisionamento_solicitacoes (
    id_solicitacao BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    maquina_virtual_id BIGINT UNSIGNED NOT NULL,
    usuario_inicial VARCHAR(64) NOT NULL DEFAULT 'root' COMMENT 'Login Linux enviado ao cloud-init; não é senha',
    metodo_acesso ENUM('senha', 'chave_ssh') NOT NULL DEFAULT 'senha',
    situacao ENUM('rascunho', 'confirmado', 'enviado', 'concluido', 'cancelado') NOT NULL DEFAULT 'rascunho',
    confirmado_em DATETIME NULL,
    enviado_em DATETIME NULL COMMENT 'Após envio ao Proxmox: credenciais descartadas da memória da aplicação',
    concluido_em DATETIME NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    alterado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_solicitacao_vm (maquina_virtual_id),
    INDEX idx_solicitacao_situacao (situacao),
    CONSTRAINT fk_solicitacao_vm
        FOREIGN KEY (maquina_virtual_id) REFERENCES maquinas_virtuais(id_maquina_virtual)
);

CREATE TABLE IF NOT EXISTS eventos_provisionamento (
    id_evento BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    solicitacao_id BIGINT UNSIGNED NOT NULL,
    situacao ENUM('pendente', 'em_execucao', 'sucesso', 'falha') NOT NULL DEFAULT 'pendente',
    mensagem TEXT NULL,
    registrado_por BIGINT UNSIGNED NULL COMMENT 'Usuário que registrou ou disparou o evento, quando aplicável',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    alterado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_eventos_solicitacao (solicitacao_id),
    INDEX idx_eventos_registrado_por (registrado_por),
    CONSTRAINT fk_evento_solicitacao
        FOREIGN KEY (solicitacao_id) REFERENCES provisionamento_solicitacoes(id_solicitacao),
    CONSTRAINT fk_evento_usuario
        FOREIGN KEY (registrado_por) REFERENCES usuarios(id_usuario)
);


-- INSERÇÕES INICIAIS PARA TESTES
-- Usuário administrador: inserir manualmente no banco (nunca via front).
-- Vínculo sugerido: usuarios_contas com papel administrador e conta_id NULL.


INSERT INTO papeis (nome_papel, descricao, escopo) VALUES
('administrador', 'Administrador da plataforma — concedido somente por INSERT direto no banco.', 'plataforma'),
('titular', 'Papel de cadastro na conta em sessão (não é a entidade PF/PJ titular).', 'titular'),
('operador', 'Operador da conta — VMs, pedidos e pagamentos; não altera cadastro.', 'operador');

INSERT INTO permissoes (nome_permissao, descricao, dominio) VALUES
('admin.access', 'Acessar painel administrativo global da plataforma.', 'admin'),
('users.manage', 'Gerenciar usuários (criar, editar, bloquear, excluir).', 'admin'),
('roles.manage', 'Gerenciar papéis e permissões.', 'admin'),
('plans.manage', 'CRUD de planos VPS (criar, editar, suspender, soft delete).', 'admin'),
('orders.view', 'Visualizar todos os pedidos da plataforma.', 'admin'),
('payments.view', 'Visualizar todos os pagamentos da plataforma.', 'admin'),
('vms.view_all', 'Visualizar todas as VMs da plataforma.', 'admin'),
('vms.manage_all', 'Gerenciar todas as VMs da plataforma.', 'admin'),
('provision.view', 'Visualizar eventos de provisionamento da plataforma.', 'admin'),
('provision.manage', 'Acionar e diagnosticar provisionamento na plataforma.', 'admin'),
('own.profile', 'Completar e editar cadastro da conta em sessão.', 'admin'),
('own.orders', 'Criar e visualizar pedidos da conta em sessão.', 'operacional'),
('own.payments', 'Efetuar pagamento dos pedidos da conta em sessão.', 'operacional'),
('own.vms.view', 'Visualizar VMs da conta em sessão.', 'operacional'),
('own.vms.configure', 'Configurar VM (sabor, hostname, usuários) após pagamento.', 'operacional'),
('own.vms.power', 'Ligar, desligar e reiniciar VMs da conta em sessão.', 'operacional');

INSERT INTO papeis_permissoes (papel_id, permissao_id)
SELECT p.id_papel, pe.id_permissao
FROM papeis p
JOIN permissoes pe
WHERE p.nome_papel = 'administrador';

INSERT INTO papeis_permissoes (papel_id, permissao_id)
SELECT p.id_papel, pe.id_permissao
FROM papeis p
JOIN permissoes pe ON pe.nome_permissao = 'own.profile'
WHERE p.nome_papel = 'titular';

INSERT INTO papeis_permissoes (papel_id, permissao_id)
SELECT p.id_papel, pe.id_permissao
FROM papeis p
JOIN permissoes pe ON pe.nome_permissao IN (
    'own.orders',
    'own.payments',
    'own.vms.view',
    'own.vms.configure',
    'own.vms.power'
)
WHERE p.nome_papel = 'operador';

INSERT INTO planos_vps (nome, slug, descricao, vcpu, ram_mb, storage_gb, banda_mbps, preco_mensal) VALUES
('VPS Start', 'vps-start', 'Plano inicial para testes e pequenos projetos.', 1, 1024, 20, 100, 49.90),
('VPS Core', 'vps-core', 'Plano intermediário para aplicações web.', 2, 2048, 40, 100, 89.90),
('VPS Pro', 'vps-pro', 'Plano avançado para cargas maiores.', 4, 4096, 80, 200, 159.90);

INSERT INTO sabores_linux (nome, slug, distribuicao, versao, template_proxmox) VALUES
('Debian 13', 'debian-13', 'Debian', '13', 'debian-13-cloudinit-template'),
('Ubuntu Server 24.04 LTS', 'ubuntu-2404', 'Ubuntu Server', '24.04 LTS', 'ubuntu-2404-cloudinit-template');

-- Usuário administrador da plataforma (inserir após schema v7.7).
-- E-mail: admin@singularys.net | Senha: Admin@Singularys7

INSERT INTO usuarios (email, senha_hash, email_verificado_em, situacao)
SELECT 'admin@singularys.net',
       '$2y$10$srdT99L.cFhQqnLtzf50Tui/wigPdFIEVLOD7mbp17vsnSUwJkQ0m',
       NOW(),
       'ativo'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email = 'admin@singularys.net');

INSERT INTO usuarios_contas (usuario_id, conta_id, papel_id, situacao)
SELECT u.id_usuario, NULL, p.id_papel, 'ativo'
  FROM usuarios u
  JOIN papeis p ON p.nome_papel = 'administrador' AND p.escopo = 'plataforma'
 WHERE u.email = 'admin@singularys.net'
   AND NOT EXISTS (
       SELECT 1 FROM usuarios_contas uc
        WHERE uc.usuario_id = u.id_usuario AND uc.papel_id = p.id_papel AND uc.conta_id IS NULL
   );
