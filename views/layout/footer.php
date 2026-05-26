    </main>

    <footer class="bg-light border-top py-5 mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <h6 class="fw-bold text-primary">Singularys</h6>
                    <p class="text-muted small">Portal de Deploy de Máquinas Virtuais com provisionamento automático.</p>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold">Links</h6>
                    <ul class="list-unstyled small">
                        <li><a href="<?= BASE_URL ?: '/' ?>" class="text-decoration-none text-muted">Home</a></li>
                        <li><a href="<?= BASE_URL ?>/equipe" class="text-decoration-none text-muted">Equipe</a></li>
                        <li><a href="<?= BASE_URL ?>/termos" class="text-decoration-none text-muted">Termos</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold">Contato</h6>
                    <p class="text-muted small">
                        <strong>Email:</strong> suporte@singularys.net<br>
                        <strong>Suporte:</strong> 24/7
                    </p>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center text-muted small">
                <p>&copy; <?= date('Y') ?> Singularys. Todos os direitos reservados. | Projeto P1 - FATEC Jahu</p>
            </div>
        </div>
    </footer>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</html>
