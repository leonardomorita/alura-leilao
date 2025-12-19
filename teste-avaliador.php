<?php

use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Usuario;
use Alura\Leilao\Service\Avaliador;

require 'vendor/autoload.php';

// Padrões Arrange-Act-Assert e GivenWhenThen

// Cenário (Arrange) ou (Given)
$leilao = new Leilao('Fiat 147 0KM');

$maria = new Usuario('Maria');
$joao = new Usuario('João');

$leilao->recebeLance(new Lance($joao, 2000));
$leilao->recebeLance(new Lance($maria, 2500));

// Ação a ser testada (Act) ou (When)
$leiloeiro = new Avaliador();
$leiloeiro->avalia($leilao);

$maiorValor = $leiloeiro->getMaiorValor();
$valorEsperado = 2500;

// Verificação (Assert) ou (Then)
if ($valorEsperado == $maiorValor) {
    echo 'TESTE OK';
} else {
    echo 'TESTE FALHOU';
}
