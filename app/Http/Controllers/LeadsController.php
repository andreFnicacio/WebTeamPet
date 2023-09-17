<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Leads;

class LeadsController extends Controller
{
    private function previous($email)
    {
        return Leads::where('email', $email)->
                      orderBy('id', "DESC")->
                      first();
    }
    public function store(Request $request)
    {
        IndicacoesController::simular($request);

        $email = $request->get('email');
        $origem = $request->get('origem');
        $dados = $request->get('dados');
        $id_vinculo = null;
        $previous = $this->previous($email);
        if($previous) {
            if($previous->origem == $origem) {
                return [
                    "status"  => false,
                    "message" => "Lead já cadastrado"
                ];
            }

            $id_vinculo = $previous->id;
        }

        return Leads::create([
            'timestamps' => false,
            'email' => $email,
            'dados' => $dados,
            'origem' => $origem,
            'id_vinculo' => $id_vinculo
        ]);
    }

    public function converter(Request $request) {
        $email = $request->get('email');

        return Leads::where('email', $email)->
        where('convertido', null)->
        update([
            'convertido' => new Carbon()
        ]);
    }

    public function atender(Request $request) {
        $email = $request->get('email');

        return Leads::where('email', $email)->
        where('atendido', null)->
        update([
            'atendido' => new Carbon()
        ]);
    }
}
