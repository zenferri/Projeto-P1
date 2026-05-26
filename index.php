<?php
session_start();

// Autoload básico de classes do projeto
spl_autoload_register(function ($class) {
    $paths = [__DIR__ . '/controllers', __DIR__ . '/models', __DIR__ . '/config'];
    foreach ($paths as $path) {
        $file = $path . '/' . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

require_once __DIR__ . '/routes/web.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$baseUrl = $basePath === '/' ? '' : $basePath;
define('BASE_URL', $baseUrl);

$path = '/' . trim(substr($requestUri, strlen($basePath)), '/');
if ($path === '//' || $path === '') {
    $path = '/';
}

if (($path === '/index.php' || $path === '/index.php/') && !empty($_GET['page'])) {
    $pageName = trim(strtolower($_GET['page']), '/');
    $path = $pageName === '' ? '/' : '/' . $pageName;
}

$method = $_SERVER['REQUEST_METHOD'];
$routeKey = $path . '|' . $method;

if (!isset($routes[$routeKey])) {
    if (isset($routes[$path . '|GET'])) {
        $routeKey = $path . '|GET';
    } elseif (!empty($_GET['page'])) {
        $pageAlias = '/' . trim(strtolower($_GET['page']), '/') . '|GET';
        if (isset($routes[$pageAlias])) {
            $routeKey = $pageAlias;
        }
    }
}

if (!isset($routes[$routeKey])) {
    http_response_code(404);
    require __DIR__ . '/views/404.php';
    exit;
}

[$controllerName, $action] = $routes[$routeKey];
$controller = new $controllerName();
$controller->{$action}();
