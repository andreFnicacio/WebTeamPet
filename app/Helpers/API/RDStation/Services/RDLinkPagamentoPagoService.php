<?php
namespace App\Helpers\API\RDStation\Services;

use App\Helpers\API\RDStation\v1_2\RDStationConversionClient;
use App\LifepetCompraRapida;
use App\LinkPagamento;
use Illuminate\Support\Facades\Log;
use App\Models\Pets;
use App\Models\RDStationEnvio;

class RDLinkPagamentoPagoService {

    const IDENTIFIER = 'link-pagamento-pago';

    public function __construct() {
        //Log::useDailyFiles(storage_path().'/logs/rd-station/pets-aniversarios/enviados.log');
    }

    public function process(LinkPagamento $link) {
        $payload = $this->createPayload($link);

        $this->send($payload);
        $this->saveSent($link);
    }


    public function createPayload(LinkPagamento $link) {
        return [
            'identificador' => self::IDENTIFIER,
            'id_link_pagamento' => $link->id,
            'valor' => $link->valor,
            'email' => $link->cliente->email,
            'nome' => $link->cliente->nome,
        ];
    }
    
    public function send($payload) {
        $client = new RDStationConversionClient();
        $client->request($payload);
    }

    public function saveSent(LinkPagamento $link) {
        $rdStationSent = new RDStationEnvio();
        $rdStationSent->fill([
            'tabela' => 'links_pagamento',
            'tabela_id' => $link->id,
            'identificador' => self::IDENTIFIER
        ]);
        $rdStationSent->save();
    }
}