<?php
namespace App\Helpers\API\RDStation\Services;

use App\Helpers\API\RDStation\v1_2\RDStationConversionClient;
use Illuminate\Support\Facades\Log;
use App\Models\Pets;
use App\Models\RDStationEnvio;

class RDPetBirthdayService {

    const IDENTIFIER = 'aniversario-pet';

    public function __construct() {
        //Log::useDailyFiles(storage_path().'/logs/rd-station/pets-aniversarios/enviados.log');
    }

    public function process() {
        $month=date("m");
        $day=date("d");
        
        $birthdayPets = $this->getBirthdayPets($month, $day);
    
        foreach($birthdayPets as $pet) {

            if($this->checkAlreadySent($pet)) {
                continue;
            }

            $payload = $this->createPayload($pet);
            
            Log::info($payload);

            $this->send($payload);
            $this->saveSent($pet);

        }

    }

    public function getBirthdayPets(int $month, int $day) {
        $petBirthdays = Pets::where('ativo', true)
            ->whereRaw("MONTH(data_nascimento)={$month} AND DAY(data_nascimento)={$day}")
            ->get();
        return $petBirthdays;
    }

    public function checkAlreadySent(Pets $pet) {
        $sent = RDStationEnvio::where('identificador', self::IDENTIFIER)
            ->where('tabela', 'pets')
            ->where('tabela_id', $pet->id)
            ->whereRaw("YEAR(created_at) = YEAR(CURDATE())")
            ->count();

        if($sent > 0) {
            return true;
        }

        return false;
    }

    public function createPayload(Pets $pet) {
        return [
            'identificador' => self::IDENTIFIER,
            'pet_anivers_data' => $pet->data_nascimento->format('d/m/Y'),
            'pet_anivers_nome' => $pet->nome_pet,
            'pet_anivers_sexo' => $pet->sexo,
            'pet_anivers_tipo' => $pet->tipo,
            'email' => $pet->cliente->email,
            'name' => $pet->cliente->nome_cliente
        ];
    }
    
    public function send($payload) {
        $client = new RDStationConversionClient();
        $client->request($payload);
    }

    public function saveSent(Pets $pet) {

        $rdStationSent = new RDStationEnvio();
        $rdStationSent->fill([
            'tabela' => 'pets',
            'tabela_id' => $pet->id,
            'identificador' => self::IDENTIFIER
        ]);
        $rdStationSent->save();
        
    }
}