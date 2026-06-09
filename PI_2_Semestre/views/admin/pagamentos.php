<?php
$titulo = "Admin - Pagamentos";
$layoutArea = "app";
$adminAtivo = "pagamentos";
require_once "views/partials/head.php";
require_once "views/partials/header.php";
?>
<main class="py-5">
    <div class="container">
        <h1 class="h3 app-page-title fw-bold mb-4">Pagamentos</h1>
        <?php require "views/partials/admin_nav.php"; ?>
        <?php if (!empty($msg)) { ?><div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div><?php } ?>
        <div class="app-card p-4 table-responsive">
            <table class="table table-hover table-app align-middle mb-0">
                <thead><tr><th>#</th><th>Pedido</th><th>Valor</th><th>Forma</th><th>Endereço</th><th>Situação</th><th>Ação</th></tr></thead>
                <tbody>
                <?php foreach ($pagamentos as $pg) { ?>
                    <tr>
                        <td><?php echo (int) $pg->id_pagamento; ?></td>
                        <td><?php echo (int) $pg->pedido_id; ?></td>
                        <td>R$ <?php echo number_format((float) $pg->valor, 2, ",", "."); ?></td>
                        <td><?php echo htmlspecialchars($pg->forma_pagamento); ?></td>
                        <td><?php echo htmlspecialchars($pg->logradouro . ", " . $pg->numero . " — " . $pg->cidade); ?></td>
                        <td><?php echo htmlspecialchars($pg->situacao); ?></td>
                        <td>
                            <form method="post" action="index.php?controle=adminController&metodo=salvarPagamento" class="d-flex gap-1">
                                <input type="hidden" name="pagamento_id" value="<?php echo (int) $pg->id_pagamento; ?>">
                                <select name="situacao" class="form-select form-select-sm">
                                    <?php foreach ($situacoesPagamento as $sit) { ?>
                                        <option value="<?php echo $sit; ?>" <?php echo $pg->situacao === $sit ? "selected" : ""; ?>>
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
