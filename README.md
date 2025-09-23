<p align="center" style="font-size:28px;"><strong>Documentação do PI</em></strong></p>

# FATEC JAHU

**Faculdade de Tecnologia de Jahu**

**Curso:** Desenvolvimento de Software Multiplataforma  
**Disciplina:** Engenharia de Software I  
**Turma:** 2025.2

## Projeto

**Portal de Deploy de Máquinas Virtuais (Proxmox)**<br>
**Codinome: <i>Habitat</i>**<br>
**Versão 1.0 – 2025.2**

### Integrantes

- Guilherme Henrique Volpato
- José Augusto Zen Ferri
- Mauro Barbosa de Azevedo Junior
- Rafael Henrique Biliasi
- Renan de Fabio

**Data:** Setembro/2025
<br>

## Sumário

- [Resumo](#resumo)
- [1. Introdução](#1-introdução)
  - [1.1 Problema de Pesquisa](#11-problema-de-pesquisa)
  - [1.2 Objetivos](#12-objetivos)
    - [1.2.1 Objetivo Geral](#121-objetivo-geral)
    - [1.2.2 Objetivos Específicos](#122-objetivos-específicos)
  - [1.3 Justificativa](#13-justificativa)
- [2. Referencial Teórico](#2-referencial-teórico)
- [3. Metodologia](#3-metodologia)
- [4. Requisitos Funcionais e Não Funcionais](#4-requisitos-funcionais-e-não-funcionais)
  - [4.1 Requisitos Funcionais](#41-requisitos-funcionais)
    - [RF01](#rf01) • [RF02](#rf02) • [RF03](#rf03) • [RF04](#rf04) • [RF05](#rf05)
    - [RF06](#rf06) • [RF07](#rf07)
  - [4.2 Requisitos Não Funcionais](#42-requisitos-não-funcionais)
    - [RNF01](#rnf01) • [RNF02](#rnf02) • [RNF03](#rnf03) • [RNF04](#rnf04) • [RNF05](#rnf05)
    - [RNF06](#rnf06) • [RNF07](#rnf07)
- [5. Protótipo de Fluxo de Provisionamento](#5-protótipo-de-fluxo-de-provisionamento)
- [6. Protótipo de Arquitetura Técnica](#6-protótipo-de-arquitetura-técnica)
- [7. Segurança e Governança](#7-segurança-e-governança)
- [8. Cronograma da Primeira Etapa](#8-cronograma-da-primeira-etapa-20252)
- [9. Referências](#9-referências)

## Resumo

O presente projeto de pesquisa tem como finalidade o desenvolvimento de um portal web para a Hospedaria Internet, voltado ao provisionamento automático de máquinas virtuais (VMs) em infraestrutura Proxmox. Atualmente, a contratação de servidores virtuais exige contato direto com a equipe técnica, que realiza a configuração manualmente. Esse modelo gera atrasos e limita a escalabilidade da oferta. A pesquisa busca propor, modelar e validar um sistema que permita ao cliente, de forma autônoma, selecionar planos de VPS, realizar o pagamento online e inicializar automaticamente sua instância virtual. O projeto fundamenta-se em referenciais teóricos de computação em nuvem, automação de infraestrutura e comércio eletrônico, aplicando metodologia de análise de requisitos, modelagem conceitual, implementação prototípica e validação prática. O resultado esperado é a criação de uma solução que otimize processos, reduza custos operacionais e amplie a experiência do cliente.

## 1. Introdução

O presente projeto de pesquisa está inserido no contexto da evolução dos serviços de hospedagem e virtualização, que têm como tendência a automação e a oferta de soluções self-service. A Hospedaria Internet já dispõe de sistema para cadastro e liberação de espaço em ambiente pré-configurado, com compartilhamento de recursos do servidor. Todavia, quando um cliente solicita um servidor virtual próprio, o processo ainda depende de contato direto com a empresa, que realiza manualmente a configuração e disponibilização do serviço. Esse cenário cria uma lacuna de eficiência e limita a escalabilidade do negócio.

### 1.1 Problema de Pesquisa

A problemática que se busca enfrentar consiste em suprir a ausência de um mecanismo automatizado que permita ao cliente criar e gerenciar sua própria instância de servidor virtual, sem a necessidade de intervenção humana. Pretende-se, com este projeto, eliminar essa limitação, permitindo que o usuário final escolha entre diferentes planos, efetue o pagamento diretamente no portal e inicialize sua máquina virtual de maneira imediata, segura e autônoma.

### 1.2 Objetivos

#### 1.2.1 Objetivo Geral

Desenvolver um portal para provisionamento automático de máquinas virtuais em ambiente Proxmox, integrado a sistema de pagamento online.

#### 1.2.2 Objetivos Específicos

- Implementar cadastro e autenticação de clientes;
- Disponibilizar catálogo de planos VPS;
- Integrar com gateway de pagamento;
- Automatizar clonagem de templates no Proxmox com _cloud-init_;
- Gerar credenciais no painel do cliente;
- Garantir logs de auditoria e políticas de ciclo de vida.

### 1.3 Justificativa

A proposta justifica-se pela necessidade de modernização do processo de provisionamento da Hospedaria Internet. A automação permitirá maior agilidade, redução de custos operacionais e aumento da satisfação do cliente. Além disso, alinha-se às práticas do mercado de computação em nuvem, fortalecendo a competitividade da empresa e criando diferenciais estratégicos frente à concorrência.

## 2. Referencial Teórico

A pesquisa fundamenta-se em conceitos de computação em nuvem, infraestrutura como serviço (IaaS) e automação de infraestrutura por meio de APIs e ferramentas como Proxmox e cloud-init. O National Institute of Standards and Technology (NIST) define computação em nuvem como “(...) um modelo que possibilita o acesso ubíquo, conveniente e sob demanda, por meio da rede, a um conjunto compartilhado de recursos computacionais configuráveis (por exemplo, redes, servidores, armazenamento, aplicações e serviços), que podem ser rapidamente provisionados e liberados com o mínimo de esforço de gerenciamento ou interação com o provedor de serviços (...).” (NIST, 2011).

Segundo Rios (2019, p. 16-17), citando o trabalho de Turban et al. (2018), expõe que, <i>no campo do comércio eletrônico, a integração entre sistemas de pagamento e plataformas digitais é apontada como fundamental para garantir experiências fluidas de consumo. Esses referenciais fornecem base teórica para a modelagem e a implementação do portal proposto.</i>

## 3. Metodologia

A pesquisa seguirá abordagem aplicada, com caráter exploratório e descritivo. A metodologia compreende as etapas: 1) entrevista e levantamento de requisitos funcionais e não funcionais; 2) modelagem conceitual e lógica do sistema; 3) implementação prototípica em ambiente controlado; 4) integração com gateway de pagamento; 5) testes de provisionamento em Proxmox com uso de templates cloud-init; 6) análise de desempenho e confiabilidade; 7) documentação e validação junto.

## 4. Requisitos Funcionais e Não Funcionais

### 4.1 Requisitos Funcionais

- <a id="rf01"></a>**RF01** – Cadastrar e autenticar clientes: O sistema deve permitir o registro de novos clientes, com validação de e-mail, bem como o login seguro de usuários existentes.

- <a id="rf02"></a>**RF02** – Catalogar planos de VPS: O portal deve exibir a lista de planos disponíveis, com especificações de CPU, memória, armazenamento e preço.

- <a id="rf03"></a>**RF03** – Selecionar e realizar pedido: O cliente deve poder selecionar um plano, gerar um pedido e visualizar os detalhes antes de confirmar.

- <a id="rf04"></a>**RF04** – Integrar com gateway de pagamento: O sistema deve permitir que o cliente realize pagamento online (cartão/Pix), recebendo confirmação automática via webhook.

- <a id="rf05"></a>**RF05** – Provisionar automaticamente a VM: Após confirmação de pagamento, o sistema deve disparar o processo de criação da VM no Proxmox, a partir de um template configurado com cloud-init.

- <a id="rf06"></a>**RF06** – Configurar a VM: O sistema deve aplicar automaticamente CPU, memória, armazenamento, rede e hostname conforme o plano contratado.

- <a id="rf07"></a>**RF07** – Gerar e entregar as credenciais: O sistema deve registrar e disponibilizar ao cliente as credenciais de acesso à sua VM no painel do portal.

### 4.2 Requisitos Não Funcionais

- <a id="rnf01"></a>**RNF01** – Disponibilidade SLA: O portal deve estar disponível em 99,5% do tempo mensal, excetuando-se manutenções programadas.

- <a id="rnf02"></a>**RNF02** – Desempenho: resposta < 3s

- <a id="rnf03"></a>**RNF03** – Escalabilidade: O sistema deve suportar crescimento do número de clientes e provisionamento simultâneo de múltiplas VMs, sem degradação significativa de performance.

- <a id="rnf04"></a>**RNF04** – Segurança: Todas as comunicações devem ser realizadas via HTTPS (TLS 1.2 ou superior), e as senhas armazenadas com criptografia forte.

- <a id="rnf05"></a>**RNF05** – Confiabilidade: O provisionamento de VMs deve ser idempotente, garantindo que um mesmo pedido não gere múltiplas máquinas em caso de repetição de eventos do gateway.

- <a id="rnf06"></a>**RNF06** – Usabilidade: A interface deve ser intuitiva e responsiva, acessível em navegadores modernos e dispositivos móveis.

- <a id="rnf07"></a>**RNF07** – Conformidade com a LGPD: O portal deve estar em conformidade com a LGPD (Lei Geral de Proteção de Dados) no tratamento de dados pessoais de clientes.

## 5. Protótipo de Fluxo de Provisionamento

1. Cliente seleciona plano e cria Pedido (status: created).

2. Checkout no gateway; Webhook aprovado → Pedido = paid

3. Criação de trabalho na fila: Provisionar VM (pedidoId).

4. Orquestrador invoca API Proxmox: clone do Template → CPU/RAM/Disk → cloud‑init (ciuser/sshkeys/hostname/ipconfig0) → start.

5. Descoberta de IP (QEMU Guest Agent ou DHCP leases), gravação da VM e atualização do Pedido para provisioned.

6. Notificação por e-mail ao cliente com hostname/IP/credenciais e exibição no painel.

7. Em erros, registro de EventoProvisionamento e rollback (destroy).

---

## 6. Protótipo de Arquitetura Técnica

A arquitetura proposta adota um modelo em camadas para assegurar modularidade, escalabilidade e segurança no provisionamento automático de máquinas virtuais. Após discussões com o gestor de redes da Hospedaria Internet, foram sugeridas as seguintes ferramentas:
• Camada de Apresentação (Front-end): Responsável pela interação com o cliente, será implementada utilizando frameworks modernos como Next.js ou React, garantindo responsividade, acessibilidade em múltiplos dispositivos e experiência de uso intuitiva.

• Camada de Aplicação (Back-end): Implementada em Node.js/Express ou Python/Django REST Framework, será responsável pela lógica de negócios, autenticação de clientes, gestão de pedidos, integração com gateway de pagamento e comunicação com os demais serviços da arquitetura.

• Camada de Dados: Utilização de PostgreSQL como sistema gerenciador de banco de dados relacional, armazenando informações de clientes, planos, pedidos, pagamentos, logs de auditoria e metadados das máquinas virtuais.

• Camada de Mensageria e Orquestração: A comunicação assíncrona e o gerenciamento de tarefas de provisionamento serão realizados por meio de Redis ou RabbitMQ, assegurando confiabilidade, escalabilidade e idempotência no tratamento dos eventos. O Orquestrador de Provisionamento atuará como serviço especializado, consumindo mensagens da fila e executando as chamadas à API do Proxmox.

• Camada de Infraestrutura Virtualizada: A infraestrutura de máquinas virtuais será gerida em ambiente Proxmox, utilizando templates configurados com cloud-init, possibilitando a criação rápida e padronizada de VMs.

• Camada de Rede e Proxy Reverso: O acesso externo às VMs e ao portal será intermediado por Traefik ou Nginx, garantindo balanceamento de carga, roteamento dinâmico de subdomínios e emissão automática de certificados digitais.

• Camada de Integração com Pagamentos: O portal contará com integração a gateways de pagamento como Stripe ou Mercado Pago, que enviarão confirmações via webhooks para disparo do processo de provisionamento.

• Camada de Segurança: Todas as comunicações serão realizadas por meio de TLS (HTTPS), assegurando confidencialidade e integridade dos dados. Além disso, serão aplicadas boas práticas de controle de acesso, criptografia de credenciais e conformidade com a LGPD.

---

## 7. Segurança e Governança

• Tokens de API Proxmox com menor privilégio e escopo restrito.

• Validação de assinatura e idempotência do webhook.

• Logs de eventos e auditoria de acesso.

• Limites de recursos e políticas de suspensão.

• Backups de metadados e, conforme plano, snapshot/backup da VM.

---

## 8. Cronograma da Primeira Etapa (2025.2)

| Mês      | Dia | Tarefa                              |
| -------- | --- | ----------------------------------- |
| Agosto   | 25  | Formação da Equipe                  |
| Setembro | 01  | Definição do Tema e Objetivo        |
| Setembro | 22  | Documento dos Requisitos            |
| Setembro | 29  | Estudo de Viabilidade               |
| Outubro  | 13  | Modelo de Negócio Canva             |
| Outubro  | 20  | Planejamento do design              |
| Outubro  | 27  | Modelagens DER                      |
| Novembro | 03  | Entrega do Protótipo                |
| Novembro | 25  | Entrega da Aplicação + Documentação |
| Novembro | 25  | Apresentação do PI                  |

---

## 9. Referências

NATIONAL INSTITUTE OF STANDARDS AND TECHNOLOGY. The NIST Definition of Cloud Computing. NIST Special Publication 800-145. Gaithersburg: NIST, 2011. Disponível em: https://nvlpubs.nist.gov/nistpubs/legacy/sp/nistspecialpublication800-145.pdf. Acesso: 07/09/2025.

RIOS, Larissa Soares de Queiroz. Intenção de compra no social commerce: um estudo sobre a perspectiva dos consumidores brasileiros. 2019. Dissertação (Mestrado em Administração) – Universidade Federal de Sergipe, São Cristóvão, 2019. Disponível em: https://ri.ufs.br/bitstream/riufs/14120/2/LARISSA_SOARES_QUEIROZ_RIOS.pdf. Acesso em: 07/09/2025.
