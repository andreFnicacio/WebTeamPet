<?php

namespace App\Http\Controllers;

use App\Helpers\API\Superlogica\Client;
use App\Helpers\API\Superlogica\Invoice;
use App\Helpers\API\Superlogica\Plans;
use App\Models\Clientes;
use App\Models\Indicacoes;
use App\Models\Pets;
use App\Models\PetsPlanos;
use App\Models\PreCadastros;
use App\Models\Raca;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Session;
use stdClass;

class FreemiumController extends AppBaseController
{
    const ID_PLANO_FACIL = 32;
    const VALOR_ADESAO = 75.00;
    const CODIGO_MENSALIDADE = 999999982;
    const CODIGO_ADESAO = 999999983;

    public function index() {
        return view('freemium.index');
    }

    public function indicar(Request $request)
    {
        $indicacoes = $request->get('indicacao');
        foreach($indicacoes as $indicacao) {
            $dadosIndicacao = [
                'id_cliente' => $request->get('idCliente'),
                'nome' => $indicacao['nome_amigo'],
                'email' => $indicacao['email_amigo'],
                'telefone' => $indicacao['celular_amigo'],
            ];

            if(!Indicacoes::where('email', $dadosIndicacao['email'])->exists()) {
                $i = Indicacoes::create($dadosIndicacao);
            }

            self::notifyIndicacao($dadosIndicacao, $dadosIndicacao['email']);
        }

        return redirect(route('freemium.sucesso'));
    }

    public function indicacoes($idCliente)
    {
        return view('freemium.indicacoes', ['idCliente' => $idCliente]);
    }


    public function sucesso()
    {
        return view('freemium.sucesso');
    }

    public static function cadastrarSuperlogica(Request $request)
    {
        //Realizar pagamento
        $clienteData = $request->get('cliente');

        if(Clientes::where('cpf', $clienteData['cpf'])->exists()) {
            $error = "Você já possui um cadastro conosco. Por enquanto este plano está disponível apenas para novos clientes. Você pode aderir o plano com outro CPF.";
            return view('freemium.index', ['error' => $error]);
        }

        $idUsuarioSuperlogica = null;
        $pago = self::assinar($request, $idUsuarioSuperlogica);

        if(!$pago["signed"]) {
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


        if($request->filled('indicacao')) {
            $indicacao = $request->get('indicacao');
            self::notifyIndicacao([
                'nome' => $indicacao['nome']
            ], $indicacao['email']);
        }

        return view('freemium.sucesso');
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

        return redirect(route('freemium.indicacoes', $cliente->id));
    }

    public function cadastro()
    {
        return view('freemium.cadastro');
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
            $invoiceManager = new Invoice();

            $cliente = new Clientes();
            $cliente->id_externo = $idUsuarioSuperlogica;


            $result = $invoiceManager->charge($cliente, $preco, null, "Adesão", self::CODIGO_ADESAO);

            if(is_array($result)) {
                $result = $result[0];
            }


            if($result->status == "200") {
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

    public static function upload(UploadedFile $file, $cliente, $description = "")
    {
        $cliente = (new Clientes())->find($cliente->id);

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
                'binded_id' => $cliente->id,
                'user_id' => $cliente->id_usuario
            ]);
            if ($upload) {
                //self::setSuccess('Arquivo carregado com sucesso.');
                return $path;
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
        $subject = 'Fácil Participativo - Quase lá!';
        $view  = view('mail.freemium.continuacao')->with($dadosCliente);
        $message = $view->render();
        $headers = 'From: Atendimento - Lifepet <atendimento@lifepet.com.br>' . "\r\n" .
            'Reply-To: ' . "atendimento@lifepet.com.br" . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);

        $to2 = "cadastro@lifepet.com.br";
        $subject2 = 'Novo Cliente - Fácil Participativo';
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
        $view  = view('mail.freemium.aprovacao')->with($dadosCliente);
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
        $view  = view('mail.freemium.atendimento_novo_cliente')->with($dadosCliente);
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
        $view  = view('mail.freemium.indicacao')->with($dadosIndicacao);
        $message = $view->render();
        $headers = 'From: Atendimento - Lifepet <atendimento@lifepet.com.br>' . "\r\n" .
            'Reply-To: ' . "atendimento@lifepet.com.br" . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);
    }

    /**
     * Adesão do Plano FREE
     */
    public function plano_free() 
    {
        $cpf = request('cpf');
        $preCadastro = (new PreCadastros());
        if (!$cpf) {
            return redirect('https://lifepet.com.br/planos/free');
        } else {

            $cpf = trim(str_replace('.', '', str_replace('-', '', $cpf)));
            // Verificar se é cliente existente
            $cliente = (new Clientes())->whereRaw('TRIM(REPLACE(REPLACE(cpf, \'.\', \'\'), \'-\', \'\')) = \'' . $cpf . '\'')->first();

            if ($cliente) {

                if ($cliente->status_pagamento != (new \App\Models\Clientes())::PAGAMENTO_EM_DIA) {
                    return redirect(route('clientes.sucesso_plano_free', [
                        'cliente' => true, 
                        'err' => '001' // INADIMPLENTE
                    ]));
                }

                foreach ($cliente->pets as $pet) {
                    // Se o plano do pet for o PLANO FREE BRASIL
                    if ($pet->plano()->id === 43 || $pet->plano()->id === 42) { 
                        return redirect(route('clientes.sucesso_plano_free'));
                    }
                }
                $dados_cliente = $cliente;
            } else {
                $preCadastro = $preCadastro->where('cpf', $cpf)->first();
                if (!$preCadastro) {
                    return redirect('https://lifepet.com.br/planos/free');
                }
                $dados_cliente = $preCadastro;
            }

        }

        $racas = (new Raca())->orderBy('nome', 'ASC')->get();
        $appBase = (new AppBaseController());
        $checklistProposta = $appBase::$checklistPropostaPlanoFree;

        return view('freemium.adesao_plano_free')
            ->with('racas', $racas)
            ->with('cliente', $cliente)
            ->with('checklistProposta', $checklistProposta)
            ->with('preCadastro', $preCadastro);
    }

    public function sucesso_plano_free(Request $request)
    {
        return view('freemium.sucesso_plano_free');
    }

    public function adesao_plano_free(Request $request) 
    {
        $input = $request->all();

        if (request('id_cliente')) {
            $cliente = (new Clientes())->find(request('id_cliente'));
        } else {
            $cliente = (new Clientes())->create($input['cliente']);
            $areaClienteController = (new AreaClienteController);
            $areaClienteController::$emailBoasVindasInsideSales = false;
            $areaClienteController->doCreateUser($cliente->id);
            $preCadastro = (new PreCadastros())->find($input['id_pre_cadastro']);
            $preCadastro->id_cliente = $cliente->id;
            $preCadastro->data_adesao = (new Carbon())->now();
            $preCadastro->save();
            
            //Fazer upload dos documentos.
            // self::upload($request->documentos_comprovante_residencia, $cliente, "Comprovante de Residência");
            // self::upload($request->documentos_cnh_rg_frente, $cliente, "CNH/RG Frente");
            // self::upload($request->documentos_cnh_rg_verso, $cliente, "CNH/RG Verso");
        }
        
        $propostas = $cliente->getPropostas();
        $propostas[] = $this->formataDadosProposta($input);
        $cliente->dados_proposta = json_encode($propostas);

        // $cliente->dados_proposta = $this->formataDadosProposta($input);
        $cliente->save();
    
        $pet = $input['pet'];
        $plano = $input['plano'];

        // Trata formatos
        $valor_plano = str_replace(',', '.', str_replace('.', '', $plano['valor_plano']));
        $valor_plano = number_format($valor_plano, 2, '.', '');
        $valor_adesao = str_replace(',', '.', str_replace('.', '', $plano['valor_adesao']));
        $valor_adesao = number_format($valor_adesao, 2, '.', '');
        $mes_reajuste = explode('-', $plano['data_inicio_contrato']);
        $mes_reajuste = abs($mes_reajuste[1]);

        $dadosPet = $pet;
        $dadosPet['mes_reajuste'] = $mes_reajuste;
        $dadosPet['valor'] = $valor_plano;
        $dadosPet['id_cliente'] = $cliente->id;
        $dadosPet['participativo'] = $plano['participativo'];
        $dadosPet['familiar'] = $plano['familiar'];
        $dadosPet['nome_pet'] = ucwords(mb_strtolower($pet['nome_pet']));
        $dadosPet['ativo'] = 1;
        $dadosPet['numero_microchip'] = "0";

        $newPet = (new Pets)->create($dadosPet);

        $dadosPlano = $plano;
        $dadosPlano['id_pet'] = $newPet->id;
        $dadosPlano['status'] = 'P';
        $dadosPlano['vendedor'] = 1;
        $dadosPlano['valor_momento'] = $valor_plano;
        $dadosPlano['adesao'] = $valor_adesao;
        $dadosPlano['data_inicio_contrato'] = (new Carbon())->createFromFormat('Y-m-d',  $plano['data_inicio_contrato'])->format('d/m/Y');
        
        $petPlano = (new PetsPlanos)->create($dadosPlano);

        $newPet->id_pets_planos = $petPlano->id;
        $newPet->save();
        
        $client = new \GuzzleHttp\Client();
        $client->post(
            'https://www.rdstation.com.br/api/1.2/conversions',
            array(
                'form_params' => array(
                    'token_rdstation' => "0eb70ce4d806faa1a1a23773e3d174d4",
                    'identificador' => "finalizou-plano-free",
                    'email' => $cliente->email,
                    'name' => $cliente->nome_cliente,
                    // 'custom_fields[607875]' => jQuery("input[name='senha']").val(),
                    // 'custom_fields[607874]' => jQuery("input[name='cpf']").val(),
                )
            )
        );

        return redirect(route('clientes.sucesso_plano_free'));

    }

    public function formataDadosProposta($input) 
    {
        $cliente = new stdClass();
        $cliente->dados['nome_cliente'] = $input['cliente']['nome_cliente'];
        $cliente->dados['sexo'] = $input['cliente']['sexo'];
        $cliente->dados['cpf'] = $input['cliente']['cpf'];
        $cliente->dados['rg'] = $input['cliente']['rg'];
        $cliente->dados['email'] = $input['cliente']['email'];
        $cliente->dados['celular'] = $input['cliente']['celular'];
        $cliente->dados['telefone_fixo'] = '';
        $cliente->dados['data_nascimento'] = $input['cliente']['data_nascimento'];
        $cliente->dados['observacoes'] = '';
        $cliente->dados['vencimento'] = '';
        $cliente->endereco['cep'] = $input['cliente']['cep'];
        $cliente->endereco['rua'] = $input['cliente']['rua'];
        $cliente->endereco['numero_endereco'] = $input['cliente']['numero_endereco'];
        $cliente->endereco['bairro'] = $input['cliente']['bairro'];
        $cliente->endereco['cidade'] = $input['cliente']['cidade'];
        $cliente->endereco['estado'] = 'ES';
        $cliente->endereco['complemento_endereco'] = '';
        $cliente->forma_pagamento = "Nenhum";
        $cliente->cartao['numero_cartao'] = '';
        $cliente->cartao['nome_cartao'] = '';
        $cliente->cartao['validade'] = '';
        $cliente->cartao['cvv'] = '';

        $pet = new stdClass();
        $pet->pet['nome_pet'] = $input['pet']['nome_pet'];
        $pet->pet['tipo'] = $input['pet']['tipo'];
        $pet->pet['sexo'] = $input['pet']['sexo'];
        $pet->pet['id_raca'] = $input['pet']['id_raca'];
        $pet->pet['data_nascimento'] = $input['pet']['data_nascimento'];
        $pet->pet['contem_doenca_pre_existente'] = '';
        $pet->pet['doencas_pre_existentes'] = '';
        $pet->pet['observacoes'] = '';
        $pet->plano['id_plano'] = '42';
        $pet->plano['participativo'] = $input['plano']['participativo'];
        $pet->plano['familiar'] = $input['plano']['familiar'];
        $pet->plano['data_inicio_contrato'] = (new Carbon())->today()->format('d/m/Y');
        $pet->plano['id_vendedor'] = '1';
        $pet->plano['valor_adesao'] = '0,00';
        $pet->plano['valor_plano'] = '0,00';
        $pet->plano['regime'] = $input['plano']['regime'];

        $appBase = (new AppBaseController());
        $checklistProposta = $appBase::$checklistPropostaPlanoFree;
        $checklistDoencasProposta = $appBase::$checklistDoencasProposta;
        
        $i = 0;
        foreach($checklistProposta as $check) {
            $checklist[$i]['item'] = $check;
            $checklist[$i]['ok'] = 'on';
            $i++;
        }
        foreach($checklistDoencasProposta as $doenca) {
            $doencas[]['doenca'] = $doenca;
        }

        $dadosProposta = [
            "cliente" => $cliente,
            "pets" => [
                $pet
            ],
            "doencas_pre_existentes" => $doencas,
            "checklist" => $checklist,
            "versao" => 'v1',
            "aceite" => true,
            "data_proposta" => Carbon::today()->format('Y-m-d'),
        ];
        return $dadosProposta;
    }
}
