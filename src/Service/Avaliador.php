<?php

namespace Alura\Leilao\Service;

use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;

class Avaliador
{
    private $maiorValor = -INF;
    private $menorValor = INF;
    private $maioresLances;

    public function avalia(Leilao $leilao): void
    {
        $lances = $leilao->getLances();

        foreach ($lances as $lance) {
            $valorDoLance = $lance->getValor();
            if ($valorDoLance > $this->maiorValor) {
                $this->maiorValor = $valorDoLance;
            } 
            
            if ($valorDoLance < $this->menorValor) {
                $this->menorValor = $valorDoLance;
            }
        }

        usort($lances, function (Lance $lance1, Lance $lance2) {
            return $lance2->getValor() - $lance1->getValor();
        });

        $this->maioresLances = array_slice($lances, 0, 3);
    }

    public function getMaiorValor(): float
    {
        return $this->maiorValor;
    }

    public function getMenorValor(): float
    {
        return $this->menorValor;
    }

    /**
     * @return Lance[]
     */
    public function getMaioresLances(): array
    {
        return $this->maioresLances;
    }
}
