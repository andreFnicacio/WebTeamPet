<?php
namespace App\Helpers\API\RDStation\Services;

use App\Helpers\API\RDStation\v1_2\RDStationConversionClient;
use App\LifepetCompraRapida;
use App\LinkPagamento;
use App\Models\Cancelamento;
use App\Models\Renovacao;
use Illuminate\Support\Facades\Log;
use App\Models\Pets;
use App\Models\RDStationEnvio;

class RDCancelamentoAutomaticoService {

    const IDENTIFIER = 'cancelamento-automatico-inadimplencia';

    public function __construct() {
        //Log::useDailyFiles(storage_path().'/logs/rd-station/pets-aniversarios/enviados.log');
    }

    public function process(Cancelamento $cancelamento) {
        $payload = $this->createPayload($cancelamento);

        $this->send($payload);
        $this->saveSent($cancelamento);
    }


    public function createPayload(Cancelamento $cancelamento) {
        $data = [
            'identificador' => self::IDENTIFIER,
            'cancelamento' => $cancelamento->id,
            'nome_pet' => $cancelamento->pet->nome_pet,
            'nome_cliente' => $cancelamento->pet->cliente->nome_cliente,
            'email' =>  $cancelamento->pet->cliente->email,
            'data_cancelamento' => $cancelamento->created_at->format('d/m/Y')
        ];

        return $data;
    }
    
    public function send($payload) {
        $client = new RDStationConversionClient();
        return $client->request($payload);
    }

    public function saveSent(Cancelamento $cancelamento) {
        $rdStationSent = new RDStationEnvio();
        $rdStationSent->fill([
            'tabela' => 'cancelamentos',
            'tabela_id' => $cancelamento->id,
            'identificador' => self::IDENTIFIER
        ]);
        $rdStationSent->save();
    }
}