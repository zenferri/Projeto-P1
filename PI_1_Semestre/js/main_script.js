// Espera o HTML carregar antes de rodar o script
document.addEventListener("DOMContentLoaded", function () {
    // Caminho da página para onde o usuário será levado após fazer login
    var LOGIN_TARGET = "dashboard.html";

    // Seleciona todos os formulários com a classe .login-form
    var loginForms = document.querySelectorAll(".login-form");

    // Para cada formulário de login, adiciona o comportamento de redirecionar
    loginForms.forEach(function (form) {
        form.addEventListener("submit", function (event) {
            // Impede o envio normal do formulário (não vai pro servidor - a página é mero modelo sem back-end)
            event.preventDefault();

            // Redireciona o usuário para a página da dashboard
            window.location.href = LOGIN_TARGET;
        });
    });

      // Aqui começam as funções relacionadas ao modal "esqueci senha"

    // Esta função garante que o modal de "esqueci a senha" exista.
    // Se ele não existir, cria o HTML e adiciona no body.
    function ensureForgotModal() {
        // Verifica se o modal já existe no DOM (obj é escalar o front e por isso a verificação)
        var modalElement = document.getElementById("forgotPasswordModal");
        if (modalElement) {
            return modalElement; // já existe, só devolve
        }

        // Cria um elemento temporário para montar o HTML do modal
        var wrapper = document.createElement("div");
        wrapper.innerHTML = (
            '<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">' +
                '<div class="modal-dialog modal-dialog-centered">' +
                    '<div class="modal-content">' +
                        '<div class="modal-header">' +
                            '<h5 class="modal-title" id="forgotPasswordModalLabel">Recuperar senha</h5>' +
                            '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>' +
                        '</div>' +
                        '<div class="modal-body">' +
                            '<form id="forgotPasswordForm" novalidate>' +
                                '<div class="mb-3">' +
                                    '<label class="form-label" for="forgotEmail">E-mail cadastrado*</label>' +
                                    '<input type="email" class="form-control" id="forgotEmail" required placeholder="nome@exemplo.com">' +
                                    '<div class="invalid-feedback">Informe um e-mail válido.</div>' +
                                '</div>' +
                                '<div class="alert alert-info d-none" id="forgotFeedback" role="alert"></div>' +
                                '<button type="submit" class="btn btn-primary w-100" id="forgotSubmitBtn">Enviar instruções</button>' +
                            '</form>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );

        // Adiciona o modal montado dentro do body
        document.body.appendChild(wrapper.firstElementChild);

        // Busca novamente o modal já inserido
        modalElement = document.getElementById("forgotPasswordModal");
        return modalElement;
    }

    // Esta função abre o modal, configura seus eventos e cuida do fluxo
    function showForgotModal() {
        var modalElement = ensureForgotModal();

        // Cria (ou recupera) a instância do modal do Bootstrap
        var modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);

        // Busca os elementos internos do modal
        var emailInput = modalElement.querySelector("#forgotEmail");
        var feedback = modalElement.querySelector("#forgotFeedback");
        var submitBtn = modalElement.querySelector("#forgotSubmitBtn");
        var form = modalElement.querySelector("#forgotPasswordForm");

        // Função para limpar o estado visual do modal
        function resetState() {
            if (form) {
                form.reset();
            }
            if (feedback) {
                feedback.classList.add("d-none");
                feedback.textContent = "";
            }
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = "Enviar instruções";
            }
            if (emailInput) {
                emailInput.classList.remove("is-invalid");
            }
        }

        // Só adiciona os eventos do formulário uma única vez
        if (form && !form.dataset.initialized) {
            form.dataset.initialized = "true";

            // Quando o usuário enviar o formulário de recuperação de senha
            form.addEventListener("submit", function (event) {
                event.preventDefault();

                if (!emailInput) {
                    return;
                }

                // Usa a validação nativa do HTML5 para checar o e-mail
                if (!emailInput.checkValidity()) {
                    emailInput.classList.add("is-invalid");
                    emailInput.reportValidity();
                    return;
                }

                // E-mail válido: remove estado de erro
                emailInput.classList.remove("is-invalid");

                // Desativa o botão e mostra mensagem de "enviando"
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = "Enviando...";
                }

                // Mostra uma mensagem de feedback simulando envio
                if (feedback) {
                    feedback.classList.remove("d-none");
                    feedback.textContent =
                        "Se " + emailInput.value + " estiver cadastrado, você receberá um link de recuperação em instantes.";
                }

                // Simula um atraso de processamento (como se fosse o servidor)
                setTimeout(function () {
                    modalInstance.hide(); // fecha o modal
                    resetState();         // limpa para o próximo uso
                }, 3800);
            });

            // Quando o modal for fechado (por qualquer forma), reseta o estado
            modalElement.addEventListener("hidden.bs.modal", resetState);
        }

        // Abre o modal na tela
        modalInstance.show();

        // Coloca o foco no campo de e-mail para facilitar a digitação
        if (emailInput) {
            emailInput.focus();
        }
    }

    // Captura cliques no documento inteiro para tratar o link "Esqueceu sua senha?"
    document.body.addEventListener("click", function (event) {
        // Procura o elemento com a classe .forgot-password-link
        var trigger = event.target.closest(".forgot-password-link");

        if (trigger) {
            event.preventDefault();
            showForgotModal();
        }
    });
});