<?php

class Usuario
{
    public function __construct(
        private int $idUsuario = 0,
        private string $email = "",
        private string $senhaHash = "",
        private string $situacao = "pendente_verificacao_email",
        private string $nomeExibicao = ""
    ) {}

    public function getIdUsuario() { return $this->idUsuario; }
    public function getEmail() { return $this->email; }
    public function getSenhaHash() { return $this->senhaHash; }
    public function getSituacao() { return $this->situacao; }
    public function getNomeExibicao() { return $this->nomeExibicao; }

    public function setIdUsuario(int $idUsuario) { $this->idUsuario = $idUsuario; }
    public function setEmail(string $email) { $this->email = $email; }
    public function setSenhaHash(string $senhaHash) { $this->senhaHash = $senhaHash; }
    public function setSituacao(string $situacao) { $this->situacao = $situacao; }
    public function setNomeExibicao(string $nomeExibicao) { $this->nomeExibicao = $nomeExibicao; }
}
