<?php
$titulo = "Admin - Planos";
$layoutArea = "app";
$adminAtivo = "planos";
require_once "views/partials/head.php";
require_once "views/partials/header.php";
?>
<main class="py-5">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <h1 class="h3 app-page-title fw-bold mb-0">Planos VPS</h1>
            <a class="btn btn-singularys btn-sm" href="index.php?controle=adminController&metodo=planoForm">Novo plano</a>
        </div>
        <?php require "views/partials/admin_nav.php"; ?>
        <?php if (!empty($msg)) { ?><div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div><?php } ?>
        <div class="app-card p-4 table-responsive">
            <table class="table table-hover table-app align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th><th>Nome</th><th>Slug</th><th>Preço</th><th>vCPU</th><th>RAM</th><th>Situação</th><th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($planos as $pl) { ?>
                    <tr class="<?php echo $pl->deletado_em ? "table-secondary" : ""; ?>">
                        <td><?php echo (int) $pl->id_plano; ?></td>
                        <td><?php echo htmlspecialchars($pl->nome); ?></td>
                        <td><?php echo htmlspecialchars($pl->slug); ?></td>
                        <td>R$ <?php echo number_format((float) $pl->preco_mensal, 2, ",", "."); ?></td>
                        <td><?php echo (int) $pl->vcpu; ?></td>
                        <td><?php echo (int) round($pl->ram_mb / 1024); ?> GB</td>
                        <td>
                            <?php echo htmlspecialchars($pl->situacao); ?>
                            <?php if ($pl->deletado_em) { ?><br><small class="text-muted">soft delete</small><?php } ?>
                        </td>
                        <td class="text-nowrap">
                            <a class="btn btn-sm btn-outline-primary"
                               href="index.php?controle=adminController&metodo=planoForm&id=<?php echo (int) $pl->id_plano; ?>">Editar</a>
                            <?php if (!$pl->deletado_em) { ?>
                                <form method="post" action="index.php?controle=adminController&metodo=excluirPlano" class="d-inline"
                                      onsubmit="return confirm('Desativar este plano (soft delete)?');">
                                    <input type="hidden" name="id_plano" value="<?php echo (int) $pl->id_plano; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Desativar</button>
                                </form>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php require_once "views/partials/footer.php"; ?>
