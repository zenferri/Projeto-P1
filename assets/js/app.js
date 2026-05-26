document.addEventListener('DOMContentLoaded', function () {
    const tipoPF = document.getElementById('tipoPF');
    const tipoPJ = document.getElementById('tipoPJ');
    const pjField = document.getElementById('pjField');
    const cpfField = document.getElementById('cpfField');
    const cnpjField = document.getElementById('cnpjField');
    const copyPixBtn = document.getElementById('copyPixBtn');
    const pixPanel = document.getElementById('pixPanel');
    const cardPanel = document.getElementById('cardPanel');

    function toggleCadastroType() {
        if (tipoPJ && tipoPJ.checked) {
            pjField.style.display = 'block';
            cpfField.style.display = 'none';
            if (cnpjField) {
                cnpjField.style.display = 'block';
            }
        } else {
            pjField.style.display = 'none';
            cpfField.style.display = 'block';
            if (cnpjField) {
                cnpjField.style.display = 'none';
            }
        }
    }

    if (tipoPF && tipoPJ && pjField && cpfField) {
        tipoPF.addEventListener('change', toggleCadastroType);
        tipoPJ.addEventListener('change', toggleCadastroType);
        toggleCadastroType();
    }

    const paymentRadios = document.querySelectorAll('input[name="paymentMethod"]');
    if (paymentRadios.length > 0 && pixPanel && cardPanel) {
        paymentRadios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                pixPanel.classList.toggle('hidden', this.value !== 'pix');
                cardPanel.classList.toggle('hidden', this.value !== 'card');
            });
        });
    }

    if (copyPixBtn) {
        copyPixBtn.addEventListener('click', function () {
            const pixText = 'suporte@singularys.net';
            navigator.clipboard.writeText(pixText).then(function () {
                copyPixBtn.textContent = 'PIX copiado!';
                setTimeout(function () {
                    copyPixBtn.textContent = 'Copiar PIX';
                }, 1800);
            });
        });
    }
});
