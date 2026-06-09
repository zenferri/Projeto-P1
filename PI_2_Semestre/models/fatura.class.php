<?php
/*
 * Fatura mensal da assinatura.
 */
class Fatura
{
    public function __construct(
        private int $idFatura = 0,
        private int $idAssinatura = 0,
        private float $valorTotal = 0.0,
        private string $status = "Paga"
    ) {}

    public function getIdFatura() { return $this->idFatura; }
    public function getIdAssinatura() { return $this->idAssinatura; }
    public function getValorTotal() { return $this->valorTotal; }
    public function getStatus() { return $this->status; }
}

