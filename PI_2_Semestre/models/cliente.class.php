<?php
/*
 * Dados da conta contratante (contas + pessoas_fisicas / pessoas_juridicas).
 */
class Cliente
{
    public function __construct(
        private int $idConta = 0,
        private string $tipoCliente = "PF",
        private string $nome = "",
        private string $sobrenome = "",
        private string $cpf = "",
        private string $dataNascimento = "",
        private string $nomeEmpresarial = "",
        private string $nomeFantasia = "",
        private string $cnpj = "",
        private string $situacaoConta = "ativa"
    ) {}

    public function getIdConta() { return $this->idConta; }
    public function getTipoCliente() { return $this->tipoCliente; }
    public function getNome() { return $this->nome; }
    public function getSobrenome() { return $this->sobrenome; }
    public function getCpf() { return $this->cpf; }
    public function getDataNascimento() { return $this->dataNascimento; }
    public function getNomeEmpresarial() { return $this->nomeEmpresarial; }
    public function getNomeFantasia() { return $this->nomeFantasia; }
    public function getCnpj() { return $this->cnpj; }
    public function getSituacaoConta() { return $this->situacaoConta; }

    public function setIdConta(int $idConta) { $this->idConta = $idConta; }
    public function setTipoCliente(string $tipoCliente) { $this->tipoCliente = $tipoCliente; }
    public function setSituacaoConta(string $situacaoConta) { $this->situacaoConta = $situacaoConta; }
}
