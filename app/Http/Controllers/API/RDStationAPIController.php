<?php

namespace App\Http\Controllers\API;
use Illuminate\Support\Facades\Log;
use App\Helpers\API\RDStation\Services\RDPetBirthdayService;

/**
 * Class RDStationMailAPIController
 * @package App\Http\Controllers\API
 */

class RDStationAPIController  {

    public function enviarEmailPetsAniversariantes() {

        try {
            $rdPetAniversarioService = new RDPetBirthdayService();
            $rdPetAniversarioService->process();

            return response()->json(["msg" => "Dados enviados com sucesso!"], 200);
        } catch (\Exception $e) {

            //Log::useDailyFiles(storage_path().'/logs/rd-station/pets-aniversarios/erros.log');
            Log::error($e->getMessage());
            return response()->json(["msg" => "Falha ao enviar os dados de aniversários de pet pra RD Station"], 400);
            
        }
       

    }
}