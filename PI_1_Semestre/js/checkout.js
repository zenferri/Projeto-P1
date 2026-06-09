// Fluxo da aplicação:
// 	•	Ao carregar a página, o script verifica se o checkout existe, define um plano padrão, tenta ler o plano da URL e preenche o resumo.
// 	•	Ele configura o modal de seleção de servidor para permitir troca de plano, atualizando resumo e URL.
// 	•	Alterna automaticamente entre layouts de PIX e cartão conforme o método escolhido.
// 	•	Aplica máscaras nos campos de cartão para facilitar a digitação.
// 	•	Facilita a cópia do código PIX com feedback visual.
// 	•	Na finalização, valida o formulário (no caso do cartão), mostra uma mensagem de sucesso e redireciona para a dashboard.

(() => {

    // URL de destino após pagamento
    const DASHBOARD_URL = 'dashboard.html';

    // Wrapper do resumo de checkout. se não existir, não faz nada
    const selectionWrapper = document.getElementById('checkoutSummary');
    if (!selectionWrapper) {
        return;
    }

    // Seleção padrão do plano (fallback)
    const DEFAULT_SELECTION = {
        planId: 'desempenho',
        planLabel: 'Plano Desempenho',
        price: 69.9,
        serverName: 'API • SP01',
        region: 'São Paulo',
        vcpu: '8 vCPU',
        ram: '16 GB RAM',
        storage: '100 GB SSD NVMe',
        network: 'Rede 1 Gbps dedicada'
    };

    // Formata um número como moeda em pt-BR
    const formatCurrency = (value) => {
        const numberValue = Number(value) || 0;
        return numberValue.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    };

    // Lê parâmetros da URL e monta o objeto de seleção
    const readSelectionFromParams = () => {
        const params = new URLSearchParams(window.location.search);
        const rawPrice = params.get('preco');
        const price = rawPrice ? Number(rawPrice.replace(',', '.')) : DEFAULT_SELECTION.price;
        return {
            planId: params.get('plano') || DEFAULT_SELECTION.planId,
            planLabel: params.get('planLabel') || DEFAULT_SELECTION.planLabel,
            price,
            serverName: params.get('servidor') || DEFAULT_SELECTION.serverName,
            region: params.get('regiao') || DEFAULT_SELECTION.region,
            vcpu: params.get('vcpu') || DEFAULT_SELECTION.vcpu,
            ram: params.get('ram') || DEFAULT_SELECTION.ram,
            storage: params.get('storage') || DEFAULT_SELECTION.storage,
            network: params.get('network') || DEFAULT_SELECTION.network
        };
    };

    // Estado atual da seleção
    let selection = readSelectionFromParams();

    const serverNameEl = document.getElementById('checkoutServerName');
    const planLabelEl = document.getElementById('checkoutPlanLabel');
    const specsListEl = document.getElementById('checkoutSpecs');
    const priceEls = [
        document.getElementById('checkoutPrice'),
        document.getElementById('checkoutSubtotal'),
        document.getElementById('checkoutTotal')
    ];

    // Aplica a seleção atual ao resumo (DOM)
    const applySelectionToSummary = () => {
        if (serverNameEl) {
            serverNameEl.textContent = selection.serverName;
        }
        if (planLabelEl) {
            const regionSuffix = selection.region ? ` • ${selection.region}` : '';
            planLabelEl.textContent = `${selection.planLabel}${regionSuffix}`;
        }
        if (specsListEl) {
            specsListEl.innerHTML = '';
            [selection.vcpu, selection.ram, selection.storage, selection.network]
                .filter(Boolean)
                .forEach((spec) => {
                    const li = document.createElement('li');
                    li.textContent = spec;
                    specsListEl.appendChild(li);
                });
        }
        priceEls.forEach((el) => {
            if (el) {
                el.textContent = formatCurrency(selection.price);
            }
        });
    };

    // Atualiza a URL com os parâmetros da seleção atual (sem recarregar)
    const updateUrlWithSelection = () => {
        const url = new URL(window.location.href);
        url.searchParams.set('plano', selection.planId);
        url.searchParams.set('planLabel', selection.planLabel);
        url.searchParams.set('preco', selection.price.toFixed(2));
        url.searchParams.set('servidor', selection.serverName);
        url.searchParams.set('regiao', selection.region);
        url.searchParams.set('vcpu', selection.vcpu);
        url.searchParams.set('ram', selection.ram);
        url.searchParams.set('storage', selection.storage);
        url.searchParams.set('network', selection.network);
        window.history.replaceState(null, '', `${url.pathname}${url.search}`);
    };

    // Preenche o resumo na carga inicial
    applySelectionToSummary();

    // Converte um valor de preço para número ou null
    const parsePriceValue = (value) => {
        if (value === undefined || value === null || value === '') {
            return null;
        }
        const normalized = value.toString().replace(',', '.');
        const numberValue = Number(normalized);
        return Number.isFinite(numberValue) ? numberValue : null;
    };

    // Modal de troca de servidor/plano
    const changeServerButton = document.getElementById('btnChangeServer');
    const serverSelectionModalElement = document.getElementById('serverSelectionModal');
    const serverSelectionModal = serverSelectionModalElement ? new bootstrap.Modal(serverSelectionModalElement) : null;
    const modalSelectButtons = serverSelectionModalElement
        ? Array.from(serverSelectionModalElement.querySelectorAll('.modal-select-plan'))
        : [];

    // Abre o modal ao clicar em "trocar servidor"
    changeServerButton?.addEventListener('click', () => {
        serverSelectionModal?.show();
    });

    // Atualiza a seleção com os dados do botão escolhido no modal
    const handleServerSelection = (dataset) => {
        if (!dataset) {
            return;
        }
        const parsedPrice = parsePriceValue(dataset.planPrice);
        selection = {
            ...selection,
            planId: dataset.planId || selection.planId,
            planLabel: dataset.planLabel || selection.planLabel,
            price: parsedPrice ?? selection.price,
            serverName: dataset.serverName || selection.serverName,
            region: dataset.serverRegion || selection.region,
            vcpu: dataset.serverVcpu || selection.vcpu,
            ram: dataset.serverRam || selection.ram,
            storage: dataset.serverStorage || selection.storage,
            network: dataset.serverNetwork || selection.network
        };
        applySelectionToSummary();
        updateUrlWithSelection();
        serverSelectionModal?.hide();
    };

    // Liga cada botão do modal ao handler de seleção
    modalSelectButtons.forEach((button) => {
        button.addEventListener('click', () => {
            handleServerSelection(button.dataset);
        });
    });

    // Alternância entre PIX e cartão
    const pixRadio = document.getElementById('paymentPix');
    const cardRadio = document.getElementById('paymentCard');
    const pixContent = document.getElementById('pixContent');
    const cardContent = document.getElementById('cardContent');

    const togglePaymentViews = () => {
        const isPix = pixRadio?.checked;
        if (pixContent) {
            pixContent.classList.toggle('d-none', !isPix);
        }
        if (cardContent) {
            cardContent.classList.toggle('d-none', !!isPix);
        }
    };

    pixRadio?.addEventListener('change', togglePaymentViews);
    cardRadio?.addEventListener('change', togglePaymentViews);
    togglePaymentViews();

    // Campos de cartão + máscaras
    const cardNumber = document.getElementById('cardNumber');
    const cardExpiry = document.getElementById('cardExpiry');
    const cardCVV = document.getElementById('cardCVV');

    const digitsOnly = (value) => value.replace(/\D/g, '');

    // Máscara do número do cartão: 1234 1234 1234 1234
    cardNumber?.addEventListener('input', () => {
        const digits = digitsOnly(cardNumber.value).slice(0, 16);
        const grouped = digits.replace(/(.{4})/g, '$1 ').trim();
        cardNumber.value = grouped;
    });

    // Máscara de validade: MM/AA
    cardExpiry?.addEventListener('input', () => {
        const digits = digitsOnly(cardExpiry.value).slice(0, 4);
        if (digits.length <= 2) {
            cardExpiry.value = digits;
        } else {
            cardExpiry.value = `${digits.slice(0, 2)}/${digits.slice(2)}`;
        }
    });

    // CVV: até 4 dígitos
    cardCVV?.addEventListener('input', () => {
        cardCVV.value = digitsOnly(cardCVV.value).slice(0, 4);
    });

    // Cópia do código PIX
    const pixCodeInput = document.getElementById('pixCode');
    const copyPixButton = document.getElementById('btnCopyPix');
    copyPixButton?.addEventListener('click', () => {
        if (!pixCodeInput) {
            return;
        }
        let copyAction;
        if (navigator.clipboard?.writeText) {
            // API moderna de clipboard
            copyAction = navigator.clipboard.writeText(pixCodeInput.value);
        } else {
            // Fallback legado
            pixCodeInput.select();
            const didCopy = document.execCommand && document.execCommand('copy');
            pixCodeInput.setSelectionRange(0, 0);
            copyAction = didCopy ? Promise.resolve() : Promise.reject();
        }
        copyAction
            .then(() => {
                copyPixButton.textContent = 'Copiado!';
                copyPixButton.disabled = true;
                setTimeout(() => {
                    copyPixButton.textContent = 'Copiar';
                    copyPixButton.disabled = false;
                }, 2000);
            })
            .catch(() => {
                copyPixButton.textContent = 'Copie manualmente';
            });
    });

    // Finalização do pagamento
    const finalizeButton = document.getElementById('btnFinalize');
    const statusMessage = document.getElementById('paymentStatus');
    const cardForm = document.getElementById('cardForm');

    const getSelectedMethod = () => (cardRadio?.checked ? 'card' : 'pix');

    finalizeButton?.addEventListener('click', () => {
        // Se for cartão, exige formulário válido (HTML5)
        if (getSelectedMethod() === 'card' && cardForm) {
            if (!cardForm.checkValidity()) {
                cardForm.reportValidity();
                return;
            }
        }
        finalizeButton.disabled = true;
        finalizeButton.textContent = 'Processando pagamento...';
        if (statusMessage) {
            statusMessage.classList.remove('d-none');
            statusMessage.textContent = 'Pagamento aprovado! Redirecionando para a dashboard...';
        }
        setTimeout(() => {
            window.location.href = DASHBOARD_URL;
        }, 2800);
    });
})();
