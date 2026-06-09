<?php

/** @var Plano $planoSelecionado */ ?>
<div class="app-card-dark p-4 h-100">
    <p class="text-uppercase small mb-2 opacity-75">Plano selecionado</p>
    <h2 class="h4 fw-bold mb-1"><?php echo htmlspecialchars($planoSelecionado->getNome()); ?></h2>
    <p class="fs-5 mb-3"><?php echo htmlspecialchars($planoSelecionado->getPrecoFormatado()); ?>/mês</p>
    <ul class="list-unstyled mb-0 small">
        <li class="mb-1"><?php echo (int) $planoSelecionado->getVcpu(); ?> vCPU</li>
        <li class="mb-1"><?php echo (int) $planoSelecionado->getRamGb(); ?> GB RAM</li>
        <li class="mb-1"><?php echo (int) $planoSelecionado->getArmazenamentoGb(); ?> GB SSD</li>
        <li><?php echo (int) $planoSelecionado->getLarguraBandaMbps(); ?> Mbps</li>
    </ul>
</div>