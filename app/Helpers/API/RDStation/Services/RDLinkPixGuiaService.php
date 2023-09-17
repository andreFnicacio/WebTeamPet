<?php

namespace App\Helpers\API\RDStation\Services;

use App\Helpers\API\RDStation\v1_2\RDStationConversionClient;
use App\LinkPagamento;
use App\Models\RDStationEnvio;
use App\Pix;

class RDLinkPixGuiaService
{
    const IDENTIFIER = 'link-pagamento-pix';

    public function __construct()
    {
        //Log::useDailyFiles(storage_path().'/logs/rd-station/pets-aniversarios/enviados.log');
    }

    public function process(Pix $pix)
    {
        $payload = $this->createPayload($pix);

        $this->send($payload);
        $this->saveSent($pix);
    }


    public function createPayload(Pix $pix)
    {
        return [
            'identificador' => self::IDENTIFIER,
            'id_pix' => $pix->id,
            'valor' => $pix->amount,
            'qr_code' => $pix->qr_code,
            'email' => $pix->cliente->email,
            'nome' => $pix->cliente->nome_cliente,
        ];
    }

    public function send($payload)
    {
        $client = new RDStationConversionClient();
        $client->request($payload);
    }

    public function saveSent(Pix $pix)
    {
        $rdStationSent = new RDStationEnvio();
        $rdStationSent->fill([
            'tabela' => 'pix',
            'tabela_id' => $pix->id,
            'identificador' => self::IDENTIFIER
        ]);
        $rdStationSent->save();
    }
}