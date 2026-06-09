<?php
$titulo = "Admin - Editar plano";
$layoutArea = "app";
$adminAtivo = "planos";
$editando = $plano !== null;
require_once "views/partials/head.php";
require_once "views/partials/header.php";
?>
<main class="py-5">
    <div class="container col-lg-8">
        <h1 class="h3 app-page-title fw-bold mb-4"><?php echo $editando ? "Editar plano" : "Novo plano"; ?></h1>
        <?php require "views/partials/admin_nav.php"; ?>
        <div class="app-card p-4">
            <form method="post" action="index.php?controle=adminController&metodo=salvarPlano">
                <input type="hidden" name="id_plano" value="<?php echo (int) ($plano->id_plano ?? 0); ?>">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label" for="nome">Nome</label>
                        <input class="form-control" id="nome" name="nome" required
                               value="<?php echo htmlspecialchars($plano->nome ?? $_POST["nome"] ?? ""); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="slug">Slug</label>
                        <input class="form-control" id="slug" name="slug" required
                               value="<?php echo htmlspecialchars($plano->slug ?? $_POST["slug"] ?? ""); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="descricao">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="2"><?php echo htmlspecialchars($plano->descricao ?? ""); ?></textarea>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="vcpu">vCPU</label>
                        <input type="number" min="1" class="form-control" id="vcpu" name="vcpu" required
                               value="<?php echo (int) ($plano->vcpu ?? 1); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="ram_gb">RAM (GB)</label>
                        <input type="number" min="1" class="form-control" id="ram_gb" name="ram_gb" required
                               value="<?php echo (int) round(($plano->ram_mb ?? 1024) / 1024); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="storage_gb">Storage (GB)</label>
                        <input type="number" min="1" class="form-control" id="storage_gb" name="storage_gb" required
                               value="<?php echo (int) ($plano->storage_gb ?? 20); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="banda_mbps">Banda (Mbps)</label>
                        <input type="number" min="1" class="form-control" id="banda_mbps" name="banda_mbps" required
                               value="<?php echo (int) ($plano->banda_mbps ?? 100); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="preco_mensal">Preço mensal (R$)</label>
                        <input type="text" class="form-control" id="preco_mensal" name="preco_mensal" required
                               value="<?php echo htmlspecialchars($plano->preco_mensal ?? "49.90"); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="situacao">Situação</label>
                        <select class="form-select" id="situacao" name="situacao">
                            <?php foreach (["ativo", "inativo", "suspenso"] as $sit) { ?>
                                <option value="<?php echo $sit; ?>"
                                    <?php echo ($plano->situacao ?? "ativo") === $sit ? "selected" : ""; ?>>
                                    <?php echo $sit; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-singularys">Salvar</button>
                    <a class="btn btn-outline-secondary" href="index.php?controle=adminController&metodo=planos">Voltar</a>
                </div>
            </form>
        </div>
    </div>
</main>
<?php require_once "views/partials/footer.php"; ?>
