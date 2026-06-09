(() => {

    // Função principal de inicialização do fluxo de cadastro
    const initCadastroModal = () => {
        const form = document.getElementById('cadastroForm');

        // Evita rodar duas vezes se o formulário não existe ou já foi inicializado
        if (!form || form.dataset.initialized === 'true') {
            return;
        }

        form.dataset.initialized = 'true';

        // Captura de elementos do DOM (modais, campos, botões etc.)
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
        const passwordInput = document.getElementById('inputPassword');
        const passwordConfirmInput = document.getElementById('inputPasswordConfirm');
        const verificationModalElement = document.getElementById('verificationModal');
        const verificationEmail = document.getElementById('verificationEmail');
        const verificationForm = document.getElementById('verificationForm');
        const verificationFormFields = document.getElementById('verificationFormFields');
        const verificationCodeInput = document.getElementById('verificationCode');
        const seeOtherOptionsButton = document.getElementById('seeOtherOptionsBtn');
        const serverSelectionModalElement = document.getElementById('serverSelectionModal');
        const serverSummaryModalElement = document.getElementById('serverSummaryModal');
        const changeServerButton = document.getElementById('changeServerBtn');
        const goToCartButton = document.getElementById('goToCartBtn');
        const serverSummaryElements = {
            planName: document.getElementById('serverSummaryPlanName'),
            planPrice: document.getElementById('serverSummaryPlanPrice'),
            planLabel: document.getElementById('serverSummaryPlanLabel'),
            name: document.getElementById('serverSummaryName'),
            region: document.getElementById('serverSummaryRegion'),
            vcpu: document.getElementById('serverSummaryVcpu'),
            ram: document.getElementById('serverSummaryRam'),
            storage: document.getElementById('serverSummaryStorage'),
            network: document.getElementById('serverSummaryNetwork')
        };
        const termsLink = document.getElementById('termsLink');
        const termsModalElement = document.getElementById('termsModal');
        const termsModalContent = document.getElementById('termsModalContent');
        const termsAgreeButton = document.getElementById('termsAgreeBtn');
        const termsDeclineButton = document.getElementById('termsDeclineBtn');

        // URL base do carrinho e catálogo de planos
        const CART_URL = 'carrinho.html';
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
                resumoTitulo: 'Puro desempenho',
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

        // Instâncias dos modais Bootstrap, se existirem! 
        const cadastroModal = cadastroModalElement ? bootstrap.Modal.getOrCreateInstance(cadastroModalElement) : null;
        const verificationModal = verificationModalElement ? new bootstrap.Modal(verificationModalElement) : null;
        const serverSelectionModal = serverSelectionModalElement ? new bootstrap.Modal(serverSelectionModalElement) : null;
        const serverSummaryModal = serverSummaryModalElement ? new bootstrap.Modal(serverSummaryModalElement) : null;
        const termsModal = termsModalElement ? bootstrap.Modal.getOrCreateInstance(termsModalElement) : null;

        // Flags (ou variáveis) de estado
        let shouldValidate = false;
        let termsShouldReturnToCadastro = false;
        let termsLoaded = false;
        let termsLoading = false;
        let selectedServerOption = null;
        let sendToCartAfterSelection = false;
        const serverOptionsByPlan = {};

        // Helper para deixar só dígitos e limitar tamanho
        const digitsOnly = (value, maxLength) => value.replace(/\D/g, '').slice(0, maxLength);

        // Copia simples de dataset de um elemento
        const cloneDataset = (dataset = {}) => {
            const clone = {};
            if (!dataset) {
                return clone;
            }
            Object.keys(dataset).forEach((key) => {
                clone[key] = dataset[key];
            });
            return clone;
        };
        // Normaliza chave do plano e resolve definição
        const normalizePlanKey = (value) => (value ? value.toLowerCase() : '');

        const getPlanDefinition = (planKey) => {
            const normalized = normalizePlanKey(planKey || planoSelecionado.id);
            return PLANOS[normalized] || planoSelecionado;
        };

        // Monta objeto selectedServerOption a partir de dataset ou plano padrão
        const setSelectedServerOptionFromDataset = (dataset = {}) => {
            const planDefinition = getPlanDefinition(dataset.planId);
            selectedServerOption = {
                planId: normalizePlanKey(dataset.planId) || planDefinition.id,
                planLabel: dataset.planLabel || `Plano ${planDefinition.nome}`,
                planPrice: dataset.planPrice || planDefinition.preco.toFixed(2),
                planPriceLabel: dataset.planPriceLabel || planDefinition.precoLabel,
                serverName: dataset.serverName || planDefinition.nome,
                serverRegion: dataset.serverRegion || '',
                serverVcpu: dataset.serverVcpu || '',
                serverRam: dataset.serverRam || '',
                serverStorage: dataset.serverStorage || '',
                serverNetwork: dataset.serverNetwork || ''
            };
        };

        // Garante que sempre haja uma seleção de servidor
        const ensureInitialServerSelection = () => {
            if (selectedServerOption && selectedServerOption.planId) {
                return;
            }
            const planKey = normalizePlanKey(planoSelecionado.id);
            if (serverOptionsByPlan[planKey]) {
                setSelectedServerOptionFromDataset(serverOptionsByPlan[planKey]);
            } else {
                setSelectedServerOptionFromDataset({});
            }
        };

        // Atualiza o modal de resumo de servidor
        const updateServerSummaryModal = () => {
            ensureInitialServerSelection();
            const selection = selectedServerOption || {};
            const planDefinition = getPlanDefinition(selection.planId);
            if (serverSummaryElements.planName) {
                serverSummaryElements.planName.textContent = selection.planLabel || planDefinition.nome;
            }
            if (serverSummaryElements.planPrice) {
                serverSummaryElements.planPrice.textContent = selection.planPriceLabel || planDefinition.precoLabel;
            }
            if (serverSummaryElements.planLabel) {
                serverSummaryElements.planLabel.textContent = planDefinition.resumoDescricao || '';
            }
            if (serverSummaryElements.name) {
                serverSummaryElements.name.textContent = selection.serverName || planDefinition.nome;
            }
            if (serverSummaryElements.region) {
                serverSummaryElements.region.textContent = selection.serverRegion
                    ? `Região ${selection.serverRegion}`
                    : 'Região não informada';
            }
            if (serverSummaryElements.vcpu) {
                serverSummaryElements.vcpu.textContent = selection.serverVcpu || 'vCPU não informada';
            }
            if (serverSummaryElements.ram) {
                serverSummaryElements.ram.textContent = selection.serverRam || 'Memória não informada';
            }
            if (serverSummaryElements.storage) {
                serverSummaryElements.storage.textContent = selection.serverStorage || 'Armazenamento não informado';
            }
            if (serverSummaryElements.network) {
                serverSummaryElements.network.textContent = selection.serverNetwork || 'Rede não informada';
            }
        };
        // Monta URL do carrinho com query params do plano
        const buildCartUrlFromSelection = () => {
            ensureInitialServerSelection();
            const selection = selectedServerOption || {};
            const planDefinition = getPlanDefinition(selection.planId);
            const cartUrl = new URL(CART_URL, window.location.href);
            const planKey = selection.planId || planDefinition.id;
            cartUrl.searchParams.set('plano', planKey);
            cartUrl.searchParams.set('planLabel', selection.planLabel || `Plano ${planDefinition.nome}`);
            cartUrl.searchParams.set('preco', selection.planPrice || planDefinition.preco.toFixed(2));
            if (selection.serverName) {
                cartUrl.searchParams.set('servidor', selection.serverName);
            }
            if (selection.serverRegion) {
                cartUrl.searchParams.set('regiao', selection.serverRegion);
            }
            if (selection.serverVcpu) {
                cartUrl.searchParams.set('vcpu', selection.serverVcpu);
            }
            if (selection.serverRam) {
                cartUrl.searchParams.set('ram', selection.serverRam);
            }
            if (selection.serverStorage) {
                cartUrl.searchParams.set('storage', selection.serverStorage);
            }
            if (selection.serverNetwork) {
                cartUrl.searchParams.set('network', selection.serverNetwork);
            }
            return cartUrl;
        };

        // Redireciona para o carrinho
        const navigateToCart = () => {
            const cartUrl = buildCartUrlFromSelection();
            window.location.href = `${cartUrl.pathname}${cartUrl.search}`;
        };

        // Exibe modal de resumo de plaon
        const showServerSummaryModal = () => {
            updateServerSummaryModal();
            if (serverSummaryModal) {
                serverSummaryModal.show();
            }
        };

        // Após verificação de email, leva para o resumo de servidor
        const proceedToServerSummary = () => {
            if (verificationModal && verificationModalElement) {
                const handleHidden = () => {
                    showServerSummaryModal();
                    verificationModalElement.removeEventListener('hidden.bs.modal', handleHidden);
                };
                verificationModalElement.addEventListener('hidden.bs.modal', handleHidden);
                verificationModal.hide();
            } else {
                showServerSummaryModal();
            }
        };

        // Aplica dados do plano selecionado na seção hero
        const applyPlanSummary = () => {
            const { heroName, heroPrice, resumoTitulo, resumoDescricao, specsList } = planSummaryElements;
            // e aqui faz atualização dos elementos
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

        // Troca o plano pela chave
        const setPlanByKey = (planKey) => {
            const normalizedPlanKey = (planKey || '').toLowerCase();
            if (!normalizedPlanKey || !PLANOS[normalizedPlanKey]) {
                return false;
            }
            if (planoSelecionado.id === normalizedPlanKey) {
                applyPlanSummary();
                return true;
            }
            planoSelecionado = PLANOS[normalizedPlanKey];
            applyPlanSummary();
            return true;
        };

        // Lê o plano da query string se houver ?plano=...
        const selectPlanFromQuery = () => {
            const params = new URLSearchParams(window.location.search);
            const planParam = params.get('plano');
            if (!setPlanByKey(planParam)) {
                applyPlanSummary();
            }
        };
        selectPlanFromQuery();

        // Helpers de validação, máscaras, eventos, termos etc....
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
        // Campo para cadastro e checagem de senha 
        if (passwordInput) {
            const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;
            const validatePasswordMatch = () => {
                if (!passwordConfirmInput) {
                    return;
                }
                if (!passwordConfirmInput.value) {
                    passwordConfirmInput.setCustomValidity('');
                } else if (passwordInput.value !== passwordConfirmInput.value) {
                    passwordConfirmInput.setCustomValidity('As senhas precisam ser iguais.');
                } else {
                    passwordConfirmInput.setCustomValidity('');
                }
                updateFieldValidityClass(passwordConfirmInput);
            };
            passwordInput.addEventListener('input', () => {
                if (!passwordInput.value) {
                    passwordInput.setCustomValidity('');
                } else if (!passwordPattern.test(passwordInput.value)) {
                    passwordInput.setCustomValidity('Use 8 caracteres com letras, números e símbolo.');
                } else {
                    passwordInput.setCustomValidity('');
                }
                updateFieldValidityClass(passwordInput);
                validatePasswordMatch();
            });
            if (passwordConfirmInput) {
                passwordConfirmInput.addEventListener('input', () => {
                    validatePasswordMatch();
                });
            }
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
                proceedToServerSummary();
            });
        }
        const showServerSelectionModal = (triggerButton = null) => {
            if (!serverSelectionModal) {
                return;
            }
            if (triggerButton) {
                triggerButton.disabled = true;
            }
            serverSelectionModal.show();
            if (triggerButton && serverSelectionModalElement) {
                const enableButton = () => {
                    triggerButton.disabled = false;
                    serverSelectionModalElement.removeEventListener('hidden.bs.modal', enableButton);
                };
                serverSelectionModalElement.addEventListener('hidden.bs.modal', enableButton);
            }
        };

        if (seeOtherOptionsButton && serverSelectionModal) {
            seeOtherOptionsButton.addEventListener('click', () => {
                showServerSelectionModal(seeOtherOptionsButton);
            });
        }

        const selectPlanButtons = serverSelectionModalElement
            ? Array.from(serverSelectionModalElement.querySelectorAll('.select-plan-option'))
            : [];

        selectPlanButtons.forEach((button) => {
            const planKey = normalizePlanKey(button.dataset.planId);
            if (planKey) {
                serverOptionsByPlan[planKey] = cloneDataset(button.dataset);
            }
        });

        ensureInitialServerSelection();

        const handlePlanSelectionFromModal = (button) => {
            const dataset = button.dataset ? cloneDataset(button.dataset) : {};
            const planUpdated = setPlanByKey(dataset.planId);
            if (!planUpdated) {
                applyPlanSummary();
            }
            setSelectedServerOptionFromDataset(dataset);
            const shouldNavigateAfterSelection = sendToCartAfterSelection;
            sendToCartAfterSelection = false;
            if (serverSelectionModal) {
                serverSelectionModal.hide();
            }
            setTimeout(() => {
                button.disabled = false;
                if (shouldNavigateAfterSelection) {
                    navigateToCart();
                }
            }, shouldNavigateAfterSelection ? 50 : 200);
        };

        selectPlanButtons.forEach((button) => {
            button.addEventListener('click', () => {
                button.disabled = true;
                handlePlanSelectionFromModal(button);
            });
        });

        if (serverSelectionModalElement) {
            serverSelectionModalElement.addEventListener('hidden.bs.modal', () => {
                sendToCartAfterSelection = false;
            });
        }

        if (goToCartButton) {
            goToCartButton.addEventListener('click', () => {
                goToCartButton.disabled = true;
                navigateToCart();
            });
        }

        if (changeServerButton) {
            changeServerButton.addEventListener('click', () => {
                sendToCartAfterSelection = true;
                const openSelectionModal = () => {
                    showServerSelectionModal();
                };
                if (serverSummaryModalElement && serverSummaryModalElement.classList.contains('show')) {
                    serverSummaryModalElement.addEventListener('hidden.bs.modal', openSelectionModal, { once: true });
                    if (serverSummaryModal) {
                        serverSummaryModal.hide();
                    }
                } else {
                    openSelectionModal();
                }
            });
        }

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
            fetch('termosuso.html', { cache: 'no-cache' })
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
                window.location.href = 'index.html';
            });
        }
    };

    // Namespace global da aplicação
    window.Singularys = window.Singularys || {};
    window.Singularys.initCadastroModal = initCadastroModal;

    // Inicialização automática quando o DOM estiver pronto
    if (document.readyState !== 'loading') {
        initCadastroModal();
    } else {
        document.addEventListener('DOMContentLoaded', initCadastroModal);
    }
})();
