<?php require __DIR__ . '/layout/header.php'; ?>
<section class="section page-section">
    <div class="dashboard-grid">
        <div class="dashboard-card">
            <span class="eyebrow">Painel do cliente</span>
            <h1>Bem-vindo, <?= htmlspecialchars($user['nome']) ?></h1>
            <p>Seu servidor está ativo e você pode acompanhar o status e uso direto por aqui.</p>
            <div class="dashboard-details">
                <div>
                    <strong>Plano atual</strong>
                    <p><?= htmlspecialchars($plan['title']) ?> • R$ <?= number_format($plan['price'], 2, ',', '.') ?>/mês</p>
                </div>
                <div>
                    <strong>Servidor</strong>
                    <p><?= htmlspecialchars($plan['cpu']) ?> · <?= htmlspecialchars($plan['ram']) ?> · <?= htmlspecialchars($plan['storage']) ?></p>
                </div>
            </div>
            <div class="dashboard-actions">
                <a class="button" href="<?= BASE_URL ?>/carrinho">Alterar pagamento</a>
                <a class="button button-outline" href="<?= BASE_URL ?>/cadastro">Novo plano</a>
            </div>
        </div>
        <div class="dashboard-card metric-card">
            <h2>Métricas em tempo real</h2>
            <div class="metric-row">
                <div>
                    <span>CPU</span>
                    <strong>62%</strong>
                </div>
                <div class="progress"><div style="width: 62%;"></div></div>
            </div>
            <div class="metric-row">
                <div>
                    <span>Memória</span>
                    <strong>48%</strong>
                </div>
                <div class="progress"><div style="width: 48%;"></div></div>
            </div>
            <div class="metric-row">
                <div>
                    <span>Armazenamento</span>
                    <strong>68 GB / 100 GB</strong>
                </div>
                <div class="progress"><div style="width: 68%;"></div></div>
            </div>
            <div class="metric-row">
                <div>
                    <span>Tráfego</span>
                    <strong>2,9 TB</strong>
                </div>
                <div class="progress"><div style="width: 58%;"></div></div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/layout/footer.php'; ?>
