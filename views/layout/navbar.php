<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold text-primary" href="<?= BASE_URL ?: '/' ?>">
            <span class="me-2">◆</span> Singularys
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= $activePage === 'home' ? 'active' : '' ?>" href="<?= BASE_URL ?: '/' ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $activePage === 'equipe' ? 'active' : '' ?>" href="<?= BASE_URL ?>/equipe">Equipe</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/#opcoes">Planos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $activePage === 'login' ? 'active' : '' ?>" href="<?= BASE_URL ?>/login">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= in_array($activePage, ['cadastro', 'contratar']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/cadastro">
                        <span class="badge bg-primary text-white">Contratar</span>
                    </a>
                </li>
                <?php if ($user): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $activePage === 'dashboard' ? 'active' : '' ?>" href="<?= BASE_URL ?>/dashboard">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/logout">Sair</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
