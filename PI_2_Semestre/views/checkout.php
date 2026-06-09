<?php
$titulo = "Carrinho e pagamento - Singularys";
$descricao = "Finalize a contratação do plano no carrinho.";
$layoutArea = "app";
require_once "views/partials/head.php";
require_once "views/partials/header.php";
?>
<main class="py-5">
    <div class="container">
        <div class="mb-4">
            <p class="text-uppercase text-muted small mb-1">Carrinho</p>
            <h1 class="h3 app-page-title fw-bold">Pagamento</h1>
            <p class="text-muted mb-0">
                O plano <strong><?php echo htmlspecialchars($plano->getNome()); ?></strong> está no carrinho.
                Escolha a forma de pagamento e conclua a compra.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="app-card p-4">
                    <h2 class="h5 fw-semibold mb-3">Forma de pagamento</h2>

                    <?php if (!empty($msg["geral"])) { ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($msg["geral"]); ?></div>
                    <?php } ?>

                    <form action="index.php?controle=pedidoController&metodo=checkout" method="post">
                        <input type="hidden" name="codigo_plano" value="<?php echo htmlspecialchars($plano->getCodigo()); ?>">

                        <fieldset class="mb-4">
                            <legend class="fs-6 fw-semibold mb-3">Endereço de faturamento</legend>
                            <?php if (count($enderecos) === 0) { ?>
                                <div class="alert alert-warning">
                                    Cadastre um endereço na sua conta antes de pagar.
                                    <a href="index.php?controle=contaController&metodo=adicionarEndereco">Adicionar endereço</a>
                                </div>
                            <?php } else { ?>
                                <?php foreach ($enderecos as $idx => $end) { ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="endereco_id"
                                               id="end_<?php echo (int) $end->id_endereco; ?>"
                                               value="<?php echo (int) $end->id_endereco; ?>"
                                               <?php echo $idx === 0 ? "checked" : ""; ?> required>
                                        <label class="form-check-label" for="end_<?php echo (int) $end->id_endereco; ?>">
                                            <?php echo htmlspecialchars($end->logradouro . ", " . $end->numero); ?>
                                            — <?php echo htmlspecialchars($end->bairro . " · " . $end->cidade . "/" . $end->estado); ?>
                                            <span class="text-muted">CEP <?php echo htmlspecialchars($end->cep); ?></span>
                                        </label>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </fieldset>

                        <fieldset class="mb-4">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="metodo_pagamento" id="pix" value="pix">
                                <label class="form-check-label" for="pix">PIX</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="metodo_pagamento" id="cartao" value="cartao">
                                <label class="form-check-label" for="cartao">Cartão de crédito</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metodo_pagamento" id="simulado" value="simulado" checked>
                                <label class="form-check-label" for="simulado">
                                    <strong>Simulado</strong>
                                    <span class="text-muted small d-block">Confirma o pagamento imediatamente (ambiente de testes).</span>
                                </label>
                            </div>
                        </fieldset>

                        <div class="alert alert-info small">
                            PIX e cartão serão integrados ao gateway em versões futuras.
                            Neste sprint, use <strong>Simulado</strong> para finalizar a contratação.
                        </div>

                        <button class="btn btn-singularys btn-lg w-100" type="submit"
                            <?php echo count($enderecos) === 0 ? "disabled" : ""; ?>>Concluir compra</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="app-card-dark p-4">
                    <p class="text-uppercase small opacity-75 mb-2">Resumo do carrinho</p>
                    <h2 class="h4 fw-bold"><?php echo htmlspecialchars($plano->getNome()); ?></h2>
                    <ul class="list-unstyled mb-4">
                        <li><?php echo (int) $plano->getVcpu(); ?> vCPU</li>
                        <li><?php echo (int) $plano->getRamGb(); ?> GB RAM</li>
                        <li><?php echo (int) $plano->getArmazenamentoGb(); ?> GB SSD</li>
                        <li><?php echo (int) $plano->getLarguraBandaMbps(); ?> Mbps</li>
                    </ul>
                    <hr class="border-light opacity-25">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Total hoje</span>
                        <strong class="fs-5"><?php echo htmlspecialchars($plano->getPrecoFormatado()); ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once "views/partials/footer.php"; ?>
