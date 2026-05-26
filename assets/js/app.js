document.addEventListener('DOMContentLoaded', function () {
    const tipoPF = document.getElementById('tipoPF');
    const tipoPJ = document.getElementById('tipoPJ');
    const cpfField = document.getElementById('cpfField');
    const cnpjField = document.getElementById('cnpjField');
    const copyPixBtn = document.getElementById('copyPixBtn');
    const pixTab = document.getElementById('pixTab');
    const cardTab = document.getElementById('cardTab');
    const paymentMethodInput = document.getElementById('paymentMethod');
    const cardNumber = document.getElementById('cardNumber');
    const cardExpiry = document.getElementById('cardExpiry');
    const cardCvv = document.getElementById('cardCvv');
    const checkoutForm = document.getElementById('checkoutForm');
    const signupForm = document.getElementById('signupForm');
    const loginForm = document.getElementById('loginForm');

    function toggleCadastroType() {
        if (tipoPJ && tipoPJ.checked) {
            cpfField.classList.add('d-none');
            cnpjField.classList.remove('d-none');
        } else {
            cpfField.classList.remove('d-none');
            cnpjField.classList.add('d-none');
        }
    }

    if (tipoPF && tipoPJ && cpfField && cnpjField) {
        tipoPF.addEventListener('change', toggleCadastroType);
        tipoPJ.addEventListener('change', toggleCadastroType);
        toggleCadastroType();
    }

    if (copyPixBtn) {
        copyPixBtn.addEventListener('click', function () {
            const pixCode = document.getElementById('pixCode');
            if (!pixCode) return;
            navigator.clipboard.writeText(pixCode.value).then(() => {
                const original = this.innerHTML;
                this.innerHTML = '✓ Copiado!';
                this.classList.add('btn-success');
                setTimeout(() => {
                    this.innerHTML = original;
                    this.classList.remove('btn-success');
                }, 1800);
            });
        });
    }

    if (pixTab && paymentMethodInput) {
        pixTab.addEventListener('click', function () {
            paymentMethodInput.value = 'pix';
        });
    }

    if (cardTab && paymentMethodInput) {
        cardTab.addEventListener('click', function () {
            paymentMethodInput.value = 'card';
        });
    }

    if (cardNumber) {
        cardNumber.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').replace(/(.{4})/g, '$1 ').trim();
        });
    }

    if (cardExpiry) {
        cardExpiry.addEventListener('input', function () {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 2) {
                value = value.slice(0, 2) + '/' + value.slice(2, 4);
            }
            this.value = value;
        });
    }

    if (cardCvv) {
        cardCvv.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 3);
        });
    }

    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function (event) {
            if (!checkoutForm.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            checkoutForm.classList.add('was-validated');
        });
    }

    if (signupForm) {
        signupForm.addEventListener('submit', function (event) {
            if (!signupForm.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            signupForm.classList.add('was-validated');
        });
    }

    if (loginForm) {
        loginForm.addEventListener('submit', function (event) {
            if (!loginForm.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            loginForm.classList.add('was-validated');
        });
    }
});
