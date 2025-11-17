(() => {
    const initCadastroModal = () => {
        const form = document.getElementById('cadastroForm');
        if (!form || form.dataset.initialized === 'true') {
            return;
        }

        form.dataset.initialized = 'true';

        const cadastroModalElement = document.getElementById('exampleModal');
        const pfFields = document.getElementById('pfFields');
        const pjFields = document.getElementById('pjFields');
        const tipoPF = document.getElementById('tipoPF');
        const tipoPJ = document.getElementById('tipoPJ');
        const pfInputs = form.querySelectorAll('[data-required-group="pf"]');
        const pjInputs = form.querySelectorAll('[data-required-group="pj"]');
        const cepInput = document.getElementById('inputZip');
        const cpfInput = document.getElementById('inputCPF');
        const cnpjInput = document.getElementById('inputCNPJ');
        const phoneInput = document.getElementById('inputWhatsapp');
        const emailInput = document.getElementById('inputEmail');
        const verificationModalElement = document.getElementById('verificationModal');
        const verificationEmail = document.getElementById('verificationEmail');
        const verificationForm = document.getElementById('verificationForm');
        const verificationFormFields = document.getElementById('verificationFormFields');
        const verificationSuccessMessage = document.getElementById('verificationSuccessMessage');
        const verificationCodeInput = document.getElementById('verificationCode');
        const chooseServerButton = document.getElementById('btnChooseServer');
        const serverSelectionModalElement = document.getElementById('serverSelectionModal');
        const termsLink = document.getElementById('termsLink');
        const termsModalElement = document.getElementById('termsModal');
        const termsModalContent = document.getElementById('termsModalContent');
        const termsAgreeButton = document.getElementById('termsAgreeBtn');
        const termsDeclineButton = document.getElementById('termsDeclineBtn');
        const CART_URL = '/carrinho.html';
        const PLANOS = {
            essencial: {
                id: 'essencial',
                nome: 'Essencial',
                preco: 29.9,
                precoLabel: 'R$ 29,90/mês',
                resumoTitulo: 'Entrada inteligente',
                resumoDescricao: '2 vCPU • 4 GB RAM • 40 GB SSD',
                specs: ['vCPU dedicada para workloads leves', '200 Mbps de rede estável', 'Snapshots mensais incluídos']
            },
            avancado: {
                id: 'avancado',
                nome: 'Avançado',
                preco: 39.9,
                precoLabel: 'R$ 39,90/mês',
                resumoTitulo: 'Equilíbrio e escala',
                resumoDescricao: '4 vCPU • 8 GB RAM • 80 GB SSD NVMe',
                specs: ['Rede de 500 Mbps com baixa latência', 'Snapshots semanais automáticos', 'Suporte 24/7 prioritário']
            },
            desempenho: {
                id: 'desempenho',
                nome: 'Desempenho',
                preco: 69.9,
                precoLabel: 'R$ 69,90/mês',
                resumoTitulo: 'VM dedicada para crescer',
                resumoDescricao: '8 vCPU • 16 GB RAM • 100 GB SSD NVMe',
                specs: ['Rede 1 Gbps dedicada', 'Backup incremental diário', 'Provisionamento em 30 segundos']
            }
        };
        let planoSelecionado = PLANOS.desempenho;
        const planSummaryElements = {
            heroName: document.getElementById('selectedPlanName'),
            heroPrice: document.getElementById('selectedPlanPrice'),
            resumoTitulo: document.getElementById('planResumeTitle'),
            resumoDescricao: document.getElementById('planResumeDescription'),
            specsList: document.getElementById('selectedPlanSpecs')
        };

        const cadastroModal = cadastroModalElement ? bootstrap.Modal.getOrCreateInstance(cadastroModalElement) : null;
        const verificationModal = verificationModalElement ? new bootstrap.Modal(verificationModalElement) : null;
        const serverSelectionModal = serverSelectionModalElement ? new bootstrap.Modal(serverSelectionModalElement) : null;
        const termsModal = termsModalElement ? bootstrap.Modal.getOrCreateInstance(termsModalElement) : null;
        let shouldValidate = false;
        let termsShouldReturnToCadastro = false;
        let termsLoaded = false;
        let termsLoading = false;

        const digitsOnly = (value, maxLength) => value.replace(/\D/g, '').slice(0, maxLength);

        const applyPlanSummary = () => {
            const { heroName, heroPrice, resumoTitulo, resumoDescricao, specsList } = planSummaryElements;
            if (heroName) {
                heroName.textContent = planoSelecionado.nome;
            }
            if (heroPrice) {
                heroPrice.textContent = planoSelecionado.precoLabel;
            }
            if (resumoTitulo) {
                resumoTitulo.textContent = planoSelecionado.resumoTitulo;
            }
            if (resumoDescricao) {
                resumoDescricao.textContent = planoSelecionado.resumoDescricao;
            }
            if (specsList) {
                specsList.innerHTML = '';
                planoSelecionado.specs.forEach((spec) => {
                    const li = document.createElement('li');
                    li.textContent = spec;
                    specsList.appendChild(li);
                });
            }
        };

        const selectPlanFromQuery = () => {
            const params = new URLSearchParams(window.location.search);
            const planParam = params.get('plano');
            if (planParam) {
                const key = planParam.toLowerCase();
                if (PLANOS[key]) {
                    planoSelecionado = PLANOS[key];
                }
            }
            applyPlanSummary();
        };
        selectPlanFromQuery();

        const isFieldElement = (element) =>
            element instanceof HTMLInputElement ||
            element instanceof HTMLSelectElement ||
            element instanceof HTMLTextAreaElement;

        const updateFieldValidityClass = (field) => {
            if (!isFieldElement(field)) {
                return;
            }
            if (!shouldValidate || field.disabled) {
                field.classList.remove('is-invalid');
                return;
            }
            field.classList.toggle('is-invalid', !field.checkValidity());
        };

        const highlightInvalidFields = () => {
            shouldValidate = true;
            const fields = form.querySelectorAll('input, select, textarea');
            fields.forEach(updateFieldValidityClass);
        };

        const setGroupState = (inputs, isActive) => {
            inputs.forEach((input) => {
                input.disabled = !isActive;
                input.required = isActive;
                if (!isActive) {
                    input.classList.remove('is-invalid');
                }
                if (isActive && shouldValidate) {
                    updateFieldValidityClass(input);
                }
            });
        };

        const togglePessoa = () => {
            if (!tipoPF || !tipoPJ || !pfFields || !pjFields) {
                return;
            }
            const isPJ = tipoPJ.checked;
            pfFields.classList.toggle('d-none', isPJ);
            pjFields.classList.toggle('d-none', !isPJ);
            setGroupState(pfInputs, !isPJ);
            setGroupState(pjInputs, isPJ);
        };

        const formatCEP = (value) => {
            const digits = digitsOnly(value, 8);
            if (digits.length <= 5) {
                return digits;
            }
            return `${digits.slice(0, 5)}-${digits.slice(5)}`;
        };

        const formatCPF = (value) => {
            const digits = digitsOnly(value, 11);
            const parts = [];
            if (digits.length > 0) {
                parts.push(digits.slice(0, 3));
            }
            if (digits.length >= 4) {
                parts.push(digits.slice(3, 6));
            }
            if (digits.length >= 7) {
                parts.push(digits.slice(6, 9));
            }
            let formatted = parts.join('.');
            if (digits.length > 9) {
                formatted += `-${digits.slice(9)}`;
            }
            return formatted;
        };

        const formatCNPJ = (value) => {
            const digits = digitsOnly(value, 14);
            let formatted = '';
            if (digits.length > 0) {
                formatted += digits.slice(0, 2);
            }
            if (digits.length >= 3) {
                formatted += `.${digits.slice(2, 5)}`;
            }
            if (digits.length >= 6) {
                formatted += `.${digits.slice(5, 8)}`;
            }
            if (digits.length >= 9) {
                formatted += `/${digits.slice(8, 12)}`;
            }
            if (digits.length >= 13) {
                formatted += `-${digits.slice(12, 14)}`;
            }
            return formatted;
        };

        const formatPhone = (value) => {
            const digits = digitsOnly(value, 11);
            if (!digits) {
                return '';
            }
            if (digits.length <= 2) {
                return `(${digits}`;
            }
            if (digits.length <= 7) {
                return `(${digits.slice(0, 2)}) ${digits.slice(2)}`;
            }
            return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7, 11)}`;
        };

        const resetVerificationState = () => {
            if (verificationForm) {
                verificationForm.reset();
            }
            if (verificationFormFields) {
                verificationFormFields.classList.remove('d-none');
            }
            if (verificationSuccessMessage) {
                verificationSuccessMessage.classList.add('d-none');
            }
            if (chooseServerButton) {
                chooseServerButton.disabled = false;
            }
        };

        if (cepInput) {
            cepInput.addEventListener('input', () => {
                const formatted = formatCEP(cepInput.value);
                if (formatted !== cepInput.value) {
                    cepInput.value = formatted;
                }
            });
        }

        if (cpfInput) {
            cpfInput.addEventListener('input', () => {
                const formatted = formatCPF(cpfInput.value);
                if (formatted !== cpfInput.value) {
                    cpfInput.value = formatted;
                }
            });
        }

        if (cnpjInput) {
            cnpjInput.addEventListener('input', () => {
                const formatted = formatCNPJ(cnpjInput.value);
                if (formatted !== cnpjInput.value) {
                    cnpjInput.value = formatted;
                }
            });
        }

        if (phoneInput) {
            phoneInput.addEventListener('input', () => {
                const formatted = formatPhone(phoneInput.value);
                if (formatted !== phoneInput.value) {
                    phoneInput.value = formatted;
                }
            });
        }

        if (emailInput) {
            const emailPattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
            emailInput.addEventListener('input', () => {
                if (!emailInput.value) {
                    emailInput.setCustomValidity('');
                } else if (!emailPattern.test(emailInput.value)) {
                    emailInput.setCustomValidity('Use o formato usuario@dominio.com');
                } else {
                    emailInput.setCustomValidity('');
                }
                updateFieldValidityClass(emailInput);
            });
        }

        if (verificationCodeInput) {
            verificationCodeInput.addEventListener('input', () => {
                verificationCodeInput.value = digitsOnly(verificationCodeInput.value, 6);
                verificationCodeInput.setCustomValidity('');
            });
        }

        if (verificationModalElement) {
            verificationModalElement.addEventListener('hidden.bs.modal', resetVerificationState);
        }

        form.addEventListener('input', (event) => {
            if (isFieldElement(event.target)) {
                updateFieldValidityClass(event.target);
            }
        });

        form.addEventListener('change', (event) => {
            if (isFieldElement(event.target)) {
                updateFieldValidityClass(event.target);
            }
        });

        form.addEventListener('invalid', (event) => {
            if (shouldValidate && isFieldElement(event.target)) {
                event.target.classList.add('is-invalid');
            }
        }, true);

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            highlightInvalidFields();
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            if (verificationModal) {
                resetVerificationState();
                if (verificationEmail && emailInput) {
                    verificationEmail.textContent = emailInput.value;
                }
                verificationModal.show();
            }
        });

        if (verificationForm) {
            verificationForm.addEventListener('submit', (event) => {
                event.preventDefault();
                if (!verificationCodeInput) {
                    return;
                }
                if (!/^\d{6}$/.test(verificationCodeInput.value.trim())) {
                    verificationCodeInput.setCustomValidity('Informe os 6 dígitos numéricos recebidos.');
                    verificationCodeInput.reportValidity();
                    return;
                }
                verificationCodeInput.setCustomValidity('');
                if (verificationFormFields) {
                    verificationFormFields.classList.add('d-none');
                }
                if (verificationSuccessMessage) {
                    verificationSuccessMessage.classList.remove('d-none');
                }
                if (chooseServerButton) {
                    chooseServerButton.focus();
                }
            });
        }
        if (chooseServerButton && serverSelectionModal) {
            chooseServerButton.addEventListener('click', () => {
                chooseServerButton.disabled = true;
                if (verificationModal) {
                    verificationModal.hide();
                }
                setTimeout(() => {
                    serverSelectionModal.show();
                    chooseServerButton.disabled = false;
                }, 200);
            });
        }

        const sendToCartButtons = serverSelectionModalElement
            ? serverSelectionModalElement.querySelectorAll('.send-to-cart')
            : [];

        const buildCartUrl = (dataset) => {
            const cartUrl = new URL(CART_URL, window.location.href);
            const planId = (dataset.planId || planoSelecionado.id || '').toLowerCase();
            const planLabel = dataset.planLabel || `Plano ${planoSelecionado.nome}`;
            const price = dataset.planPrice || planoSelecionado.preco.toFixed(2);
            cartUrl.searchParams.set('plano', planId);
            cartUrl.searchParams.set('planLabel', planLabel);
            cartUrl.searchParams.set('preco', price);
            cartUrl.searchParams.set('servidor', dataset.serverName || planoSelecionado.nome);
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

        sendToCartButtons.forEach((button) => {
            button.addEventListener('click', () => {
                button.disabled = true;
                const cartUrl = buildCartUrl(button.dataset);
                window.location.href = `${cartUrl.pathname}${cartUrl.search}`;
            });
        });

        if (tipoPF && tipoPJ) {
            tipoPF.addEventListener('change', togglePessoa);
            tipoPJ.addEventListener('change', togglePessoa);
            togglePessoa();
        }

        const loadTermsContent = () => {
            if (!termsModalContent || termsLoaded || termsLoading) {
                return;
            }
            termsLoading = true;
            fetch('/termos.html', { cache: 'no-cache' })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }
                    return response.text();
                })
                .then((html) => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const termosSection = doc.querySelector('.termos');
                    termsModalContent.innerHTML = termosSection ? termosSection.innerHTML : html;
                    termsLoaded = true;
                })
                .catch((error) => {
                    console.error('Erro ao carregar termos:', error);
                    termsModalContent.innerHTML = '<p>Não foi possível carregar os Termos de Uso.</p>';
                })
                .finally(() => {
                    termsLoading = false;
                });
        };

        if (termsModalElement) {
            termsModalElement.addEventListener('show.bs.modal', loadTermsContent);
            termsModalElement.addEventListener('hidden.bs.modal', () => {
                if (termsShouldReturnToCadastro && cadastroModal) {
                    setTimeout(() => {
                        cadastroModal.show();
                    }, 150);
                }
                termsShouldReturnToCadastro = false;
            });
        }

        if (termsLink && termsModal) {
            termsLink.addEventListener('click', (event) => {
                event.preventDefault();
                termsShouldReturnToCadastro = true;
                loadTermsContent();
                if (cadastroModal && cadastroModalElement.classList.contains('show')) {
                    const openTerms = () => {
                        termsModal.show();
                    };
                    cadastroModalElement.addEventListener('hidden.bs.modal', openTerms, { once: true });
                    cadastroModal.hide();
                } else {
                    termsModal.show();
                }
            });
        }

        if (termsAgreeButton && termsModal) {
            termsAgreeButton.addEventListener('click', () => {
                termsShouldReturnToCadastro = true;
                termsModal.hide();
            });
        }

        if (termsDeclineButton && termsModal) {
            termsDeclineButton.addEventListener('click', () => {
                termsShouldReturnToCadastro = false;
                termsModal.hide();
                window.location.href = '/index.html';
            });
        }
    };

    window.Singularys = window.Singularys || {};
    window.Singularys.initCadastroModal = initCadastroModal;

    if (document.readyState !== 'loading') {
        initCadastroModal();
    } else {
        document.addEventListener('DOMContentLoaded', initCadastroModal);
    }
})();
