<?php
/*
 * Entidade Plano do catalogo comercial (tabela planos_vps).
 */
class Plano
{
    public function __construct(
        private int $idPlano = 0,
        private string $slug = "",
        private string $nome = "",
        private float $precoMensal = 0.0,
        private int $vcpu = 0,
        private int $ramMb = 0,
        private int $storageGb = 0,
        private int $bandaMbps = 100,
        private string $situacao = "ativo"
    ) {}

    public function getIdPlano() { return $this->idPlano; }
    public function getSlug() { return $this->slug; }
    /** Alias usado nas views e rotas (?plano=slug). */
    public function getCodigo() { return $this->slug; }
    public function getNome() { return $this->nome; }
    public function getPrecoMensal() { return $this->precoMensal; }
    public function getVcpu() { return $this->vcpu; }
    public function getRamMb() { return $this->ramMb; }
    public function getArmazenamentoGb() { return $this->storageGb; }
    public function getLarguraBandaMbps() { return $this->bandaMbps; }
    public function getSituacao() { return $this->situacao; }

    // Funções auxiliares para formatar os dados para a view - MB para GB
    public function getRamGb()
    {
        return (int) round($this->ramMb / 1024); // converte RAM de MB (banco) para view em GB.
    }

    public function getPrecoFormatado()
    {
        return "R$ " . number_format($this->precoMensal, 2, ",", ".");
    }

    // Função para retornar o catalogo inicial de planos
    public static function catalogoInicial()
    {
        return [
            new Plano(1, "vps-start", "VPS Start", 49.90, 1, 1024, 20, 100),
            new Plano(2, "vps-core", "VPS Core", 89.90, 2, 2048, 40, 100),
            new Plano(3, "vps-pro", "VPS Pro", 159.90, 4, 4096, 80, 200),
        ];
    }

    public static function buscarNoCatalogoInicial(string $slug)
    {
        foreach (self::catalogoInicial() as $plano) {
            if ($plano->getSlug() === strtolower($slug)) {
                return $plano;
            }
        }

        return null;
    }
}
