document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('cadastroForm');
            const pfFields = document.getElementById('pfFields');
            const pjFields = document.getElementById('pjFields');
            const tipoPF = document.getElementById('tipoPF');
            const tipoPJ = document.getElementById('tipoPJ');
            const pfInputs = document.querySelectorAll('[data-required-group="pf"]');
            const pjInputs = document.querySelectorAll('[data-required-group="pj"]');
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
            const goDashboardButton = document.getElementById('btnGoDashboard');
            const DASHBOARD_URL = '/dashboard.html';

            const verificationModal = verificationModalElement ? new bootstrap.Modal(verificationModalElement) : null;
            let shouldValidate = false;

            const digitsOnly = (value, maxLength) => value.replace(/\D/g, '').slice(0, maxLength);

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
                if (goDashboardButton) {
                    goDashboardButton.disabled = false;
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
                    if (goDashboardButton) {
                        goDashboardButton.focus();
                    }
                });
            }

            if (goDashboardButton) {
                goDashboardButton.addEventListener('click', () => {
                    window.location.href = DASHBOARD_URL;
                });
            }

            tipoPF.addEventListener('change', togglePessoa);
            tipoPJ.addEventListener('change', togglePessoa);
            togglePessoa();
        });

        document.addEventListener("DOMContentLoaded", function () {

    // Carregar termos externos no box rolável
    fetch("/termosuso.html")
        .then(response => response.text())
        .then(html => {
            const box = document.getElementById("termsBox");
            if (box) {
                box.innerHTML = html;
            }
        })
        .catch(error => {
            console.error("Erro ao carregar termos:", error);
            const box = document.getElementById("termsBox");
            if (box) {
                box.innerHTML = "<p>Não foi possível carregar os Termos de Uso.</p>";
            }
        });

});
