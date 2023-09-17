<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class Welcome extends Mailable
{
    const NAORESPONDA_LIFEPET_COM_BR = 'noreply@lifepet.com.br';

    use Queueable;

    private $customerName;
    private $planName;

    public function __construct($customerName, $planName)
    {
        $this->customerName = $customerName;
        $this->planName = $planName;
    }

    public function build()
    {
        return $this->view('mail.welcome')
            ->from(self::NAORESPONDA_LIFEPET_COM_BR, 'Lifepet')
            ->subject("Seja bem vindo a Lifepet")
            ->with([
                'customerName' => $this->customerName,
                'planName' => $this->planName,
            ])
            ->attach(storage_path('docs/contrato_lifepet.pdf'), [
                'as' => 'Contrato Lifepet.pdf',
                'mime' => 'application/pdf',
            ])
            ->attach(storage_path('docs/tabela_copart.pdf'), [
                'as' => 'Tabela Coparticipação.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}