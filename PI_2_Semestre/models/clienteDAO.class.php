<?php
require_once __DIR__ . "/conexao.class.php";

class ClienteDAO extends Conexao
{
    public function __construct()
    {
        parent::__construct();
    }

    public function cadastrarComUsuario(
        Cliente $cliente,
        Endereco $endereco,
        Usuario $usuario,
        string $senhaPlana,
        string $representanteNome = "",
        string $representanteCpf = "",
        string $codigoPais = "55",
        string $ddd = "",
        string $numeroTelefone = "",
        string $tipoTelefone = "celular"
    ) {
        if (!$this->possuiConexao()) {
            return $this->mensagemBancoIndisponivel();
        }

        try {
            $papelId = $this->buscarPapelId("titular");
            if ($papelId === null) {
                return "Papel titular nao encontrado no banco.";
            }

            $email = strtolower(trim($usuario->getEmail()));
            $senhaHash = password_hash($senhaPlana, PASSWORD_DEFAULT);
            if ($senhaHash === false) {
                return "Nao foi possivel proteger a senha informada.";
            }

            $tipoPessoa = strtolower($cliente->getTipoCliente());
            $this->db->beginTransaction();

            $stmUsuario = $this->db->prepare(
                "INSERT INTO usuarios (email, senha_hash, email_verificado_em, situacao)
                 VALUES (?, ?, NOW(), 'ativo')"
            );
            $stmUsuario->execute([$email, $senhaHash]);
            $usuarioId = (int) $this->db->lastInsertId();

            $stmConta = $this->db->prepare(
                "INSERT INTO contas (tipo_pessoa, situacao) VALUES (?, 'ativa')"
            );
            $stmConta->execute([$tipoPessoa]);
            $contaId = (int) $this->db->lastInsertId();

            if ($tipoPessoa === "pf") {
                $nomeCompleto = trim($cliente->getNome() . " " . $cliente->getSobrenome());
                $stmPF = $this->db->prepare(
                    "INSERT INTO pessoas_fisicas (conta_id, usuario_id, nome, cpf, data_nascimento)
                     VALUES (?, ?, ?, ?, ?)"
                );
                $stmPF->execute([
                    $contaId,
                    $usuarioId,
                    $nomeCompleto,
                    $cliente->getCpf(),
                    $cliente->getDataNascimento(),
                ]);
            } else {
                $stmPJ = $this->db->prepare(
                    "INSERT INTO pessoas_juridicas
                        (conta_id, nome_empresarial, nome_fantasia, cnpj, representante_nome, representante_cpf)
                     VALUES (?, ?, ?, ?, ?, ?)"
                );
                $stmPJ->execute([
                    $contaId,
                    $cliente->getNomeEmpresarial(),
                    $cliente->getNomeFantasia() ?: null,
                    $cliente->getCnpj(),
                    $representanteNome,
                    $representanteCpf,
                ]);
            }

            $stmEndereco = $this->db->prepare(
                "INSERT INTO enderecos (cep, logradouro, numero, complemento, bairro, cidade, estado, pais)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmEndereco->execute([
                $endereco->getCep(),
                $endereco->getLogradouro(),
                $endereco->getNumero(),
                $endereco->getComplemento() ?: null,
                $endereco->getBairro(),
                $endereco->getCidade(),
                strtoupper($endereco->getEstado()) ?: null,
                $endereco->getPais(),
            ]);
            $enderecoId = (int) $this->db->lastInsertId();

            $stmCe = $this->db->prepare(
                "INSERT INTO contas_enderecos (conta_id, endereco_id, situacao) VALUES (?, ?, 'ativo')"
            );
            $stmCe->execute([$contaId, $enderecoId]);

            $stmTel = $this->db->prepare(
                "INSERT INTO telefones (codigo_pais, ddd, numero, tipo_telefone)
                 VALUES (?, ?, ?, ?)"
            );
            $stmTel->execute([
                $codigoPais,
                $ddd !== "" ? $ddd : null,
                $numeroTelefone,
                $tipoTelefone,
            ]);
            $telefoneId = (int) $this->db->lastInsertId();

            $stmCt = $this->db->prepare(
                "INSERT INTO contas_telefones (conta_id, telefone_id) VALUES (?, ?)"
            );
            $stmCt->execute([$contaId, $telefoneId]);

            $stmVinculo = $this->db->prepare(
                "INSERT INTO usuarios_contas (usuario_id, conta_id, papel_id, situacao)
                 VALUES (?, ?, ?, 'ativo')"
            );
            $stmVinculo->execute([$usuarioId, $contaId, $papelId]);

            $this->db->commit();
            return ["conta_id" => $contaId, "usuario_id" => $usuarioId];
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            return "Problema ao cadastrar o cliente.";
        }
    }

    private function buscarPapelId(string $nomePapel): ?int
    {
        $stm = $this->db->prepare("SELECT id_papel FROM papeis WHERE nome_papel = ? LIMIT 1");
        $stm->execute([$nomePapel]);
        $dado = $stm->fetch();

        return $dado ? (int) $dado->id_papel : null;
    }
}
