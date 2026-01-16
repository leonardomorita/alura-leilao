<?php

namespace Alura\Leilao\Tests\Service;

use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Usuario;
use Alura\Leilao\Service\Avaliador;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AvaliadorTest extends TestCase
{
    private Avaliador $avaliador;

    protected function setUp(): void
    {
        $this->avaliador = new Avaliador();
    }

    public static function entregaLeiloes()
    {
        return [
            'ordem-crescente' => [self::leilaoComLancesEmOrdemCrescente()],
            'ordem-decrescente' => [self::leilaoComLancesEmOrdemDecrescente()],
            'ordem-aleatoria' => [self::leilaoComLancesEmOrdemAleatoria()]
        ];
    }

    public static function leilaoComLancesEmOrdemCrescente(): Leilao
    {
        // Cenário (Arrange) ou (Given)

        $leilao = new Leilao('Fiat 147 0KM');

        $joao = new Usuario('João');
        $maria = new Usuario('Maria');
        $ana = new Usuario('Ana');

        $leilao->recebeLance(new Lance($joao, 1000));
        $leilao->recebeLance(new Lance($maria, 1500));
        $leilao->recebeLance(new Lance($ana, 2000));

        return $leilao;
    }

    public static function leilaoComLancesEmOrdemDecrescente(): Leilao
    {
        // Cenário (Arrange) ou (Given)

        $leilao = new Leilao('Fiat 147 0KM');

        $joao = new Usuario('João');
        $maria = new Usuario('Maria');
        $ana = new Usuario('Ana');

        $leilao->recebeLance(new Lance($ana, 2000));
        $leilao->recebeLance(new Lance($maria, 1500));
        $leilao->recebeLance(new Lance($joao, 1000));

        return $leilao;
    }

    public static function leilaoComLancesEmOrdemAleatoria(): Leilao
    {
        // Cenário (Arrange) ou (Given)

        $leilao = new Leilao('Fiat 147 0KM');

        $joao = new Usuario('João');
        $maria = new Usuario('Maria');
        $ana = new Usuario('Ana');

        $leilao->recebeLance(new Lance($maria, 1500));
        $leilao->recebeLance(new Lance($ana, 2000));
        $leilao->recebeLance(new Lance($joao, 1000));

        return $leilao;
    }

    #[DataProvider('entregaLeiloes')]
    public function testAvaliadorDeveAcharMaiorValor(Leilao $leilao)
    {
        // Padrões Arrange-Act-Assert e GivenWhenThen

        // Ação a ser testada (Act) ou (When)
        $this->avaliador->avalia($leilao);

        // Verificação (Assert) ou (Then)
        static::assertEquals(2000, $this->avaliador->getMaiorValor());
    }

    #[DataProvider('entregaLeiloes')]
    public function testAvaliadorDeveAcharMenorValor(Leilao $leilao)
    {
        // Padrões Arrange-Act-Assert e GivenWhenThen

        // Ação a ser testada (Act) ou (When)
        $this->avaliador->avalia($leilao);

        // Verificação (Assert) ou (Then)
        static::assertEquals(1000, $this->avaliador->getMenorValor());
    }

    #[DataProvider('entregaLeiloes')]
    public function testAvaliadorDeveOrdenarOs3Lances(Leilao $leilao)
    {
        // Padrões Arrange-Act-Assert e GivenWhenThen

        // Ação a ser testada (Act) ou (When)
        $this->avaliador->avalia($leilao);

        // Verificação (Assert) ou (Then)
        $lances = $this->avaliador->getTresMaioresLances();

        static::assertCount(3, $lances);
        static::assertEquals(2000, $lances[0]->getValor());
        static::assertEquals(1500, $lances[1]->getValor());
        static::assertEquals(1000, $lances[2]->getValor());
    }

    public function testAvaliadorDeveRetornarOsMaioresLancesDisponiveis()
    {
        // Padrões Arrange-Act-Assert e GivenWhenThen

        // Cenário (Arrange) ou (Given)
        $leilao = new Leilao('Fiat 147 0KM');

        $leilao->recebeLance(new Lance(new Usuario('João'), 1000));
        $leilao->recebeLance(new Lance(new Usuario('Maria'), 1500));

        // Ação a ser testada (Act) ou (When)
        $this->avaliador->avalia($leilao);

        // Verificação (Assert) ou (Then)
        static::assertCount(2, $this->avaliador->getTresMaioresLances());
    }
}
