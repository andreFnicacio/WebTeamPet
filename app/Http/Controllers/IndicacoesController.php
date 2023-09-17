<?php

namespace App\Http\Controllers;

use App\Models\Indicacoes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class IndicacoesController extends AppBaseController
{
    const DESCONTO = 10;
    /**
     * Validade em meses
     */
    const VALIDADE = 1;
    const ECOMMERCE = "https://www.lifepet.com.br";
    const INDICACOES_POR_VEZ = 3;

    public function indique()
    {
        return view('area_cliente.v2.indicar');
    }

    public static function is_array_empty($arr){
       if(is_array($arr)){
          foreach($arr as $value){
             if(!empty($value)){
                return false;
             }
          }
       }

       return true;
    }

    public function indicarVarios(Request $request)
    {
        $cliente = self::loggedClient();
        $emails = $request->get('email');
        $nomes = $request->get('nome');
        $telefones = $request->get('telefone');

        if(self::is_array_empty($emails)) {
            self::setError('Indique ao menos um email', 'Erro');
            return back();
        }

        for($i = 0; $i < self::INDICACOES_POR_VEZ; $i++) {
            $email = $emails[$i];
            $nome = $nomes[$i];
            $telefone = isset($telefones[$i]) ? $telefones[$i] : null;

//            dump($email);
//            dump($nome);
//            dump($telefone);
//            dd($request->all());

            if(!isset($email[$i])) {
                continue;
            }

            if(Indicacoes::where('email', $email)->exists()) {
                self::setError('O email ' . $email . ' já foi indicado', 'Email em uso!');
                return back();
            }
            $indicacao = Indicacoes::create([
                'nome' => $nome,
                'email' => $email,
                'telefone' => $telefone,
                'id_cliente' => $cliente->id,
            ]);

            $this->notifyIndicacao($indicacao, [
                'link' => self::ECOMMERCE,
                'corpo' => "",
                'nome_cliente' => $cliente->nome_cliente,
                'nome' => $indicacao->nome,
                'email' => $indicacao->email,
            ]);
        }

        self::setSuccess('Indicações feitas com sucesso', 'Parabéns');
        return redirect(route('indicacoes.listar'));
    }

    public function indicar(Request $request)
    {
        //$validade = (new Carbon())->addHours(self::VALIDADE);
        $cliente = self::loggedClient();
        $corpo = "";

        $indicacao = Indicacoes::create([
            'nome' => $request->get('nome'),
            'email' => $request->get('email'),
            'id_cliente' => $cliente->id,
        ]);

        $query = http_build_query([
            'indicacao' => $indicacao->id
        ]);

        $url = self::ECOMMERCE . "?" . $query;

        $this->notifyIndicacao($indicacao, [
            'link' => $url,
            'corpo' => $corpo,
            'nome' => $indicacao->nome
        ]);

        self::toast('Indicação feita com ', 'sucesso', 'font-green-meadow');
        return redirect(route('indicacoes.listar'));
    }

    public function listar()
    {
        $cliente = self::loggedClient();
        $indicacoes = Indicacoes::where('id_cliente', $cliente->id)->orderBy('updated_at')->get();

        return view('area_cliente.v2.lista_indicacoes', [
            'indicacoes' => $indicacoes
        ]);
    }

    public function notifyIndicacao($indicacao, array $dados = [])
    {
        Mail::send('mail.indicacao', $dados, function($message) use ($indicacao) {
            $message->to($indicacao->email)
                ->subject("Você foi indicado para a Lifepet!");
        });

        Mail::send('mail.indicacao_interno', $dados, function($message) use ($indicacao) {
            $message->to("comercial@lifepet.com.br")
                ->cc("cadastro@lifepet.com.br")
                ->subject("Nova indicação!");
        });

//        $to      = $indicacao->email;
//        $subject = "Você foi indicado para a Lifepet!";
//        $view  = view('mail.indicacao')->with($dados);
//        $message = $view->render();
//        $headers = 'From: Lifepet <contato@lifepet.com.br>' . "\r\n" .
//            "Reply-To: contato@lifepet.com.br \r\n";
//        $headers .= "MIME-Version: 1.0\r\n";
//        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n" .
//            'X-Mailer: PHP/' . phpversion();
//
//        mail($to, $subject, $message, $headers);
    }

    public static function simular(Request $request)
    {
        if(!$request->get('id_indicacao')) {
            return false;
        }

        $indicacao = Indicacoes::firstOrFail($request->get('id_indicacao'));
        $indicacao->simulada = new Carbon();
        return $indicacao->update();
    }

    public static function comprar(Request $request, $id_recebimento)
    {
        if(!$request->get('email')) {
            return self::sendError('Indicação não encontrada', 500);
        }

        $indicacao = Indicacoes::where('email',$request->get('email'))->first();
        $indicacao->comprado = new Carbon();
        $indicacao->id_superlogica = $id_recebimento;
        return $indicacao->update();
    }

    public static function pagar($id_superlogica)
    {
        $indicacao = Indicacoes::where('id_superlogica', $id_superlogica);
        if($indicacao) {
            $indicacao->pago = new Carbon();
        }

        return $indicacao->update();
    }

    public static function confirmar($email){
        $indicacao = Indicacoes::where('email', $email)->first();
        if($indicacao) {
            $agora = new Carbon();
            $indicacao->pago = $agora;
            $indicacao->comprado = $agora;
            $indicacao->simulado = $agora;
            $indicacao->update();
        }
    }
}
