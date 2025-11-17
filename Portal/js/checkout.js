(() => {
    const DASHBOARD_URL = '/dashboard.html';
    const selectionWrapper = document.getElementById('checkoutSummary');
    if (!selectionWrapper) {
        return;
    }

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

    const formatCurrency = (value) => {
        const numberValue = Number(value) || 0;
        return numberValue.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    };

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

    const selection = readSelectionFromParams();

    const serverNameEl = document.getElementById('checkoutServerName');
    const planLabelEl = document.getElementById('checkoutPlanLabel');
    const specsListEl = document.getElementById('checkoutSpecs');
    const priceEls = [
        document.getElementById('checkoutPrice'),
        document.getElementById('checkoutSubtotal'),
        document.getElementById('checkoutTotal')
    ];

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

    const cardNumber = document.getElementById('cardNumber');
    const cardExpiry = document.getElementById('cardExpiry');
    const cardCVV = document.getElementById('cardCVV');

    const digitsOnly = (value) => value.replace(/\D/g, '');

    cardNumber?.addEventListener('input', () => {
        const digits = digitsOnly(cardNumber.value).slice(0, 16);
        const grouped = digits.replace(/(.{4})/g, '$1 ').trim();
        cardNumber.value = grouped;
    });

    cardExpiry?.addEventListener('input', () => {
        const digits = digitsOnly(cardExpiry.value).slice(0, 4);
        if (digits.length <= 2) {
            cardExpiry.value = digits;
        } else {
            cardExpiry.value = `${digits.slice(0, 2)}/${digits.slice(2)}`;
        }
    });

    cardCVV?.addEventListener('input', () => {
        cardCVV.value = digitsOnly(cardCVV.value).slice(0, 4);
    });

    const pixCodeInput = document.getElementById('pixCode');
    const copyPixButton = document.getElementById('btnCopyPix');
    copyPixButton?.addEventListener('click', () => {
        if (!pixCodeInput) {
            return;
        }
        let copyAction;
        if (navigator.clipboard?.writeText) {
            copyAction = navigator.clipboard.writeText(pixCodeInput.value);
        } else {
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

    const finalizeButton = document.getElementById('btnFinalize');
    const statusMessage = document.getElementById('paymentStatus');
    const cardForm = document.getElementById('cardForm');

    const getSelectedMethod = () => (cardRadio?.checked ? 'card' : 'pix');

    finalizeButton?.addEventListener('click', () => {
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
        }, 1800);
    });
})();
