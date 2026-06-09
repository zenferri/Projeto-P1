<?php
class Endereco
{
    public function __construct(
        private int $idEndereco = 0,
        private string $logradouro = "",
        private string $numero = "",
        private string $complemento = "",
        private string $bairro = "",
        private string $cidade = "",
        private string $estado = "",
        private string $cep = "",
        private string $pais = "Brasil"
    ) {}

    public function getIdEndereco() { return $this->idEndereco; }
    public function getLogradouro() { return $this->logradouro; }
    public function getNumero() { return $this->numero; }
    public function getComplemento() { return $this->complemento; }
    public function getBairro() { return $this->bairro; }
    public function getCidade() { return $this->cidade; }
    public function getEstado() { return $this->estado; }
    public function getCep() { return $this->cep; }
    public function getPais() { return $this->pais; }

    public function setIdEndereco(int $idEndereco): void
    {
        $this->idEndereco = $idEndereco;
    }
}
