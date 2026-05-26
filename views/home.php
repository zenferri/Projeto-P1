<?php require __DIR__ . '/layout/header.php'; ?>

<!-- Hero -->
<section class="hero py-5 bg-light">
    <div class="container py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-3">
                    <span class="text-primary">Poder</span> para Criar
                    <br>
                    <span class="text-primary">Estrutura</span> para Crescer
                </h1>
                <p class="lead text-muted mb-4">Servidores virtuais escaláveis, executados no Brasil.</p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="<?= BASE_URL ?>/cadastro" class="btn btn-primary btn-lg">Contratar Agora</a>
                    <a href="#opcoes" class="btn btn-outline-primary btn-lg">Ver Planos</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="bg-white rounded-4 p-4 border shadow-sm">
                    <h5 class="fw-bold mb-3">provision_site.sh</h5>
                    <pre class="text-success" style="font-size: 0.85rem; max-height: 250px; overflow-y: auto;">$ havoc create --vm "api-singularys" --vcpus 2 --ram 4096
✓ template: ubuntu-24.04-lts
✓ network: vlan-10
✓ storage: ssd-01 (40G)
✓ ssh: key: zen@singularys
✓ dns: api.singularys.net → 10.0.10.12
→ deploying...
✓ done in 30s</pre>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Planos -->
<section id="opcoes" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold mb-3">Escolha a performance ideal</h2>
            <p class="lead text-muted">Modelos claros, preço transparente e recursos fáceis de comparar</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($plans as $plan): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm hover-shadow">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-2"><?= htmlspecialchars($plan['title']) ?></h5>
                        <p class="display-6 text-primary fw-bold mb-3">
                            R$ <?= number_format($plan['price'], 2, ',', '.') ?>
                            <small class="text-muted fs-6">/mês</small>
                        </p>
                        <p class="text-muted small mb-3"><?= htmlspecialchars($plan['description']) ?></p>
                        <ul class="list-unstyled small mb-4">
                            <li class="mb-2">✓ <?= htmlspecialchars($plan['cpu']) ?></li>
                            <li class="mb-2">✓ <?= htmlspecialchars($plan['ram']) ?></li>
                            <li class="mb-2">✓ <?= htmlspecialchars($plan['storage']) ?></li>
                        </ul>
                        <a href="<?= BASE_URL ?>/cadastro?plano=<?= urlencode($plan['code']) ?>" class="btn btn-primary w-100">
                            Selecionar Plano
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Diferenciais -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold mb-3">Por que escolher Singularys?</h2>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6">
                <div class="d-flex gap-3">
                    <div class="text-primary fs-3">⚡</div>
                    <div>
                        <h5 class="fw-bold">Provisionamento</h5>
                        <p class="text-muted">Sua máquina virtual pronta para uso em segundos, sem burocracia.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex gap-3">
                    <div class="text-primary fs-3">🚀</div>
                    <div>
                        <h5 class="fw-bold">Performance</h5>
                        <p class="text-muted">Infraestrutura otimizada para qualquer carga de trabalho.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex gap-3">
                    <div class="text-primary fs-3">📈</div>
                    <div>
                        <h5 class="fw-bold">Escalabilidade</h5>
                        <p class="text-muted">Expanda ou reduza seus recursos com um clique.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex gap-3">
                    <div class="text-primary fs-3">🔒</div>
                    <div>
                        <h5 class="fw-bold">Segurança</h5>
                        <p class="text-muted">Proteção em múltiplas camadas com monitoramento contínuo.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5">
    <div class="container text-center">
        <h2 class="display-6 fw-bold mb-4">Pronto para começar?</h2>
        <p class="lead text-muted mb-4">Implante sua máquina virtual em poucos cliques</p>
        <a href="<?= BASE_URL ?>/cadastro" class="btn btn-primary btn-lg">Contratar Agora</a>
    </div>
</section>

<style>
    .hover-shadow {
        transition: box-shadow 0.3s ease, transform 0.3s ease;
    }
<?php require __DIR__ . '/layout/footer.php'; ?>
