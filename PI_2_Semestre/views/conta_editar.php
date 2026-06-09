<?php
require_once "models/telefoneHelper.class.php";
$titulo = "Minha conta - Singularys";
$layoutArea = "app";
require_once "views/partials/head.php";
require_once "views/partials/header.php";
$isPf = ($conta->tipo_pessoa ?? "") === "pf";
?>
<main class="py-5">
    <div class="container">
        <h1 class="h3 app-page-title fw-bold mb-1">Minha conta #<?php echo (int) $conta->id_conta; ?></h1>
        <p class="text-muted mb-4">Edite cadastro, telefones e endereços. A situação da conta não pode ser alterada pelo portal.</p>
        <?php if ($flash !== "") { ?><div class="alert alert-success"><?php echo htmlspecialchars($flash); ?></div><?php } ?>
        <?php if (!empty($msg["geral"])) { ?><div class="alert alert-danger"><?php echo htmlspecialchars($msg["geral"]); ?></div><?php } ?>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="app-card p-4">
                    <h2 class="h5 fw-semibold mb-3">Dados cadastrais</h2>
                    <form method="post" action="index.php?controle=contaController&metodo=editar">
                        <?php if ($isPf) { ?>
                            <div class="mb-3">
                                <label class="form-label" for="nome">Nome completo</label>
                                <input class="form-control" id="nome" name="nome" required
                                       value="<?php echo htmlspecialchars($conta->pf_nome ?? ""); ?>">
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="cpf">CPF</label>
                                    <input class="form-control" id="cpf" name="cpf" required
                                           value="<?php echo htmlspecialchars($conta->cpf ?? ""); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="data_nascimento">Data de nascimento</label>
                                    <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required
                                           value="<?php echo htmlspecialchars($conta->data_nascimento ?? ""); ?>">
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="mb-3">
                                <label class="form-label" for="nome_empresarial">Razão social</label>
                                <input class="form-control" id="nome_empresarial" name="nome_empresarial" required
                                       value="<?php echo htmlspecialchars($conta->nome_empresarial ?? ""); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="nome_fantasia">Nome fantasia</label>
                                <input class="form-control" id="nome_fantasia" name="nome_fantasia"
                                       value="<?php echo htmlspecialchars($conta->nome_fantasia ?? ""); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="cnpj">CNPJ</label>
                                <input class="form-control" id="cnpj" name="cnpj" required
                                       value="<?php echo htmlspecialchars($conta->cnpj ?? ""); ?>">
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="representante_nome">Representante legal</label>
                                    <input class="form-control" id="representante_nome" name="representante_nome" required
                                           value="<?php echo htmlspecialchars($conta->representante_nome ?? ""); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="representante_cpf">CPF do representante</label>
                                    <input class="form-control" id="representante_cpf" name="representante_cpf" required
                                           value="<?php echo htmlspecialchars($conta->representante_cpf ?? ""); ?>">
                                </div>
                            </div>
                        <?php } ?>
                        <button class="btn btn-singularys mt-3" type="submit">Salvar cadastro</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="app-card p-4 mb-4">
                    <h2 class="h5 fw-semibold mb-3">Telefones</h2>
                    <?php if (empty($telefones)) { ?>
                        <p class="text-muted">Nenhum telefone cadastrado.</p>
                    <?php } else { ?>
                        <ul class="list-group list-group-flush mb-3">
                            <?php foreach ($telefones as $tel) { ?>
                                <li class="list-group-item px-0">
                                    <span class="badge text-bg-secondary me-1">
                                        <?php echo htmlspecialchars(TelefoneHelper::rotuloTipo($tel->tipo_telefone)); ?>
                                    </span>
                                    +<?php echo htmlspecialchars($tel->codigo_pais); ?>
                                    (<?php echo htmlspecialchars($tel->ddd ?? ""); ?>)
                                    <?php echo htmlspecialchars($tel->numero); ?>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                    <form method="post" action="index.php?controle=contaController&metodo=adicionarTelefone" class="row g-2">
                        <div class="col-4">
                            <label class="form-label small" for="codigo_pais">País</label>
                            <input class="form-control form-control-sm" id="codigo_pais" name="codigo_pais" value="55">
                        </div>
                        <div class="col-3">
                            <label class="form-label small" for="ddd">DDD</label>
                            <input class="form-control form-control-sm" id="ddd" name="ddd" required maxlength="3">
                        </div>
                        <div class="col-5">
                            <label class="form-label small" for="numero_telefone">Número</label>
                            <input class="form-control form-control-sm" id="numero_telefone" name="numero_telefone" required maxlength="11">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-sm btn-outline-primary">Adicionar telefone</button>
                        </div>
                    </form>
                </div>
                <div class="app-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 fw-semibold mb-0">Endereços</h2>
                        <a class="btn btn-sm btn-outline-primary" href="index.php?controle=contaController&metodo=adicionarEndereco">Novo</a>
                    </div>
                    <?php if (count($enderecos) === 0) { ?>
                        <p class="text-muted mb-0">Nenhum endereço vinculado.</p>
                    <?php } else { ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($enderecos as $end) { ?>
                                <li class="list-group-item px-0">
                                    <strong><?php echo htmlspecialchars($end->logradouro . ", " . $end->numero); ?></strong><br>
                                    <span class="text-muted small"><?php echo htmlspecialchars($end->cidade . "/" . $end->estado . " · CEP " . $end->cep); ?></span>
                                    <form method="post" action="index.php?controle=contaController&metodo=removerEndereco" class="mt-2">
                                        <input type="hidden" name="endereco_id" value="<?php echo (int) $end->id_endereco; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Remover da conta</button>
                                    </form>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once "views/partials/footer.php"; ?>
