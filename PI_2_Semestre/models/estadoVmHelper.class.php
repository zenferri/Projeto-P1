<?php
class EstadoVmHelper
{
    public static function rotulo(string $estado): string
    {
        $mapa = [
            "aguardando_configuracao" => "Aguardando configuração e provisionamento",
            "aguardando_provisionamento" => "Aguardando provisionamento",
            "em_criacao" => "Em criação",
            "ativa" => "Ativa",
            "destruida" => "Destruída",
        ];

        return $mapa[$estado] ?? ucfirst(str_replace("_", " ", $estado));
    }
}
