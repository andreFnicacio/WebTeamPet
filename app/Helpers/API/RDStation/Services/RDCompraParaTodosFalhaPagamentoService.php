<?php
namespace App\Helpers\API\RDStation\Services;

use App\Helpers\API\RDStation\v1_2\RDStationConversionClient;
use App\LifepetCompraRapida;
use App\Models\Planos;
use Illuminate\Support\Facades\Log;
use App\Models\Pets;
use App\Models\RDStationEnvio;

class RDCompraParaTodosFalhaPagamentoService {

    const DEFAULT_IDENTIFIER = 'erro-pagamento-paratodos';
    protected $identificador = null;

    public function __construct(Planos $plano) {
        //Log::useDailyFiles(storage_path().'/logs/rd-station/pets-aniversarios/enviados.log');
        $this->identificador = self::DEFAULT_IDENTIFIER;
        if($plano->configuracao) {
            $this->identificador = $plano->configuracao->rd__gatilho_erro_pagamento ?: $this->identificador;
        }
    }

    public function process(LifepetCompraRapida $compraRapida) {
        $payload = $this->createPayload($compraRapida);

        $this->send($payload);
        $this->saveSent($compraRapida);
    }


    public function createPayload(LifepetCompraRapida $compraRapida) {
        return [
            'identificador' => $this->identificador,
            'plano' => $compraRapida->id_plano,
            'email' => $compraRapida->email,
            'nome' => $compraRapida->nome,
            'CODIGO_HASH_PARATODOS' => $compraRapida->hash
        ];
    }
    
    public function send($payload) {
        $client = new RDStationConversionClient();
        $client->request($payload);
    }

    public function saveSent(LifepetCompraRapida $compraRapida) {
        $rdStationSent = new RDStationEnvio();
        $rdStationSent->fill([
            'tabela' => 'lifepet_compra_rapida',
            'tabela_id' => $compraRapida->id,
            'identificador' => $this->identificador
        ]);
        $rdStationSent->save();
    }
}