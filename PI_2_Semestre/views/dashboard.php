<?php
require_once "models/telefoneHelper.class.php";
$titulo = "Dashboard - Singularys";
$descricao = "Dashboard de VMs e provisionamentos do cliente.";
$layoutArea = "app";
require_once "models/estadoVmHelper.class.php";
require_once "views/partials/head.php";
require_once "views/partials/header.php";
?>
<main class="py-5">
    <div class="container">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
            <div>
                <p class="text-uppercase text-muted small mb-1">Área do cliente</p>
                <h1 class="h3 app-page-title fw-bold mb-1">
                    Olá, <?php echo htmlspecialchars($_SESSION["usuario_nome"] ?? "usuário"); ?>
                </h1>
                <p class="text-muted mb-0">Pedidos pagos podem existir antes da VM ficar pronta.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-singularys" href="index.php?controle=planoController&metodo=listar">Contratar VPS</a>
                <a class="btn btn-outline-secondary" href="index.php?controle=contaController&metodo=editar">Minha conta</a>
                <a class="btn btn-outline-primary" href="mailto:suporte@singularys.net">Suporte</a>
            </div>
        </div>

        <?php if ($flashSucesso !== "") { ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($flashSucesso); ?></div>
        <?php } ?>

        <?php if (!empty($telefones)) { ?>
            <div class="app-card p-3 mb-4">
                <span class="text-muted small d-block mb-2">Telefones da conta</span>
                <?php foreach ($telefones as $tel) { ?>
                    <span class="me-3">
                        <strong><?php echo htmlspecialchars(TelefoneHelper::rotuloTipo($tel->tipo_telefone)); ?>:</strong>
                        +<?php echo htmlspecialchars($tel->codigo_pais); ?>
                        (<?php echo htmlspecialchars($tel->ddd ?? ""); ?>)
                        <?php echo htmlspecialchars($tel->numero); ?>
                    </span>
                <?php } ?>
            </div>
        <?php } ?>

        <div class="row g-4 mb-4">
            <div class="col-lg-7">
                <div class="app-card p-4 h-100">
                    <h2 class="h5 fw-semibold app-page-title mb-3">Máquinas virtuais</h2>
                    <?php if (count($maquinasVirtuais) === 0) { ?>
                        <p class="text-muted mb-0">Você ainda não possui produtos entregues. Consulte os provisionamentos abaixo.</p>
                    <?php } else { ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-app align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Plano</th>
                                        <th>IP</th>
                                        <th>Estado</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($maquinasVirtuais as $vm) { ?>
                                        <tr>
                                            <td>
                                                <?php echo htmlspecialchars($vm->hostname); ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($vm->sabor_linux); ?></small>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($vm->plano); ?>
                                                <br><small class="text-muted"><?php echo (int) ($vm->vcpu_aplicado ?? $vm->vcpu); ?> vCPU / <?php echo (int) round(($vm->ram_mb_aplicado ?? $vm->ram_mb) / 1024); ?> GB</small>
                                            </td>
                                            <td><?php echo htmlspecialchars($vm->ipv4_publico ?? "—"); ?></td>
                                            <td>
                                                <span class="badge <?php echo $vm->estado === "destruida" ? "text-bg-secondary" : "text-bg-warning text-dark"; ?> badge-situacao">
                                                    <?php echo htmlspecialchars(EstadoVmHelper::rotulo($vm->estado)); ?>
                                                </span>
                                            </td>
                                            <td class="text-nowrap">
                                                <?php if ($vm->estado === "aguardando_configuracao") { ?>
                                                    <form method="post" action="index.php?controle=maquinaVirtualController&metodo=salvarHostname" class="d-flex gap-1 mb-1">
                                                        <input type="hidden" name="maquina_id" value="<?php echo (int) $vm->id_maquina_virtual; ?>">
                                                        <input type="text" name="hostname" class="form-control form-control-sm" placeholder="Hostname" required
                                                               value="<?php echo $vm->hostname !== "vm-pendente" ? htmlspecialchars($vm->hostname) : ""; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-primary">Salvar</button>
                                                    </form>
                                                <?php } ?>
                                                <?php if ($vm->estado !== "destruida") { ?>
                                                    <form method="post" action="index.php?controle=maquinaVirtualController&metodo=excluir"
                                                          onsubmit="return confirm('Marcar esta VM como destruida? Ela permanece no historico da sua conta.');">
                                                        <input type="hidden" name="maquina_id" value="<?php echo (int) $vm->id_maquina_virtual; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                                                    </form>
                                                <?php } else { ?>
                                                    <span class="text-muted small">No historico</span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="app-card p-4 h-100">
                    <h2 class="h5 fw-semibold app-page-title mb-3">Ações comerciais</h2>
                    <p class="text-muted">Veja o histórico de pedidos que originaram cada entrega.</p>
                    <a class="btn btn-outline-primary" href="index.php?controle=pedidoController&metodo=listar">Ver pedidos</a>
                </div>
            </div>
        </div>

        <div class="app-card p-4">
            <h2 class="h5 fw-semibold app-page-title mb-3">Provisionamentos recentes</h2>
            <?php if (count($provisionamentos) === 0) { ?>
                <p class="text-muted mb-0">Nenhum processo técnico registrado para esta conta.</p>
            <?php } else { ?>
                <div class="table-responsive">
                    <table class="table table-hover table-app align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Plano</th>
                                <th>Situação</th>
                                <th>Último evento</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($provisionamentos as $provisionamento) { ?>
                                <tr>
                                    <td><?php echo (int) $provisionamento->id_solicitacao; ?></td>
                                    <td><?php echo htmlspecialchars($provisionamento->plano); ?></td>
                                    <td><span class="badge text-bg-light text-dark border badge-situacao"><?php echo htmlspecialchars($provisionamento->situacao); ?></span></td>
                                    <td><?php echo htmlspecialchars($provisionamento->mensagem ?? "Sem evento"); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>
    </div>
</main>
<?php require_once "views/partials/footer.php"; ?>
