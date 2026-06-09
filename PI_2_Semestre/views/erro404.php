<?php
$titulo = "Página não encontrada - Singularys";
$layoutArea = "app";
require_once "views/partials/head.php";
require_once "views/partials/header.php";
?>
<main class="py-5">
    <div class="container">
        <div class="app-card p-4 text-center">
            <h1 class="h3 app-page-title fw-bold">Bad, bad server! No donuts for you!</h1>
            <p class="text-muted mb-4">A página não foi encontrada.</p>
            <a class="btn btn-singularys" href="index.php">Voltar para a página inicial</a>
        </div>
    </div>
</main>
<?php require_once "views/partials/footer.php"; ?>
