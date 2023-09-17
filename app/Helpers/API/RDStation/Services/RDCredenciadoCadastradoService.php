<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 03/11/2021
 * Time: 13:17
 */

namespace App\Helpers\API\RDStation\Services;


use App\Helpers\API\RDStation\v1_2\RDStationConversionClient;
use App\Models\RDStationEnvio;
use Modules\Clinics\Entities\Clinicas;

class RDCredenciadoCadastradoService
{
    const IDENTIFIER = 'credenciado-cadastrado';

    public function __construct() {
        //Log::useDailyFiles(storage_path().'/logs/rd-station/pets-aniversarios/enviados.log');
    }

    public function process(Clinicas $clinica) {
        $payload = $this->createPayload($clinica);

        $this->send($payload);
        $this->saveSent($clinica);
    }


    public function createPayload(Clinicas $clinica) {
        $data = [
            'identificador' => self::IDENTIFIER,
            'id' => $clinica->id,
            'nome' => $clinica->nome_clinica,
            'email' =>  $clinica->email_contato,
            'estado' => $clinica->estado,
            'cidade' => $clinica->cidade,
            'ativo' => $clinica->ativo ? 'SIM' : 'NÃO'
        ];

        return $data;
    }

    public function send($payload) {
        $client = new RDStationConversionClient();
        return $client->request($payload);
    }

    public function saveSent(Clinicas $clinica) {
        $rdStationSent = new RDStationEnvio();
        $rdStationSent->fill([
            'tabela' => 'clinicas',
            'tabela_id' => $clinica->id,
            'identificador' => self::IDENTIFIER
        ]);

        $rdStationSent->save();
    }
}