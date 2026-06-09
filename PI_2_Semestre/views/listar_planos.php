<?php
$titulo = "Produtos - Singularys";
$descricao = "Catálogo de planos VPS Singularys.";
$layoutArea = "app";
require_once "views/partials/head.php";
require_once "views/partials/header.php";
?>
<main class="py-5">
    <div class="container">
        <h1 class="h3 app-page-title fw-bold mb-2">Catálogo de planos</h1>
        <p class="text-muted mb-4">Escolha um plano para iniciar o cadastro e a contratação.</p>

        <div class="row g-4">
            <?php foreach ($planos as $plano) { ?>
                <div class="col-md-4">
                    <div class="app-card p-4 h-100 d-flex flex-column">
                        <h2 class="h5 fw-semibold app-page-title"><?php echo htmlspecialchars($plano->getNome()); ?></h2>
                        <p class="fs-4 fw-bold text-primary mb-3"><?php echo htmlspecialchars($plano->getPrecoFormatado()); ?><small class="fs-6 text-muted fw-normal">/mês</small></p>
                        <ul class="list-unstyled text-muted small flex-grow-1 mb-4">
                            <li class="mb-1"><?php echo (int) $plano->getVcpu(); ?> vCPU</li>
                            <li class="mb-1"><?php echo (int) $plano->getRamGb(); ?> GB RAM</li>
                            <li class="mb-1"><?php echo (int) $plano->getArmazenamentoGb(); ?> GB SSD</li>
                            <li><?php echo (int) $plano->getLarguraBandaMbps(); ?> Mbps</li>
                        </ul>
                        <a class="btn btn-singularys w-100"
                           href="index.php?controle=planoController&metodo=contratar&plano=<?php echo urlencode($plano->getCodigo()); ?>">
                            Contratar
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</main>
<?php require_once "views/partials/footer.php"; ?>
