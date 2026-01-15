<?php

namespace Alura\Leilao\Model;

class Leilao
{
    public const LISTA_MENSAGENS_DE_ERRO = [
        'usuario-nao-pode-propor-2-lances-consecutivos' => 'Usuário não pode propor 2 lances consecutivos.',
        'usuario-fez-5-lances' => 'Usuário não pode propor mais de 5 lances por leilão.'
    ];

    /** @var Lance[] */
    private $lances;

    /** @var string */
    private $descricao;

    /** @var bool */
    private $finalizado;

    public function __construct(string $descricao)
    {
        $this->descricao = $descricao;
        $this->lances = [];
        $this->finalizado = false;
    }

    public function recebeLance(Lance $lance)
    {
        if (count($this->lances) && $this->lanceEhDoUltimoUsuario($lance)) {
            throw new \DomainException($this::LISTA_MENSAGENS_DE_ERRO['usuario-nao-pode-propor-2-lances-consecutivos']);
        }

        if ($this->quantidadeLancesPorUsuario($lance->getUsuario()) >= 5) {
            throw new \DomainException($this::LISTA_MENSAGENS_DE_ERRO['usuario-fez-5-lances']);
        }

        $this->lances[] = $lance;
    }

    public function finaliza()
    {
        $this->finalizado = true;     
    }

    /**
     * @return Lance[]
     */
    public function getLances(): array
    {
        return $this->lances;
    }

    public function getFinalizado(): bool
    {
        return $this->finalizado;
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
