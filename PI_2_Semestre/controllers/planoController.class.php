<?php
require_once "models/conexao.class.php";
require_once "models/plano.class.php";
require_once "models/planoDAO.class.php";
require_once "models/carrinhoSessao.class.php";

class PlanoController
{
    public function listar()
    {
        $planoDAO = new PlanoDAO();
        $planos = $planoDAO->buscarTodos();

        if (!is_array($planos) || count($planos) === 0) {
            $planos = Plano::catalogoInicial();
        }

        require_once "views/listar_planos.php";
    }

    /**
     * Inicia contratacao: carrinho + login ou checkout direto.
     */
    public function contratar()
    {
        $slug = strtolower(trim($_GET["plano"] ?? ""));
        $plano = $this->resolverPlano($slug);

        if (!$plano) {
            http_response_code(404);
            require_once "views/erro404.php";
            return;
        }

        CarrinhoSessao::definirPlano($plano->getSlug());

        if ($this->usuarioPodeContratar()) {
            header("Location:index.php?controle=pedidoController&metodo=checkout");
            exit;
        }

        header("Location:index.php?controle=usuarioController&metodo=login");
        exit;
    }

    private function resolverPlano(string $slug): ?Plano
    {
        if ($slug === "") {
            return null;
        }

        $planoDAO = new PlanoDAO();
        $plano = $planoDAO->buscarPorSlug($slug);

        return $plano ?: Plano::buscarNoCatalogoInicial($slug);
    }

    private function usuarioPodeContratar(): bool
    {
        return !empty($_SESSION["usuario_id"]) && !empty($_SESSION["conta_id"]);
    }
}
