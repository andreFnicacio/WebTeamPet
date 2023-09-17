<?php


namespace App\Helpers\API\Zenvia\Webhook;


use App\Helpers\GamificationCredenciados;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class Adapter
{
    public static function routes()
    {
        self::receberRespostaSMS();
    }

    private static function receberRespostaSMS()
    {
        Route::post('/zenvia/respostaSms', function (Request $request) {
            $data = $request->callbackMoRequest;

            $sms = Sms::where('id', $data->id);
            if($sms) {
                $sms->response = $data->body;
                $sms->update();
            }

            if($sms->finalidade === "avaliacao_credenciado") {
                self::registrarAvaliacaoCredenciado($sms);
            }
        })->name('zenvia.resposta');
    }

    private static function registrarAvaliacaoCredenciado($sms)
    {
        if($sms->historicoUso) {
            $filteredNumbers = array_filter(preg_split("/\D+/", $sms->response));
            $firstOccurence = reset($filteredNumbers);
            $gamification = new GamificationCredenciados($sms->numero_guia);
            $gamification->applyGameficationAvaliacaoCredenciado($firstOccurence);
        } else {
            return false;
        }
    }
}