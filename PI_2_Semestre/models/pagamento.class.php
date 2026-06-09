<?php
class Pagamento
{
    public function __construct(
        private int $idPagamento = 0,
        private int $idPedido = 0,
        private int $idEndereco = 0,
        private string $formaPagamento = "simulado",
        private float $valor = 0.0,
        private string $situacao = "confirmado",
        private string $gateway = "simulado"
    ) {}

    public function getIdPagamento() { return $this->idPagamento; }
    public function getIdPedido() { return $this->idPedido; }
    public function getIdEndereco() { return $this->idEndereco; }
    public function getFormaPagamento() { return $this->formaPagamento; }
    public function getValor() { return $this->valor; }
    public function getSituacao() { return $this->situacao; }
    public function getGateway() { return $this->gateway; }
}
