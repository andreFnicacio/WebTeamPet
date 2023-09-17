<?php

namespace App\Http\Controllers;

use App\Helpers\API\Getnet\Service;
use App\Pix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PixController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new Service();
    }

    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount'            => 'numeric|min:0.01|required',
            'order_id'          => 'string|required',
            'customer_id'       => 'numeric|required|exists:clientes,id',
            'callback_url'      => 'url|required',
            'local_description' => 'text|required'
        ]);

        if($validator->fails()) {
            $messages = "\n" . join("\n", $validator->getMessageBag()->all());
            abort(500, 'Um ou mais campos obrigatórios não cumprem os requisitos de validação.' . $messages);
        }
        list($amount, $order_id, $customer_id) = [$request->get('amount'), $request->get('order_id'), $request->get('customer_id')];

        $pixData = $this->service->makePix($amount, $order_id, $customer_id);

        $localData = (object) [
            'id_cliente'        => $customer_id,
            'local_description' => $request->get('local_description'),
            'callback_url'      => $request->get('callback_url')
        ];
        $pix = Pix::adapt($pixData, $localData);

        return $pix;
    }

    public function confirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_type'      => 'string|required',
            'order_id'          => 'required',
            'customer_id'       => 'required',
            'payment_id'        => 'required',
            'amount'            => 'required',
            'status'            => 'required',
            'transaction_id'    => 'required',
            'transaction_timestamp'    => 'required',
            'receiver_psp_name' => 'required',
            'receiver_psp_code' => 'required',
            'receiver_name'     => 'required',
            'terminal_nsu'      => 'required',
        ]);

        if($validator->fails()) {
            $messages = "\n" . join("\n", $validator->getMessageBag()->all());
            abort(500, 'Um ou mais campos obrigatórios não cumprem os requisitos de validação.' . $messages);
        }


    }
}
