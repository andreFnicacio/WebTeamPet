<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Nps;
use App\Http\Controllers\AppBaseController;

class NpsAPIController extends AppBaseController
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'nota' => 'integer|required|min:0|max:10',
            'origem' => 'string|required|in:web,mobile,zendesk,email,whatsapp,ticket,surveyMonkey'
        ]);

        $input = $request->all();

        try {
            $nps = Nps::create($input);
        } catch (Exception $e) {
            return response()->json(["msg" => $e->getMessage()], $e->getCode());
        }

        return response()->json(["msg" => "Obrigado pela avaliação!"], 200);
    }

    public function publicStore(Request $request)
    {
        $this->validate($request, [
            'nota' => 'integer|required|min:0|max:10',
            'origem' => 'string|required|in:web,mobile,zendesk,email,whatsapp,ticket,surveyMonkey'
        ]);


        if(!$request->filled('nps_token') || !session()->get('nps_token') || $request->nps_token != session()->get('nps_token')) {
            return response()->json(["msg" => "Acesso negado!"], 200);
        }

        $input = $request->all();

        try {
            $nps = Nps::create($input);
        } catch (Exception $e) {
            return response()->json(["msg" => $e->getMessage()], $e->getCode());
        }

        return response()->json(["msg" => "Obrigado pela avaliação!"], 200);
    }

    public function getNps()
    {
        $npsGlobal = Nps::getNpsGlobal();

        return response()->json([
            "nps" => $npsGlobal['nps'],
            "status" => $npsGlobal['status']
        ], 200);
    }

    public function getToken(Request $request) {
        $token = md5(uniqid(rand(), true));
        $request->session()->put('nps_token', $token);
        return response()->json(['nps_token' => $request->session()->get('nps_token')]);
    }

    public function verificarCliente(Request $request) {
        if(!$request->filled('nps_token') || !session()->get('nps_token') || $request->nps_token != session()->get('nps_token')) {
            return response()->json(["msg" => "Acesso negado!"], 200);
        }

        $this->validate($request, [
            'email' => 'required|email',
        ]);

        
    }
}
