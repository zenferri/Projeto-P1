<?php require __DIR__ . '/layout/header.php'; ?>

<section class="section bg-light-soft">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h1 class="h3 fw-bold">Entrar no painel Singularys</h1>
                            <p class="text-muted mb-0">Use seu e-mail e senha para acessar sua área de cliente.</p>
                        </div>
                        <form method="POST" action="<?= BASE_URL ?>/login" id="loginForm" novalidate>
                            <div class="mb-3">
                                <label for="loginEmail" class="form-label">E-mail</label>
                                <input type="email" class="form-control" id="loginEmail" name="email" required>
                            </div>
                            <div class="mb-4">
                                <label for="loginPassword" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="loginPassword" name="senha" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Entrar</button>
                        </form>
                        <div class="text-center mt-4 small text-muted">
                            Ainda não possui conta? <a href="<?= BASE_URL ?>/cadastro" class="text-primary">Cadastre-se agora</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/layout/footer.php'; ?>
