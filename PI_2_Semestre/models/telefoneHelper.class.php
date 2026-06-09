<?php
class TelefoneHelper
{
    /**
     * Valida DDD e número brasileiros e retorna o tipo para o banco (fixo|celular).
     */
    public static function validarBrasil(string $ddd, string $numero): array
    {
        $ddd = preg_replace("/\D/", "", $ddd);
        $numero = preg_replace("/\D/", "", $numero);

        if (strlen($ddd) < 2 || strlen($ddd) > 3) {
            return ["ok" => false, "msg" => "DDD invalido."];
        }

        if (strlen($numero) === 9 && $numero[0] === "9") {
            return [
                "ok" => true,
                "ddd" => $ddd,
                "numero" => $numero,
                "tipo" => "celular",
            ];
        }

        if (strlen($numero) === 8) {
            return [
                "ok" => true,
                "ddd" => $ddd,
                "numero" => $numero,
                "tipo" => "fixo",
            ];
        }

        return [
            "ok" => false,
            "msg" => "Numero invalido: celular deve ter 9 digitos comecando com 9; fixo deve ter 8 digitos.",
        ];
    }

    public static function rotuloTipo(string $tipo): string
    {
        return $tipo === "celular" ? "Celular" : "Fixo";
    }
}
