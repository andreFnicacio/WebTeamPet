<?php
namespace App\Helpers\API\RDStation\Services;

use App\Helpers\API\RDStation\v1_2\RDStationConversionClient;
use Illuminate\Support\Facades\Log;
use App\Models\Clientes;
use App\Models\RDStationEnvio;

class RDSendBoletoAvulsoService {

    const IDENTIFIER = 'novo-boleto-avulso';

    private $finBoleto;
    private $cliente;

    public function __construct($finBoleto, Clientes $cliente) {
        $this->finBoleto = $finBoleto;
        $this->cliente = $cliente;
        //Log::useDailyFiles(storage_path().'/logs/rd-station/boletos-avulsos/enviados.log');
    }

    public function process() {
        $payload = $this->createPayload();
        $this->send($payload);
        $this->saveSent();

        Log::info($payload);
    }

    public function createPayload() {

        return [
            'identificador' => self::IDENTIFIER,
            'DataVencimento' => (new \DateTime($this->finBoleto->due_date))->format('d/m/Y'),
            'IdBoleto' => $this->finBoleto->hash,
            'ValorBoleto' => number_format($this->finBoleto->amount, 2, ',', '.'),
            'email' => $this->cliente->email,
            'name' => $this->cliente->nome_cliente
        ];
    }
    
    public function send($payload) {
        $client = new RDStationConversionClient();
        $client->request($payload);
    }

    public function saveSent() {

        $rdStationSent = new RDStationEnvio();
        $rdStationSent->fill([
            'tabela' => 'clientes',
            'tabela_id' => $this->cliente->id,
            'identificador' => self::IDENTIFIER,
            'descricao' => 'Boleto avulso para primeiro pagamento'
        ]);
        $rdStationSent->save();
        
    }
}