<?php

namespace Alura\Leilao\Tests\Service;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Service\Encerrador;
use PHPUnit\Framework\TestCase;

class EncerradorTest extends TestCase
{
    public function testLeiloesComMaisDeUmaSemanaDevemSerEncerrados()
    {
        $fiat147 = new Leilao('Fiat 147 0KM', new \DateTimeImmutable('8 days ago'));
        $variant = new Leilao('Variant 1972 0KM', new \DateTimeImmutable('10 days ago'));

        $leilaoDaoMock = $this->createMock(LeilaoDao::class);

        $leilaoDaoMock->method('recuperarNaoFinalizados')
            ->willReturn([$fiat147, $variant]);

        $leilaoDaoMock->expects($this->exactly(2))
            ->method('atualiza')
            ->willReturnCallback(function (Leilao $leilao) use ($fiat147, $variant) {
                static $call = 0;

                if ($call === 0) {
                    $this->assertSame($fiat147, $leilao);
                } else if ($call === 1) {
                    $this->assertSame($variant, $leilao);
                }

                $call++;
            });
        
        // Act
        $encerrador = new Encerrador($leilaoDaoMock);
        $encerrador->encerra();

        // Assert
        $leiloesFinalizados = [$fiat147, $variant];

        self::assertTrue($leiloesFinalizados[0]->estaFinalizado());
        self::assertTrue($leiloesFinalizados[1]->estaFinalizado());
    }
}
