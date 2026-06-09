<?php
/*
 * View da home.
 *
 * A colecao $planos vem do InicioController. Ela pode vir do DAO ou do
 * catalogo inicial do model quando o banco ainda nao foi importado.
 */
$titulo = "Singularys";
$descricao = "Contrate VPS Singularys, servidores virtuais escaláveis, executados no Brasil.";
$layoutArea = "landpage"; // isso define se chama no header.php o "app" ou o "landpage". O "app" é o layout do app e o "landpage" é o layout da landpage.
require_once "views/partials/head.php";
require_once "views/partials/header.php";
?>
<section class="introducao">
    <div class="introducao-int reveal up" id="sobre">
        <h1><strong>Poder para <span class="criar">Criar</span></strong></h1>
        <h1><strong>Estrutura para <span class="crescer">Crescer</span></strong></h1>
        <h2>Servidores virtuais escalaveis, executados no Brasil.</h2>
    </div>
    <div class="illus" aria-hidden="true">
        <div class="panel">
            <h4>ssh user@vps-pro.singularys.com</h4>
            <pre>Debian GNU/Linux comes with ABSOLUTELY NO WARRANTY, 
to the extent permitted by applicable law.
Last login: Wed May 20 08:50:27 2026 from 192.168.0.130
user@vps-pro:~#</pre>
        </div>
    </div>
</section>

<section class="produtos" id="opcoes">
    <h1>Escolha a performance ideal para o seu projeto</h1>
</section>

<section class="preco" id="preco">
    <?php foreach ($planos as $indice => $plano) { ?>
        <div class="preco-item reveal <?php echo $indice === 1 ? "up" : ($indice === 0 ? "left" : "right"); ?>">
            <h2><?php echo htmlspecialchars($plano->getNome()); ?></h2>
            <span>
                <sup>R$</sup><?php echo number_format($plano->getPrecoMensal(), 2, ",", "."); ?><sub>/mes</sub>
            </span>
            <ul>
                <li><?php echo (int) $plano->getVcpu(); ?> vCPU</li>
                <li><?php echo (int) $plano->getRamGb(); ?> GB RAM</li>
                <li><?php echo (int) $plano->getArmazenamentoGb(); ?> GB SSD</li>
                <li><?php echo (int) $plano->getLarguraBandaMbps(); ?> Mbps</li>
            </ul>
            <a class="botao-cadastro"
               href="index.php?controle=planoController&metodo=contratar&plano=<?php echo urlencode($plano->getCodigo()); ?>">
                Selecionar
            </a>
        </div>
    <?php } ?>
</section>

<section class="qualidade" id="qualidade">

        <div class="qualidade-item reveal left">
            <h2>Provisionamento</h2>
            <p>Sua máquina virtual pronta para uso em segundos, sem burocracia nem espera.</p>
        </div>

        <div class="qualidade-item reveal up">
            <h2>Performance</h2>
            <p>Infraestrutura de alto nível, otimizada para qualquer carga de trabalho e demanda crítica.</p>
        </div>

        <div class="qualidade-item reveal right">
            <h2>Escalabilidade</h2>
            <p>Expanda ou reduza seus recursos com um clique, quando e como quiser.</p>
        </div>

        <div class="qualidade-item reveal left">
            <h2>Segurança</h2>
            <p>Proteção em múltiplas camadas, com monitoramento contínuo e isolamento de dados para máxima
                tranquilidade.</p>
        </div>

        <div class="qualidade-item reveal up">
            <h2>Suporte 24/7</h2>
            <p>Equipe técnica disponível o tempo todo para garantir estabilidade e resolver qualquer necessidade com
                agilidade.</p>
        </div>

        <div class="qualidade-item reveal right">
            <h2>Uptime</h2>
            <p>Conectividade contínua e confiável para que seu projeto permaneça online, sempre disponível e sem
                interrupções.</p>
        </div>

    </section>

<?php require_once "views/partials/footer.php"; ?>

