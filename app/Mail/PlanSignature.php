<?php

namespace App\Mail;

use App\Models\Clientes;
use App\Models\Pets;
use App\Models\Planos;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PlanSignature extends Mailable
{
    use Queueable, SerializesModels;

    private $plano, $pet;
    public $attachments;

    const SUBJECT = 'Bem-vindo(a) à Lifepet!';

    /**
     * Create a new message instance.
     *
     * @param Planos $plano
     * @param Pets $pet
     * @return void
     */
    public function __construct(Planos $plano, Pets $pet)
    {
        $this->attachments = [];
        $this->plano = $plano;
        $this->pet = $pet;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->view('mail.plans.signature')->with([
            'plano' => $this->plano,
            'pet' => $this->pet
        ]);
        $this->to($this->pet->cliente->email);
        $this->subject(self::SUBJECT);

        //Add attachments;
        $this->attachTabela();
        $this->attachContrato();

        return $mail;
    }

    private function attachTabela()
    {
        $tabela = $this->plano->tabela;
        if(!$tabela) {
            return false;
        }
        $realPath = storage_path('app/' . $tabela->path);
        if(!file_exists($realPath)) {
            return false;
        }

        return $this->attach($realPath, [
            'as' => $tabela->original_name,
            'mime' => $tabela->mime,
        ]);
    }

    private function attachContrato()
    {
        $contrato = $this->plano->contrato;
        if(!$contrato) {
            return false;
        }
        $realPath = storage_path('app/' . $contrato->path);
        if(!file_exists($realPath)) {
            return false;
        }

        return $this->attach($realPath, [
            'as' => $contrato->original_name,
            'mime' => $contrato->mime,
        ]);
    }
}
