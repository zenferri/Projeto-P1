<?php
/*
 * Assinatura recorrente gerada por um item de pedido aprovado.
 */
class Assinatura
{
    public function __construct(
        private int $idAssinatura = 0,
        private int $idCliente = 0,
        private int $idPedidoItemOrigem = 0,
        private int $idPlano = 0,
        private string $status = "Ativa"
    ) {}

    public function getIdAssinatura() { return $this->idAssinatura; }
    public function getIdCliente() { return $this->idCliente; }
    public function getIdPedidoItemOrigem() { return $this->idPedidoItemOrigem; }
    public function getIdPlano() { return $this->idPlano; }
    public function getStatus() { return $this->status; }
}

