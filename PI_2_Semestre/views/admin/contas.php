<?php
$titulo = "Admin - Contas";
$layoutArea = "app";
$adminAtivo = "contas";
require_once "views/partials/head.php";
require_once "views/partials/header.php";
?>
<main class="py-5">
    <div class="container">
        <h1 class="h3 app-page-title fw-bold mb-4">Contas</h1>
        <?php require "views/partials/admin_nav.php"; ?>
        <?php if (!empty($msg)) { ?><div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div><?php } ?>
        <div class="app-card p-4">
            <div class="table-responsive">
                <table class="table table-hover table-app align-middle mb-0">
                    <thead><tr><th>ID</th><th>Tipo</th><th>Titular</th><th>Documento</th><th>Situação</th><th>Ação</th></tr></thead>
                    <tbody>
                    <?php foreach ($contas as $c) { ?>
                        <tr>
                            <td><?php echo (int) $c->id_conta; ?></td>
                            <td><?php echo strtoupper(htmlspecialchars($c->tipo_pessoa ?? "—")); ?></td>
                            <td><?php echo htmlspecialchars($c->titular_nome ?? "—"); ?></td>
                            <td><?php echo htmlspecialchars($c->documento ?? "—"); ?></td>
                            <td><?php echo htmlspecialchars($c->situacao); ?></td>
                            <td>
                                <?php
                                $bloqueada = !empty($c->eh_conta_propria) || !empty($c->tem_titular_administrador);
                                if ($bloqueada) { ?>
                                    <span class="text-muted small" title="Conta protegida: propria ou vinculada a administrador">Protegida</span>
                                <?php } else { ?>
                                    <form method="post" action="index.php?controle=adminController&metodo=alterarConta" class="d-flex gap-1">
                                        <input type="hidden" name="conta_id" value="<?php echo (int) $c->id_conta; ?>">
                                        <select name="situacao" class="form-select form-select-sm">
                                            <option value="ativa" <?php echo $c->situacao === "ativa" ? "selected" : ""; ?>>ativa</option>
                                            <option value="inativa" <?php echo $c->situacao === "inativa" ? "selected" : ""; ?>>inativa</option>
                                            <option value="bloqueada" <?php echo $c->situacao === "bloqueada" ? "selected" : ""; ?>>bloqueada</option>
                                        </select>
                                        <button class="btn btn-sm btn-outline-primary" type="submit">Salvar</button>
                                    </form>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php require_once "views/partials/footer.php"; ?>
