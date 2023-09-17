<?php

namespace Modules\Mobile\Http\Controllers;

use App\Helpers\Utils;
use App\Http\Controllers\AppBaseController;
use App\Models\Clientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Mobile\Entities\Push;
use Modules\Mobile\Jobs\SendPushNotifications;

class PusherController extends AppBaseController
{
    public function index(Request $request)
    {
        $pushes = Push::orderBy('id', 'DESC')->get();
        return view('mobile::pusher.index', ['pushes' => $pushes]);
    }

    public function create(Request $request) {
        return view('mobile::pusher.create');
    }

    public function check(Request $request)
    {
        /**
         * @var \Illuminate\Validation\Validator $v
         */
        $validator = Validator::make($request->all(), [
            'file' => 'file|required|mimes:csv,txt',
            'index' => 'required'
        ]);

        if ($validator->fails()) {
            $messages = join("\n", $validator->getMessageBag()->all());
            $messages = str_replace('file', 'O arquivo', $messages);
            self::setError($messages, 'Oops.');

            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $index = $request->get('index');

        $clientsEligible = [];

        try {
            $clients = Utils::csvToArray($request->file, ";");

            foreach($clients as $clientData) {
                $client = Clientes::where('nome_cliente', $clientData[$index])
                    ->whereNotNull('token_firebase')
                    ->first(['token_firebase', 'id', 'nome_cliente', 'ativo', 'email']);
                if ($client) {
                    $clientsEligible[] = $client;
                }

                $client = null;
            }

        } catch (\Exception $e) {
            self::setError("Não foi possível processar o push.\n{$e->getMessage()}");
            return redirect()->back();
        }

        //Garante a unicidade do cliente na lista removendo duplicados.
        $clientsEligible = collect($clientsEligible);
        $clientsEligible = $clientsEligible->unique('id');

        $pushClients = $clientsEligible->all();

        $request->session()->put(auth()->user()->id . '_open_push', $pushClients);

        if(count($clientsEligible) <= 0) {
            self::setError('Não foi possível encontrar clientes para o envio.');
            return redirect()->back();
        }

        return view('mobile::pusher.preview', ['clientes' => $clientsEligible, 'index' => $index]);
    }

    public function send(Request $request)
    {
        /**
         * @var \Illuminate\Validation\Validator $validator
         */
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'message' => 'required'
        ]);

        if ($validator->fails()) {
            $messages = join("\n", $validator->getMessageBag()->all());
            self::setError($messages, 'Oops.');

            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $clients = $request->session()->get(auth()->user()->id . '_open_push');

        $title = $request->get('title');
        $message = $request->get('message');

        $mappedClients = array_map(function(Clientes $c) {
            $mc = new \stdClass();
            $mc->id = $c->id;
            $mc->nome_cliente = $c->nome_cliente;
            $mc->email = $c->email;
            $mc->token_firebase = $c->token_firebase;
            $mc->ativo = $c->ativo;

            return $mc;
        }, $clients);

        $push = (new Push())->fill([
            'title' => $title,
            'message' => $message,
            'count' => count($clients),
            'status' => Push::STATUS_ABERTO,
            'progress' => 0,
            'meta' => json_encode($mappedClients),
            'author' => auth()->user()->id
        ]);

        if(!$push->checksum()) {
            self::setWarning('Um envio duplicado de pushes foi interrompido.');
            return redirect(route('mobile.pusher.index'));
        }

        $push->save();


        $job = (new SendPushNotifications($push, $clients));
        dispatch($job);

        self::setSuccess('Seu push foi enfileirado e será executado em breve. Atualize a página para acompanhar o progresso.');

        return redirect(route('mobile.pusher.index'));
    }
}
