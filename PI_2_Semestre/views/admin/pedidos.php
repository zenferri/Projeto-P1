<?php
$titulo = "Admin - Pedidos";
$layoutArea = "app";
$adminAtivo = "pedidos";
require_once "views/partials/head.php";
require_once "views/partials/header.php";
?>
<main class="py-5">
    <div class="container">
        <h1 class="h3 app-page-title fw-bold mb-4">Pedidos</h1>
        <?php require "views/partials/admin_nav.php"; ?>
        <?php if (!empty($msg)) { ?><div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div><?php } ?>
        <div class="app-card p-4 table-responsive">
            <table class="table table-hover table-app align-middle mb-0">
                <thead><tr><th>#</th><th>Conta</th><th>Plano</th><th>Valor</th><th>Situação</th><th>Data</th><th>Ação</th></tr></thead>
                <tbody>
                <?php foreach ($pedidos as $p) { ?>
                    <tr>
                        <td><?php echo (int) $p->id_pedido; ?></td>
                        <td><?php echo htmlspecialchars($p->conta_nome); ?></td>
                        <td><?php echo htmlspecialchars($p->plano_nome); ?></td>
                        <td>R$ <?php echo number_format((float) $p->valor_total, 2, ",", "."); ?></td>
                        <td><?php echo htmlspecialchars($p->situacao); ?></td>
                        <td><?php echo htmlspecialchars($p->criado_em); ?></td>
                        <td>
                            <form method="post" action="index.php?controle=adminController&metodo=salvarPedido" class="d-flex gap-1">
                                <input type="hidden" name="pedido_id" value="<?php echo (int) $p->id_pedido; ?>">
                                <select name="situacao" class="form-select form-select-sm">
                                    <?php foreach ($situacoesPedido as $sit) { ?>
                                        <option value="<?php echo $sit; ?>" <?php echo $p->situacao === $sit ? "selected" : ""; ?>>
                                            <?php echo $sit; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-primary">Salvar</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php require_once "views/partials/footer.php"; ?>
