<?php
namespace App\Helpers\API\RDStation\v1_2;
use Illuminate\Support\Facades\Log;

class RDStationException extends \Exception {

    /**
     * Construtor
     *
     * @param string $msg
     */
    public function __construct($msg) {
        parent::__construct($msg);
        //Log::useDailyFiles(storage_path().'/logs/rd-station/v1_2/erros.log');
        Log::error($msg);
    }
}