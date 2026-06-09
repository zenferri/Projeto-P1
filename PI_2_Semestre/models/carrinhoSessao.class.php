<?php

class CarrinhoSessao
{
    private const CHAVE = "carrinho"; // Chave da sessão para o carrinho

    public static function definirPlano(string $slugPlano): void // o void não é necessário, mas é uma boa prática. Ele serve para indicar que a função não retorna nada.
    {
        $slug = strtolower(trim($slugPlano));
        if ($slug === "") {
            return;
        }

        $_SESSION[self::CHAVE] = [
            "plano_slug" => $slug,
            "adicionado_em" => time(),
        ];
    }

    public static function obterSlug(): ?string // o ?string não é necessário, mas é uma boa prática. Ele serve para indicar que a função pode retornar null.
    {
        $slug = $_SESSION[self::CHAVE]["plano_slug"] ?? null; 
        return is_string($slug) && $slug !== "" ? $slug : null; // se o slug for uma string e não for vazio, retorna o slug, caso contrário, retorna null.
    }

    public static function possuiItem(): bool // o bool não é necessário, mas é uma boa prática. Ele serve para indicar que a função retorna um booleano.
    {
        return self::obterSlug() !== null; // se o slug não for null, retorna true, caso contrário, retorna false.
    }

    public static function limpar(): void
    {
        unset($_SESSION[self::CHAVE]);
    }
}
