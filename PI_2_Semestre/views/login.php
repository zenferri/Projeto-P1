<?php
$titulo = "Login - Singularys";
$descricao = "Acesso de clientes, operadores e administradores Singularys.";
$layoutArea = "app";
require_once "models/carrinhoSessao.class.php";
$planoPendente = CarrinhoSessao::obterSlug();
if ($planoPendente) {
    $urlCadastro = "index.php?controle=clienteController&metodo=cadastrar&plano=" . urlencode($planoPendente);
} else {
    $urlCadastro = "index.php?controle=clienteController&metodo=cadastrar&intencao=cadastro";
}
require_once "views/partials/head.php";
require_once "views/partials/header.php";
?>
<main class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="app-card p-4 p-md-5">
                    <h1 class="h3 app-page-title fw-bold mb-2">Entrar no console</h1>
                    <?php if ($planoPendente) { ?>
                        <div class="alert alert-info py-2 small">
                            Faça login para continuar a contratação do plano selecionado.
                        </div>
                    <?php } else { ?>
                        <p class="text-muted mb-4">Use e-mail e senha. Informe o ID da conta apenas para acessar outra conta autorizada.</p>
                    <?php } ?>

                    <?php if (!empty($msg["geral"])) { ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($msg["geral"]); ?></div>
                    <?php } ?>

                    <form action="index.php?controle=usuarioController&metodo=login" method="post" novalidate>
                        <div class="mb-3">
                            <label for="conta_id" class="form-label">ID da conta <span class="text-muted">(opcional)</span></label>
                            <input type="number" min="1" id="conta_id" name="conta_id" class="form-control"
                                   placeholder="Deixe vazio para sua conta principal"
                                   value="<?php echo htmlspecialchars($contaIdForm ?? ""); ?>">
                            <small class="text-danger mvc-error"><?php echo htmlspecialchars($msg["conta_id"] ?? ""); ?></small>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" id="email" name="email" class="form-control" required
                                   value="<?php echo htmlspecialchars($email ?? ""); ?>">
                            <small class="text-danger mvc-error"><?php echo htmlspecialchars($msg["email"] ?? ""); ?></small>
                        </div>
                        <div class="mb-4">
                            <label for="senha" class="form-label">Senha</label>
                            <input type="password" id="senha" name="senha" class="form-control" required>
                            <small class="text-danger mvc-error"><?php echo htmlspecialchars($msg["senha"] ?? ""); ?></small>
                        </div>
                        <button class="btn btn-singularys w-100 btn-lg" type="submit">Entrar</button>
                    </form>
                    <p class="mt-4 mb-0 text-center text-muted">
                        Ainda não tem conta?
                        <a href="<?php echo htmlspecialchars($urlCadastro); ?>">Cadastre-se</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once "views/partials/footer.php"; ?>
