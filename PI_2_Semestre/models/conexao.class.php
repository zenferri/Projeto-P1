<?php
/*
 * Classe base de conexão com o banco.
 */
class Conexao
{
    protected $db = null;
    protected $erroConexao = "";

    public function __construct()
    {
        /*
         * Os valores abaixo podem ser alterados por variaveis de ambiente.
         * Se nenhuma variavel existir, usamos configuracao local de estudo.
         */
        $host = getenv("SINGULARYS_DB_HOST") ?: "localhost";
        $banco = getenv("SINGULARYS_DB_NAME") ?: "singularys2";
        $usuario = getenv("SINGULARYS_DB_USER") ?: "root";
        $senha = getenv("SINGULARYS_DB_PASS") ?: "";
        $parametros = "mysql:host={$host};dbname={$banco};charset=utf8mb4";

        try {
            $this->db = new PDO($parametros, $usuario, $senha);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            /*
             * Guardamos o erro no objeto em vez de encerrar a pagina.
             * Assim a home ainda abre antes de o SQL ser importado.
             */
            $this->erroConexao = $e->getMessage();
            $this->db = null;
        }
    }

    protected function possuiConexao()
    {
        return $this->db instanceof PDO;
    }

    protected function mensagemBancoIndisponivel()
    {
        return "Banco indisponivel. Importe banco_de_dados/schema_singularys_v7.7_sprint1_corrigido.sql e revise a conexao.";
    }
}

