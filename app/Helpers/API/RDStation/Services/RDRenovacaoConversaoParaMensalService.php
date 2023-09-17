<?php
namespace App\Helpers\API\RDStation\Services;

use App\Helpers\API\RDStation\v1_2\RDStationConversionClient;
use App\Helpers\Utils;
use App\Http\Util\LogEvent;
use App\Http\Util\Logger;
use App\Http\Util\LogPriority;
use App\LifepetCompraRapida;
use App\LinkPagamento;
use App\Models\Renovacao;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Pets;
use App\Models\RDStationEnvio;

class RDRenovacaoConversaoParaMensalService {

    const IDENTIFIER = 'renovacoes-conversao-para-mensal';

    public function __construct() {
        //Log::useDailyFiles(storage_path().'/logs/rd-station/pets-aniversarios/enviados.log');
    }

    public function process(Renovacao $renovacao) {
        //Cliente
        $payload = $this->createPayload($renovacao);

        $logger = new Logger('renovacao', 'renovacao', 1);
        $dadosJson = $renovacao->toLog(['gatilho' => self::IDENTIFIER, 'payload' => $payload]);

        try {
            $this->send($payload);
            $logger->register(LogEvent::NOTIFY, LogPriority::LOW, "Acabamos de enviar um email de conversão automática de ANUAL para MENSAL para o cliente.\n$dadosJson", $renovacao->id);
        } catch (\Exception $exception) {
            $logger->register(LogEvent::ERROR, LogPriority::HIGH, "Não foi possível enviar o email de conversão automática de ANUAL para MENSAL.\n$dadosJson", $renovacao->id);
        }

        //Financeiro
        $payload = $this->createPayloadFinanceiro($renovacao);
        $this->send($payload);

        $this->saveSent($renovacao);
    }


    public function createPayload(Renovacao $renovacao) {
       $valorBruto = $renovacao->valor_bruto;
        if($renovacao->valor_bruto === $renovacao->valor_original && $renovacao->reajuste > 0) {
            $valorBruto = $valorBruto * (1 + $renovacao->reajuste);
        }

       $data = [
            'identificador' => self::IDENTIFIER,
            'renovacao' => $renovacao->id,
            'email' => $renovacao->cliente->email,
            'renovacao__nome_pet' => $renovacao->pet->nome_pet,
            'renovacao__nome_cliente' => $renovacao->pet->cliente->nome_cliente,
            'renovacao__data_conversao' => Carbon::now()->format(Utils::BRAZILIAN_DATE),
            'renovacao__novo_valor' => "R$ " . number_format($valorBruto / 12, 2, ',', '')
        ];

        return $data;
    }

    public function createPayloadFinanceiro(Renovacao $renovacao) {
        $data = [
            'identificador' => self::IDENTIFIER . '-financeiro',
            'renovacao' => $renovacao->id,
            'email' => 'financeiro@lifepet.com.br',
            'renovacao__nome_pet' => $renovacao->pet->nome_pet,
            'renovacao__nome_cliente' => $renovacao->pet->cliente->nome_cliente,
            'renovacao__data_conversao' => Carbon::now()->format(Utils::BRAZILIAN_DATE),
            'renovacao__novo_valor' => "R$ " . number_format($renovacao->valor / 12, 2, ',', '')
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