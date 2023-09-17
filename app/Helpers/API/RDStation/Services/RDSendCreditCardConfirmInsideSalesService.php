<?php
namespace App\Helpers\API\RDStation\Services;

use App\Helpers\API\RDStation\v1_2\RDStationConversionClient;
use Illuminate\Support\Facades\Log;
use App\Models\Clientes;
use App\Models\RDStationEnvio;

class RDSendCreditCardConfirmInsideSalesService {

    const IDENTIFIER = 'comprou-cartao';

    private $valor;
    private $dataVencimento;
    private $cliente;

    public function __construct($valor, $dataVencimento, Clientes $cliente) {
        $this->valor = $valor;
        $this->dataVencimento = $dataVencimento;
        $this->cliente = $cliente;
        //Log::useDailyFiles(storage_path().'/logs/rd-station/cartao-confirm-inside-sales/enviados.log');
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
            'DataVencimento' => (new \DateTime($this->dataVencimento))->format('d/m/Y'),
            'ValorComprouCartao' => number_format($this->valor, 2, ',', '.'),
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
            'descricao' => 'Inside Sales - Confirmação de compra com cartão de crédito'
        ]);
        $rdStationSent->save();
        
    }
}