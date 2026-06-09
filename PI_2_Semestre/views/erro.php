<?php
$titulo = "Erro - Singularys";
$layoutArea = "app";
require_once "views/partials/head.php";
require_once "views/partials/header.php";
?>
<main class="py-5">
    <div class="container">
        <div class="app-card p-4">
            <div class="alert alert-danger mb-0">
                <h1 class="h5 fw-semibold">Erro</h1>
                <p class="mb-0"><?php echo htmlspecialchars($mensagemErro ?? "Erro interno do servidor."); ?></p>
            </div>
        </div>
    </div>
</main>
<?php require_once "views/partials/footer.php"; ?>
