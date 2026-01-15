<?php

namespace Alura\Leilao\Tests\Models;

use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Usuario;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LeilaoTest extends TestCase
{
    #[DataProvider('geraLances')]
    public function testLeilaoDeveReceberLances(
        int $quantidadeLances, Leilao $leilao, array $valores
    ) {
        self::assertCount($quantidadeLances, $leilao->getLances());

        foreach($valores as $i => $valor) {
            self::assertEquals($valor, $leilao->getLances()[$i]->getValor());
        }
    }

    public function testLeilaoNaoDeveReceberLancesRepetidos()
    {
        $leilao = new Leilao('Variante');
        $ana = new Usuario('Ana');

        $leilao->recebeLance(new Lance($ana, 1000));
        $leilao->recebeLance(new Lance($ana, 1500));

        self::assertCount(1, $leilao->getLances());
        self::assertEquals(1000, $leilao->getLances()[0]->getValor());
    }

    public function testLeilaoNaoDeveAceitarMaisDe5LancesPorUsuario()
    {
        $leilao = new Leilao('Brasília Amarela');

        $joao = new Usuario('João');
        $maria = new Usuario('Maria');

        $leilao->recebeLance(new Lance($joao, 1000));
        $leilao->recebeLance(new Lance($maria, 1500));
        $leilao->recebeLance(new Lance($joao, 2000));
        $leilao->recebeLance(new Lance($maria, 2500));
        $leilao->recebeLance(new Lance($joao, 3000));
        $leilao->recebeLance(new Lance($maria, 3500));
        $leilao->recebeLance(new Lance($joao, 4000));
        $leilao->recebeLance(new Lance($maria, 4500));
        $leilao->recebeLance(new Lance($joao, 5000));
        $leilao->recebeLance(new Lance($maria, 5500));

        $leilao->recebeLance(new Lance($joao, 6000));

        self::assertCount(10, $leilao->getLances());
        self::assertEquals(5500, $leilao->getLances()[count($leilao->getLances()) - 1]->getValor());
    }

    public static function geraLances()
    {
        $joao = new Usuario('João');
        $maria = new Usuario('Maria');

        $leilaoCom2Lances = new Leilao('Fiat 147 0KM');
        $leilaoCom2Lances->recebeLance(new Lance($joao, 1000));
        $leilaoCom2Lances->recebeLance(new Lance($maria, 2000));

        $leilaoCom1Lance = new Leilao('Fusca 1970');
        $leilaoCom1Lance->recebeLance(new Lance($maria, 5000));

        return [
            '2-lances' => [2, $leilaoCom2Lances, [1000, 2000]],
            '1-lance' => [1, $leilaoCom1Lance, [5000]]
        ];
    }
}
