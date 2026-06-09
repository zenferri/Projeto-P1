<?php
$usuarioLogado = !empty($_SESSION["usuario_id"]);
$layoutArea = $layoutArea ?? "landpage";
$isAdmin = ($usuarioLogado && ($_SESSION["usuario_papel"] ?? "") === "administrador");
?>

<!-- Se o layoutArea for app ($layoutArea === "app"), exibe o topbar e o navbar do app
Se o layoutArea for landpage ($layoutArea === "landpage"), exibe o superinfo e o menu e o top bar do landpage-->

<?php if ($layoutArea === "app") {  // Aqui checa o layout. Ele busca o "app" ou "landpage" em cada view. ?> 
    <div class="app-topbar py-2">
        <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
            <?php if ($usuarioLogado) { ?>
                <span>Usuário: <?php echo htmlspecialchars($_SESSION["usuario_nome"] ?? "usuário"); ?></span>
            <?php } else { ?>
                <span>Portal do cliente Singularys</span>
            <?php } ?>
            <div class="d-flex gap-3">
                <a href="./termos.html">Termos de Uso</a>
                <a href="mailto:suporte@singularys.net">Suporte</a>
                <a href="./equipe.html#quem_somos">Nossa equipe</a>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-app">
        <div class="container">
            <a class="navbar-brand" href="index.php">Singularys</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarApp"
                    aria-controls="navbarApp" aria-expanded="false" aria-label="Menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarApp">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-1">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?controle=planoController&metodo=listar">Produtos</a>
                    </li>
                    <?php if ($isAdmin) { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controle=adminController&metodo=painel">Admin</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controle=adminController&metodo=contas">Contas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controle=usuarioController&metodo=logout">Logout</a>
                        </li>
                    <?php } elseif ($usuarioLogado) { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controle=dashboardController&metodo=cliente">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controle=contaController&metodo=editar">Minha conta</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controle=pedidoController&metodo=listar">Pedidos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controle=usuarioController&metodo=logout">Logout</a>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controle=usuarioController&metodo=login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-singularys btn-sm ms-lg-2"
                               href="index.php?controle=clienteController&metodo=cadastrar&intencao=cadastro">Cadastre-se</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>
<?php } else { // se não for app, então cai aqui, em landpage! Preciso fazer alguns ajustes ainda! ?>
    <section class="menu-top">
        <div class="superinfo-bg">
            <div class="superinfo<?php echo $usuarioLogado ? " superinfo-logado" : ""; ?>">
                <?php if ($usuarioLogado) { ?>
                    <p class="text-white mb-0">Usuário: <?php echo htmlspecialchars($_SESSION["usuario_nome"] ?? "usuário"); ?></p>
                    <span class="superinfo-links">
                        <a href="./termos.html">Termos de Uso</a>
                        <a href="mailto:suporte@singularys.net">Suporte</a>
                        <a href="./equipe.html#quem_somos">Nossa equipe</a>
                    </span>
                <?php } else { ?>
                    <a href="./termos.html">Termos de Uso</a>
                    <a href="mailto:suporte@singularys.net">Suporte</a>
                    <a href="./equipe.html#quem_somos">Nossa equipe</a>
                <?php } ?>
            </div>
        </div>
        <header class="menu-bg">
            <div class="menu">
                <div class="menu-logo">
                    <a href="index.php">Singularys</a>
                </div>
                <nav class="menu-nav">
                    <ul>
                        <li><a href="index.php">Inicio</a></li>
                        <li><a href="index.php?controle=planoController&metodo=listar">Produtos</a></li>
                        <?php if ($isAdmin) { ?>
                            <li><a href="index.php?controle=adminController&metodo=painel">Admin</a></li>
                            <li><a href="index.php?controle=usuarioController&metodo=logout">Logout</a></li>
                        <?php } elseif ($usuarioLogado) { ?>
                            <li><a href="index.php?controle=dashboardController&metodo=cliente">Dashboard</a></li>
                            <li><a href="index.php?controle=contaController&metodo=editar">Minha conta</a></li>
                            <li><a href="index.php?controle=pedidoController&metodo=listar">Pedidos</a></li>
                            <li><a href="index.php?controle=usuarioController&metodo=logout">Logout</a></li>
                        <?php } else { ?>
                            <li><a href="index.php?controle=usuarioController&metodo=login">Login</a></li>
                            <li>
                                <a class="botao-cadastro"
                                   href="index.php?controle=clienteController&metodo=cadastrar&intencao=cadastro">Cadastre-se</a>
                            </li>
                        <?php } ?>
                    </ul>
                </nav>
            </div>
        </header>
    </section>
<?php } ?>
