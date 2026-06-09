<?php $adminAtivo = $adminAtivo ?? ""; ?>
<nav class="nav nav-pills flex-column flex-lg-row gap-2 mb-4">
    <a class="nav-link <?php echo $adminAtivo === "painel" ? "active" : ""; ?>"
       href="index.php?controle=adminController&metodo=painel">Painel</a>
    <a class="nav-link <?php echo $adminAtivo === "contas" ? "active" : ""; ?>"
       href="index.php?controle=adminController&metodo=contas">Contas</a>
    <a class="nav-link <?php echo $adminAtivo === "usuarios" ? "active" : ""; ?>"
       href="index.php?controle=adminController&metodo=usuarios">Usuários</a>
    <a class="nav-link <?php echo $adminAtivo === "planos" ? "active" : ""; ?>"
       href="index.php?controle=adminController&metodo=planos">Planos</a>
    <a class="nav-link <?php echo $adminAtivo === "pedidos" ? "active" : ""; ?>"
       href="index.php?controle=adminController&metodo=pedidos">Pedidos</a>
    <a class="nav-link <?php echo $adminAtivo === "pagamentos" ? "active" : ""; ?>"
       href="index.php?controle=adminController&metodo=pagamentos">Pagamentos</a>
</nav>
