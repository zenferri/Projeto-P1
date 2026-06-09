<?php
/*
 * Controller da pagina publica inicial.
 */
require_once "models/conexao.class.php";
require_once "models/plano.class.php";
require_once "models/planoDAO.class.php";

class InicioController
{
    public function inicio()
    {
        /*
         * A home precisa mostrar planos mesmo antes de o banco ser importado.
         * Quando o MySQL ja estiver disponivel, o DAO passa a ser a fonte.
         * Em ambiente inicial de estudo, usamos o catalogo de fallback do model.
         */
        $planoDAO = new PlanoDAO();
        $planos = $planoDAO->buscarAtivos();

        if (!is_array($planos) || count($planos) === 0) {
            $planos = Plano::catalogoInicial();
        }

        require_once "views/home.php";
    }
}

