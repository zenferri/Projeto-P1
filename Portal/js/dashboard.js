// IIFFE (Immediately Invoked Function Expression): função para não poluir o escopo global. Fica restrito dentro da função.

(() => {

    // Busca o select de VPS
    const vpsSelector = document.getElementById('vpsSelector');
    // Se não existir, encerra o script aqui
    if (!vpsSelector) {
        return;
    }
    const sshRecoveryButton = document.getElementById('btnSendSshCredentials');
    const sshRecoveryFeedback = document.getElementById('sshRecoveryFeedback');

    // Referências aos elementos da área principal (hero) do VPS
    const heroElements = {
        displayName: document.getElementById('vpsDisplayName'),
        location: document.getElementById('vpsLocation'),
        ip: document.getElementById('vpsIp'),
        uptime: document.getElementById('vpsUptime'),
        lastBackup: document.getElementById('vpsLastBackup'),
        cpuUsage: document.getElementById('vpsCpuUsage'),
        cpuBar: document.getElementById('vpsCpuUsageBar'),
        memoryUsage: document.getElementById('vpsMemoryUsage'),
        memoryBar: document.getElementById('vpsMemoryUsageBar')
    };

    // Referências aos elementos que exibem o plano contratado
    const planElements = {
        name: document.getElementById('vpsPlanTitle'),
        specs: document.getElementById('vpsPlanSpecs'),
        trafficIncluded: document.getElementById('vpsTrafficIncluded'),
        trafficUsed: document.getElementById('vpsTrafficUsed'),
        trafficBar: document.getElementById('vpsTrafficBar'),
        nextInvoice: document.getElementById('vpsNextInvoice')
    };

    // Referências às métricas resumidas (cards abaixo do hero)
    const metricElements = {
        cpu: {
            value: document.getElementById('metricCpuUsage'),
            bar: document.getElementById('metricCpuBar'),
            extra: document.getElementById('metricCpuExtra')
        },
        memory: {
            value: document.getElementById('metricMemoryUsage'),
            bar: document.getElementById('metricMemoryBar'),
            extra: document.getElementById('metricMemoryExtra')
        },
        storage: {
            value: document.getElementById('metricStorageUsage'),
            bar: document.getElementById('metricStorageBar'),
            extra: document.getElementById('metricStorageExtra')
        },
        network: {
            value: document.getElementById('metricNetworkUsage'),
            bar: document.getElementById('metricNetworkBar'),
            extra: document.getElementById('metricNetworkExtra')
        }
    };

    // Lista ESTATICA de VPS com todos os dados que serão exibidos na dashboard
    const VPS_LIST = [
        {
            id: 'api',                      // usado como valor do select
            label: 'VM • api-singularys',   // texto usado no selec
            name: 'VM • api-singularys',
            location: 'Ubuntu 24.04 LTS — Região São Paulo • VLAN 10',
            ip: '18.120.40.12',
            uptime: '18 dias 04:23',
            lastBackup: 'há 8h',
            cpuUsagePercent: 62,
            cpuUsageLabel: '62%',
            memoryUsagePercent: 48,
            memoryUsageLabel: '48%',
            planName: 'Desempenho',
            planSpecs: '8 vCPU • 16 GB RAM • 100 GB SSD NVMe',
            trafficIncluded: '5 TB',
            trafficUsed: '2.9 TB',
            trafficUsagePercent: 58,
            nextInvoice: 'Próxima fatura em 12 de dezembro',
            // Métricas que alimentam os cards inferiores
            metrics: {
                cpuValue: '62%',
                cpuBar: 62,
                cpuExtra: 'Carga média 15 min: 1.32',
                memoryValue: '7.6 / 16 GB',
                memoryBar: 48,
                memoryExtra: 'Buffer + cache: 1.2 GB',
                storageValue: '68 / 100 GB',
                storageBar: 68,
                storageExtra: 'Snapshots ocupam 12 GB',
                networkValue: '420 Mbps',
                networkBar: 42,
                networkExtra: 'Pico hoje: 820 Mbps'
            }
        },
        {
            id: 'db',
            label: 'VM • db-cluster',
            name: 'VM • db-cluster',
            location: 'Rocky Linux 9 — Região São Paulo • VLAN 20',
            ip: '189.40.72.77',
            uptime: '26 dias 11:08',
            lastBackup: 'há 2h',
            cpuUsagePercent: 48,
            cpuUsageLabel: '48%',
            memoryUsagePercent: 62,
            memoryUsageLabel: '62%',
            planName: 'Desempenho Plus',
            planSpecs: '12 vCPU • 32 GB RAM • 200 GB SSD NVMe',
            trafficIncluded: '8 TB',
            trafficUsed: '4.1 TB',
            trafficUsagePercent: 51,
            nextInvoice: 'Próxima fatura em 05 de janeiro',
            metrics: {
                cpuValue: '48%',
                cpuBar: 48,
                cpuExtra: 'Carga média 15 min: 1.05',
                memoryValue: '19.8 / 32 GB',
                memoryBar: 62,
                memoryExtra: 'Buffer + cache: 2.7 GB',
                storageValue: '150 / 200 GB',
                storageBar: 75,
                storageExtra: 'Snapshots ocupam 18 GB',
                networkValue: '310 Mbps',
                networkBar: 31,
                networkExtra: 'Pico hoje: 540 Mbps'
            }
        },
        {
            id: 'dev',
            label: 'VM • dev-lab',
            name: 'VM • dev-lab',
            location: 'Debian 12 — Região Curitiba • VLAN 30',
            ip: '170.83.92.14',
            uptime: '08 dias 07:55',
            lastBackup: 'há 1 dia',
            cpuUsagePercent: 28,
            cpuUsageLabel: '28%',
            memoryUsagePercent: 36,
            memoryUsageLabel: '36%',
            planName: 'Essencial',
            planSpecs: '4 vCPU • 8 GB RAM • 80 GB SSD NVMe',
            trafficIncluded: '3 TB',
            trafficUsed: '1.1 TB',
            trafficUsagePercent: 36,
            nextInvoice: 'Próxima fatura em 02 de janeiro',
            metrics: {
                cpuValue: '28%',
                cpuBar: 28,
                cpuExtra: 'Carga média 15 min: 0.64',
                memoryValue: '3.1 / 8 GB',
                memoryBar: 36,
                memoryExtra: 'Buffer + cache: 0.6 GB',
                storageValue: '42 / 80 GB',
                storageBar: 53,
                storageExtra: 'Snapshots ocupam 6 GB',
                networkValue: '120 Mbps',
                networkBar: 12,
                networkExtra: 'Pico hoje: 210 Mbps'
            }
        }
    ];

    // Cria um mapa { id: objetoVps } para acesso rápido por id
    const VPS_MAP = VPS_LIST.reduce((acc, item) => {
        acc[item.id] = item;
        return acc;
    }, {});

    // Função utilitária para definir texto em um elemento, com fallback '-'
    const setText = (element, value) => {
        if (element) {
            element.textContent = value || '-';
        }
    };
    // Função utilitária para ajustar a largura da barra de progresso
    const setBar = (element, percent) => {
        if (element && typeof percent === 'number') {
            element.style.width = `${Math.max(0, Math.min(percent, 100))}%`;
        }
    };
    const setSshFeedback = (message, variant = 'muted') => {
        if (!sshRecoveryFeedback) {
            return;
        }
        sshRecoveryFeedback.classList.remove('d-none', 'text-success', 'text-white-50');
        sshRecoveryFeedback.classList.add(variant === 'success' ? 'text-success' : 'text-white-50');
        sshRecoveryFeedback.textContent = message;
    };

    // Aplica os dados de um VPS específico em todos os elementos da página
    const applyVpsData = (vps) => {
        if (!vps) {
            return;
        }

        // area hero
        setText(heroElements.displayName, vps.name);
        setText(heroElements.location, vps.location);
        setText(heroElements.ip, vps.ip);
        setText(heroElements.uptime, vps.uptime);
        setText(heroElements.lastBackup, vps.lastBackup);
        setText(heroElements.cpuUsage, vps.cpuUsageLabel);
        setBar(heroElements.cpuBar, vps.cpuUsagePercent);
        setText(heroElements.memoryUsage, vps.memoryUsageLabel);
        setBar(heroElements.memoryBar, vps.memoryUsagePercent);

        // plano
        setText(planElements.name, vps.planName);
        setText(planElements.specs, vps.planSpecs);
        setText(planElements.trafficIncluded, vps.trafficIncluded);
        setText(planElements.trafficUsed, vps.trafficUsed);
        setBar(planElements.trafficBar, vps.trafficUsagePercent);
        setText(planElements.nextInvoice, vps.nextInvoice);

        // metricas - CPU
        setText(metricElements.cpu.value, vps.metrics.cpuValue);
        setBar(metricElements.cpu.bar, vps.metrics.cpuBar);
        setText(metricElements.cpu.extra, vps.metrics.cpuExtra);

        // metricas - memória
        setText(metricElements.memory.value, vps.metrics.memoryValue);
        setBar(metricElements.memory.bar, vps.metrics.memoryBar);
        setText(metricElements.memory.extra, vps.metrics.memoryExtra);

        // metricas - storage
        setText(metricElements.storage.value, vps.metrics.storageValue);
        setBar(metricElements.storage.bar, vps.metrics.storageBar);
        setText(metricElements.storage.extra, vps.metrics.storageExtra);

        // metricas - uso da rede
        setText(metricElements.network.value, vps.metrics.networkValue);
        setBar(metricElements.network.bar, vps.metrics.networkBar);
        setText(metricElements.network.extra, vps.metrics.networkExtra);
    };

    // Preenche o <select> com as opções de VPS
    VPS_LIST.forEach((vps) => {
        const option = document.createElement('option');
        option.value = vps.id;              // valor usado internamente
        option.textContent = vps.label;     // texto exibido para o usuário
        vpsSelector.appendChild(option);
    });

    // Aplica na tela os dados do VPS atualmente selecionado no <select>
    const applyCurrentSelection = () => {
        // Pega o VPS pelo id selecionado ou cai no primeiro da lista
        const selected = VPS_MAP[vpsSelector.value] || VPS_LIST[0];
        applyVpsData(selected);
    };

    // Atualiza a dashboard sempre que o usuário trocar o VPS no select
    vpsSelector.addEventListener('change', applyCurrentSelection);
    // Define o valor inicial do select para o primeiro VPS da lista
    vpsSelector.value = VPS_LIST[0]?.id || '';
    // Aplica os dados iniciais
    applyCurrentSelection();

    // Botão "Comprar novo VPS"
    const buyButton = document.getElementById('btnBuyNewVps');
    // Elemento do modal de seleção de servidor
    const serverSelectionModalElement = document.getElementById('dashboardServerSelectionModal');
    // Instância do modal Bootstrap, se o elemento existir
    const serverSelectionModal = serverSelectionModalElement
        ? new bootstrap.Modal(serverSelectionModalElement)
        : null;

    // Ao clicar no botão de compra, abre o modal (se existir)
    buyButton?.addEventListener('click', () => {
        serverSelectionModal?.show();
    });

    // Monta a URL do carrinho com base nos data-attributes do botão
    const buildCartUrl = (dataset) => {
        // Cria URL relativa a partir da URL atual
        const cartUrl = new URL('carrinho.html', window.location.href);
        if (dataset.planId) {
            cartUrl.searchParams.set('plano', dataset.planId);
        }
        if (dataset.planLabel) {
            cartUrl.searchParams.set('planLabel', dataset.planLabel);
        }
        if (dataset.planPrice) {
            cartUrl.searchParams.set('preco', dataset.planPrice);
        }
        if (dataset.serverName) {
            cartUrl.searchParams.set('servidor', dataset.serverName);
        }
        if (dataset.serverRegion) {
            cartUrl.searchParams.set('regiao', dataset.serverRegion);
        }
        if (dataset.serverVcpu) {
            cartUrl.searchParams.set('vcpu', dataset.serverVcpu);
        }
        if (dataset.serverRam) {
            cartUrl.searchParams.set('ram', dataset.serverRam);
        }
        if (dataset.serverStorage) {
            cartUrl.searchParams.set('storage', dataset.serverStorage);
        }
        if (dataset.serverNetwork) {
            cartUrl.searchParams.set('network', dataset.serverNetwork);
        }
        return cartUrl;
    };

    // Seleciona os botões de plano dentro do modal (cada card de servidor)
    const modalButtons = serverSelectionModalElement
        ? serverSelectionModalElement.querySelectorAll('.dashboard-select-plan')
        : [];

    // Para cada botão de plano, adiciona o comportamento de ir para o carrinho
    modalButtons.forEach((button) => {
        button.addEventListener('click', () => {
            button.disabled = true; // evita clique duplo
            const cartUrl = buildCartUrl(button.dataset || {});
            // Redireciona para o carrinho com os parâmetros montados
            window.location.href = `${cartUrl.pathname}${cartUrl.search}`;
        });
    });

    // Botão para recuperar credenciais SSH 
    if (sshRecoveryButton) {
        const defaultLabel = sshRecoveryButton.textContent.trim() || 'Enviar credenciais SSH';
        sshRecoveryButton.addEventListener('click', () => {
            sshRecoveryButton.disabled = true;
            sshRecoveryButton.textContent = 'Enviando...';
            setSshFeedback('Reenviando usuário e senha SSH...', 'muted');
            setTimeout(() => {
                setSshFeedback('Credenciais enviadas para o e-mail cadastrado.', 'success');
                sshRecoveryButton.disabled = false;
                sshRecoveryButton.textContent = defaultLabel;
            }, 1600);
        });
    }
})();
