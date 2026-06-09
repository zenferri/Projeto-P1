<?php
/*
 * Entidade operacional MaquinaVirtual.
 *
 * Ela aponta para o item de pedido que a originou, mas nao substitui o pedido.
 */
class MaquinaVirtual
{
    public function __construct(
        private int $idMaquinaVirtual = 0,
        private int $idPedidoItem = 0,
        private int $idAssinatura = 0,
        private string $nome = "",
        private string $hostname = "",
        private string $ipPublico = "",
        private string $status = "Provisionando"
    ) {}

    public function getIdMaquinaVirtual() { return $this->idMaquinaVirtual; }
    public function getIdPedidoItem() { return $this->idPedidoItem; }
    public function getIdAssinatura() { return $this->idAssinatura; }
    public function getNome() { return $this->nome; }
    public function getHostname() { return $this->hostname; }
    public function getIpPublico() { return $this->ipPublico; }
    public function getStatus() { return $this->status; }
}

