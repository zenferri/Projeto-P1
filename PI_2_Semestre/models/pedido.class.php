<?php
class Pedido
{
    public function __construct(
        private int $idPedido = 0,
        private int $idConta = 0,
        private float $valorTotal = 0.0,
        private string $situacao = "pago"
    ) {}

    public function getIdPedido() { return $this->idPedido; }
    public function getIdConta() { return $this->idConta; }
    public function getValorTotal() { return $this->valorTotal; }
    public function getSituacao() { return $this->situacao; }
}
