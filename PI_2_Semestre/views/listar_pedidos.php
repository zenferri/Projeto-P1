<?php
$titulo = "Pedidos - Singularys";
$descricao = "Pedidos comerciais Singularys.";
$layoutArea = "app";
require_once "views/partials/head.php";
require_once "views/partials/header.php";
?>
<main class="py-5">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <div>
                <h1 class="h3 app-page-title fw-bold mb-1">Pedidos</h1>
                <p class="text-muted mb-0">Histórico comercial da sua conta.</p>
            </div>
            <a class="btn btn-outline-secondary btn-sm" href="index.php?controle=dashboardController&metodo=cliente">Voltar à dashboard</a>
        </div>

        <div class="app-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover table-app align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Plano</th>
                            <th>Situação</th>
                            <th>Valor</th>
                            <th>Criado em</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($pedidos) === 0) { ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Nenhum pedido encontrado.</td>
                            </tr>
                        <?php } ?>
                        <?php foreach ($pedidos as $pedido) { ?>
                            <tr>
                                <td><?php echo (int) $pedido->id_pedido; ?></td>
                                <td><?php echo htmlspecialchars($pedido->plano_nome); ?></td>
                                <td><span class="badge text-bg-light text-dark border badge-situacao"><?php echo htmlspecialchars($pedido->situacao); ?></span></td>
                                <td>R$ <?php echo number_format((float) $pedido->valor_total, 2, ",", "."); ?></td>
                                <td><?php echo htmlspecialchars($pedido->criado_em); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php require_once "views/partials/footer.php"; ?>
