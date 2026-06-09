<?php
$titulo = "Admin - Painel";
$layoutArea = "app";
$adminAtivo = "painel";
require_once "views/partials/head.php";
require_once "views/partials/header.php";
?>
<main class="py-5">
    <div class="container">
        <h1 class="h3 app-page-title fw-bold mb-1">Administração da plataforma</h1>
        <p class="text-muted mb-4">Visão global do banco Singularys (schema v7.7).</p>
        <?php require "views/partials/admin_nav.php"; ?>
        <div class="row g-3">
            <div class="col-md-3"><div class="app-card p-3"><span class="text-muted small">Usuários</span><p class="fs-4 fw-bold mb-0"><?php echo (int) ($resumo["usuarios"] ?? 0); ?></p></div></div>
            <div class="col-md-3"><div class="app-card p-3"><span class="text-muted small">Contas</span><p class="fs-4 fw-bold mb-0"><?php echo (int) ($resumo["contas"] ?? 0); ?></p></div></div>
            <div class="col-md-3"><div class="app-card p-3"><span class="text-muted small">Pedidos</span><p class="fs-4 fw-bold mb-0"><?php echo (int) ($resumo["pedidos"] ?? 0); ?></p></div></div>
            <div class="col-md-3"><div class="app-card p-3"><span class="text-muted small">VMs</span><p class="fs-4 fw-bold mb-0"><?php echo (int) ($resumo["vms"] ?? 0); ?></p></div></div>
        </div>
    </div>
</main>
<?php require_once "views/partials/footer.php"; ?>
