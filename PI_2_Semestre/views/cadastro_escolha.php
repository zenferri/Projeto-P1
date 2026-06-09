<?php
$titulo = "Cadastro - Singularys";
$descricao = "Crie sua conta Singularys.";
$layoutArea = "app";
$colFormulario = $exibirPlanoContratacao ? "col-lg-8" : "col-12";
$sufixoPlano = $exibirPlanoContratacao && $planoSelecionado
    ? "&plano=" . urlencode($planoSelecionado->getCodigo())
    : "";
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
                    <h1 class="h3 app-page-title fw-bold mb-2">Criar conta</h1>
                    <p class="text-muted mb-4">
                        <?php if ($exibirPlanoContratacao) { ?>
                            Escolha o tipo de titular para continuar a contratação do plano selecionado.
                        <?php } else { ?>
                            Escolha se o cadastro será de pessoa física ou jurídica.
                        <?php } ?>
                    </p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <a class="text-decoration-none text-body"
                               href="index.php?controle=clienteController&metodo=cadastrar&tipo=PF<?php echo $sufixoPlano; ?>">
                                <div class="app-card app-choice-card p-4 text-center h-100">
                                    <div class="display-6 text-primary mb-2">PF</div>
                                    <h2 class="h5 fw-semibold">Pessoa Física</h2>
                                    <p class="text-muted small mb-0">CPF, nome completo e data de nascimento.</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a class="text-decoration-none text-body"
                               href="index.php?controle=clienteController&metodo=cadastrar&tipo=PJ<?php echo $sufixoPlano; ?>">
                                <div class="app-card app-choice-card p-4 text-center h-100">
                                    <div class="display-6 text-primary mb-2">PJ</div>
                                    <h2 class="h5 fw-semibold">Pessoa Jurídica</h2>
                                    <p class="text-muted small mb-0">CNPJ, razão social e nome fantasia.</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <p class="mt-4 mb-0 text-center text-muted small">
                        Já possui conta?
                        <a href="index.php?controle=usuarioController&metodo=login">Faça login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once "views/partials/footer.php"; ?>
