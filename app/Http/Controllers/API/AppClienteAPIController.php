<?php

namespace App\Http\Controllers\API;

use App\Helpers\API\Financeiro\DirectAccess\Services\CreditCardService;
use App\Helpers\API\Financeiro\DirectAccess\Services\CustomerService;
use App\Helpers\API\Financeiro\Financeiro;
use App\Helpers\Utils;
use App\Http\Controllers\{Controller, PetsController};
use App\Http\Controllers\API\Traits\{AngelAPITrait};
use App\Models\{Clientes, DocumentosClientes, DocumentosPets, Indicacoes, Nps, Parametros, Pets, PlanosGrupos, Uploads};
use App\Models\PlanosProcedimentos;
use App\Models\Procedimentos;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash, Mail};
use Illuminate\Support\Str;
use Modules\Clinics\Entities\Clinicas;
use Modules\Guides\Entities\HistoricoUso;
use Modules\Mobile\Services\PushNotificationService;
use Modules\Veterinaries\Entities\AvaliacoesPrestadores;
use Response;

class AppClienteAPIController extends Controller
{
    use AngelAPITrait;

    public static $permissoes = [
        'solicitar_carteirinha' => false,
        'add_foto' => true,
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->content = array();
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => request('username'), 'password' => request('password')])) {

            $user = Auth::user();
            $cliente = (new Clientes())->where('id_usuario', $user->id)->first();

            if ($cliente) {
                $objToken = $user->createToken("Lifepet APP Cliente", ['*']);
                $status = 200;
                $this->content['expires_in'] = Carbon::now()->diffInSeconds(Carbon::now()->addDays(365));
                $this->content['access_token'] = $objToken->accessToken;
            } else {
                $this->content['msg'] = "Cliente não encontrado!";
                $status = 401;
            }
        } else {
            $this->content['msg'] = "Email ou senha inválida!";
            $status = 401;
        }

        return response()->json($this->content, $status);
    }

    public function atualizaTokenFirebase()
    {
        $user = Auth::user();
        if ($user) {
            $token_firebase = request('token_firebase');

            DB::table('clientes')->where('token_firebase', $token_firebase)->update(['token_firebase' => null]);

            $cliente = (new Clientes())->where('id_usuario', $user->id)->first();
            $cliente->token_firebase = $token_firebase;
            $cliente->save();
            return response()->json(['cliente' => ['token_firebase' => $cliente->token_firebase]], 200);
        } else {
            return response()->json(['msg' => 'Usuário não encontrado!'], 404);
        }
    }

    public function primeiroAcesso()
    {
        $cpf = request('cpf');
        $celular = request('celular');
        $senha = request('senha');

        $cliente = (new Clientes())->whereRaw('TRIM(REPLACE(REPLACE(cpf, \'.\', \'\'), \'-\', \'\')) = \'' . $cpf . '\'');

        if (request('data_nascimento')) {
            $data_nascimento = (new Carbon())->createFromFormat('d/m/Y', request('data_nascimento'))->format('Y-m-d');
            $cliente = $cliente->where('data_nascimento', $data_nascimento);
        }
        $cliente = $cliente->first();

        if (!$cliente) {
            return response()->json(["msg" => "Cliente não encontrado!"], 404);
        } else {

            if ($cliente->id_usuario) {
                $user = (new User())->find($cliente->id_usuario);
                $user->password = Hash::make($senha);
                $user->save();
            } else {

                $userExistente = (new User())->where('email', $cliente->email)->first();

                if ($userExistente) {

                    $user = $userExistente;

                    $clienteUserExistente = (new Clientes())->where('id_usuario', $user->id)->exists();

                    if ($clienteUserExistente) {
                        return response()->json(["msg" => "Você já possui acesso!"], 403);
                    } else {
                        $cliente->id_usuario = $user->id;
                        $cliente->save();
                        $user->password = bcrypt($senha);
                        $user->save();
                    }
                } else {
                    $user = (new User())->create([
                        'name' => $cliente->nome_cliente,
                        'email' => $cliente->email,
                        'password' => bcrypt($senha)
                    ]);
                    $cliente->id_usuario = $user->id;
                    $cliente->save();
                }
            }

            $cliente->celular = $celular;
            $cliente->save();

            return response()->json(["usuario" => $user], 200);
        }
    }

    public function recuperarSenha()
    {
        $celular = request('celular');
        $cliente = (new Clientes())
            ->whereRaw('TRIM(REPLACE(REPLACE(REPLACE(REPLACE(celular, \'(\', \'\'), \')\', \'\'), \' \', \'\'), \'-\', \'\')) = \'' . $celular . '\'')
            ->where('ativo',1)
            ->whereNotNull('id_usuario')
            ->first();

        if (!$cliente) {
            return response()->json(["msg" => "Cliente não encontrado!"], 404);
        } else {
            return response()->json(["id_cliente" => $cliente->id], 200);
        }
    }

    public function novaSenha()
    {
        $cliente = (new Clientes())->find(request('id_cliente'));

        if ($cliente) {
            if ($cliente->id_usuario) {

                $user = (new User())->find($cliente->id_usuario);

                $user->forceFill([
                    'password' => bcrypt(request('senha')),
                    'remember_token' => Str::random(60),
                ])->save();

                return response()->json(["usuario" => $user], 200);
            } else {

                return response()->json(["msg" => "Usuário não encontrado!"], 404);
            }
        } else {
            return response()->json(["msg" => "Cliente não encontrado!"], 404);
        }
    }

    public function addFoto(Request $request, $idPet)
    {

        if (!$idPet) {
            return response()->json(["msg" => "Pet não encontrado!"], 404);
        }
        if (!request('foto')) {
            return response()->json(["msg" => "Foto não recebida!"], 403);
        }

        $pet = (new Pets())->find($idPet);

        if ($pet) {

            $file = request('foto');

            if ($file->isValid()) {
                $upload = PetsController::setFoto($pet, $file);
                return response()->json(["msg" => "Foto alterada com sucesso!"], 200);
            } else {
                return response()->json(["msg" => "O arquivo enviado não é válido!"], 403);
            }
        } else {
            return response()->json(["msg" => "Pet não encontrado!"], 404);
        }
    }

    public function solicitacaoCarteirinha(Request $request, $idPet)
    {
        $user = Auth::user();
        if ($user) {
            $cliente = (new Clientes())->where('id_usuario', $user->id)->first();
        }
        if (!$idPet) {
            return response()->json(["msg" => "Pet não encontrado!"], 404);
        }

        $pet = (new Pets())->find($idPet);

        $html = "
            <strong>Nome do pet:</strong> <br>
            <a href='https://app.lifepet.com.br/pets/".$idPet."/edit'>".$request->get('nomePet')."</a> <br>
            <br>
            <strong>Nome do tutor:</strong> <br>
            ".$request->get('nomeTutor')." <br>
            <br>
            <strong>Cidade:</strong> <br>
            ".$request->get('cidadeParaRetirar')." <br>
            <br>
            <strong>Foto:</strong> <br>
            <a href='".url('/') . '/' . $pet->foto."'>".url('/') . '/' . $pet->foto."</a> <br>
            <br>
            <br>
            Está ciente que uma segunda-via acarretará em uma cobrança extra: Meus dados estão correto e estou ciente que uma nova via com correções de dados poderá ter um custo de R$ 12,00.
        ";
        $mail = Mail::send(array(), array(), function ($message) use ($html) {
            $message->to('alexandre.moreira@lifepet.com.br')
              ->cc('raquel.menezes@lifepet.com.br')
              ->subject('Nova solicitação de carteirinha')
              ->setBody($html, 'text/html');
          });

        return response()->json(["msg" => "Carteirinha solicitada com sucesso!"], 200);

    }

    public function avaliacaoPrestador(Request $request, $idPet)
    {
        $user = Auth::user();
        if ($user) {
            $cliente = (new Clientes())->where('id_usuario', $user->id)->first();
        } else {
            return response()->json(["msg" => "Cliente não encontrado!"], 404);
        }
        if (!$idPet) {
            return response()->json(["msg" => "Pet não encontrado!"], 404);
        }

        $data = $request->all();
        $avaliacao = (new AvaliacoesPrestadores())->find($data['id_avaliacao']);
        if (!$avaliacao) {
            return response()->json(["msg" => "Avaliação não encontrada!"], 404);
        }
        $avaliacao->nota = $data['nota'];
        $avaliacao->comentario = $data['comentario'];
        $avaliacao->save();

        return response()->json(["msg" => "Avaliação efetuada com sucesso!"], 200);
    }

    public function meusPets(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $cliente = (new Clientes())->where('id_usuario', $user->id)->first();
        }

        $data = new \stdClass();
        $pets = (new Pets())->where('id_cliente', $cliente->id);

        if ($request->get('ativo') == 'true') {
            $pets = $pets->where('ativo', 1);
        }

        if ($request->get('angel') == 'true') {
            $pets = $pets->where('angel', 1);
        } elseif ($request->get('angel') == 'false') {
            $pets = $pets->where('angel', 0);
        }

        $data->pets = $pets->get()->map(function ($pet) {
            return $this->getDadosPet($pet);
        });
        return Response::json($data);
    }

    public function petDados($idPet)
    {
        if (!$idPet) {
            return response()->json(["msg" => "Pet não encontrado!"], 404);
        }

        $pet = (new Pets())->find($idPet);
        return [
            'pet' => $this->getDadosPet($pet)
        ];
    }

    public function petGuias($idPet)
    {
        if (!$idPet) {
            return response()->json(["msg" => "Pet não encontrado!"], 404);
        }

        $data = new \stdClass();
        $data->guias = (new Pets())->find($idPet)
            ->historicoUsos()
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($guia) {
                return self::getDadosGuia($guia);
            });
        return Response::json($data);
    }

    public function petGuiaDetalhes($idPet, $numeroGuia)
    {
        if (!$idPet || !$numeroGuia) {
            if (!$idPet) {
                return response()->json(["msg" => "Pet não encontrado!"], 404);
            } else {
                return response()->json(["msg" => "Guia não encontrada!"], 404);
            }
        }

        $data = new \stdClass();
        $data->guia = new \stdClass();

        $historicoUsos = (new Pets())->find($idPet)->historicoUsos()->where('numero_guia', $numeroGuia);
        $procedimentos = clone $historicoUsos;
        $guia = $historicoUsos->first();

        // $data->guia->id = $guia->id;
        // $data->guia->numero_guia = $guia->numero_guia;
        // $data->guia->solicitante = $guia->id_solicitante ? $guia->solicitante->nome_clinica : null;
        // $data->guia->tipo = $guia->tipo_atendimento;
        // $data->guia->status = $guia->status;

        // $data->guia->procedimentos = $procedimentos->get()->map(function ($guia) {
        //     return [
        //         'id' => $guia->procedimento->id,
        //         'nome' => $guia->procedimento->nome_procedimento
        //     ];
        // });

        $data->guia = self::getDadosGuia($guia);

        return Response::json($data);
    }

    public function petPlanoCobertura($idPet)
    {
        if (!$idPet) {
            return response()->json(["msg" => "Pet não encontrado!"], 404);
        }

        $status_carencia = [
            Pets::STATUS_CARENCIA_COMPLETO => 'COMPLETO',
            Pets::STATUS_CARENCIA_INCOMPLETO => 'INCOMPLETO',
            Pets::STATUS_CARENCIA_PARCIAL => 'PARCIAL',
        ];

        $data = new \stdClass();
        $pet = (new Pets())->find($idPet);
        $vigencias = $pet->vigencias();

        $data->vigencia_inicio = $vigencias[0]->format('d/m/Y');
        $data->vigencia_fim = $vigencias[1]->format('d/m/Y');

        if ($pet->isBichos()) {
            $data->is_bichos = true;
            $cobertura = $pet->getPlacarCarenciasPorProcedimento();
            // dd($cobertura);
            // $cobertura = array_unique($cobertura);
            foreach ($cobertura as $key => $cob) {
                $c = new \stdClass();
                $c->id = $cob['id'];
                $c->nome = $cob['nome'];
                $c->status = ($cob['carencia_dias'] == 0 ? $status_carencia[Pets::STATUS_CARENCIA_COMPLETO] : $status_carencia[Pets::STATUS_CARENCIA_INCOMPLETO]);
                $c->contratado = trim(explode('<', explode('>', $cob['qtd_permitida'])[1])[0]);
                $c->utilizado = trim(explode('<', explode('>', $cob['qtd_utilizada'])[1])[0]);
                $c->disponivel = trim(explode('<', explode('>', $cob['qtd_restante'])[1])[0]);
                $c->carencia_dias = $cob['carencia_dias'] == 0 ? "Período de carência cumprido!" : "Faltam: " . $cob['carencia_dias'] . " para o término do período de carência.";
                $data->cobertura[] = $c;
            }
            $data->cobertura = collect($data->cobertura)->unique();
        } else {
            $data->is_bichos = false;
            $cobertura = $pet->getPlacarCarenciasPorGrupo();
            foreach ($cobertura as $key => $cob) {
                if($cob['grupo']['id'] == 10101041) {
                    continue;
                }

                $c = new \stdClass();
                $c->id = $cob['grupo']['id'];
                $c->nome = $cob['grupo']['nome_grupo'];
                $c->status = $status_carencia[$cob['carencia_status']];
                $c->contratado = $cob['historicoUso']['qtd_permitida'];
                $c->utilizado = $cob['historicoUso']['qtd_utilizada'];
                $c->disponivel = $cob['historicoUso']['qtd_restante'];

                $data->cobertura[] = $c;
            }
        }

        return Response::json($data);
    }

    public function petPlanoCoberturaProcedimentos($idPet, $idGrupo)
    {
        if (!$idPet) {
            return response()->json(["msg" => "Pet não encontrado!"], 404);
        }

        $data = new \stdClass();
        $pet = (new Pets())->find($idPet);
        $plano = $pet->plano();

        $carencias = $pet->getCarenciasProcedimentos();

        if ($plano->bichos == '1')
        {
            $pg = PlanosProcedimentos::where('id_plano', $plano->id)
            ->where('id_procedimento', $idGrupo)->get();

            $data->procedimentos = $pg->map(function ($proc) use ($pet, $idGrupo, $pg, $carencias, $plano) {

                $carencia = $proc->bichos_carencia;
                $carenciaRestante = $carencia - $carencias[$proc->id_procedimento];
                $diasCarencia = $carenciaRestante > 0 ? $carenciaRestante : 0;
                $labelCarencia = $diasCarencia == 0 ? "Cumpriu" : "Faltam {$carenciaRestante} dias";

                $procedimento = $proc->procedimento;

                $p = new \stdClass();
                $p->id = $proc->id_procedimento;
                $p->nome = $procedimento->nome_procedimento;
                $p->intervalo_usos = $procedimento->intervalo_usos;
                $p->dias_carencia = $diasCarencia;
                $p->carencia = $labelCarencia;
                $p->valor_cliente = Utils::money(PlanosProcedimentos::getValorBeneficio($procedimento, $plano));
                $p->valor_plano_participativo_antigo = Utils::money(PlanosProcedimentos::getValorBeneficio($procedimento, $plano));
                $p->plano_isento = $plano->isento;
                $p->valor_plano_isento = Utils::money(PlanosProcedimentos::getValorIsento($procedimento, $plano));
                $p->valor_plano_participativo_novo = Utils::money(PlanosProcedimentos::getValorIsento($procedimento, $plano));
                return $p;

            });
        } else {
            $pg = (new PlanosGrupos())
                ->where('plano_id', $plano->id)
                ->where('grupo_id', $idGrupo)
                ->first();


            $data->procedimentos = $pet->getProcedimentosPorGrupo($pg)->map(function ($proc) use ($pet, $idGrupo, $pg, $carencias, $plano) {

                $excecao = \App\Models\PetsGrupos::where('id_pet', $pet->id)->where('id_grupo', $idGrupo)->first();
                $carencia = $excecao ? $excecao->dias_carencia : $pg->dias_carencia;
                $carenciaRestante = $carencia - $carencias[$proc->id];
                $diasCarencia = $carenciaRestante > 0 ? $carenciaRestante : 0;
                $labelCarencia = $diasCarencia == 0 ? "Cumpriu" : "Faltam {$carenciaRestante} dias";

                $procedimento = Procedimentos::find($proc->id);

                $p = new \stdClass();
                $p->id = $proc->id;
                $p->nome = $proc->nome_procedimento;
                $p->intervalo_usos = $proc->intervalo_usos;
                $p->dias_carencia = $diasCarencia;
                $p->carencia = $labelCarencia;
                $p->valor_cliente = Utils::money(PlanosProcedimentos::getValorCliente($procedimento, $plano));
                $p->valor_plano_participativo_antigo = Utils::money(PlanosProcedimentos::getValorCliente($procedimento, $plano));
                $p->plano_isento = $plano->isento;
                $p->valor_plano_isento = Utils::money(PlanosProcedimentos::getValorIsento($procedimento, $plano));
                $p->valor_plano_participativo_novo = Utils::money(PlanosProcedimentos::getValorIsento($procedimento, $plano));
                return $p;
            });
        }

        $data->procedimentos = $data->procedimentos->filter(function($p) {
            return $p->id !== 101011925;
        });

        return Response::json($data);
    }

    public function cobrancasPorCpf(Request $request, $cpf)
    {
        $secret = 'l1f3p3t';
        $token = 'afe37c0a4a0a8e48a43604abfe617388';
        if(!$request->get('token') || $request->get('token') != $token) {
            return abort(403, 'Você não está autorizado a executar essa ação.');
        }

        $cliente = Clientes::cpf($cpf)->first();

        if(!$cliente) {
            return abort(404, 'Não foi encontrado um cliente com o CPF informado.');
        }

        $data = new \stdClass();

        if($cliente->forma_pagamento === 'cartao') {
            $data->mensagem = "Oi {$cliente->nome_cliente} em nosso cadastro a sua opção de pagamento é pelo cartão de crédito, não há boletos para pagamento.";
            return Response::json($data);
        }


        $data->cobrancas = $cliente->cobrancas()
            ->where('status', 1)
            ->whereNull('acordo')
            ->whereNull('cancelada_em')
            ->orderBy('competencia', 'DESC')
            ->get()
            ->filter(function($c) {
                return $c->pagamentos->count() == 0;
            })
            ->map(function ($cobranca) use ($request, $data) {
                $pagamentos = $cobranca->pagamentos()->get();
                $status = "A Vencer";

                if ((new Carbon())->gt($cobranca->data_vencimento->addDays(2))) {
                    $status = "Atrasado";
                    $cobranca->status = 0;
                }

                $cob = new \stdClass();
                $cob->id = $cobranca->id;
                $cob->status = $status;
                $competencia = explode('-', $cobranca->competencia);
                $cob->competencia_mes = Utils::getMonthName($competencia[1]);
                $cob->competencia_ano = $competencia[0];
                $cob->valor_original = Utils::money($cobranca->valor_original);
                $cob->data_vencimento = $cobranca->data_vencimento->format('d/m/Y');
                $cob->status = strtoupper($status);

                switch ($status) {
                    case 'Atrasado':
                        $cob->helper = "O plano é automaticamente <b>suspenso</b> para atendimento após 72h de atraso e automaticamente <b>cancelado</b> após 60 dias. Não perca a proteção para seu pet.";
                        $cob->dias_atraso = (new Carbon())->diffInDays($cobranca->data_vencimento);
                        $cob->link_segunda_via = $cobranca->linkSegundaVia();
                        break;
                    case 'A Vencer':
                        $cob->helper = "O plano é automaticamente <b>suspenso</b> para atendimento após 72h de atraso e automaticamente <b>cancelado</b> após 60 dias. Não perca a proteção para seu pet.";
                        $cob->dias_para_vencimento = (new Carbon())->diffInDays($cobranca->data_vencimento);
                        $cob->link_segunda_via = $cobranca->linkSegundaVia();
                        break;
                }
                return $cob;
            })->reject(function ($value) {
                return $value === false;
            });

        if(count($data->cobrancas) == 0) {
            $data->mensagem = "Oi {$cliente->nome_cliente}, não consta em seu cadastro boletos em aberto ou atrasados ;) \n" .
                              "Agradecemos por manter seu plano em dia!";
        } else {
            $data->mensagem = "Oi {$cliente->nome_cliente}, confira abaixo o(s) boleto(s) que constam para pagamento. É muito importante manter o seu plano em dia e a assistência do(s) seu(s) pet(s) garantida =)\n";
            foreach($data->cobrancas  as $c) {
                $mensagemCobranca = "{$c->competencia_ano}/{$c->competencia_mes}: {$c->valor_original} {$c->link_segunda_via} ({$c->status})\n";

                $data->mensagem .= $mensagemCobranca;
            }
        }

        return Response::json($data);
    }

    public function clienteCobrancas(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $cliente = (new Clientes())->where('id_usuario', $user->id)->first();
        }

        $data = new \stdClass();
        $data->cobrancas = $cliente->cobrancas()
            ->where('status', 1)
            ->whereNull('acordo')
            ->whereNull('cancelada_em')
            ->orderBy('competencia', 'DESC')
            ->get()
            ->map(function ($cobranca) use ($request, $data) {

//                $pagamentos = $cobranca->pagamentos()->where(function($query) {
//                    return $query->where(function($query) {
//                        //Verificar se o pagamento e do SF
//                        $query->whereNotNull('id_financeiro');
//                        $query->whereNull('id_pagamento_superlogica');
//                    })->orWhere(function($query) {
//                        //Pagamento do superlogica
//                        $query->whereNull('id_financeiro');
//                        $query->whereNotNull('id_pagamento_superlogica');
//                    });
//                })->get();
                //Existem pagamentos que não são oriundos de nenhum sistema.
                $pagamentos = $cobranca->pagamentos()->get();

                $valorPago = $pagamentos->sum('valor_pago');
                if($valorPago >= $cobranca->valor_original) {
                    $valorPago = $cobranca->valor_original;
                }

                $status = "A Vencer";

                if ($cobranca->status && count($pagamentos)) {
                    $status = "Pago";
                } else {
                    if ((new Carbon())->gt($cobranca->data_vencimento->addDays(2))) {
                        $status = "Atrasado";
                        $cobranca->status = 0;
                    }
                }

                if ($request->get('atrasadas') == 'true' && $status == 'Pago') {
                    return false;
                } else {

                    $cob = new \stdClass();
                    $cob->id = $cobranca->id;
                    $cob->status = $status;
                    $competencia = Utils::dateExploded($cobranca->competencia);//explode('-', $cobranca->competencia);
                    $cob->competencia_mes = Utils::getMonthName($competencia[1]);
                    $cob->competencia_ano = $competencia[0];
                    $cob->valor_original = Utils::money($cobranca->valor_original);
                    $cob->data_vencimento = $cobranca->data_vencimento->format('d/m/Y');

                    switch ($status) {
                        case 'Pago':

                            $cob->valor_pago = Utils::money($valorPago);
                            $cob->data_pagamento = $pagamentos->first()->data_pagamento->format('d/m/Y');

                            break;
                        case 'Atrasado':

                            $cob->helper = "O plano é automaticamente <b>suspenso</b> para atendimento após 72h de atraso e automaticamente <b>cancelado</b> após 60 dias. Não perca a proteção para seu pet.";
                            $cob->dias_atraso = (new Carbon())->diffInDays($cobranca->data_vencimento);
                            $cob->link_segunda_via = $cobranca->linkSegundaVia();

                            break;
                        case 'A Vencer':

                            $cob->helper = "O plano é automaticamente <b>suspenso</b> para atendimento após 72h de atraso e automaticamente <b>cancelado</b> após 60 dias. Não perca a proteção para seu pet.";
                            $cob->dias_para_vencimento = (new Carbon())->diffInDays($cobranca->data_vencimento);
                            $cob->link_segunda_via = $cobranca->linkSegundaVia();

                            break;
                    }

                    return $cob;
                }
            })->reject(function ($value) {
                return $value === false;
            });

        return Response::json($data);
    }

    public function clienteCobrancaDados($idCobranca)
    {
        $user = Auth::user();
        if ($user) {
            $cliente = (new Clientes())->where('id_usuario', $user->id)->first();
        }

        return $cliente->cobrancas()->where('id', $idCobranca)->get();
    }

    public function clienteDocumentos()
    {
        $user = Auth::user();
        if ($user) {
            $cliente = (new Clientes())->where('id_usuario', $user->id)->first();
        }

        $data = new \stdClass();
        $data->documentos = $cliente->uploads()->get()->map(function ($doc) {
            $d = new \stdClass();
            $d->description = $doc->description;
            $d->extension = $doc->extension;
            $d->url = url('/') . '/' . $doc->path;
            $d->size = $doc->size;
            return $d;
        });

        //Documentos de planos:
        /**
         * @var Pets $pet
         */
        foreach($cliente->pets as $pet) {
            if($pet->ativo && $pet->plano()) {
                $plano = $pet->plano();
                if($plano->id) {
                    foreach($plano->documentos() as $documento) {
                        $docObject = new \stdClass();
                        $docObject->description = $documento->description;
                        $docObject->extension = $documento->extension;
                        $docObject->url = url('/') . '/' . $documento->path;
                        $docObject->size = $documento->size;

                        $data->documentos[] = $docObject;
                    }
                }
            }
        }

        return Response::json($data);
    }

    public function clienteDocumentosCadastrais() 
    {
        $user = Auth::user();
        if ($user) {
            $cliente = (new Clientes())->where('id_usuario', $user->id)->first();
        }
        $hidden_attributes = ['id_usuario_aprovacao', 'id_usuario_reprovacao', 'data_envio', 'data_reprovacao', 'data_aprovacao'];
        $documentos_pets = [];
        foreach ($cliente->pets as $pet) {
            if ($pet->documentos()->exists()) {
                $pet_doc = [
                    'id_pet' => $pet->id,
                    'nome_pet' => $pet->nome_pet,
                    'possui_reprovado' => false
                ];
                foreach ($pet->documentos as $doc) {
                    $documentos = $doc->makeHidden($hidden_attributes);
                    $documentos->uploads = $documentos->uploads()->get()->map(function ($d) {
                        return [
                            'id' => $d->id,
                            'path' => url('/') . '/' . $d->path,
                        ];
                    });
                    if ($documentos->status == DocumentosPets::STATUS_REPROVADO) {
                        $pet_doc['possui_reprovado'] = true;
                    }
                    $pet_doc['documentos_pets'][] = $documentos;
                }
                $documentos_pets[] = $pet_doc;
            }
        }
        
        $documentos_pendentes = $cliente->documentos()
                                        ->where('avaliacao_obrigatoria', 1)
                                        ->where('status', DocumentosClientes::STATUS_PENDENTE)
                                        ->get()
                                        ->makeHidden($hidden_attributes)
                                        ->map(function ($doc) {
                                            $doc->uploads = $doc->uploads()->select(['id','path'])->get()->map(function ($d) {
                                                return [
                                                    'id' => $d->id,
                                                    'path' => url('/') . '/' . $d->path,
                                                ];
                                            });
                                            return $doc;
                                        });
        
        $documentos_reprovados = $cliente->documentos()
                                        ->where('avaliacao_obrigatoria', 1)
                                        ->where('status', DocumentosClientes::STATUS_REPROVADO)
                                        ->get()
                                        ->makeHidden($hidden_attributes)
                                        ->map(function ($doc) {
                                            $doc->uploads = $doc->uploads()->select(['id','path'])->get()->map(function ($d) {
                                                return [
                                                    'id' => $d->id,
                                                    'path' => url('/') . '/' . $d->path,
                                                ]; 
                                            });
                                            return $doc;
                                        });

        $documentos_pets_reprovados = [];
        foreach ($documentos_pets as $pet) {
            $pet_docs = $pet;
            $pet_docs['documentos_pets'] = array_filter($pet_docs['documentos_pets'], function ($doc) {
                return ($doc['status'] == DocumentosPets::STATUS_REPROVADO);
            });
            // collect($pet_docs['documentos_pets'])->where('status', DocumentosPets::STATUS_REPROVADO)->toArray();
            if ($pet_docs['documentos_pets']) {
                $documentos_pets_reprovados[] = $pet_docs;
            }
        }
        
        $data = [
            'documentos_pendentes' => $documentos_pendentes,
            'documentos_reprovados' => $documentos_reprovados,
            'documentos_pets' => $documentos_pets,
            'documentos_pets_reprovados' => $documentos_pets_reprovados
        ];
//
//        $data = [
//            'documentos_pendentes' => [],
//            'documentos_reprovados' => [],
//            'documentos_pets' => [],
//            'documentos_pets_reprovados' => []
//        ];

        return $data;
    }
    
    public function clienteDocumentosCadastraisEnviar(Request $request) 
    {
        $user = Auth::user();
        if ($user) {
            $cliente = (new Clientes())->where('id_usuario', $user->id)->first();
        }

        $documentos_clientes = $request->file('documentos_clientes');
        $documentos_pets = $request->file('documentos_pets');
        
        if ($documentos_clientes) {
            foreach ($documentos_clientes as $id_doc => $files) {
                $documento = DocumentosClientes::find($id_doc);
                $documento->update([
                    'status' => DocumentosClientes::STATUS_ENVIADO,
                    'data_envio' => Carbon::now()
                ]);
                foreach ($files as $file) {
                    Uploads::makeUpload($file, 'documentos_clientes', $documento->id, $documento->nome);
                }
            }
        }
        
        if ($documentos_pets) {
            foreach ($documentos_pets as $id_doc => $files) {
                $documento = DocumentosPets::find($id_doc);
                $documento->update([
                    'status' => DocumentosPets::STATUS_ENVIADO,
                    'data_envio' => Carbon::now()
                ]);
                foreach ($files as $file) {
                    Uploads::makeUpload($file, 'documentos_pets', $documento->id, $documento->nome);
                }
            }
        }

        return response()->json([
            "msg" => "Documentos enviados com sucesso!",
            "status" => self::getStatusDocumentacaoCliente($cliente)
        ], 200);
    }

    public function clienteIndicacoes()
    {
        $user = Auth::user();
        if ($user) {
            $cliente = (new Clientes())->where('id_usuario', $user->id)->first();
        }

        $data = new \stdClass();
        $data->indicacoes = $cliente->indicacoes()->get()->map(function ($ind) {
            $i = new \stdClass();
            $i->id = $ind->id;
            $i->nome = $ind->nome;
            $i->email = $ind->email;
            $i->telefone = $ind->telefone;
            return $i;
        });
        return Response::json($data);
    }

    public function clienteIndicar(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $cliente = (new Clientes())->where('id_usuario', $user->id)->first();
        }

        if (!$request->get('telefone')) {
            return response()->json(["msg" => "O campo de telefone é obrigatório!"], 401);
        } elseif (!$request->get('nome')) {
            return response()->json(["msg" => "O campo de nome é obrigatório!"], 401);
        } else {

            /**
             * Verifica indicação por TELEFONE
             */
            $telefoneExists = false;
            $telefoneClientesExists = (new Clientes())
                ->whereRaw('TRIM(REPLACE(REPLACE(REPLACE(REPLACE(celular, \'(\', \'\'), \')\', \'\'), \' \', \'\'), \'-\', \'\')) = \'' . $request->get('telefone') . '\'')
                ->exists();
            $telefoneIndicacoesExists = (new Indicacoes())
                ->whereRaw('TRIM(REPLACE(REPLACE(REPLACE(REPLACE(telefone, \'(\', \'\'), \')\', \'\'), \' \', \'\'), \'-\', \'\')) = \'' . $request->get('telefone') . '\'')
                ->exists();
            $telefoneExists = $telefoneClientesExists || $telefoneIndicacoesExists;


            /**
             * Verifica indicação por EMAIL
             */
            $emailExists = false;
            if ($request->get('email')) {
                $email_indicado = $request->get('email');
                $emailClientesExists = (new Clientes())->where('email', $email_indicado)->exists();
                $emailIndicacoesExists = (new Indicacoes())->where('email', $email_indicado)->exists();
                $emailExists = $emailClientesExists || $emailIndicacoesExists;
            } else {
                $email_indicado = null;
            }

            if ($telefoneExists || $emailExists) {
                return response()->json(["msg" => "Este cliente já foi indicado ou já é um cliente Lifepet!"], 401);
            } else {

                (new \App\Models\Indicacoes())->create([
                    'id_cliente' => $cliente->id,
                    'nome' => $request->get('nome'),
                    'email' => $email_indicado,
                    'telefone' => $request->get('telefone')
                ]);

                $client = new \GuzzleHttp\Client();
                $client->post(
                    'https://www.rdstation.com.br/api/1.2/conversions',
                    array(
                        'form_params' => array(
                            'token_rdstation' => "0eb70ce4d806faa1a1a23773e3d174d4",
                            'identificador' => "indicacao-de-cliente",
                            'email' => $email_indicado ?: "indicadosememail@lifepet.com.br",
                            'mobile_phone' => $request->get('telefone'),
                            'name' => $request->get('nome'),
                            'custom_fields[808149]' => $cliente->nome_cliente,
                            'custom_fields[808152]' => $cliente->id,
                        )
                    )
                );

                return response()->json(["msg" => "Cliente indicado com sucesso!"], 200);
            }

        }

    }

    public function assinarGuia(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $cliente = (new Clientes())->where('id_usuario', $user->id)->first();
        }

        if (!$request->get('numero_guia')) {
            return response()->json(["msg" => "O número da guia ou a senha não foi reconhecido!"], 401);
        } else if (!$request->get('senha_plano')) {
            return response()->json(["msg" => "A senha não confere"], 401);
        }

        $res = $cliente->assinarGuia($request->get('numero_guia'), $request->get('senha_plano'), 2);

        return response()->json(["msg" => $res['msg']], $res['http']);
    }

    public function resetarAssinaturasGuias()
    {
        $guias = (new \Modules\Guides\Entities\HistoricoUso())::whereNotNull('assinatura_cliente')->update([
            'assinatura_cliente' => null,
            'data_assinatura_cliente' => null,
            'meio_assinatura_cliente' => null
        ]);
        return response()->json(["msg" => "Sucesso!"], 200);
    }

    public function clienteDados()
    {
        $user = Auth::user();
        if ($user) {
            $cliente = (new Clientes())->where('id_usuario', $user->id)->first();
        }

        // START - Tratamento Celular
        $celular = explode('/', $cliente->celular)[0];
        $celular = explode(' - ', $celular)[0];

        if (strlen($celular) && strpos($celular, '(', 1) !== false) {
            $celular = explode('(', $celular)[0];
        }

        $celular = trim($celular);
        $celular = str_replace(' ', '', $celular);
        $celular = str_replace('(', '', $celular);
        $celular = str_replace(')', '', $celular);
        $celular = str_replace('-', '', $celular);

        $celular = preg_replace('/[^0-9]/', '', $celular);

        if (substr_count($celular, ' ') > 2) {
            $celular = explode(' ', $celular);
            $celular = $celular[0] . $celular[1];
        }

        $cliente->celular = $celular;
        // END - Tratamento Celular

        $cliente->nome_cliente = ucwords(mb_strtolower($cliente->nome_cliente));
        $nome_curto = explode(" ", $cliente->nome_cliente);
        $nome_curto = $nome_curto[0] . ' ' . $nome_curto[1];

        $exploded_nome_cliente = explode(" ", $cliente->nome_cliente);
        $nome_cliente = $exploded_nome_cliente[0] . ' ' . (isset($exploded_nome_cliente[1]) ? $exploded_nome_cliente[1] : '');

        $pets = $cliente->pets;
        $isLifepet = false;
        $isAngel = false;
        foreach ($pets as $pet) {
            if ($pet->petsPlanosAtual()->exists()) {
                $isLifepet = true;
            }

            if ($pet->angel) {
                $isAngel = true;
            }
        }

        // NPS
        $exibirNps = false;
        $npsExists = Nps::where('id_cliente', $cliente->id)
                    ->where('created_at', '>=', Carbon::now()->subDays(90))
                    // ->where(function ($query) {
                    //     $query->whereRaw('MONTH(created_at) = ?', [Carbon::now()->month]);
                    //     $query->orWhere('created_at', '>=', Carbon::now()->subDays(20));
                    // })
                    ->exists();
        if (!$npsExists) {
            $exibirNps = true;
        }

        return response()->json([
            'cliente' => [
                "id" => $cliente->id,
                "inadimplente" => $cliente->statusPagamento() == Clientes::PAGAMENTO_EM_DIA ? false : true,
                "nome_cliente" => ucwords(mb_strtolower($nome_cliente)),
                "nome_completo" => ucwords(mb_strtolower($cliente->nome_cliente)),
                "cpf" => $cliente->cpf,
                "rg" => $cliente->rg,
                "data_nascimento" => $cliente->data_nascimento->format('d/m/Y'),
                "cep" => $cliente->cep,
                "rua" => $cliente->rua,
                "numero_endereco" => $cliente->numero_endereco,
                "complemento_endereco" => $cliente->complemento_endereco,
                "bairro" => $cliente->bairro,
                "cidade" => $cliente->cidade,
                "estado" => $cliente->estado,
                "telefone_fixo" => $cliente->telefone_fixo,
                "celular" => $cliente->celular,
                "email" => $cliente->email,
                "ativo" => $cliente->ativo,
                "sexo" => $cliente->sexo,
                "estado_civil" => $cliente->estado_civil,
                "vencimento" => $cliente->vencimento,
                "participativo" => $cliente->participativo,
                "segundo_responsavel_nome" => $cliente->segundo_responsavel_nome,
                "segundo_responsavel_email" => $cliente->segundo_responsavel_email,
                "segundo_responsavel_telefone" => $cliente->segundo_responsavel_telefone,
                "token_firebase" => $cliente->token_firebase,
                "senha_plano" => $cliente->senha_plano,

                "lifepet" => $isLifepet,
                "angel" => $isAngel,
                "status_documentacao" => self::getStatusDocumentacaoCliente($cliente),

                "exibir_nps" => $exibirNps,

                "forma_pagamento" => $cliente->forma_pagamento
            ]
        ]);
    }

    public function getCartoesCredito(Request $request) {
        $user = Auth::user();
        if ($user) {
            $cliente = (new Clientes())->where('id_usuario', $user->id)->first();
        }

        if($cliente->forma_pagamento != Clientes::FORMA_PAGAMENTO_CARTAO) {
            return [
                'error' => true,
                'message' => 'Cliente não optante pela forma de pagamento de \'CARTÃO\''
            ];
        }

        if(!empty($cliente->id_externo)) {
            try {
                $customer = CustomerService::getByRefcode($cliente->id_externo);
                $cards = CreditCardService::getCards($customer);
            }
            catch (\Exception $e){
                return [
                    'error' => true,
                    'message' => "Erro ao obter dados do sistema financeiro. (1)\n",
                    'exception' => [
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]
                ];
            }

            return [
                'error' => false,
                'message' => 'Sucesso',
                'data' => [
                    'cartoes' => $cards
                ]
            ];
        }

        return [
            'error' => true,
            'message' => 'Erro ao obter dados do sistema financeiro. (2)'
        ];
    }

    public function getCartaoPrincipal(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $cliente = (new Clientes())->where('id_usuario', $user->id)->first();
        }
        try {
            $customer = CustomerService::getByRefcode($cliente->id_externo);
            $default = CreditCardService::getDefaultCreditCard($customer);
            return [
                'cartao_principal' => $default
            ];
        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
                'exception' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ];
        }
    }

    public function getFormaPagamento(Request $request)
    {
        $user = Auth::user();
        $cliente = (new Clientes())->where('id_usuario', $user->id)->first();

        return [
            'forma_pagamento' => $cliente->forma_pagamento
        ];
    }

    public function setCartaoPrincipal(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $cliente = (new Clientes())->where('id_usuario', $user->id)->first();
        }

        if(!$cliente) {
            abort(404, 'Cliente não encontrado');
        }

        $id_cartao = $request->get('card_id');

        //self::notice("SF: O cartão principal do cliente $customer_id foi alterado para o cartão $card_id", 'sistema_financeiro', null, null, auth()->user()->id);

        try {
            $customer = CustomerService::getByRefcode($cliente->id_externo);
            CreditCardService::setAsDefault($customer, $id_cartao);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function inserirCartao(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $cliente = (new Clientes())->where('id_usuario', $user->id)->first();
        } else {
            abort(404, 'Cliente não encontrado');
        }

        $finance = new Financeiro();
        $logger = new \App\Http\Util\Logger('app_cliente');

        try {
            $info = $finance->get('/customer/refcode/'.$cliente->id_externo);
            $customer = $info->data;
            $card = null;
            $card = $finance->addCreditCard($customer, $request->all());
            if($card) {

                try {
                    $finance->get("/customer/card/default/{$cliente->id_externo}/{$card->id}", []);
                } catch (\Exception $e) {
                    $logger->register(\App\Http\Util\LogEvent::ERROR, \App\Http\Util\LogPriority::HIGH,
                        "Não foi possível definir o cartão #{$card->id} como principal. REF_CODE #{$cliente->id_externo}", $cliente->id, 'clientes'
                    );
                }

                $card->default = true;
                return [
                    'error' => null,
                    'message' => 'Cartão adicionado com sucesso',
                    'status' => true,
                    'data' => $card
                ];
            } else {
                return [
                    'error' => null,
                    'message' => 'Erro desconhecido ao adicionar cartão.',
                    'status' => false,
                    'data' => $card
                ];
            }
        } catch (\Exception $e) {
            $logger->register(\App\Http\Util\LogEvent::ERROR, \App\Http\Util\LogPriority::HIGH,
                "Não foi possível salvar o cartão de crédito. O número do cartão informado é inválido. Cliente #{$cliente->id}", $cliente->id, 'clientes'
            );

            return [
                'erro' => $e->getMessage(),
                'message' => 'O número do cartão informado é inválido.',
                'status' => false,
                'exception' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ];
        }
    }

    public function inativarCartao(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $cliente = (new Clientes())->where('id_usuario', $user->id)->first();
        } else {
            abort(404, 'Cliente não encontrado');
        }

        //$finance = new Financeiro();
        $logger = new \App\Http\Util\Logger('app_cliente');

        $id_cartao = $request->get('card_id', null);
        if(!$id_cartao) {
            abort(500, 'O ID do cartão (card_id) é obrigatório para a operação.');
        }

        $cartoes = $this->getCartoesCredito($request);
        if($cartoes['error']) {
            abort(500, 'Não foi possível checar se o cartão pertente ao cliente solicitante.');
        }
        if(empty($cartoes['data']['cartoes'])) {
            abort(500, 'O cliente não possui cartões cadastrados, portanto não pode excluir.');
        }

        $cartoes = $cartoes['data']['cartoes'];
        $cartoes = collect($cartoes);
        $cartao = $cartoes->where('id', $id_cartao)->first();
        if(!$cartao) {
            abort(500, 'O cartão informado não pertence ao solicitante da exclusão.');
        }

        try {
            $customer = CustomerService::getByRefcode($cliente->id_externo);
            CreditCardService::remove($customer, $id_cartao);

            $logger->register(\App\Http\Util\LogEvent::DELETE, \App\Http\Util\LogPriority::HIGH,
                "O cartão #$id_cartao foi excluído do SF. Cliente #{$cliente->id}", $cliente->id, 'clientes'
            );

            return [
                'erro' => false,
                'mensagem' => 'O cartão foi excluído com sucesso.',
            ];
        } catch (\Exception $e) {
            $logger->register(\App\Http\Util\LogEvent::ERROR, \App\Http\Util\LogPriority::HIGH,
                "Não foi possível excluir o cartão de crédito #$id_cartao. Cliente #{$cliente->id}", $cliente->id, 'clientes'
            );

            return [
                'erro' => true,
                'mensagem' => $e->getMessage()
            ];
        }
    }

    public function redeCredenciada(Request $request)
    {
        $user = Auth::user();
        $data = new \stdClass();
        if($user) {
            $cliente = Clientes::where('id_usuario', $user->id)->first();

            $pets = $cliente->pets()->where('ativo', 1)->get();
            $clinicas = null;

            foreach ($pets as $p) {
                /**
                 * @var Pets $p
                 */
                $p = $p->plano();
                $credenciados = $p->credenciados()->select('clinicas.id')->get()->map(function($c) { return $c->id; });
                if(!$clinicas) {
                    $clinicas = $credenciados;
                } else {
                    $clinicas->merge($credenciados);
                }
            }


            $clinicas = Clinicas::whereIn('id', $clinicas->unique())->get();
            $clinicas = (new Clinicas())->mapRedeCredenciada($clinicas);
        } else {
            $clinicas = (new Clinicas())->getRedeCredenciadaMap();
        }

        // Filtros
        if ($request->get('cidade')) {
            $clinicas = $clinicas->where('cidade', ucwords(mb_strtolower($request->get('cidade'))));
        }
        if ($request->get('estado')) {
            $clinicas = $clinicas->where('estado', ucwords(mb_strtolower($request->get('estado'))));
        }
        if ($request->get('numero_guia')) {
            $clinicas = $clinicas->filter(function($clinica) use ($request) {
                $clinicaObj = (new \Modules\Clinics\Entities\Clinicas())->find($clinica->id);
                return $clinicaObj->checkCategoriaGuia($request->get('numero_guia'));
            });
        }

        $data->clinicas = [];
        foreach ($clinicas as $clinica) {
            $clinica->estado = mb_strtoupper($clinica->estado);
            $clinica->cidade = mb_strtoupper($clinica->cidade);
            $data->clinicas[] = $clinica;
        }

        return Response::json($data);
    }

    public function redeCredenciadaPorPet(Request $request, $idPet)
    {
        $data = new \stdClass();
        if(!$idPet) {
            return abort(500, 'id_pet é um parâmetro obrigatório.');
        }

        $pet = Pets::find($idPet);
        if(!$pet) {
            return abort(404, 'Pet não encontrado.');
        }


        $p = $pet->plano();
        $clinicas = $p->credenciados()->where('ativo', 1)
            ->where('exibir_site', 1)
            ->whereNotNull('nome_site')
            ->whereNotNull('lat')
            ->whereNotNull('lng')->get();
        $clinicas = (new Clinicas())->mapRedeCredenciada($clinicas);
        // Filtros
        if ($request->get('cidade')) {
            $clinicas = $clinicas->where('cidade', ucwords(mb_strtolower($request->get('cidade'))));
        }
        if ($request->get('estado')) {
            $clinicas = $clinicas->where('estado', ucwords(mb_strtolower($request->get('estado'))));
        }
        if ($request->get('numero_guia')) {
            $clinicas = $clinicas->filter(function($clinica) use ($request) {
                $clinicaObj = (new \Modules\Clinics\Entities\Clinicas())->find($clinica->id);
                return $clinicaObj->checkCategoriaGuia($request->get('numero_guia'));
            });
        }

        $data->clinicas = [];
        foreach ($clinicas as $clinica) {
            $clinica->estado = mb_strtoupper($clinica->estado);
            $clinica->cidade = mb_strtoupper($clinica->cidade);
            $data->clinicas[] = $clinica;
        }

        return Response::json($data);
    }

    public function webviews()
    {
        $webviews = [
            'adesao' => 'https://www.lifepet.com.br/planos',
            'ajuda' => 'https://www.lifepet.com.br/ajudaapp',
        ];

        return response()->json(["webviews" => $webviews], 200);
    }

    public function parametros()
    {
        $params = (new Parametros())->where('tipo', 'LIKE', 'app_cliente%')->get();

        $parametros = [];
        foreach ($params as $param) {
            $tipo = str_replace('app_cliente_', '', $param->tipo);
            $parametros[$tipo][$param->chave] = $param->valor;
        }

        return response()->json($parametros, 200);
    }

    public function cronPushNotifications() {

        self::pushAniversariantes();

        $status = \App\Http\Util\Logger::log(
            \App\Http\Util\LogMessages::EVENTO['NOTIFICACAO'],
            'Push Notifications',
            \App\Http\Util\LogMessages::IMPORTANCIA['BAIXA'],
            "Os push notifications do dia " . (new \Carbon\Carbon())->format('d/m/Y') . " foram enviados com sucesso.");

        return [
            'msg' => 'Sucesso!'
        ];
    }

    public function home(Request $request){
        $dados = [
            'dados' => $this->clienteDados()->getData()->cliente,
            'pets' => $this->meusPets($request)->getData()->pets,
            'cobrancas' => $this->clienteCobrancas($request)->getData()->cobrancas
        ];
        return $dados;
    }

    public function inicial(Request $request){
        $user = Auth::user();
        if ($user) {
            $cliente = (new Clientes())->where('id_usuario', $user->id)->first();
        }

        $dados = $this->clienteDados()->getData()->cliente;
        $pets = $this->meusPets($request)->getData()->pets;
        $cobrancas = $this->clienteCobrancas($request)->getData()->cobrancas;
        $cobrancas = collect($cobrancas)->whereIn('status', ['Atrasado', 'A Vencer']);

        $id_pets =  $cliente->pets()->where('ativo', 1)->get()->pluck('id');
        $guias = (new \Modules\Guides\Entities\HistoricoUso())
            ->whereIn('id_pet', $id_pets)
            ->where('tipo_atendimento', (new \Modules\Guides\Entities\HistoricoUso())::TIPO_ENCAMINHAMENTO)
            ->where('status', '!=',(new \Modules\Guides\Entities\HistoricoUso())::STATUS_RECUSADO)
            ->whereNull('realizado_em')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($guia) {
                return self::getDadosGuia($guia);
            });

        $guias_assinatura = (new \Modules\Guides\Entities\HistoricoUso())
            ->whereIn('id_pet', $id_pets)
            ->where('status', (new \Modules\Guides\Entities\HistoricoUso())::STATUS_LIBERADO)
            ->whereNull('meio_assinatura_cliente')
            ->whereNull('assinatura_cliente')
            ->where(function ($query) {
                $query->where('tipo_atendimento', (new \Modules\Guides\Entities\HistoricoUso())::TIPO_NORMAL);
                $query->orWhere(function ($query) {
                    $query->where('tipo_atendimento', (new \Modules\Guides\Entities\HistoricoUso())::TIPO_ENCAMINHAMENTO);
                    $query->whereNotNull('realizado_em');
                });
            })
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($guia) {
                return self::getDadosGuia($guia);
            });

        $carteiraDigitalSaldo = $cliente->carteiraDigitalSaldo();
        $carteiraDigitalSaldo = number_format($carteiraDigitalSaldo, 2, ',', '.');

        $dados = [
            'avisos' => [
                'guias' => $guias,
                'guias_assinatura' => $guias_assinatura,
                'cobrancas' => $cobrancas,
            ],
            'dados' => $dados,
            'pets' => $pets,
            'carteira_digital' => [
                'saldo' => $carteiraDigitalSaldo
            ]
        ];
        return $dados;
    }

    public function avisos(Request $request){
        $user = Auth::user();
        if ($user) {
            $cliente = (new Clientes())->where('id_usuario', $user->id)->first();
        }

        $dados = $this->clienteDados()->getData()->cliente;
        $pets = $this->meusPets($request)->getData()->pets;
        $cobrancas = $this->clienteCobrancas($request)->getData()->cobrancas;
        $cobrancas = collect($cobrancas)->whereIn('status', ['Atrasado', 'A Vencer']);

        $id_pets =  $cliente->pets()->where('ativo', 1)->get()->pluck('id');
        $guias = (new \Modules\Guides\Entities\HistoricoUso())
            ->whereIn('id_pet', $id_pets)
            ->where('tipo_atendimento', (new \Modules\Guides\Entities\HistoricoUso())::TIPO_ENCAMINHAMENTO)
            ->where('status', '!=',(new \Modules\Guides\Entities\HistoricoUso())::STATUS_RECUSADO)
            ->whereNull('realizado_em')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($guia) {
                return self::getDadosGuia($guia);
            });

        $guias_assinatura = (new \Modules\Guides\Entities\HistoricoUso())
            ->whereIn('id_pet', $id_pets)
            ->where('status', (new \Modules\Guides\Entities\HistoricoUso())::STATUS_LIBERADO)
            ->whereNull('meio_assinatura_cliente')
            ->whereNull('assinatura_cliente')
            ->where(function ($query) {
                $query->where('tipo_atendimento', (new \Modules\Guides\Entities\HistoricoUso())::TIPO_NORMAL);
                $query->orWhere(function ($query) {
                    $query->where('tipo_atendimento', (new \Modules\Guides\Entities\HistoricoUso())::TIPO_ENCAMINHAMENTO);
                    $query->whereNotNull('realizado_em');
                });
            })
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($guia) {
                return self::getDadosGuia($guia);
            });

        $dados = [
            'guias' => $guias,
            'guias_assinatura' => $guias_assinatura,
            'cobrancas' => $cobrancas,
        ];
        return $dados;
    }

    public function getBancos() {
        $bancos = [];
        $bancosPrioridade = [
            '033' => 'Banco Santander (Brasil) S.A.',
            '237' => 'Banco Bradesco S.A.',
            '341' => 'Itaú Unibanco S.A.',
            '001' => 'Banco do Brasil S.A.',
            '104' => 'Caixa Econômica Federal',
            '399' => 'HSBC Bank Brasil S.A. - Banco Múltiplo',
            '745' => 'Banco Citibank S.A.',
            '004' => 'Banco do Nordeste do Brasil S.A.',
            '422' => 'Banco Safra S.A.',
            '074' => 'Banco J. Safra S.A.',
            '655' => 'Banco Votorantim S.A.'
        ];

        $listaBancos = Utils::getBancos();
        unset($listaBancos['033'],$listaBancos['237'],$listaBancos['341'],$listaBancos['001'],$listaBancos['104'],$listaBancos['399'],$listaBancos['745'],$listaBancos['004'],$listaBancos['422'],$listaBancos['074'],$listaBancos['655']);

        $listaBancos = $bancosPrioridade + $listaBancos;
        foreach ($listaBancos as $cod => $nome) {
            $bancos[] = (object) [
                'id' => (string) $cod,
                'nome' => $nome
            ];
        }

        return [
            'bancos' => $bancos
        ];
    }

    public function avaliarNps(Request $request) {
		$user = Auth::user();
		if (!Auth::check()) {
			return response()->json(["msg" => "Não Autorizado"], 403);
        }
        
        $this->validate($request, [
            'nota' => 'integer|required',
        ]);

		$cliente = (new Clientes())->where('id_usuario', $user->id)->first();

        $input = $request->all();
        $input['origem'] = 'mobile';
        $input['id_cliente'] = $cliente->id;

        try {
            $nps = Nps::create($input);
        } catch (Exception $e) {
            return response()->json(["msg" => $e->getMessage()], $e->getCode());
        }

        return response()->json(["msg" => "Obrigado pela avaliação!"], 200);
    }

    /**
     * Métodos privados
     */
    private function getDadosPet($pet) 
    {

        $placeholders = [
            'CACHORRO' => url('/') . '/assets/layouts/layout2/img/placeholder_cao.jpg',
            'GATO' => url('/') . '/assets/layouts/layout2/img/placeholder_gato.jpg',
        ];

        $plano = $pet->plano();
        $exploded_nome_pet = explode(" ", $pet->nome_pet);
        $nome_pet = $exploded_nome_pet[0] . ' ' . (isset($exploded_nome_pet[1]) ? $exploded_nome_pet[1] : '');

        $idade = $pet->data_nascimento->age;
        $valor_angel =  DB::table('plano_angel_valores')
                        ->where('idade_min', '<=', $idade)
                        ->where('idade_max', '>=', $idade)
                        ->first();

        $pet = [
            "id" => $pet->id,
            "nome_pet" => ucwords(mb_strtolower($nome_pet)),
            "nome_completo" => ucwords(mb_strtolower($pet->nome_pet)),
            "tipo" => $pet->tipo,
            "raca" => ucwords(mb_strtolower($pet->raca->nome)),
            "numero_microchip" => $pet->numero_microchip,
            "sexo" => $pet->sexo == "F" ? "Fêmea" : "Macho",
            "data_nascimento" => $pet->data_nascimento->format('d/m/Y'),
            "ativo" => $pet->ativo,
            "foto" => $pet->foto ? url('/') . '/' . $pet->foto : $placeholders[$pet->tipo],
            "foto_placeholder" => $pet->foto ? false : true,
            "id_plano" => $plano->id,
            "nome_plano" => ucwords(mb_strtolower($plano->nome_plano)),
            'plano_isento' => $plano->isento,
            'plano_participativo_novo' => $plano->participativo,
            'plano_participativo_antigo' => $pet->participativo,
            "imagem_carteirinha" => $pet->getImagemCarteirinha(),

            'angel' => $pet->angel,
            "data_angel" => $pet->data_angel ? $pet->data_angel->format('d/m/Y') : null,
            "carencia_angel_restante" => $pet->carenciaAngelRestante(),
            'idade' => $idade,
            'valor_mensal_angel' => $valor_angel ? $valor_angel->valor_mensal : 0,
            'valor_anual_angel' => $valor_angel ? $valor_angel->valor_anual : 0,
        ];

        $pet['permissoes'] = $this->getPermissoes($pet);
        $pet['avaliacoes'] = $this->getAvaliacoes($pet);

        return $pet;
    }

    private function getDadosGuia($guia)
    {
        $guiaObj = new \stdClass();
        $guiaObj->id = $guia->id;
        $guiaObj->id_pet = $guia->pet->id;
        $guiaObj->nome_pet = $guia->pet->nome_pet;
        $guiaObj->nome_prestador = $guia->prestador ? $guia->prestador->nome : "Não informado";
        $guiaObj->nome_clinica = $guia->clinica->nome_clinica;
        $guiaObj->numero_guia = $guia->numero_guia;
        $guiaObj->data_liberacao = $guia->data_liberacao ? $guia->data_liberacao->format('d/m/Y') : null;
        $guiaObj->data_expiracao = $guia->data_liberacao ? $guia->data_liberacao->addDays(15)->format('d/m/Y') : null;
        $guiaObj->nome_clinica = ucwords(mb_strtolower($guia->clinica->nome_clinica));
        $guiaObj->status = $guia->status;
        $guiaObj->tipo = ucwords(mb_strtolower($guia->tipo_atendimento));
        $guiaObj->tipo_atendimento = ucwords(mb_strtolower($guia->tipo_atendimento));
        $guiaObj->realizado_em = $guia->realizado_em ? Carbon::parse($guia->realizado_em)->format('d/m/Y') : null;
        $guiaObj->assinatura_cliente = $guia->assinatura_cliente;
        $guiaObj->data_assinatura_cliente = $guia->data_assinatura_cliente ? $guia->data_assinatura_cliente->format('d/m/Y H:i') : null;
        $guiaObj->meio_assinatura_cliente = $guia->meio_assinatura_cliente;
        $guiaObj->assinatura_prestador = $guia->assinatura_prestador;
        $guiaObj->data_assinatura_prestador = $guia->data_assinatura_prestador;

        $guiaObj->assinar = true;
        if ($guiaObj->meio_assinatura_cliente != null || $guiaObj->status == HistoricoUso::STATUS_RECUSADO) {
            $guiaObj->assinar = false;
        } elseif ($guia->tipo_atendimento == HistoricoUso::TIPO_ENCAMINHAMENTO && !$guiaObj->realizado_em) {
            $guiaObj->assinar = false;
        }

        $guiaObj->procedimentos = (new \Modules\Guides\Entities\HistoricoUso())->where('numero_guia', $guia->numero_guia)->get()->map(function ($guia) {
            return [
                'id' => $guia->procedimento->id,
                'nome' => $guia->procedimento->nome_procedimento
            ];
        });

        $guiaObj->data = $guia->created_at->format('d/m/Y');
        // if ($guia->tipo_atendimento == HistoricoUso::TIPO_ENCAMINHAMENTO && $guia->status == HistoricoUso::STATUS_LIBERADO) {
        if ($guia->realizado_em) {
            $guiaObj->data = Carbon::parse($guia->realizado_em)->format('d/m/Y');
        }

        return $guiaObj;
    }

    private function getPermissoes($pet)
    {
        $permissoes = self::$permissoes;

        if ($pet['id_plano'] == 42 || $pet['id_plano'] == 43) {
            $permissoes['solicitar_carteirinha'] = false;
        }

        return $permissoes;
    }

    private function getAvaliacoes($pet)
    {
        $datas = (new AvaliacoesPrestadores())->where('id_pet', $pet['id'])->orderBy('created_at', 'desc')->groupBy('created_at')->pluck('created_at');

        $avaliacoes = [];
        $i = 0;
        foreach ($datas as $data) {
            $avaliacoes[$i]['data'] = Utils::getWeekName($data->dayOfWeek) . ' • ' . $data->format('d') . ' de ' . Utils::getMonthName($data->month) . ' de ' . $data->year;
            $av_prest = (new AvaliacoesPrestadores())->where('id_pet', $pet['id'])->where('created_at', $data)->orderBy('created_at', 'desc')->get();
            foreach ($av_prest as $av) {
                $avaliacoes[$i]['avaliacoes'][] = [
                    'id' => $av->id,
                    'prestador' => $av->prestador->nome,
                    'clinica' => $av->clinica->nome_site ?: $av->clinica->nome_clinica,
                    'nota' => $av->nota,
                    'comentario' => $av->comentario
                ];
            }
            $i++;
        }

        return $avaliacoes;
    }

    private static function pushAniversariantes()
    {
        $clientes = (new \App\Models\Pets())
            ->whereRaw('MONTH(data_nascimento) = ' . \Carbon\Carbon::today()->format('m'))
            ->whereRaw('DAY(data_nascimento) = ' . \Carbon\Carbon::today()->format('d'))
            ->where('ativo', 1)
            ->whereHas('cliente', function ($query) {
                return $query->whereNotNull('token_firebase')->where('ativo', 1);
            })
            ->get()
            ->map(function ($pet) {

                $cliente = $pet->cliente;
                if ($cliente->token_firebase) {

                    $primeiroNomePet = ucwords(mb_strtolower(explode(' ', $pet->nome_pet)[0]));
                    $emojiFesta = "\u{1F389} \u{1F38A} \u{1F973}";
                    $emojiTipo = ($pet->tipo == "CACHORRO" ? "\u{1F436}" : "\u{1F431}");
                    $tratativa = ($pet->sexo == "M" ? "dele" : "dela");

                    $title = "Parabéns {$primeiroNomePet}! {$emojiFesta} {$emojiTipo}";
                    $msg = "Hoje é o dia {$tratativa}! A Lifepet deseja muitas felicidades e claro, muita saúde!!!";

                    $pushNotification = (new PushNotificationService($cliente, $title, $msg));
                    $pushNotification->send();

                    return [
                        'cliente' => $cliente->nome_cliente,
                        'pet' => $pet->nome_pet,
                    ];
                }
            });
            return $clientes;
    }

    public function statusDocumentacaoCliente()
    {
		$user = Auth::user();
		if (!Auth::check()) {
			return response()->json(["msg" => "Não Autorizado"], 403);
		}
        $cliente = (new Clientes())->where('id_usuario', $user->id)->first();
        return response()->json(["status" => self::getStatusDocumentacaoCliente($cliente)], 200);
    }

    private static function getStatusDocumentacaoCliente($cliente)
    {
        return DocumentosClientes::STATUS_APROVADO;

        $status = null;
        $documentos_obrigatorios = $cliente->documentos()->where('avaliacao_obrigatoria', 1)->get()->toArray();
        // $docs_cliente_enviado = $cliente->documentos()->where('avaliacao_obrigatoria', 1)->where('status' , DocumentosClientes::STATUS_ENVIADO)->exists();
        foreach ($cliente->pets as $pet) {
            foreach ($pet->documentos()->where('avaliacao_obrigatoria', 1)->get()->toArray() as $doc) {
                $documentos_obrigatorios[] = $doc;
            }
        }
        
        // Verificar tela de documentos pendentes
        $documentos_pendentes = collect($documentos_obrigatorios)->where('status', DocumentosClientes::STATUS_PENDENTE)->count();
        if ($documentos_pendentes) {
            $status = 'PENDENTE';
            return $status;
        }

        // Verificar tela de documentos reprovados
        $documentos_reprovados = collect($documentos_obrigatorios)->where('status', DocumentosClientes::STATUS_REPROVADO)->count();
        if ($documentos_reprovados) {
            $status = 'REPROVADO';
            return $status;
        }
        
        // Verificar tela de documentos em análise
        $documentos_enviados = collect($documentos_obrigatorios)->where('status', DocumentosClientes::STATUS_ENVIADO)->count();
        if ($documentos_enviados && !$cliente->pets()->where('ativo', 1)->exists()) {
            $status = 'EM_ANALISE';
        }

        return $status;
    }

    public function planosDocumentos(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $cliente = (new Clientes())->where('id_usuario', $user->id)->first();
        }

        $data = new \stdClass();
        $data->documentos = [];
        //Documentos de planos:
        /**
         * @var Pets $pet
         */
        foreach($cliente->pets as $pet) {
            if($pet->ativo && $pet->plano()) {
                $plano = $pet->plano();
                if($plano->id) {
                    foreach($plano->documentos() as $documento) {
                        $docObject = new \stdClass();
                        $docObject->description = $documento->description;
                        $docObject->extension = $documento->extension;
                        $docObject->url = url('/') . '/' . $documento->path;
                        $docObject->size = $documento->size;
                        $documento->pet = [
                            'id' => $pet->id,
                            'nome' => $pet->nome_pet
                        ];
                        $docObject->plano = [
                            'nome' => $plano->nome_plano,
                            'id' => $plano->id
                        ];
                        $docObject->detalhes = $documento->documento;

                        $data->documentos[] = $docObject;
                    }
                }
            }
        }

        return Response::json($data);
    }

}
