<?php

namespace Alura\Leilao\Tests\Service;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Service\Encerrador;
use PHPUnit\Framework\TestCase;

class LeilaoDaoMock extends LeilaoDao
{
    private $leiloes = [];

    public function salva(Leilao $leilao): void
    {
        $this->leiloes[] = $leilao;
    }

    public function recuperarNaoFinalizados(): array
    {
        return array_filter($this->leiloes, function (Leilao $leilao) {
            return !$leilao->estaFinalizado();
        });
    }

    public function recuperarFinalizados(): array
    {
        return array_filter($this->leiloes, function (Leilao $leilao) {
            return $leilao->estaFinalizado();
        });
    }

    public function atualiza(Leilao $leilao) {}
}

class EncerradorTest extends TestCase
{
    public function testLeiloesComMaisDeUmaSemanaDevemSerEncerrados()
    {
        $fiat147 = new Leilao('Fiat 147 0KM', new \DateTimeImmutable('8 days ago'));
        $variant = new Leilao('Variant 1972 0KM', new \DateTimeImmutable('10 days ago'));

        $leilaoDaoMock = new LeilaoDaoMock();

        $leilaoDaoMock->salva($fiat147);
        $leilaoDaoMock->salva($variant);

        // Act
        $encerrador = new Encerrador($leilaoDaoMock);
        $encerrador->encerra();

        // Assert
        $leiloesFinalizados = $leilaoDaoMock->recuperarFinalizados();

        self::assertCount(2, $leiloesFinalizados);
        self::assertEquals('Fiat 147 0KM', $leiloesFinalizados[0]->recuperarDescricao());
        self::assertEquals('Variant 1972 0KM', $leiloesFinalizados[1]->recuperarDescricao());
    }
}
