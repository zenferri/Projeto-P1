<?php

class BaseController
{
    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../views/' . $view . '.php';
    }

    protected function redirect(string $path): void
    {
        $target = rtrim(BASE_URL, '/') . $path;
        if ($target === '') {
            $target = '/';
        }
        header('Location: ' . $target);
        exit;
    }
}
