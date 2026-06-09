<?php
$titulo = "Novo endereço - Singularys";
$layoutArea = "app";
require_once "views/partials/head.php";
require_once "views/partials/header.php";
?>
<main class="py-5">
    <div class="container col-lg-8">
        <h1 class="h3 app-page-title fw-bold mb-4">Adicionar endereço</h1>
        <?php if (!empty($msg["geral"])) { ?><div class="alert alert-danger"><?php echo htmlspecialchars($msg["geral"]); ?></div><?php } ?>
        <div class="app-card p-4">
            <form method="post" action="index.php?controle=contaController&metodo=adicionarEndereco">
                <?php require "views/partials/cadastro/endereco.php"; ?>
                <button class="btn btn-singularys" type="submit">Salvar e vincular</button>
                <a class="btn btn-outline-secondary ms-2" href="index.php?controle=contaController&metodo=editar">Voltar</a>
            </form>
        </div>
    </div>
</main>
<?php require_once "views/partials/footer.php"; ?>
