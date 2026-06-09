<?php
/*
 * Ponto de entrada do portal Singularys.
 */

 $basePath = str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"] ?? ""));

// se o basePath for vazio ou for "/", define como "/", caso contrário, define como "{$basePath}/", por exemplo, se o basePath for "/Portal_MVC/", define como "/Portal_MVC/".
$cookiePath = ($basePath === "" || $basePath === "/") ? "/" : "{$basePath}/"; 

// define o cookie de sessão no path do portal.
session_set_cookie_params([
    "lifetime" => 0,
    "path" => $cookiePath,
    "httponly" => true,
    "samesite" => "Lax",
]);
session_start();

// O controller inicial e a tela inicial são usados quando a URL não informa rota.
$controle = $_GET["controle"] ?? "inicioController";
$metodo = $_GET["metodo"] ?? "inicio";

/*
 * Rotas atualmente disponíveis.
 *
 * Para adicionar uma nova tela:
 * 1. crie o controller em controllers/*.class.php;
 * 2. crie o método público;
 * 3. registre a combinação aqui.
 */

$rotasPermitidas = [
    "inicioController" => ["inicio"],
    "usuarioController" => ["login", "logout"],
    "clienteController" => ["cadastrar"],
    "contaController" => ["editar", "adicionarEndereco", "removerEndereco", "adicionarTelefone"],
    "planoController" => ["listar", "contratar"],
    "pedidoController" => ["checkout", "listar"],
    "dashboardController" => ["cliente"],
    "maquinaVirtualController" => ["salvarHostname", "excluir"],
    "adminController" => [
        "painel",
        "contas",
        "usuarios",
        "pedidos",
        "pagamentos",
        "planos",
        "planoForm",
        "salvarPlano",
        "excluirPlano",
        "alterarConta",
        "salvarUsuario",
        "salvarPedido",
        "salvarPagamento",
    ],
];

// Se o controller ou o metodo nao fizerem parte do mapa, devolvemos 404.
if (!isset($rotasPermitidas[$controle]) || !in_array($metodo, $rotasPermitidas[$controle], true)) {
    http_response_code(404);
    require_once "views/erro404.php";
    exit;
}

$arquivoController = "controllers/{$controle}.class.php";

// Uma rota cadastrada sem arquivo correspondente indica erro de implementacao.
if (!file_exists($arquivoController)) {
    http_response_code(500);
    $mensagemErro = "Controller nao encontrado.";
    require_once "views/erro.php";
    exit;
}

require_once $arquivoController;

// PHP ignora caixa em nomes de classe, o que mantem compatibilidade com temp_loja.
$obj = new $controle();
$obj->$metodo();

