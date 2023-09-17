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

class RDRenovacaoMensalService {

    const IDENTIFIER = 'renovacao-mensal-criada';

    public function __construct() {
        //Log::useDailyFiles(storage_path().'/logs/rd-station/pets-aniversarios/enviados.log');
    }

    public function process(Renovacao $renovacao, array $valores) {
        $payload = $this->createPayload($renovacao, $valores);
        $logger = new Logger('renovacao', 'renovacao', 1);
        $dadosJson = $renovacao->toLog(['gatilho' => self::IDENTIFIER, 'payload' => $payload]);

        try {
            $this->send($payload);
            $logger->register(LogEvent::NOTIFY, LogPriority::LOW, "Acabamos de enviar um email de conversão automática de ANUAL para MENSAL para o cliente.\n$dadosJson", $renovacao->id);
        } catch (\Exception $exception) {
            $logger->register(LogEvent::ERROR, LogPriority::HIGH, "Não foi possível enviar o email de conversão automática de ANUAL para MENSAL.\n$dadosJson", $renovacao->id);
        }


        $this->saveSent($renovacao);
    }


    public function createPayload(Renovacao $renovacao, array $valores) {
        /**
         * @var LinkPagamento $link
         */
        $link = $renovacao->link;

        $data = [
            'identificador' => self::IDENTIFIER,
            'renovacao' => $renovacao->id,
            'email' => $renovacao->cliente->email,
            'renovacao__nome_pet' => $renovacao->pet->nome_pet,
            'renovacao__nome_cliente' => $renovacao->cliente->nome_cliente,
            'renovacao__regime' => $renovacao->regime,
            'renovacao__desconto' => $valores['desconto'],
            'renovacao__percentual_reajuste' => number_format($valores['percentual_reajuste'], 2, ',', ''),
            'renovacao__valor_reajuste' => number_format($valores['valor'] - $valores['valor_original'], 2, ',', ''),
            'renovacao__valor_final' => number_format($valores['valor'], 2, ',', ''),
            'renovacao__valor_original' => $valores['valor_original'],
            'renovacao__parcelas' => $valores['parcelas'],
            'renovacao__link_pagamento' => $link->link(),
            'renovacao__valor_bruto' => number_format($valores['valor']/(1 - floatval($valores['desconto']/100)), 2) //Valor sem desconto
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