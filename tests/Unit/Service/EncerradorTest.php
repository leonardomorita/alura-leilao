<?php

namespace Alura\Leilao\Tests\Unit\Service;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Service\Encerrador;
use Alura\Leilao\Service\EnviadorEmail;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EncerradorTest extends TestCase
{
    /** @var Encerrador $encerrador */
    private $encerrador;

    /** @var MockObject&EnviadorEmail $enviadorEmail */
    private $enviadorEmail;

    /** @var Leilao $leilaoFiat147 */
    private $leilaoFiat147;

    /** @var Leilao $leilaoVariant */
    private $leilaoVariant;

    protected function setUp(): void
    {
        // Arrange
        $this->leilaoFiat147 = new Leilao('Fiat 147 0KM', new \DateTimeImmutable('8 days ago'));
        $this->leilaoVariant = new Leilao('Variant 1972 0KM', new \DateTimeImmutable('10 days ago'));

        $leilaoDao = $this->createMock(LeilaoDao::class);

        // Criar um mock personalizado para o LeilaoDao. Dessa maneira, podemos atribuir argumentos no construtor da classe.
        // $leilaoDao = $this->getMockBuilder(LeilaoDao::class)
        //     ->setConstructorArgs([new \PDO('sqlite::memory:')])
        //     ->getMock();

        $leilaoDao->method('recuperarNaoFinalizados')
            ->willReturn([$this->leilaoFiat147, $this->leilaoVariant]);

        $leilaoDao->expects($this->exactly(2))
            ->method('atualiza')
            ->willReturnCallback(function (Leilao $leilao) {
                static $call = 0;

                if ($call === 0) {
                    $this->assertSame($this->leilaoFiat147, $leilao);
                } else if ($call === 1) {
                    $this->assertSame($this->leilaoVariant, $leilao);
                }

                $call++;
            });
        
        $this->enviadorEmail = $this->createMock(EnviadorEmail::class);
        
        $this->encerrador = new Encerrador($leilaoDao, $this->enviadorEmail);
    }

    public function testLeiloesComMaisDeUmaSemanaDevemSerEncerrados()
    {
        // Act
        $this->encerrador->encerra();

        // Assert
        $leiloesFinalizados = [$this->leilaoFiat147, $this->leilaoVariant];

        self::assertTrue($leiloesFinalizados[0]->estaFinalizado());
        self::assertTrue($leiloesFinalizados[1]->estaFinalizado());
    }

    public function testDeveContinuarOProcessamentoAoEncontrarErroAoEnviarEmail()
    {
        $e = new \DomainException(EnviadorEmail::MENSAGENS_ERRO['erro-ao-enviar-email']);
        $this->enviadorEmail->expects($this->exactly(2))
            ->method('notificarTerminoLeilao')
            ->willThrowException($e);

        $this->encerrador->encerra();
    }

    public function testSoDeveEnviarEmailLeilaoTerminoAposFinalizado()
    {
        $this->enviadorEmail->expects($this->exactly(2))
            ->method('notificarTerminoLeilao')
            ->willReturnCallback(function (Leilao $leilao) {
                self::assertTrue($leilao->estaFinalizado());
            });

        $this->encerrador->encerra();
    }
}
