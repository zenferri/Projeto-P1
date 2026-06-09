<?php
/*
 * Processo tecnico que cria/configura a VM no Proxmox.
 */
class Provisionamento
{
    public function __construct(
        private int $idProvisionamento = 0,
        private int $idPedidoItem = 0,
        private int $idMaquinaVirtual = 0,
        private string $status = "Fila"
    ) {}

    public function getIdProvisionamento() { return $this->idProvisionamento; }
    public function getIdPedidoItem() { return $this->idPedidoItem; }
    public function getIdMaquinaVirtual() { return $this->idMaquinaVirtual; }
    public function getStatus() { return $this->status; }
}

