<?php
namespace App\Helpers\API\RDStation\Services;

use App\Helpers\API\RDStation\v1_2\RDStationConversionClient;
use App\Http\Util\LogEvent;
use App\Http\Util\Logger;
use App\Http\Util\LogPriority;
use App\LifepetCompraRapida;
use App\LinkPagamento;
use App\Models\Renovacao;
use Illuminate\Support\Facades\Log;
use App\Models\Pets;
use App\Models\RDStationEnvio;

class RDRenovacaoConfirmadaService {

    const IDENTIFIER = 'renovacao-confirmada';

    public function __construct() {
        //Log::useDailyFiles(storage_path().'/logs/rd-station/pets-aniversarios/enviados.log');
    }

    public function process(Renovacao $renovacao) {
        $payload = $this->createPayload($renovacao);
        $logger = new Logger('renovacao', 'renovacao', 1);
        $dadosJson = $renovacao->toLog(['gatilho' => self::IDENTIFIER]);

        try {
            $this->send($payload);
            $this->saveSent($renovacao);
            $logger->register(LogEvent::NOTIFY, LogPriority::LOW, "Acabamos de enviar um email de confirmação de renovação para o cliente.\n$dadosJson", $renovacao->id);
        } catch (\Exception $exception) {
            $logger->register(LogEvent::ERROR, LogPriority::HIGH, "Não foi possível enviar o email de confirmação da renovação.\n$dadosJson", $renovacao->id);
        }
    }


    public function createPayload(Renovacao $renovacao) {
        $data = [
            'identificador' => self::IDENTIFIER,
            'renovacao' => $renovacao->id,
            'renovacao__nome_pet' => $renovacao->pet->nome_pet,
            'renovacao__nome_cliente' => $renovacao->cliente->nome_cliente,
            'email' => $renovacao->cliente->email,
            'renovacao__reajuste' => $renovacao->reajuste,
            'renovacao__regime' => $renovacao->regime,
        ];

        return $data;
    }
    
    public function send($payload) {
        $client = new RDStationConversionClient();
        $client->request($payload);
    }

    public function saveSent(Renovacao $renovacao) {
        $rdStationSent = new RDStationEnvio();
        $rdStationSent->fill([
            'tabela' => 'renovacoes',
            'tabela_id' => $renovacao->id,
            'identificador' => self::IDENTIFIER
        ]);
        $rdStationSent->save();
    }
}