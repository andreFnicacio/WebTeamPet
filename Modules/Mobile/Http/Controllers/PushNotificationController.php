<?php

namespace Modules\Mobile\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Models\Clientes;
use Illuminate\Http\Request;
use Modules\Mobile\Services\PushNotificationService;

class PushNotificationController extends AppBaseController
{
    public function create() {
        return view('mobile::push.notifications');
    }

    public function send(Request $request) {

        $clientes = (new Clientes())->whereIn('id', $request->get('id_clientes'))->get();

        $title = $request->get('titulo');
        $msg = $request->get('corpo');

        $data = [];

        $clientes->map(function ($cliente) use ($title, $msg, $data){
            $pushNotification = (new PushNotificationService($cliente, $title, $msg, $data));
            $pushNotification->send();
        });

        return redirect(route('mobile.push.notifications'));
    }

    public function sendTest(Request $request) {
        $clientId = $request->get('id_cliente');

        if (empty($clienteId)) {
            self::setError("id_cliente é obrigatório");
            return redirect()->back();
        }

        $cliente = (new Clientes())->where('id', $clientId)->first();

        $title = $request->get('titulo');
        $msg = $request->get('corpo');

        $pushNotification = (new PushNotificationService($cliente, $title, $msg, []));
        $pushNotification->send();

        return redirect(route('mobile.push.notifications'));
    }

    public function search(Request $request) {
        $input = $request->all();
        $clientes = (new Clientes())->where('ativo', 1)->whereNotNull('token_firebase');

        if (isset($input['cliente']['sexo'])) {
            $clientes->where('sexo', $input['cliente']['sexo']);
        }
        if (isset($input['cliente']['nome'])) {
            $clientes->where('nome_cliente', 'LIKE' , '%'.$input['cliente']['nome'].'%');
        }

        if (isset($input['pet']['sexo'])) {
            $clientes->whereHas('pets', function ($query) use ($input) {
                $query->where('sexo', $input['pet']['sexo']);
            });
        }

        if (isset($input['pet']['nome'])) {
            $clientes->whereHas('pets', function ($query) use ($input) {
                $query->where('nome_pet', 'LIKE' , '%'.$input['pet']['nome'].'%');
            });
        }

        return $clientes->get();
    }
}
