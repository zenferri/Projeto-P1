<?php
require_once "models/conexao.class.php";
require_once "models/sessaoHelper.class.php";
require_once "models/contaDAO.class.php";
require_once "models/enderecoDAO.class.php";
require_once "models/telefoneDAO.class.php";
require_once "models/telefoneHelper.class.php";
require_once "models/endereco.class.php";

class ContaController
{
    public function editar()
    {
        SessaoHelper::exigirContaCliente();
        $contaId = SessaoHelper::contaEmSessao();
        $usuarioId = (int) ($_SESSION["usuario_id"] ?? 0);
        $contaDAO = new ContaDAO();
        if (!$contaDAO->usuarioTemAcessoConta($usuarioId, $contaId)) {
            http_response_code(403);
            $mensagemErro = "Sem permissao para editar esta conta.";
            require_once "views/erro.php";
            return;
        }
        $enderecoDAO = new EnderecoDAO();
        $telefoneDAO = new TelefoneDAO();
        $conta = $contaDAO->buscarPorId($contaId);
        $enderecos = $enderecoDAO->listarAtivosPorConta($contaId);
        $telefones = $telefoneDAO->listarPorConta($contaId);
        $msg = [];
        $flash = $_SESSION["flash_conta"] ?? "";
        unset($_SESSION["flash_conta"]);

        if (!$conta) {
            $mensagemErro = "Conta nao encontrada.";
            require_once "views/erro.php";
            return;
        }

        if ($_POST) {
            $this->processarEdicao($contaId, $conta, $msg);
            $conta = $contaDAO->buscarPorId($contaId);
            $enderecos = $enderecoDAO->listarAtivosPorConta($contaId);
            $telefones = $telefoneDAO->listarPorConta($contaId);
        }

        require_once "views/conta_editar.php";
    }

    public function adicionarEndereco()
    {
        SessaoHelper::exigirContaCliente();
        $contaId = SessaoHelper::contaEmSessao();
        $msg = [];

        if ($_POST) {
            $endereco = new Endereco(
                logradouro: trim($_POST["logradouro"] ?? ""),
                numero: trim($_POST["numero"] ?? ""),
                complemento: trim($_POST["complemento"] ?? ""),
                bairro: trim($_POST["bairro"] ?? ""),
                cidade: trim($_POST["cidade"] ?? ""),
                estado: trim($_POST["estado"] ?? ""),
                cep: preg_replace("/\D/", "", $_POST["cep"] ?? "")
            );

            if ($endereco->getLogradouro() === "" || $endereco->getCep() === "") {
                $msg["geral"] = "Preencha os campos obrigatorios do endereco.";
            } else {
                $enderecoDAO = new EnderecoDAO();
                if ($enderecoDAO->inserirEVincularConta($contaId, $endereco)) {
                    $_SESSION["flash_conta"] = "Endereco vinculado a conta.";
                    header("Location:index.php?controle=contaController&metodo=editar");
                    exit;
                }
                $msg["geral"] = "Nao foi possivel salvar o endereco.";
            }
        }

        require_once "views/conta_endereco_form.php";
    }

    public function removerEndereco()
    {
        SessaoHelper::exigirContaCliente();
        $contaId = SessaoHelper::contaEmSessao();
        $enderecoId = (int) ($_POST["endereco_id"] ?? 0);
        $enderecoDAO = new EnderecoDAO();

        if ($enderecoId > 0 && $enderecoDAO->desativarVinculo($contaId, $enderecoId)) {
            $_SESSION["flash_conta"] = "Endereco removido da sua conta.";
        } else {
            $_SESSION["flash_conta"] = "Nao foi possivel remover o endereco.";
        }

        header("Location:index.php?controle=contaController&metodo=editar");
        exit;
    }

    public function adicionarTelefone()
    {
        SessaoHelper::exigirContaCliente();
        $contaId = SessaoHelper::contaEmSessao();
        $usuarioId = (int) ($_SESSION["usuario_id"] ?? 0);
        $contaDAO = new ContaDAO();
        if (!$contaDAO->usuarioTemAcessoConta($usuarioId, $contaId)) {
            http_response_code(403);
            $mensagemErro = "Sem permissao para editar esta conta.";
            require_once "views/erro.php";
            return;
        }

        if (!$_POST) {
            header("Location:index.php?controle=contaController&metodo=editar");
            exit;
        }

        $codigoPais = trim($_POST["codigo_pais"] ?? "55") ?: "55";
        $validacao = TelefoneHelper::validarBrasil(
            (string) ($_POST["ddd"] ?? ""),
            (string) ($_POST["numero_telefone"] ?? "")
        );
        if (empty($validacao["ok"])) {
            $_SESSION["flash_conta"] = $validacao["msg"] ?? "Telefone invalido.";
            header("Location:index.php?controle=contaController&metodo=editar");
            exit;
        }

        $telefoneDAO = new TelefoneDAO();
        if ($telefoneDAO->inserirEVincularConta(
            $contaId,
            $codigoPais,
            $validacao["ddd"],
            $validacao["numero"],
            $validacao["tipo"]
        )) {
            $_SESSION["flash_conta"] = "Telefone adicionado a conta.";
        } else {
            $_SESSION["flash_conta"] = "Nao foi possivel salvar o telefone.";
        }

        header("Location:index.php?controle=contaController&metodo=editar");
        exit;
    }

    private function processarEdicao(int $contaId, object $conta, array &$msg): void
    {
        if (isset($_POST["situacao"]) || isset($_POST["conta_situacao"])) {
            $msg["geral"] = "Alteracao de situacao da conta nao e permitida pelo portal.";
            return;
        }

        $contaDAO = new ContaDAO();
        $ok = false;

        if ($conta->tipo_pessoa === "pf") {
            $nome = trim($_POST["nome"] ?? "");
            $cpf = preg_replace("/\D/", "", $_POST["cpf"] ?? "");
            $dataNasc = $_POST["data_nascimento"] ?? "";
            if ($nome === "" || $cpf === "") {
                $msg["geral"] = "Nome e CPF sao obrigatorios.";
                return;
            }
            $ok = $contaDAO->atualizarPf($contaId, $nome, $cpf, $dataNasc);
        } elseif ($conta->tipo_pessoa === "pj") {
            $ok = $contaDAO->atualizarPj(
                $contaId,
                trim($_POST["nome_empresarial"] ?? ""),
                trim($_POST["nome_fantasia"] ?? "") ?: null,
                preg_replace("/\D/", "", $_POST["cnpj"] ?? ""),
                trim($_POST["representante_nome"] ?? ""),
                preg_replace("/\D/", "", $_POST["representante_cpf"] ?? "")
            );
        }

        if ($ok) {
            $_SESSION["flash_conta"] = "Cadastro da conta atualizado.";
            $_SESSION["usuario_nome"] = $this->nomeExibicaoConta($contaDAO->buscarPorId($contaId));
        } else {
            $msg["geral"] = "Nao foi possivel atualizar os dados.";
        }
    }

    private function nomeExibicaoConta(?object $conta): string
    {
        if (!$conta) {
            return $_SESSION["usuario_nome"] ?? "Conta";
        }
        if ($conta->tipo_pessoa === "pj") {
            return $conta->nome_empresarial ?? "Conta";
        }
        return $conta->pf_nome ?? "Conta";
    }
}
