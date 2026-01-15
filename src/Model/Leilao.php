<?php

namespace Alura\Leilao\Model;

class Leilao
{
    /** @var Lance[] */
    private $lances;
    /** @var string */
    private $descricao;

    public function __construct(string $descricao)
    {
        $this->descricao = $descricao;
        $this->lances = [];
    }

    public function recebeLance(Lance $lance)
    {
        if (count($this->lances) && $this->lanceEhDoUltimoUsuario($lance)) {
            return;
        }

        if ($this->quantidadeLancesPorUsuario($lance->getUsuario()) >= 5) {
            return;
        }

        $this->lances[] = $lance;
    }

    /**
     * @return Lance[]
     */
    public function getLances(): array
    {
        return $this->lances;
    }

    private function lanceEhDoUltimoUsuario(Lance $lance): bool
    {
        // $ultimoLance = $this->lances[count($this->lances) - 1];
        $ultimoLance = $this->lances[array_key_last($this->lances)];

        return $lance->getUsuario() == $ultimoLance->getUsuario();
    }

    /**
     * Retorna a quantidade de lances por um usuário específico.
     * 
     * @param Usuario $usuario Usuário a ser verificado.
     * 
     * @return int Quantidade de lances pelo usuário.
     */
    private function quantidadeLancesPorUsuario(Usuario $usuario): int
    {
        return array_reduce(
            $this->lances,
            function (int $totalAcumulado, Lance $lanceAtual) use ($usuario) {
                if ($lanceAtual->getUsuario() == $usuario) {
                    return $totalAcumulado + 1;
                }
                
                return $totalAcumulado;
            },
            0
        );
    }
}
