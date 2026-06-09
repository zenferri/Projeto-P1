<?php
$titulo = "Admin - Usuários";
$layoutArea = "app";
$adminAtivo = "usuarios";
require_once "views/partials/head.php";
require_once "views/partials/header.php";
?>
<main class="py-5">
    <div class="container">
        <h1 class="h3 app-page-title fw-bold mb-4">Usuários</h1>
        <?php require "views/partials/admin_nav.php"; ?>
        <?php if (!empty($msg)) { ?><div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div><?php } ?>
        <div class="app-card p-4 table-responsive">
            <table class="table table-hover table-app align-middle mb-0">
                <thead><tr><th>ID</th><th>E-mail</th><th>Situação</th><th>Papéis</th><th>Último login</th><th>Ação</th></tr></thead>
                <tbody>
                <?php foreach ($usuarios as $u) { ?>
                    <tr>
                        <td><?php echo (int) $u->id_usuario; ?></td>
                        <td><?php echo htmlspecialchars($u->email); ?></td>
                        <td><?php echo htmlspecialchars($u->situacao); ?></td>
                        <td><?php echo htmlspecialchars($u->papeis ?? "—"); ?></td>
                        <td><?php echo htmlspecialchars($u->ultimo_login ?? "—"); ?></td>
                        <td>
                            <?php
                            $bloqueado = !empty($u->eh_administrador) || !empty($u->eh_proprio_usuario);
                            if ($bloqueado) { ?>
                                <span class="text-muted small" title="Usuario administrador ou proprio login">Protegido</span>
                            <?php } else { ?>
                                <form method="post" action="index.php?controle=adminController&metodo=salvarUsuario" class="d-flex gap-1">
                                    <input type="hidden" name="usuario_id" value="<?php echo (int) $u->id_usuario; ?>">
                                    <select name="situacao" class="form-select form-select-sm">
                                        <?php foreach ($situacoesUsuario as $sit) { ?>
                                            <option value="<?php echo $sit; ?>" <?php echo $u->situacao === $sit ? "selected" : ""; ?>>
                                                <?php echo $sit; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Salvar</button>
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
