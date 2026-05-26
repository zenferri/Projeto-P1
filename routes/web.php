<?php

$routes = [
    '/|GET' => ['HomeController', 'index'],
    '/home|GET' => ['HomeController', 'index'],
    '/contratar|GET' => ['HomeController', 'cadastro'],
    '/cadastro|GET' => ['HomeController', 'cadastro'],
    '/login|GET' => ['HomeController', 'login'],
    '/carrinho|GET' => ['OrderController', 'cart'],
    '/dashboard|GET' => ['HomeController', 'dashboard'],
    '/equipe|GET' => ['HomeController', 'equipe'],
    '/termos|GET' => ['HomeController', 'termos'],
    '/termosuso|GET' => ['HomeController', 'termos'],
    '/register|POST' => ['AuthController', 'register'],
    '/login|POST' => ['AuthController', 'login'],
    '/logout|GET' => ['AuthController', 'logout'],
    '/checkout|POST' => ['OrderController', 'checkout'],
];
