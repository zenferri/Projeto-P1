<?php

$routes = [
    '/|GET' => ['HomeController', 'index'],
    '/cadastro|GET' => ['HomeController', 'cadastro'],
    '/carrinho|GET' => ['OrderController', 'cart'],
    '/dashboard|GET' => ['HomeController', 'dashboard'],
    '/equipe|GET' => ['HomeController', 'equipe'],
    '/termos|GET' => ['HomeController', 'termos'],
    '/register|POST' => ['AuthController', 'register'],
    '/login|POST' => ['AuthController', 'login'],
    '/logout|GET' => ['AuthController', 'logout'],
    '/checkout|POST' => ['OrderController', 'checkout'],
];
