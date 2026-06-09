<?php
$titulo = "Cadastro Pessoa Física - Singularys";
$descricao = "Cadastro de titular pessoa física Singularys.";
$layoutArea = "app";
$colFormulario = $exibirPlanoContratacao ? "col-lg-8" : "col-12";
$urlTrocarTipo = "index.php?controle=clienteController&metodo=cadastrar";
if ($exibirPlanoContratacao && $planoSelecionado) {
    $urlTrocarTipo .= "&plano=" . urlencode($planoSelecionado->getCodigo());
}
require_once "views/partials/head.php";
require_once "views/partials/header.php";
?>
<main class="py-5">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <?php if ($exibirPlanoContratacao && $planoSelecionado) { ?>
                <div class="col-lg-4">
                    <?php require "views/partials/cadastro/plano_resumo.php"; ?>
                </div>
            <?php } ?>
            <div class="<?php echo $colFormulario; ?>">
                <div class="app-card p-4 p-md-5">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
                        <div>
                            <p class="text-uppercase text-muted small mb-1">Pessoa Física</p>
                            <h1 class="h3 app-page-title fw-bold mb-0">Dados do titular</h1>
                        </div>
                        <a class="btn btn-outline-secondary btn-sm" href="<?php echo htmlspecialchars($urlTrocarTipo); ?>">
                            Trocar tipo
                        </a>
                    </div>

                    <?php if (!empty($msg["geral"])) { ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($msg["geral"]); ?></div>
                    <?php } ?>

                    <form action="index.php?controle=clienteController&metodo=cadastrar" method="post" novalidate>
                        <input type="hidden" name="tipo_cliente" value="PF">
                        <?php if ($exibirPlanoContratacao && $planoSelecionado) { ?>
                            <input type="hidden" name="codigo_plano"
                                   value="<?php echo htmlspecialchars($planoSelecionado->getCodigo()); ?>">
                        <?php } ?>

                        <fieldset class="border rounded-3 p-3 mb-4">
                            <legend class="float-none w-auto px-2 fs-6 fw-semibold mb-0">Identificação</legend>
                            <div class="row g-3 mt-1">
                                <div class="col-md-6">
                                    <label class="form-label" for="nome">Nome</label>
                                    <input class="form-control" id="nome" name="nome" required
                                           value="<?php echo htmlspecialchars($dados["nome"] ?? ""); ?>">
                                    <small class="text-danger mvc-error"><?php echo htmlspecialchars($msg["nome"] ?? ""); ?></small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="sobrenome">Sobrenome</label>
                                    <input class="form-control" id="sobrenome" name="sobrenome" required
                                           value="<?php echo htmlspecialchars($dados["sobrenome"] ?? ""); ?>">
                                    <small class="text-danger mvc-error"><?php echo htmlspecialchars($msg["sobrenome"] ?? ""); ?></small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="cpf">CPF</label>
                                    <input class="form-control" id="cpf" name="cpf" required
                                           value="<?php echo htmlspecialchars($dados["cpf"] ?? ""); ?>">
                                    <small class="text-danger mvc-error"><?php echo htmlspecialchars($msg["cpf"] ?? ""); ?></small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="data_nascimento">Data de nascimento</label>
                                    <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required
                                           value="<?php echo htmlspecialchars($dados["data_nascimento"] ?? ""); ?>">
                                    <small class="text-danger mvc-error"><?php echo htmlspecialchars($msg["data_nascimento"] ?? ""); ?></small>
                                </div>
                            </div>
                        </fieldset>

                        <?php require "views/partials/cadastro/telefone.php"; ?>
                        <?php require "views/partials/cadastro/endereco.php"; ?>
                        <?php require "views/partials/cadastro/acesso.php"; ?>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="aceite_termos" value="1" id="aceite_termos">
                            <label class="form-check-label" for="aceite_termos">Li e aceito os <a href="./termos.html" target="_blank">termos de uso</a>.</label>
                            <small class="text-danger mvc-error d-block"><?php echo htmlspecialchars($msg["aceite_termos"] ?? ""); ?></small>
                        </div>

                        <button class="btn btn-singularys btn-lg w-100" type="submit">
                            <?php echo $exibirPlanoContratacao ? "Cadastrar e continuar" : "Concluir cadastro"; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once "views/partials/footer.php"; ?>
