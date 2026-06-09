<?php

class PedidoItem
{
    public function __construct(
        private int $idPedidoItem = 0,
        private int $idPedido = 0,
        private int $idPlano = 0,
        private int $quantidade = 1,
        private float $precoUnitarioSnapshot = 0.0
    ) {}

    public function getIdPedidoItem() { return $this->idPedidoItem; }
    public function getIdPedido() { return $this->idPedido; }
    public function getIdPlano() { return $this->idPlano; }
    public function getQuantidade() { return $this->quantidade; }
    public function getPrecoUnitarioSnapshot() { return $this->precoUnitarioSnapshot; }
}

