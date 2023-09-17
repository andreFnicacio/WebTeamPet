<?php

namespace App\Http\Controllers;

use App\Models\ComunicadosCredenciados;
use Illuminate\Http\Request;
use Mail;
use vendor\swiftmailer\swiftmailer\lib\classes\Swift\Transport;

class ComunicadosCredenciadosController extends AppBaseController
{
    public function create(Request $request)
    {
        return view('comunicados_credenciados.new');
    }

    public function save(Request $request)
    {
        /**
         * @var $comunicados_credenciados ComunicadosCredenciados
         */
        $data = $request->all();
        $data['corpo']=trim($data['corpo'],"\'\"");
        $data['published_at']= (new \Carbon\Carbon)::now();
        $comunicados_credenciados = ComunicadosCredenciados::create($data);
        $emails_clinicas = \Modules\Clinics\Entities\Clinicas::where('ativo', "=", '1')
                ->where('aceite_urh', "=", '1')->where('email_contato', "!=", '')->pluck('email_contato')->toArray();
        $data1 = [
            'comunicado' => $comunicados_credenciados,
            'emails_clinicas' => $emails_clinicas
        ];
        Mail::send('mail.credenciados.comunicado', $data1, function ($message) use ($emails_clinicas) {
            $message->to('credenciados@lifepet.com.br');
            $message->bcc($emails_clinicas);
            $message->subject("Lifepet - Novo Comunicado");
        });

        self::setSuccess('Comunicado registrado!');

        return redirect(route('comunicados_credenciados.listar'));
    }

    public function listar(Request $request)
    {
        $comunicados_credenciados = ComunicadosCredenciados::all();
        return view('comunicados_credenciados.index')->with([
            'comunicados_credenciados' => $comunicados_credenciados
        ]);
    }
}
