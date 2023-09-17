<?php

namespace App\Http\Controllers;

use App\Helpers\API\Superlogica\Client;
use App\Helpers\API\Superlogica\Invoice;
use App\Helpers\API\Superlogica\Plans;
use App\Models\Clientes;
use App\Models\Indicacoes;
use App\Models\Pets;
use App\Models\PetsPlanos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Session;
use Mail;

class AjudesController extends AppBaseController
{
    const ID_PLANO_FACIL = 32;
    const VALOR_ADESAO = 0.00;
    const CODIGO_MENSALIDADE = 999999982;
    const CODIGO_ADESAO = 999999983;

    public function index() {
        return view('ajudes.index');
    }

    public function indicar(Request $request)
    {
        //$indicacoes = $request->get('indicacao');
        //foreach($indicacoes as $indicacao) {
          //  $dadosIndicacao = [
          //      'id_cliente' => $request->get('idCliente'),
        //        'nome' => $indicacao['nome_amigo'],
        //        'email' => $indicacao['email_amigo'],
       //         'telefone' => $indicacao['celular_amigo'],
       //     ];
//
        //    if(!Indicacoes::where('email', $dadosIndicacao['email'])->exists()) {
      //          $i = Indicacoes::create($dadosIndicacao);
       //     }

    //        self::notifyIndicacao($dadosIndicacao, $dadosIndicacao['email']);
       // }
//
        return redirect(route('ajudes.sucesso'));
    }

    public function indicacoes($idCliente)
    {
        return view('ajudes.indicacoes', ['idCliente' => $idCliente]);
    }


    public function sucesso()
    {
        return view('ajudes.sucesso');
    }

    public static function cadastrarSuperlogica(Request $request)
    {
        //Realizar pagamento
        $clienteData = $request->get('cliente');

        if(Clientes::where('cpf', $clienteData['cpf'])->exists()) {
            $error = "Você já possui um cadastro conosco. Por enquanto este plano está disponível apenas para novos clientes. Você pode aderir o plano com outro CPF.";
            return view('ajudes.index', ['error' => $error]);
        }

        $idUsuarioSuperlogica = null;
        $pago = self::assinar($request, $idUsuarioSuperlogica);

        self::notifyContinuacao([
            'nome' => $clienteData['nome_cliente'],
            'quantidade_pets' => $request->get('pets'),
            'email' => $clienteData['email'],
            'telefone' => $clienteData['celular'],
            'dadosIndicacao' => $request->get('indicacao')
        ], $clienteData['email']);


        if($request->filled('indicacao')) {
            $indicacao = $request->get('indicacao');
            self::notifyIndicacao([
                'nome' => $indicacao['nome']
            ], $indicacao['email']);
        }

        return view('ajudes.sucesso');
    }

    public function cadastrar(Request $request)
    {
        //TODO: Campanha encerra o cadastro automático no app por enquanto
        return self::cadastrarSuperlogica($request);

        //Com pagamento confirmado, cadastrar cliente com status pendente
        $cpf = preg_replace( '/[^0-9]/', '', $clienteData["cpf"]);
        $cliente = Clientes::create([
            'nome_cliente' => $clienteData["nome_cliente"],
            'cpf' => $cpf,
            'rg' => isset($clienteData["rg"]) ? $clienteData["rg"] : "",
            'data_nascimento' => $clienteData["data_nascimento"],
            'cep' => $clienteData["cep"],
            'rua' => $clienteData["rua"],
            'numero_endereco' => $clienteData["numero_endereco"],
            'complemento_endereco' => $clienteData["complemento"],
            'bairro' => $clienteData["bairro"],
            'cidade' => $clienteData["cidade"],
            'estado' => $clienteData["estado"],
            'telefone_fixo' => isset($clienteData["telefone_fixo"]) ? $clienteData["telefone_fixo"] : "",
            'celular' => $clienteData["celular"],
            'email' => $clienteData["email"],
            'ativo' => 0,
            'id_externo' => $idUsuarioSuperlogica,
            'sexo' => $clienteData["sexo"] === "Masculino" ? "M" : "F",
            'estado_civil' => 'SOLTEIRO',
            'primeiro_acesso' => 1,
            'participativo' => 1,
            'aprovado' => 0,
            'grupo' => 'CAMPANHA FÁCIL'
        ]);

        if(!$cliente) {
            return [
                'message' => "Houve um erro na criação do seu usuário. Por favor, entre em contato com nosso atendimento.",
                'status' => false
            ];
        }

        //Cadastrar Pets
        $pets = [];
        foreach ($request->get('pets') as $p) {
            $pets[] = $pet = Pets::create([
                'nome_pet' => $p['nome_pet'],
                'numero_microchip' => '',
                'tipo' => $p['tipo'],
                'raca' => $p['raca'],
                'contem_doenca_pre_existente' => empty($p['doencas_pre_existentes']),
                'doencas_pre_existentes' => $p['doencas_pre_existentes'],
                'familiar' => count($request->get('pets')) ? 1 : 0,
                'id_cliente' => $cliente->id,
                'ativo' => 0
            ]);

            $pp = PetsPlanos::create([
                'id_pet' => $pet->id,
                'id_plano' => self::ID_PLANO_FACIL,
                'valor_momento' => 0,
                'data_inicio_contrato' => (new \Carbon\Carbon())->format('d/m/Y'),
                'participativo' => 1
            ]);
        }

        //Enviar email de confirmação
        self::notifyAprovacao([
            'nome' => $cliente->nome_cliente
        ], $cliente->email);
        //Enviar email de notificação de novo cadastro
        self::notifyNovoCliente([
            'nome' => $cliente->nome_cliente,
            'pets' => count($pets),
        ]);

        //Fazer upload dos documentos.
        foreach($request->file('cliente') as $file) {
            self::upload($file, $cliente->id);
        }

        //Redirecionar para a tela de indicações

        return redirect(route('ajudes.indicacoes', $cliente->id));
    }

    public function cadastro()
    {
        return view('ajudes.cadastro');
    }

    public static function assinar(Request $request, &$idUsuarioSuperlogica)
    {
        $all = $request->all();
        $cliente = $request->get('cliente');

        $dadosCliente = [
            'telefone' =>  $cliente["celular"],
            'nome' => $cliente["nome_cliente"],
            'cpf' => $cliente['cpf'],
            'email' => $cliente["email"],
        ];
        $dadosCartao = $request->get('cartao');
        $validadeMes = explode('/', $dadosCartao['validade'])[0];
        $validadeAno = explode('/', $dadosCartao['validade'])[1];

        $dadosCartao['validade_mes'] = $validadeMes;
        $dadosCartao['validade_ano'] = $validadeAno;

        $quantidades = $request->get('pets',1 );

        $usuarioSuperlogica = self::createSuperlogicaUser($dadosCliente, $dadosCartao);

        if($usuarioSuperlogica->status === "500") {
            return abort(500, $usuarioSuperlogica->msg);
        }

        $idUsuarioSuperlogica = $usuarioSuperlogica->data->id_sacado_sac;

        $planoId = self::ID_PLANO_FACIL;

        $plano = \App\Models\Planos::find($planoId);
        $idPlanoSuperlogica = $plano->id_superlogica_anual;

        //ANUIDADE(3)
        $idProduto = "3";

        $preco = self::VALOR_ADESAO * $quantidades;

        $dados =  [
            'PLANOS' => [],
        ];

        for($i = 0; $i < $quantidades; $i++) {
            $identificador = "PLANO_" . $plano->nome_plano .  '_VD_Ecommerce' . time();
            $dados['PLANOS'][] = [
                'ID_SACADO_SAC' => $usuarioSuperlogica->data->id_sacado_sac,
                'DT_CONTRATO_PLC' => (new Carbon())->format('m/d/Y'),
                "ST_IDENTIFICADOREXTRA_PLC" => $identificador,
                "ST_IDENTIFICADOR_PLC" => $identificador,
                "FL_NOTIFICARCLIENTE" => 1,
                "ID_PLANO_PLA" => $idPlanoSuperlogica,
                "FL_TRIAL_PLC" => 1,
            ];
        }

        $PlansManager = new Plans();
        $response = $PlansManager->sign($dados);

        if(is_array($response)) {
            $response = $response[0];
        }

        if($response->status == "200") {

            $now = new Carbon();

            //TODO: Não está sendo cobrada a adesão.
            if(!$now->gt(Carbon::createFromFormat('Y-m-d H:i', '2018-05-11 23:59'))) {
                return [
                    "signed" => true,
                    "assinatura" => $response,
                ];
            }

            //Cobrar a adesão
          //  $invoiceManager = new Invoice();

           //$cliente = new Clientes();
          //  $cliente->id_externo = $idUsuarioSuperlogica;


           // $result = $invoiceManager->charge($cliente, $preco, null, "Adesão", self::CODIGO_ADESAO);

           // if(is_array($result)) {
                //$result = $result[0];
          //  }


         //   if($result->status == "200") {
             //   return [
               //     "signed" => true,
              //      "assinatura" => $response
            //    ];
          //  } else {
              // return [
               //     "signed" => false,
               //     "assinatura" => $response,
              //      "adesao" => $result
             //   ];
           // }
       // } else if ($response->msg == "Cobrança não atingiu o valor mínimo para geração.") {
       //     return [
         //       "signed" => false,
        //        "response" => $response,
        //        'message' => "Cobrança não atingiu o valor mínimo para geração."
       //     ];
       // } else {
      //      return [
      //          "signed" => false,
       //         "response" => $response
      //      ];
        

            }
    }

    public static function upload(UploadedFile $file, $idCliente, $description = "")
    {
        if($file->isValid()) {
            $extension = $file->extension();
            $size = $file->getClientSize();
            $public = 1;
            $mime = $file->getClientMimeType();
            $originalName = $file->getClientOriginalName();

            $path = $file->store('uploads/ajudes/'.$idCliente);
            $upload = \App\Models\Uploads::create([
                'original_name' => $originalName,
                'mime'          => $mime,
                'description'   => $description,
                'extension'     => $extension,
                'size'          => $size,
                'public'        => $public,
                'path'          => $path,
                'bind_with'     => 'clientes',
                'binded_id'     => $idCliente,
            ]);
            if($upload) {
                //self::setSuccess('Arquivo carregado com sucesso.');
                return true;
            }
        } else {
            return false;
        }
    }

    public static function createSuperlogicaUser($dadosCliente, $dadosCartao) {
        $postData = [
            'ST_TELEFONE_SAC' => $dadosCliente['telefone'] ,
            'ST_NOME_SAC' => $dadosCliente["nome"],
            'ST_NOMEREF_SAC' => $dadosCliente["nome"],
            'ST_CGC_SAC' => $dadosCliente['cpf'],
            'ST_EMAIL_SAC' => $dadosCliente["email"],
            'ID_GRUPO_GRP' => 1,
        ];
        $infoPagamento = [
            'ST_CARTAO_SAC' => preg_replace('/\s+/', '', $dadosCartao["numero_cartao"]),
            'ST_MESVALIDADE_SAC' => $dadosCartao["validade_mes"],
            'ST_ANOVALIDADE_SAC' => $dadosCartao["validade_ano"],
            'ST_SEGURANCACARTAO_SAC' => $dadosCartao["cvv"],
            'FL_PAGAMENTOPREF_SAC' => 3
        ];

        /**
         * Caso o cliente já exista, modifica os dados de pagamento e retorna os dados do cliente.
         */
        if(Client::exists($dadosCliente["email"])) {
            $client = (new Client)->get([
                'pesquisa' => "todosemails:" . $dadosCliente["email"]
            ]);
            if (is_array($client)) {
                $client = $client[0];
            }
            $response = (new Client)->edit($client->id_sacado_sac, $infoPagamento);
            if(is_array($response)) {
                return $response[0];
            }
            return $response;
        }

        $postData = array_merge($postData, $infoPagamento);
        $response = (new Client)->register($postData);

        if(!$response->status == "200") {
            if ($response->msg == "CPF/CNPJ inválido") {
                header('HTTP/1.1 500 Internal Server Error');
                header('Content-Type: application/json; charset=UTF-8');
                die(json_encode(array('message' => "CPF/CNPJ inválido")));
            } else if ($response->msg == "Cartão inválido.") {
                header('HTTP/1.1 500 Internal Server Error');
                header('Content-Type: application/json; charset=UTF-8');
                die(json_encode(array('message' => "Cartão inválido")));
            }
        }

        if(!empty($response)) {
            return $response;
        }

        return null;
    }

    public static function notifyContinuacao(array $dadosCliente, $to)
    {
        $subject = 'Fácil Participativo - Ajudes - Quase lá!';
        $view  = view('mail.freemium.continuacao')->with($dadosCliente);
        $message = $view->render();
        $headers = 'From: Atendimento - Lifepet <atendimento@lifepet.com.br>' . "\r\n" .
            'Reply-To: ' . "atendimento@lifepet.com.br" . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);

        $to2 = "cadastro@lifepet.com.br";
        $subject2 = 'Novo Cliente - Fácil Participativo - Ajudes';
        $view2  = view('mail.freemium.atendimento_novo_cliente')->with($dadosCliente);
        $message2 = $view2->render();
        $headers2 = 'From: Atendimento - Lifepet <atendimento@lifepet.com.br>' . "\r\n" .
            'Reply-To: ' . "atendimento@lifepet.com.br" . "\r\n";
        $headers2 .= "MIME-Version: 1.0\r\n";
        $headers2 .= "Cc: alexandre@lifepet.com.br\r\n";
        $headers2 .= "Cc: thiago@lifepet.com.br\r\n";
        $headers2 .= "Cc: lilian@lifepet.com.br\r\n";
        $headers2 .= "Content-Type: text/html; charset=UTF-8\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to2, $subject2, $message2, $headers2);
    }

    public static function notifyAprovacao(array $dadosCliente, $to)
    {
        $data = $dadosCompra['data'] = (new Carbon())->format('d/m/Y h:i:s');
        $subject = 'Fácil Participativo - Quase lá!';
        $view  = view('mail.ajudes.aprovacao')->with($dadosCliente);
        $message = $view->render();
        $headers = 'From: Atendimento - Lifepet <atendimento@lifepet.com.br>' . "\r\n" .
            'Reply-To: ' . "atendimento@lifepet.com.br" . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);
    }

    public static function notifyNovoCliente(array $dadosCliente)
    {
        $data = $dadosCompra['data'] = (new Carbon())->format('d/m/Y h:i:s');
        $to = "cadastro@lifepet.com.br";
        $subject = 'Novo Cliente - Fácil Participativo';
        $view  = view('mail.ajudes.atendimento_novo_cliente')->with($dadosCliente);
        $message = $view->render();
        $headers = 'From: Atendimento - Lifepet <atendimento@lifepet.com.br>' . "\r\n" .
            'Reply-To: ' . "atendimento@lifepet.com.br" . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);
    }

    public static function notifyIndicacao(array $dadosIndicacao, $to)
    {
        $subject = 'Alguém falou mal de você :o';
        $view  = view('mail.ajudes.indicacao')->with($dadosIndicacao);
        $message = $view->render();
        $headers = 'From: Atendimento - Lifepet <atendimento@lifepet.com.br>' . "\r\n" .
            'Reply-To: ' . "atendimento@lifepet.com.br" . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);
    }
}
