<?php

namespace App\Http\Controllers;

use App\Helpers\API\Superlogica\Client;
use App\Helpers\API\Superlogica\Invoice;
use App\Helpers\API\Superlogica\Plans;
use App\Helpers\Utils;
use App\Models\Clientes;
use App\Models\Indicacoes;
use App\Models\Pets;
use App\Models\PetsPlanos;
use App\Models\Planos;
use App\Models\Vendedores;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Session;
use Image;

class AppCadastroClienteController extends AppBaseController
{
    const ID_PLANO_FACIL = 32;
    const VALOR_ADESAO = 75.00;
    const CODIGO_MENSALIDADE = 999999982;
    const CODIGO_ADESAO = 999999983;

    public function index()
    {
        $user = Auth::user();
        $vendedor = Vendedores::where('id_usuario', $user->id)->get()->first();
        if ($vendedor) {
            return view('app_cadastro_cliente.index');
        } else {
            return redirect('/');
        }
    }

    public function resumo($idCliente, Request $request)
    {
        $today = Carbon::today()->format('Y-m-d');
        $cliente = Clientes::find($idCliente);
        $pets = $cliente->pets()->whereIn('id', $request->get('pets'))->get();
        return view('app_cadastro_cliente.resumo', [
            'cliente' => $cliente,
            'pets' => $pets,
//            'adesoes' => $request->get('adesoes')
        ]);
    }

    public static function propostaPdf($clienteId, Request $request)
    {
        $data = $request->all();

        self::upload($data['pdf'], $clienteId, "Proposta de Adesão");
    }

    public function proposta($idCliente, Request $request)
    {
        $requisicao = $request->all();
        $cliente = Clientes::find($idCliente);
        $pets = $cliente->pets()->whereIn('id', $request->get('pets'))->get();
        $vendedor = $pets->first()->petsPlanos()->orderBy('id', 'desc')->first()->vendedor();
        $iconCheckbox = asset('_app_cadastro_cliente/proposta/img/icon-checkbox.png');

        $data = [
            'cliente' => $cliente,
            'pets' => $pets,
            'vendedor' => $vendedor,
            'iconCheckbox' => $iconCheckbox,
            'request' => $requisicao
        ];

        return view('app_cadastro_cliente.proposta', $data);
    }

    public function propostaManual($idCliente, Request $request)
    {

        $v = Validator::make($request->all(), [
            'pets' => 'required|array',
            'vendedor' => 'required|string'
        ]);

        if ($v->fails()) {
            $messages = join("\n", $v->getMessageBag()->all());
            self::setError($messages, 'Oops.');

            return back()
                ->withErrors($v)
                ->withInput();
        }

        $requisicao = $request->all();
        $cliente = Clientes::find($idCliente);
        $pets = $cliente->pets()->whereIn('id', $requisicao['pets'])->get();
        $vendedor = Vendedores::find($requisicao['vendedor']);
        $iconCheckbox = asset('_app_cadastro_cliente/proposta/img/icon-checkbox.png');

        if (isset($requisicao['data_inicio_contrato'])) {
            $data = Carbon::parse($requisicao['data_inicio_contrato']);
            $data_inicio_contrato = $data->day .
                ' de ' .
                \App\Helpers\Utils::getMonthName($data->month) .
                ' de ' .
                $data->year;
        } else {
            $data_inicio_contrato = \Carbon\Carbon::now()->format('d') .
                ' de ' .
                \App\Helpers\Utils::getMonthName(\Carbon\Carbon::now()->format('m')) .
                ' de ' .
                \Carbon\Carbon::now()->format('Y');
        }

        $data = [
            'cliente' => $cliente,
            'pets' => $pets,
            'vendedor' => $vendedor,
            'data_inicio_contrato' => $data_inicio_contrato,
            'iconCheckbox' => $iconCheckbox,
            'request' => $requisicao
        ];
//
        return view('app_cadastro_cliente.proposta_manual', $data);
    }

    public function sucesso()
    {
        return view('app_cadastro_cliente.sucesso');
    }

    public static function cadastrarSuperlogica(Request $request)
    {
        //Realizar pagamento
        $clienteData = $request->get('cliente');

        if (Clientes::where('cpf', $clienteData['cpf'])->exists()) {
            $error = "Você já possui um cadastro conosco. Por enquanto este plano está disponível apenas para novos clientes. Você pode aderir o plano com outro CPF.";
            return view('app_cadastro_cliente.index', ['error' => $error]);
        }

        $idUsuarioSuperlogica = null;
        $pago = self::assinar($request, $idUsuarioSuperlogica);

        if (!$pago["signed"]) {
            return [
                'message' => "Houve um erro no seu pagamento",
                'status' => false
            ];
        }

        self::notifyContinuacao([
            'nome' => $clienteData['nome_cliente'],
            'quantidade_pets' => $request->get('pets'),
            'email' => $clienteData['email'],
            'telefone' => $clienteData['celular'],
            'dadosIndicacao' => $request->get('indicacao')
        ], $clienteData['email']);


        if ($request->filled('indicacao')) {
            $indicacao = $request->get('indicacao');
            self::notifyIndicacao([
                'nome' => $indicacao['nome']
            ], $indicacao['email']);
        }

        return view('app_cadastro_cliente.sucesso');
    }

    public function salvarDadosResumo(Request $request)
    {

        $cliente = Clientes::find($request->get('idCliente'));
        $pets = $cliente->whereIn('id', $request->get('idPets'))->get();

        //Fazer upload dos documentos.
        $comp_pagamento = $request->file('comp_pagamento');
        if (isset($comp_pagamento)) {
            self::upload($comp_pagamento, $cliente->id, "Comprovante de pagamento");
        }

        self::notifyNovoCliente($cliente->toArray(), $pets);

        return route('app_cadastro_cliente.proposta', [$cliente->id, 'pets' => $request->get('idPets')]);
    }

    public function cadastrar(Request $request)
    {

        $clienteData = $request->get('cliente');
        $id_cliente = $request->get('id_cliente');

        $cpf = preg_replace('/[^0-9]/', '', $clienteData["cpf"]);
        $dadosCliente = [
            'nome_cliente' => $clienteData["nome_cliente"],
            'cpf' => $cpf,
            'rg' => isset($clienteData["rg"]) ? $clienteData["rg"] : "",
            'data_nascimento' => $clienteData["data_nascimento"],
            'telefone_fixo' => isset($clienteData["telefone_fixo"]) ? $clienteData["telefone_fixo"] : "",
            'celular' => $clienteData["celular"],
            'email' => $clienteData["email"],
//            'observacoes' => trim($clienteData["observacoes"]),
            'ativo' => 0,
            'sexo' => $clienteData["sexo"] === "Masculino" ? "M" : "F",
            'primeiro_acesso' => 1,
            'participativo' => 1,
            'aprovado' => 0,
            'cep' => $clienteData["cep"],
            'rua' => $clienteData["rua"],
            'numero_endereco' => $clienteData["numero_endereco"],
            'complemento_endereco' => $clienteData["complemento_endereco"],
            'bairro' => $clienteData["bairro"],
            'cidade' => $clienteData["cidade"],
            'estado' => $clienteData["estado"],
        ];

        if (!$id_cliente) {

            $usuarioSuperlogica = self::createSuperlogicaUserSemCartao($dadosCliente);
            $dadosCliente['id_externo'] = $usuarioSuperlogica->data->id_sacado_sac;
            $cliente = Clientes::create($dadosCliente);

        } else {
            self::updateSuperlogicaUser($dadosCliente);
            $cliente = Clientes::find($id_cliente);
//            session(['obs_vendedor' => $dadosCliente['observacoes'] ?: '']);
//            $dadosCliente['observacoes'] = $cliente->observacoes . ($dadosCliente['observacoes'] ? ' -- ' . $dadosCliente['observacoes'] : '');
            $cliente->fill($dadosCliente);
            $cliente->save();
        }

        if (!$cliente) {
            return [
                'message' => "Houve um erro na criação do seu usuário. Por favor, entre em contato com nosso atendimento.",
                'status' => false
            ];
        }

        //Enviar email de confirmação
        self::notifyAprovacao([
            'nome' => $cliente->nome_cliente
        ], $cliente->email);

        //Fazer upload dos documentos.
        $files = $request->file('cliente');
        self::upload($files["rg_frente"], $cliente->id, "RG/CNH Frente");
        self::upload($files["rg_verso"], $cliente->id, "RG/CNH Verso");
        if (isset($files['comp_residencia'])) {
            self::upload($files['comp_residencia'], $cliente->id, "Comprovante de residência");
        }
        ClientesController::setAssinatura($cliente, $request);

        return route('app_cadastro_cliente.cadastro_pets', [$cliente->id, $request->get('qtd_pets')]);
    }

    public function cadastrarPet(Request $request)
    {
        $petData = $request->all();
        $cliente = Clientes::find($request->get('idCliente'));
        $adesoes = [];

        if (!$cliente) {
            return [
                'message' => "Houve um erro no cadastro do Pet. Por favor, tente novamente.",
                'status' => false
            ];
        }

        foreach ($petData['pet'] as $pet) {

            $plano = Planos::find($pet['id_plano']);

            if (!$plano) {
                return [
                    'message' => "Houve um erro no cadastro do Pet. O plano não foi carregado corretamente.",
                    'status' => false
                ];
            } else {
                // Trata formatos
                $valor_plano = str_replace(',', '.', str_replace('.', '', $pet['valor_plano']));
                $valor_plano = number_format($valor_plano, 2, '.', '');
                $valor_adesao = str_replace(',', '.', str_replace('.', '', $pet['valor_adesao']));
                $valor_adesao = number_format($valor_adesao, 2, '.', '');
                $mes_reajuste = explode('/', $pet['data_inicio_contrato']);
                $mes_reajuste = abs($mes_reajuste[1]);

                $newPet = Pets::create([
                    'nome_pet' => $pet["nome_pet"],
                    'tipo' => $pet["tipo"],
                    'sexo' => $pet["sexo"],
                    'id_raca' => $pet["id_raca"],
                    'data_nascimento' => $pet["data_nascimento"],
                    'contem_doenca_pre_existente' => $pet["contem_doenca_pre_existente"],
                    'doencas_pre_existentes' => $pet["doencas_pre_existentes"],
                    'observacoes' => $pet["observacoes"],
                    'id_cliente' => $cliente->id,
                    'ativo' => 0,
                    'familiar' => $pet['familiar'],
                    'participativo' => $pet['participativo'],
                    'regime' => $pet['regime'],
                    'mes_reajuste' => $mes_reajuste,
                    'valor' => $valor_plano,
                ]);

                $petPlano = PetsPlanos::create([
                    'id_pet' => $newPet->id,
                    'id_plano' => $plano->id,
                    'data_inicio_contrato' => $pet['data_inicio_contrato'],
                    'id_vendedor' => $pet['id_vendedor'],
                    'status' => 'P',
                    'status' => 'P',
                    'valor_momento' => $valor_plano,
                    'adesao' => $valor_adesao,
                ]);

                $newPet->id_pets_planos = $petPlano->id;

                // Upload
                if (isset($pet['carteira_vacinacao'])) {
                    self::upload($pet['carteira_vacinacao'], $cliente->id, "Carteira de vacinação - " . $pet['nome_pet']);
                }
                if (isset($pet['foto'])) {
                    self::upload($pet['foto'], $cliente->id, "Foto para Carteirinha - " . $pet['nome_pet']);
                    PetsController::setFoto($newPet, $pet['foto']);
                }
                $newPet->save();
                $pets[] = $newPet->id;
            }
        }

        return route('app_cadastro_cliente.resumo', [$cliente->id, "pets" => $pets]);

    }

    public function cadastro()
    {
        $user = Auth::user();
        $vendedor = Vendedores::where('id_usuario', $user->id)->get()->first();
        $data = [
            'user' => $user,
            'vendedor' => $vendedor
        ];
        if ($vendedor) {
            return view('app_cadastro_cliente.cadastro', $data);
        } else {
            return redirect('/');
        }
    }

    public function cadastroPets($idCliente, $qtdPets)
    {
        $user = Auth::user();
        $vendedor = Vendedores::where('id_usuario', $user->id)->get()->first();
        $data = [
            'user' => $user,
            'vendedor' => $vendedor,
            'idCliente' => $idCliente,
            'qtdPets' => $qtdPets
        ];
        if ($vendedor) {
            return view('app_cadastro_cliente.cadastro_pets', $data);
        } else {
            return redirect('/');
        }
    }

    public static function assinar(Request $request, &$idUsuarioSuperlogica)
    {
        $all = $request->all();
        $cliente = $request->get('cliente');

        $dadosCliente = [
            'telefone' => $cliente["celular"],
            'nome' => $cliente["nome_cliente"],
            'cpf' => $cliente['cpf'],
            'email' => $cliente["email"],
        ];

        $dadosCartao = $request->get('cartao');
        $validadeMes = explode('/', $dadosCartao['validade'])[0];
        $validadeAno = explode('/', $dadosCartao['validade'])[1];

        $dadosCartao['validade_mes'] = $validadeMes;
        $dadosCartao['validade_ano'] = $validadeAno;

        $quantidades = $request->get('pets', 1);

        $usuarioSuperlogica = self::createSuperlogicaUserSemCartao($dadosCliente);

        if ($usuarioSuperlogica->status === "500") {
            return abort(500, $usuarioSuperlogica->msg);
        }

        $idUsuarioSuperlogica = $usuarioSuperlogica->data->id_sacado_sac;

        $planoId = self::ID_PLANO_FACIL;

        $plano = \App\Models\Planos::find($planoId);
        $idPlanoSuperlogica = $plano->id_superlogica_anual;

        //ANUIDADE(3)
        $idProduto = "3";

        $preco = self::VALOR_ADESAO * $quantidades;

        $dados = [
            'PLANOS' => [],
        ];

        for ($i = 0; $i < $quantidades; $i++) {
            $identificador = "PLANO_" . $plano->nome_plano . '_VD_Ecommerce' . time();
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

        if (is_array($response)) {
            $response = $response[0];
        }

        if ($response->status == "200") {

            $now = new Carbon();

            //TODO: Não está sendo cobrada a adesão.
            if (!$now->gt(Carbon::createFromFormat('Y-m-d H:i', '2018-05-11 23:59'))) {
                return [
                    "signed" => true,
                    "assinatura" => $response,
                ];
            }

            //Cobrar a adesão
            $invoiceManager = new Invoice();

            $cliente = new Clientes();
            $cliente->id_externo = $idUsuarioSuperlogica;


            $result = $invoiceManager->charge($cliente, $preco, null, "Adesão", self::CODIGO_ADESAO);

            if (is_array($result)) {
                $result = $result[0];
            }


            if ($result->status == "200") {
                return [
                    "signed" => true,
                    "assinatura" => $response,
                    "adesao" => $result
                ];
            } else {
                return [
                    "signed" => false,
                    "assinatura" => $response,
                    "adesao" => $result
                ];
            }
        } else if ($response->msg == "Cobrança não atingiu o valor mínimo para geração.") {
            return [
                "signed" => false,
                "response" => $response,
                'message' => "Cobrança não atingiu o valor mínimo para geração."
            ];
        } else {
            return [
                "signed" => false,
                "response" => $response
            ];
        }
    }

    public static function upload(UploadedFile $file, $idCliente, $description = "")
    {
        if ($file->isValid()) {
            $extension = $file->extension();
            $size = $file->getClientSize();
            $mime = $file->getClientMimeType();
            $originalName = $file->getClientOriginalName();
            $description = $description ?: "";
            $path = $file->store('uploads');
            $upload = \App\Models\Uploads::create([
                'original_name' => $originalName,
                'mime' => $mime,
                'description' => $description,
                'extension' => $extension,
                'size' => $size,
                'public' => 1,
                'path' => $path,
                'bind_with' => 'clientes',
                'binded_id' => $idCliente,
                'user_id' => auth()->user()->id
            ]);
            if ($upload) {
                //self::setSuccess('Arquivo carregado com sucesso.');
                return $path;
            }
        } else {
            return false;
        }
    }

    public static function createSuperlogicaUserSemCartao($dadosCliente)
    {
        $postData = [
            'ST_TELEFONE_SAC' => $dadosCliente['telefone_fixo'],
            'ST_NOME_SAC' => $dadosCliente["nome_cliente"],
            'ST_NOMEREF_SAC' => $dadosCliente["nome_cliente"],
            'ST_CGC_SAC' => $dadosCliente['cpf'],
            'ST_EMAIL_SAC' => $dadosCliente["email"],

            'ID_GRUPO_GRP' => 1,

            'ST_CEP_SAC' => $dadosCliente['cep'],
            'ST_ENDERECO_SAC' => $dadosCliente['rua'],
            'ST_NUMERO_SAC' => $dadosCliente['numero_endereco'],
            'ST_BAIRRO_SAC' => $dadosCliente['bairro'],
            'ST_COMPLEMENTO_SAC' => $dadosCliente['complemento_endereco'],
            'ST_CIDADE_SAC' => $dadosCliente['cidade'],
            'ST_ESTADO_SAC' => $dadosCliente['estado'],
        ];

//        $infoPagamento = [
//            'ST_CARTAO_SAC' => preg_replace('/\s+/', '', $dadosCartao["numero_cartao"]),
//            'ST_MESVALIDADE_SAC' => $dadosCartao["validade_mes"],
//            'ST_ANOVALIDADE_SAC' => $dadosCartao["validade_ano"],
//            'ST_SEGURANCACARTAO_SAC' => $dadosCartao["cvv"],
//            'FL_PAGAMENTOPREF_SAC' => 3
//        ];

//        /**
//         * Caso o cliente já exista, modifica os dados de pagamento e retorna os dados do cliente.
//         */
//        if(Client::exists($dadosCliente["email"])) {
//            $client = (new Client)->get([
//                'pesquisa' => "todosemails:" . $dadosCliente["email"]
//            ]);
//            if (is_array($client)) {
//                $client = $client[0];
//            }
//            $response = (new Client)->edit($client->id_sacado_sac, $infoPagamento);
//            if(is_array($response)) {
//                return $response[0];
//            }
//            return $response;
//        }

//        $postData = array_merge($postData, $infoPagamento);
        $response = (new Client)->register($postData);

        if (!$response->status == "200") {
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

        if (!empty($response)) {
            return $response;
        }

        return null;
    }

    public static function updateSuperlogicaUser($dadosCliente)
    {
        $postData = [
            'ST_TELEFONE_SAC' => $dadosCliente['telefone_fixo'],
            'ST_NOME_SAC' => $dadosCliente["nome_cliente"],
            'ST_NOMEREF_SAC' => $dadosCliente["nome_cliente"],
            'ST_CGC_SAC' => $dadosCliente['cpf'],
            'ST_EMAIL_SAC' => $dadosCliente["email"],

            'ID_GRUPO_GRP' => 1,

            'ST_CEP_SAC' => $dadosCliente['cep'],
            'ST_ENDERECO_SAC' => $dadosCliente['rua'],
            'ST_NUMERO_SAC' => $dadosCliente['numero_endereco'],
            'ST_BAIRRO_SAC' => $dadosCliente['bairro'],
            'ST_COMPLEMENTO_SAC' => $dadosCliente['complemento_endereco'],
            'ST_CIDADE_SAC' => $dadosCliente['cidade'],
            'ST_ESTADO_SAC' => $dadosCliente['estado'],
        ];

//        /**
//         * Caso o cliente já exista, modifica os dados de pagamento e retorna os dados do cliente.
//         */
        if(Client::exists($dadosCliente["email"])) {
            $client = (new Client)->get([
                'pesquisa' => "todosemails:" . $dadosCliente["email"]
            ]);
            if (is_array($client)) {
                $client = $client[0];
            }
            $response = (new Client)->edit($client->id_sacado_sac, $postData);
            if(is_array($response)) {
                return $response[0];
            }
            return $response;
        }

        return null;
    }

    public static function notifyContinuacao(array $dadosCliente, $to)
    {
        $subject = 'Fácil Participativo - Quase lá!';
        $view = view('mail.app_cadastro_cliente.continuacao')->with($dadosCliente);
        $message = $view->render();
        $headers = 'From: Atendimento - Lifepet <atendimento@lifepet.com.br>' . "\r\n" .
            'Reply-To: ' . "atendimento@lifepet.com.br" . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);

        $to2 = "cadastro@lifepet.com.br";
        $subject2 = 'Novo Cliente - Fácil Participativo';
        $view2 = view('mail.app_cadastro_cliente.atendimento_novo_cliente')->with($dadosCliente);
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
        $view = view('mail.app_cadastro_cliente.aprovacao')->with($dadosCliente);
        $message = $view->render();
        $headers = 'From: Atendimento - Lifepet <atendimento@lifepet.com.br>' . "\r\n" .
            'Reply-To: ' . "atendimento@lifepet.com.br" . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);
    }

    public static function notifyNovoCliente(array $dadosCliente, $pets)
    {
        $mail = Mail::send('mail.app_cadastro_cliente.atendimento_novo_cliente', [
            'cliente' => $dadosCliente,
            'pets' => $pets,
        ], function($message) {
            $message->to("cadastro@lifepet.com.br")
                ->cc("pedro@lifepet.com.br")
                ->cc("thiago@lifepet.com.br")
                ->subject('Novo Cliente Cadastrado - Aguardando Validação');
        });
    }

    public static function notifyIndicacao(array $dadosIndicacao, $to)
    {
        $subject = 'Alguém falou mal de você :o';
        $view = view('mail.app_cadastro_cliente.indicacao')->with($dadosIndicacao);
        $message = $view->render();
        $headers = 'From: Atendimento - Lifepet <atendimento@lifepet.com.br>' . "\r\n" .
            'Reply-To: ' . "atendimento@lifepet.com.br" . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);
    }

}
