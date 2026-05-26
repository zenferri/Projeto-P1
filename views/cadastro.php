<?php require __DIR__ . '/layout/header.php'; ?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-5">
                <div class="mb-4">
                    <h5 class="text-primary fw-bold mb-2">SEU PLANO</h5>
                    <?php if (!empty($selectedPlan)): ?>
                        <div class="card border-0 bg-white shadow-sm">
                            <div class="card-body">
                                <h4 class="card-title fw-bold mb-1"><?= htmlspecialchars($selectedPlan['title']) ?></h4>
                                <p class="text-muted small mb-3">
                                    <?= htmlspecialchars($selectedPlan['cpu']) ?> • 
                                    <?= htmlspecialchars($selectedPlan['ram']) ?> • 
                                    <?= htmlspecialchars($selectedPlan['storage']) ?>
                                </p>
                                <p class="fs-4 text-primary fw-bold">
                                    R$ <?= number_format($selectedPlan['price'], 2, ',', '.') ?>/mês
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card border-0 bg-primary text-white shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title fw-bold mb-3">Linha do tempo</h6>
                        <ol class="ps-3 mb-0">
                            <li class="mb-2 small">Preencha seus dados pessoais</li>
                            <li class="mb-2 small">Confirme o código do e-mail</li>
                            <li class="mb-2 small">Escolha o servidor</li>
                            <li class="small">Finalize o pagamento</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="card-title fw-bold mb-2">Abra sua conta Singularys</h3>
                        <p class="text-muted mb-4">Preencha os dados para continuar com o cadastro</p>

                        <form method="post" action="<?= BASE_URL ?>/register" id="signupForm" novalidate>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tipo de cadastro</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="tipoCadastro" id="tipoPF" value="pf" checked>
                                    <label class="btn btn-outline-primary" for="tipoPF">Pessoa Física</label>
                                    
                                    <input type="radio" class="btn-check" name="tipoCadastro" id="tipoPJ" value="pj">
                                    <label class="btn btn-outline-primary" for="tipoPJ">Pessoa Jurídica</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="inputNome" class="form-label">Nome completo</label>
                                <input type="text" class="form-control" id="inputNome" name="nome" required>
                            </div>

                            <div class="mb-3" id="cpfField">
                                <label for="inputCpf" class="form-label">CPF</label>
                                <input type="text" class="form-control" id="inputCpf" name="cpf" placeholder="000.000.000-00">
                            </div>

                            <div class="mb-3 d-none" id="cnpjField">
                                <label for="inputCnpj" class="form-label">CNPJ</label>
                                <input type="text" class="form-control" id="inputCnpj" name="cnpj" placeholder="00.000.000/0000-00">
                            </div>

                            <div class="mb-3">
                                <label for="inputEmail" class="form-label">E-mail corporativo</label>
                                <input type="email" class="form-control" id="inputEmail" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="inputPhone" class="form-label">WhatsApp</label>
                                <input type="tel" class="form-control" id="inputPhone" name="telefone" placeholder="(11) 90000-0000" required>
                            </div>

                            <div class="mb-3">
                                <label for="inputNascimento" class="form-label">Data de nascimento</label>
                                <input type="date" class="form-control" id="inputNascimento" name="dataNascimento">
                            </div>

                            <div class="mb-3">
                                <label for="inputEndereco" class="form-label">Endereço</label>
                                <input type="text" class="form-control" id="inputEndereco" name="endereco">
                            </div>

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="inputNumero" class="form-label">Número</label>
                                    <input type="text" class="form-control" id="inputNumero" name="numero">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="inputComplemento" class="form-label">Compl.</label>
                                    <input type="text" class="form-control" id="inputComplemento" name="complemento">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="inputPassword" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="inputPassword" name="senha" minlength="8" required>
                                <small class="text-muted">Mínimo 8 caracteres</small>
                            </div>

                            <div class="mb-4">
                                <label for="inputPasswordConfirm" class="form-label">Confirmar senha</label>
                                <input type="password" class="form-control" id="inputPasswordConfirm" name="senhaConfirmacao" minlength="8" required>
                            </div>

                            <button class="btn btn-primary btn-lg w-100" type="submit">Criar conta e continuar</button>
                            <p class="text-center text-muted small mt-3">
                                Já tem conta? <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" class="text-primary">Faça login</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/layout/footer.php'; ?>
