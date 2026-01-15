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
    private Avaliador $leiloeiro;

    protected function setUp(): void
    {
        $this->leiloeiro = new Avaliador();
    }

    public static function entregaLeiloes()
    {
        return [
            'ordem-crescente' => [self::leilaoEmOrdemCrescente()],
            'ordem-decrescente' => [self::leilaoEmOrdemDecrescente()],
            'ordem-aleatoria' => [self::leilaoEmOrdemAleatoria()]
        ];
    }

    public static function leilaoEmOrdemCrescente(): Leilao
    {
        // Cenário (Arrange) ou (Given)

        $leilao = new Leilao('Fiat 147 0KM');

        $maria = new Usuario('Maria');
        $joao = new Usuario('João');
        $ana = new Usuario('Ana');
        $jorge = new Usuario('Jorge');

        $leilao->recebeLance(new Lance($joao, 1000));
        $leilao->recebeLance(new Lance($ana, 1500));
        $leilao->recebeLance(new Lance($jorge, 1700));
        $leilao->recebeLance(new Lance($maria, 2000));

        return $leilao;
    }

    public static function leilaoEmOrdemDecrescente(): Leilao
    {
        // Cenário (Arrange) ou (Given)

        $leilao = new Leilao('Fiat 147 0KM');

        $maria = new Usuario('Maria');
        $joao = new Usuario('João');
        $ana = new Usuario('Ana');
        $jorge = new Usuario('Jorge');

        $leilao->recebeLance(new Lance($maria, 2000));
        $leilao->recebeLance(new Lance($jorge, 1700));
        $leilao->recebeLance(new Lance($ana, 1500));
        $leilao->recebeLance(new Lance($joao, 1000));

        return $leilao;
    }

    public static function leilaoEmOrdemAleatoria(): Leilao
    {
        // Cenário (Arrange) ou (Given)

        $leilao = new Leilao('Fiat 147 0KM');

        $maria = new Usuario('Maria');
        $joao = new Usuario('João');
        $ana = new Usuario('Ana');
        $jorge = new Usuario('Jorge');

        $leilao->recebeLance(new Lance($ana, 1500));
        $leilao->recebeLance(new Lance($joao, 1000));
        $leilao->recebeLance(new Lance($maria, 2000));
        $leilao->recebeLance(new Lance($jorge, 1700));

        return $leilao;
    }

    #[DataProvider('entregaLeiloes')]
    public function testAvaliadorDeveEncontrarOMaiorValorDeLances(Leilao $leilao)
    {
        // Padrões Arrange-Act-Assert e GivenWhenThen

        // Ação a ser testada (Act) ou (When)
        $this->leiloeiro->avalia($leilao);

        // Verificação (Assert) ou (Then)
        $maiorValor = $this->leiloeiro->getMaiorValor();
        self::assertEquals(2000, $maiorValor);
    }

    #[DataProvider('entregaLeiloes')]
    public function testAvaliadorDeveEncontrarOMenorValorDeLances(Leilao $leilao)
    {
        // Padrões Arrange-Act-Assert e GivenWhenThen

        // Ação a ser testada (Act) ou (When)
        $this->leiloeiro->avalia($leilao);

        // Verificação (Assert) ou (Then)
        $menorValor = $this->leiloeiro->getMenorValor();
        self::assertEquals(1000, $menorValor);
    }

    #[DataProvider('entregaLeiloes')]
    public function testAvaliadorDeveBuscar3MaioresValores(Leilao $leilao)
    {
        // Padrões Arrange-Act-Assert e GivenWhenThen

        // Ação a ser testada (Act) ou (When)
        $this->leiloeiro->avalia($leilao);

        // Verificação (Assert) ou (Then)
        $maioresValores = $this->leiloeiro->getMaioresLances();
        self::assertCount(3, $maioresValores);
        self::assertEquals(2000, $maioresValores[0]->getValor());
        self::assertEquals(1700, $maioresValores[1]->getValor());
        self::assertEquals(1500, $maioresValores[2]->getValor());
    }

    public function testLeilaoVazioNaoPodeSerAvaliado()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage(Avaliador::LISTA_MENSAGENS_DE_ERRO['leilao-vazio']);

        $leilao = new Leilao('Fusca Azul');
        $this->leiloeiro->avalia($leilao);
    }

    public function testLeilaoFinalizadoNaoPodeSerAvaliado()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage(Avaliador::LISTA_MENSAGENS_DE_ERRO['leilao-finalizado']);

        $leilao = new Leilao('Fiat 147 0KM');
        $leilao->recebeLance(new Lance(new Usuario('Teste'), 2000));
        $leilao->finaliza();

        $this->leiloeiro->avalia($leilao);
    }
}
