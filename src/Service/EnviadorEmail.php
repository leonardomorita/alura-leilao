<?php

namespace Alura\Leilao\Service;

use Alura\Leilao\Model\Leilao;

class EnviadorEmail
{
    const MENSAGENS_ERRO = [
        'erro-ao-enviar-email' => 'Erro ao enviar o e-mail.'
    ];

    /**
     * Envia e-mail informando o término do leilão.
     * 
     * @param Leilao $leilao O leilão que foi finalizado.
     * 
     * @throws \DomainException Se ocorrer um erro ao enviar o e-mail.
     * 
     * @return void
     */
    public function notificarTerminoLeilao(Leilao $leilao): void
    {
        $sucesso = mail(
            'usuario@email.com',
            'Leilão finalizado',
            'O leilão para ' . $leilao->recuperarDescricao() . ' foi finalizado.'
        );

        if (!$sucesso) {
            throw new \DomainException(self::MENSAGENS_ERRO['erro-ao-enviar-email']);
        }
    }
}
