<?php

namespace App\Http\Controllers;

use App\DAO\RenewalDAO;
use App\Helpers\API\LifepetIntegration\Domains\Pet\Pet;
use App\Helpers\API\RDStation\Services\RDRenovacaoAnualSemDescontoService;
use App\Helpers\API\RDStation\Services\RDRenovacaoAnualService;
use App\Helpers\API\RDStation\Services\RDRenovacaoMensalService;
use App\Helpers\Utils;
use App\Http\Requests\CreateRenovacaoRequest;
use App\Http\Requests\UpdateRenovacaoRequest;
use App\Http\Util\LogEvent;
use App\Http\Util\Logger;
use App\Http\Util\LogPriority;
use App\LinkPagamento;
use App\Models\Clientes;
use App\Models\Notas;
use App\Models\Pets;
use App\Models\PetsPlanos;
use App\Models\Planos;
use App\Models\Renovacao;
use App\Repositories\RenovacaoRepository;
use App\Http\Controllers\AppBaseController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Flash;
use Illuminate\Support\Facades\Validator;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class RenovacaoController extends AppBaseController
{
    /** @var  RenovacaoRepository */
    private $renovacaoRepository;

    public function __construct(RenovacaoRepository $renovacaoRepo)
    {
        $this->renovacaoRepository = $renovacaoRepo;
    }

    /**
     * Display a listing of the Renovacao.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $params = RelatoriosController::getReajustes($request);

        //Filtro de regime
        $regime = $request->get('regime');
        $params['params']['regime'] = $regime;
        $params['dados'] = array_filter($params['dados'], function($item) use ($regime) {
           if($regime === Pets::REGIME_MENSAL) {
               return $item['pet']->regime === Pets::REGIME_MENSAL;
           }

           return $item['pet']->regime !== Pets::REGIME_MENSAL;
        });
        $params['dados'] = array_map(function($item) use ($params) {
            $item['renovacao'] = $item['pet']->renovacoes()->where('competencia_ano', $params['params']['ano'])->where('status', '<>', Renovacao::STATUS_CANCELADO)->first();
            return $item;
        }, $params['dados']);



        return view('renovacao.index')
            ->with($params);
    }

    public function remake__index()
    {
        return view('renovacao.v2.validar');
    }

    public function remake__previews(Request $request)
    {
        $mes = $request->get('mes', null);
        if(!$mes) {
            $mes = (int) Carbon::now()->addMonth(1)->format('m');
        }

        //Retrieve all pets
        $pets = Pets::where('ativo', 1)
            ->where('mes_reajuste', $mes)
            ->orderBy('pets.regime', 'ASC')
            ->select('pets.*')->get();

        $renewals = $pets->map(function(Pets $p) {
            if ($p->cancelamentoAgendado() === false)
            {
                $renewal = new RenewalDAO($p);
                return $renewal;
            }
        });


        return $renewals;
    }

    /**
     * @param Request $request
     * @return int[]
     */
    public function remake__renewal_details(Request $request)
    {
        $id = $request->get('id_pet');
        $ano = (int) $request->get('ano', Carbon::today()->year);
        $mes = (int) $request->get('mes', (Carbon::today()->month + 1));

        $start = Carbon::create($ano-1, $mes)->startOfMonth();
        $end = Carbon::create($ano, $mes-1)->endOfMonth();

        if(!$id) {
            abort(404, 'Parâmetro de pet obrigatório.');
        }

        $pet = Pets::find($id);
        if(!$pet) {
            abort(404, 'Pet não encontrado.');
        }
        $response = [
            'faturado' => 0,
            'utilizado' => 0,
            'relacao_uso' => 0,
            'reajuste' => 18
        ];

        $valorBase = $pet->petsPlanosAtual()->first() ? $pet->petsPlanosAtual()->first()->valor_momento : $pet->valor;

        $valorPago = $pet->regime === 'MENSAL' ? $valorBase * 12 : $valorBase;
        $participacao = \App\Models\Participacao::where('id_pet', $pet->id)
           ->whereBetween('created_at', [$start, $end])
           ->sum('valor_participacao');

        $response['utilizado'] = $valorUtilizado = $pet->getValorUtilizado($start, $end);
        $response['faturado'] = $valorPago = $valorPago + $participacao;
        $response['relacao_uso'] = $relacao_uso = round(($valorUtilizado / ($valorPago ?: 0.01) * 100), 2);
        if ($relacao_uso <= 100) {
            $reajuste = 7.54;
        } else if ($relacao_uso <= 200) {
            $reajuste = 18;
        } else {
            $reajuste = 24;
        }

        $response['reajuste'] = $reajuste;

        return $response;
    }


    public function remake__store_new_renewal(Request $request)
    {
        $input = $request->all();
        $renovacao = RenewalDAO::newFromRequest($request);

        $rd                        = [];
        $rd['desconto']            = $renovacao->desconto;
        $rd['percentual_reajuste'] = $renovacao->reajuste;
        $rd['valor']               = $renovacao->valor;
        $rd['valor_original']      = $renovacao->valor_original;
        $rd['valor_bruto']         = $renovacao->valor_original;
        $rd['parcelas']            = $input['parcelas'];

        $status = Renovacao::STATUS_NOVO;

        if($renovacao->regime !== Pets::REGIME_MENSAL) {
            /**
             * @doc
             * Renovações anuais são enviadas como propostas via e-mail juntamente com um link de pagamento.
             * Após a confirmação do pagamento, um callback é acionado realizando o agendamento e posterior renovação automática do plano.
             */
            if($rd['desconto'] == 0) {
                (new RDRenovacaoAnualSemDescontoService())->process($renovacao, $rd);
            } else {
                (new RDRenovacaoAnualService())->process($renovacao, $rd);
            }

            //Criar nota
            $renovacao->notificarProposta();


            $logger = new Logger('renovacao', 'renovacoes', auth()->user()->id ?: 1);
            $logger->register(LogEvent::UPDATE, LogPriority::HIGH, "Uma proposta de renovação foi enviada ao cliente {$renovacao->pet->nome_pet} e está aguardando pagamento. Tutor {$renovacao->pet->cliente->nome_cliente}", $renovacao->pet->id, 'pets');
        } else {
            /**
             * @doc
             * Renovações mensais são realizadas automaticamente no momento do processo de renovação, atualizando a fatura aberta do cliente
             * que pagará no próximo mês.
             */
            (new RDRenovacaoMensalService())->process($renovacao, $rd);
            $pp = $renovacao->renovar();
            $atualizado = $renovacao->atualizarFaturaAberta();

            //self::setInfo("O plano do PET {$renovacao->pet->nome_pet} foi renovado automaticamente. Tutor {$renovacao->pet->cliente->nome_cliente}");

            $status = Renovacao::STATUS_ATUALIZADO;
            $logger = new Logger('renovacao', 'renovacoes', auth()->user()->id ?: 1);
            $logger->register(LogEvent::UPDATE, LogPriority::HIGH, "O plano do cliente {$renovacao->pet->nome_pet} foi renovado. Tutor {$renovacao->pet->cliente->nome_cliente}", $pp->id, 'pets_planos');
        }
        //Pets::find($renovacao->id_pet)->renovar($renovacao);


        return  [
            'renovacao' => [
                'renovado' => true,
                'object' => $renovacao
            ],
            'status' => RenewalDAO::getRenewalStatus($status)
        ];
    }

    public function remake__store_skip_renewal(Request $request)
    {
        $input = $request->all();
        $renovacao = RenewalDAO::skipFromRequest($request);

        $logger = new Logger('renovacao', 'renovacoes', auth()->user()->id ?: 1);
        $logger->register(LogEvent::UPDATE, LogPriority::HIGH, "O plano do cliente {$renovacao->pet->nome_pet} foi renovado sendo NÃO OPTANTE pelo reajuste. Ou seja, sem acréscimos ao valor do contrato.", $renovacao->pet->id, 'pets');
        return  [
            'renovacao' => [
                'renovado' => true,
                'object' => $renovacao
            ],
            'status' => RenewalDAO::getRenewalStatus($renovacao->status)
        ];
    }
    /**
     * Show the form for creating a new Renovacao.
     *
     * @return Response
     */
    public function create()
    {
        $clientes = Clientes::where('ativo', 1)->orderBy('nome_cliente', 'ASC')->get();
        $planos = Planos::orderBy('id', 'DESC')->get();

        return view('renovacao.create', [
            'clientes' => $clientes,
            'planos' => $planos
        ]);
    }

    /**
     * Store a newly created Renovacao in storage.
     *
     * @param CreateRenovacaoRequest $request
     *
     * @return Response
     */
    public function store(CreateRenovacaoRequest $request)
    {
        $input = $request->all();

        $renovacao = $this->renovacaoRepository->create($input);

        Flash::success('Renovacao saved successfully.');

        return redirect(route('renovacao.index'));
    }

    /**
     * Display the specified Renovacao.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $renovacao = $this->renovacaoRepository->findWithoutFail($id);

        if (empty($renovacao)) {
            Flash::error('Renovacao not found');

            return redirect(route('renovacao.index'));
        }

        return view('renovacao.show')->with('renovacao', $renovacao);
    }

    /**
     * Show the form for editing the specified Renovacao.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $renovacao = $this->renovacaoRepository->findWithoutFail($id);
        $planos = Planos::orderBy('id', 'DESC')->get();
        if (empty($renovacao)) {
            Flash::error('Renovacao not found');

            return redirect(route('renovacao.index'));
        }

        return view('renovacao.edit')->with([
            'renovacao' => $renovacao,
            'planos' => $planos
        ]);
    }

    /**
     * Update the specified Renovacao in storage.
     *
     * @param  int              $id
     * @param UpdateRenovacaoRequest $request
     *
     * @return Response
     */
    public function update($id, Request $request)
    {
        $renovacao = $this->renovacaoRepository->findWithoutFail($id);

        if (empty($renovacao)) {
            self::setError('Renovação não encontrada');
            return redirect(route('renovacao.controle'));
        }

        $v = Validator::make($request->all(), [
            'id_plano' => 'required|exists:planos,id',
            'valor' => 'required|min:1|numeric',
            'parcelas' => 'required|min:1|max:12|numeric',
            'status' => 'required',
            'justificativa' => 'required'
        ]);

        $justificativa = $request->get('justificativa');

        $logger = new Logger('renovacao', 'renovacoes', auth()->user()->id);
        $logger->register(LogEvent::UPDATE, LogPriority::HIGH, "Modificação da renovação. Justificativa: $justificativa", $renovacao->id);

        $renovacao = $this->renovacaoRepository->update($request->all(), $id);
        if(!$renovacao->link) {
            $input['valor'] = $request->get('valor');
            $input['valor_bruto'] = $renovacao->valor_bruto;
            $input['tags'] = ['renovacao','link-pagamento'];
            $input['descricao'] = 'Pagamento da renovação do plano do pet ' .  $renovacao->pet->nome_pet;
            $input['expires_at'] = Carbon::today()->addMonth()->format('d/m/Y');
            $input['valor_original'] = $renovacao->valor_original;
            $input['parcelas'] = $request->get('parcelas');
            $input['id_cliente'] = $renovacao->id_cliente;
            $link = LinkPagamento::createForRenovacao($input);

            $renovacao->id_link_pagamento = $link->id;
            $renovacao->update();
        } else {
            $renovacao->link->update([
                'valor' => $request->get('valor'),
                'parcelas' => $request->get('parcelas'),
            ]);
        }

        self::setSuccess('Renovação atualizada com sucesso.');

        return redirect(route('renovacao.controle'));
    }

    /**
     * Remove the specified Renovacao from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $renovacao = $this->renovacaoRepository->findWithoutFail($id);

        if (empty($renovacao)) {
            self::error('Renovacao not found');

            return redirect(route('renovacao.index'));
        }

        $this->renovacaoRepository->delete($id);

        Flash::success('Renovacao deleted successfully.');

        return redirect(route('renovacao.index'));
    }

    public function apiCriar(Request $request)
    {
        $input = $request->all();
        $pet = Pets::find($input['id_pet']);
        $input['id_cliente'] = $pet->cliente->id;
        $input['competencia_mes'] = sprintf("%02d", $input['competencia_mes']);

        if($request->get('status') == Renovacao::STATUS_NOVO) {
            $input['status'] = Renovacao::STATUS_NOVO;
            $input['valor'] = ($input['regime'] === Pets::REGIME_MENSAL ? $input['mensal'] : $input['anual']);
            $input['valor_bruto'] = ($input['regime'] === Pets::REGIME_MENSAL ? $input['valor_original'] : $input['valor_original'] * 12);
            $input['tags'] = ['renovacao','link-pagamento'];
            $input['descricao'] = 'Pagamento da renovação do plano do pet ' .  $pet->nome_pet;
            $input['expires_at'] = Carbon::today()->addMonth()->format('d/m/Y');
            $input['valor_original'] = ($input['regime'] === Pets::REGIME_MENSAL ? $input['valor_original'] : $input['valor_original'] * 12);

            try {
                $linkPagamento = LinkPagamento::createForRenovacao($input);
            } catch (\Exception $e) {
                return abort(500, $e->getMessage());
            }
            $input['id_link_pagamento'] = $linkPagamento->id;

            $input['valor'] = number_format($input['valor'], 2, '.', '');
            $input['valor_bruto'] = number_format($input['valor_bruto'], 2, '.', '');

        } else {
            $now = Carbon::now();
            Notas::create([
                'user_id' => 1,
                'cliente_id' => $pet->cliente->id,
                'corpo' => "Pet {$pet->nome_pet} não teve reajuste no ano de {$now->year}. Ou seja, foi renovado sem acréscimos na mensalidade/anuidade."
            ]);

            $input['status'] = Renovacao::STATUS_NAO_OPTANTE;
            $input['valor'] = 0;
        }

        $input['id_plano'] = $pet->plano()->id;

        /**
         * @var Renovacao $renovacao
         */
        $renovacao = $this->renovacaoRepository->create($input);

        if($renovacao->status == Renovacao::STATUS_NOVO) {
            $renovacao->link->update([
                'callback_url' => route('api.renovacao.callback', ['id' => $renovacao->id])
            ]);

            $valores = [];
            $valores['desconto'] = $input['desconto'];
            $valores['percentual_reajuste'] = $input['reajuste'];
            $valores['valor'] = $input['valor'];
            $valores['valor_original'] = $input['valor_original'];
            $valores['valor_bruto'] = $input['valor_bruto'];
            $valores['parcelas'] = $input['parcelas'];

            if($renovacao->regime != Pets::REGIME_MENSAL) {
                if($valores['desconto'] == 0) {
                    (new RDRenovacaoAnualSemDescontoService())->process($renovacao, $valores);
                } else {
                    (new RDRenovacaoAnualService())->process($renovacao, $valores);
                }
            } else {
                // Renovação mensal
                (new RDRenovacaoMensalService())->process($renovacao, $valores);
                $pp = $renovacao->renovar();
                $atualizado = $renovacao->atualizarFaturaAberta();

                self::setInfo("O plano do cliente {$renovacao->pet->nome_pet} foi renovado automaticamente.");

                $logger = new Logger('renovacao', 'renovacoes', 1);
                $logger->register(LogEvent::UPDATE, LogPriority::HIGH, "O plano do cliente {$renovacao->pet->nome_pet} foi renovado.", $pp->id, 'pets_planos');
            }
        }

        return $renovacao;
    }

    public function apiEditar(Request $request)
    {
        $v = Validator::make($request->all(), [
            'id_cliente' => 'required|exists:clientes,id',
            'valor' => 'required|min:1|numeric',
            'parcelas' => 'required|min:1|max:12|numeric',
            'tags' => 'required|array',
            'descricao' => 'required',
            'expires_at' => 'required|date_format:d/m/Y|after:today'
        ]);

        if ($v->fails()) {
            $messages = join("\n", $v->getMessageBag()->all());
            throw new \Exception("Não foi possível criar o link de pagamento.\n" . $messages);
        }
    }

    public function controle(Request $request)
    {
        //Filtro de data
        $data = $request->all();
        $data['ano'] = $ano = isset($data['ano']) ? $data['ano'] : Carbon::today()->year;
        $data['mes'] = $mes = isset($data['mes']) ? $data['mes'] : Carbon::today()->month + 1;
        $mes = sprintf("%02d", $mes);
        //Filtro de regime

        if($request->get('regime') == 'TODOS') {
            $regime = null;
        } else {
            if($request->get('regime') === 'ANUAL') {
                $regime = Pets::REGIMES_ANUAIS;
            } else {
                $regime = ['MENSAL'];
            }
        }

        $params['params']['regime'] = $regime;


        $renovacoesQuery = Renovacao::whereIn('status', [
            Renovacao::STATUS_NOVO,
            Renovacao::STATUS_EM_NEGOCIACAO,
            Renovacao::STATUS_PAGO,
            Renovacao::STATUS_AGENDADO,
            Renovacao::STATUS_ATUALIZADO,
            Renovacao::STATUS_CANCELADO,
            Renovacao::STATUS_CONVERTIDO
        ])->where(function($q) use ($ano, $mes) {
             $q->where(function($competenceQuery) use ($ano, $mes) {
                 $competenceQuery->where('competencia_ano', '=', $ano)
                                 ->where('competencia_mes', '=', $mes);
             })->orWhere(function($dateQuery) use ($mes, $ano) {
                 $date = Carbon::now();
                 $date->year($ano)->month($mes)->addMonth(1);

                 $dateQuery->whereMonth('created_at', $date->month)->whereYear('created_at', $date->year);
             });
          });

        if($regime) {
            $renovacoesQuery->whereIn('regime', $regime);
        }

        $renovacoes =  $renovacoesQuery->orderBy('id', 'DESC')->get();
        $renovacoes->map(function($r) {
           $petsPlanos = $r->pet->petsPlanosAtual()->first();
           $dataInicio = $petsPlanos->data_inicio_contrato;
           $uso = $r->pet->getValorUtilizado($dataInicio, Carbon::now());
           $r->uso = $uso;
           $r->data_inicio_contrato = $dataInicio;

           return $r;
        });

        $renovacoes = $renovacoes->sortBy('data_inicio_contrato');

        $naoOptantesQuery = Renovacao::whereIn('status', [
            Renovacao::STATUS_NAO_OPTANTE
        ])->where(function($q) use ($ano, $mes) {
            $q->where(function($competenceQuery) use ($ano, $mes) {
                $competenceQuery->where('competencia_ano', '=', $ano)
                    ->where('competencia_mes', '=', $mes);
            })->orWhere(function($dateQuery) use ($mes, $ano) {
                $date = Carbon::now();
                $date->year($ano)->month($mes)->addMonth(1);

                $dateQuery->whereMonth('created_at', $date->month)->whereYear('created_at', $date->year);
            });
        });

        if($regime) {
            $naoOptantesQuery->whereIn('regime', $regime);
        }

        $naoOptantes = $naoOptantesQuery->orderBy('id', 'DESC')->get();

        return view('renovacao.controle')->with([
            'renovacoes_optantes' => $renovacoes,
            'renovacoes_nao_optantes' => $naoOptantes
        ]);
    }

    public function callbackPagamento($id, Request $request)
    {
        /**
         * @var $renovacao Renovacao
         */
        $renovacao = $this->renovacaoRepository->findWithoutFail($id);
        if($renovacao->status === Renovacao::STATUS_EM_NEGOCIACAO || $renovacao->status === Renovacao::STATUS_NOVO) {
            $renovacao->update([
                'status' => Renovacao::STATUS_PAGO,
                'paid_at' => Carbon::now()
            ]);
            $dadosJson = json_encode([
                'renovacao' => $renovacao
            ]);

            $logger = new Logger('renovacao', 'renovacoes', 1);
            $logger->register(LogEvent::UPDATE, LogPriority::HIGH, "Um pagamento de renovação foi confirmado. Dados de renovação: \n$dadosJson.", $renovacao->id);

            if($renovacao->regime === Pets::REGIME_MENSAL) {
                $pp = $renovacao->renovar();
                $logger->register(LogEvent::UPDATE, LogPriority::HIGH, "O plano do PET {$renovacao->pet->nome_pet} foi renovado. Tutor {$renovacao->pet->cliente->nome_cliente}", $pp->id, 'pets_planos');
            } else {
                //Modifica o status para uma busca de cron realizar a mudança na data correta.
                $renovacao->agendar();
            }
        }


        return ['status' => true, 'data' => ['renovacao' => $renovacao->id]];
    }

    /**
     * Procedimento de realização de renovações.
     */
    public function realizarRenovacoesAgendadas($date = null)
    {
        $now = Carbon::now();
        if($date) {
            $now = $date;
        }
        $renovacoes = Renovacao::whereIn('regime', Pets::REGIMES_ANUAIS)
                 ->where('status', Renovacao::STATUS_AGENDADO)
                 ->where('competencia_mes', $now->format('m'))
                 ->where('competencia_ano', $now->format('Y'))
                 ->get();

        $filtered = $renovacoes->filter(function($r) use ($now) {

            /**
             * @var PetsPlanos $petsPlanos
             */
            $petsPlanos = $r->pet->petsPlanosAtual()->first();
            return $petsPlanos->data_inicio_contrato->format(Utils::BRAZILIAN_DATE) == $now->format(Utils::BRAZILIAN_DATE);
        });

        foreach($filtered as $renovacao) {
            /**
             * @var Renovacao $renovacao
             */
            $renovacao->renovar();
        }
        $mensagem = "As renovações agendadas para o dia {$now->format(Utils::BRAZILIAN_DATE)} foram realizadas com sucesso.";
        Logger::log(LogEvent::NOTIFY, 'renovacoes', LogPriority::HIGH, $mensagem, 1);
    }

    /**
     * @param $date string 
     * @return void
     */
    public function converterAnuais($date = null)
    {
        $now = Carbon::now();
        if($date) {
            $now = $date;
        }

        $renovacoes = Renovacao::whereIn('regime', Pets::REGIMES_ANUAIS)
            ->where('status', Renovacao::STATUS_NOVO)
            ->where('competencia_mes', $now->format('m'))
            ->where('competencia_ano', $now->format('Y'))
            ->get();

        $filtered = $renovacoes->filter(function($r) use ($now) {

            /**
             * @var PetsPlanos $petsPlanos
             */
            $petsPlanos = $r->pet->petsPlanosAtual()->first();
            /**
             * @var Carbon $data
             */

            return $petsPlanos->data_inicio_contrato->year($now->year)->lte($now);
        });

        foreach($filtered as $renovacao) {
            /**
             * @var Renovacao $renovacao
             */
            $renovacao->converter();
            if($renovacao->link) {
                $renovacao->link->invalidar();
            }
        }
        $mensagem = "As conversões de planos anuais para mensais do dia {$now->format(Utils::BRAZILIAN_DATE)} foram realizadas com sucesso.";
        Logger::log(LogEvent::NOTIFY, 'renovacoes', LogPriority::HIGH, $mensagem, 1);
    }
}
