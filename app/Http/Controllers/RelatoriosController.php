<?php

namespace App\Http\Controllers;

use App\Exports\Reports\GlobalExport;
use App\LifepetCompraRapida;
use App\Models\DadosTemporais;
use App\Models\Indicacoes;
use App\Models\PetsPlanos;
use App\Models\Planos;
use App\Models\RelatorioPetsPlanos;
use Carbon\Carbon;
use DB;
use Entrust;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use Modules\Guides\Entities\HistoricoUso;
use Illuminate\Support\Facades\Log;


class RelatoriosController extends AppBaseController
{
    protected static function getDates(Request $request) {
        if($request->filled('start')) {
            $start = Carbon::createFromFormat('d/m/Y', $request->get('start'));
            if(!$request->get('end')) {
                $end = $start->copy()->lastOfMonth();
            } else {
                $end = Carbon::createFromFormat('d/m/Y', $request->get('end'));
            }
        } else {
            $start = new Carbon('first day of this month');
            $end = new Carbon('last day of this month');
        }
        $start->setTime(0,0,0);
        $end->setTime(23,59,59);

        return [
            'start' => $start,
            'end'   => $end
        ];
    }

    /**
     * @param $request
     * @param $filters
     * @param $query
     * @param array $params
     */
    private static function setFilters($request, $filters, &$query, &$params = [])
    {
        foreach ($filters as $filter => $key) {
            if($request->filled($filter)) {
                $f = $request->get($filter);
                $params[$filter] = $f;
                /**
                 * Mais de um valor possível
                 */
                if(is_callable($key)) {
                    $result = $key($f);
                    self::buildQuery($result, $result['data'], $query);
                } elseif(is_array($key)) {
                    self::buildQuery($key, $f, $query);
                } else {
                    if(is_array($f)) {
                        $query->whereIn($key, $f);
                    } else {
                        /**
                         * Operador e campo definidos explícitamente
                         * OU igualdade direta
                         */
                        self::buildQuery($key, $f, $query);
                    }
                }

            }
        }

        if($request->filled('especies')) {
            $especies = $request->get('especies');
            if(!is_array($especies)) {
                $especies = [$especies];
            }
            $params['especies'] =  $especies;
            $query->whereHas('pet', function($query) use ($especies) {
                $query->whereIn('tipo', $especies);
            });
        }
    }

    private static function guiasRelatorioParticipativos(Request $request) {
        ini_set('max_execution_time', 300);
        list($start, $end) = array_values(self::getDates($request));

        /**
         * @var \Illuminate\Database\Eloquent\Builder {$query
         */
        $query = \Modules\Guides\Entities\HistoricoUso::orderBy('historico_uso.created_at','DESC')->
        join('pets as p', 'p.id', '=', 'historico_uso.id_pet')->
        join('clientes as c', 'c.id', '=', 'p.id_cliente')->
        join('planos as pl', 'historico_uso.id_plano', '=', 'pl.id')->
        whereBetween('historico_uso.created_at', [$start, $end])->
        where('p.participativo', '=', 1);

        $filters = [
            'planos' => [
                'field' => 'pl.id',
                'operator' => 'IN'
            ],
            'clinicas' => 'id_clinica',
            'clientes' => function($clientes) {
                return [
                    'field' => 'id_pet',
                    'operator' => 'IN',
                    'data' => \App\Models\Pets::whereIn('id_cliente', $clientes)->get(['id'])->each->setAppends([])->toArray()
                ];
            },
            'status'   => 'status',
            'autorizacao' => 'autorizacao'
        ];

        $params = [];
        self::setFilters($request, $filters, $query, $params);
             $guias = $query->get([
            'historico_uso.id',
            'historico_uso.id_pet',
            'historico_uso.id_procedimento',
            'historico_uso.id_plano',
            'historico_uso.id_clinica',
            'historico_uso.numero_guia',
            'historico_uso.valor_momento',
            'historico_uso.autorizacao',
            'historico_uso.tipo_atendimento',
            'historico_uso.status',
            'historico_uso.created_at',
            'historico_uso.data_liberacao',
            'historico_uso.realizado_em',
            'c.nome_cliente',
            'p.id_cliente'
        ]);

        $guias = $guias->filter(function($g) {
            $valido = true;
            if($g->tipo_atendimento === HistoricoUso::TIPO_ENCAMINHAMENTO) {
                $valido = !empty($g->realizado_em);
            }
            return $valido;
        })->map(function($g) {
            $g->valor_momento = \App\Models\Participacao::participadoGuia($g);
            return $g;
        });

        return [
            'guias' => $guias,
            'params' => array_merge([
                'start' => $start->format('d/m/Y'),
                'end'   => $end->format('d/m/Y'),
            ], $params)
        ];
    }

    public function participativos(Request $request)
    {
        if(!Entrust::can('relatorio_participativo')) {
            return self::notAllowed();
        }

        $params = self::guiasRelatorioParticipativos($request);
        return view('relatorios.participativo')->with($params);
    }

    public function participativosDownload(Request $request)
    {
        if(!Entrust::can('relatorio_participativo_download')) {
            return self::notAllowed();
        }
        $params = self::guiasRelatorioParticipativos($request);
        $guias = $params['guias'];

        $format = 'xlsx';
        if($request->filled('format')) {
            $format = $request->get('format');
            if(!in_array($format, ['pdf', 'xlsx'])) {
                $format = 'xlsx';
            }
        }

        $formatType = ($format === 'pdf') ? Excel::DOMPDF : Excel::XLSX;

        $data = [
            'guias' => $guias,
            'exportar' => true
        ];

        $name = 'participativo-'. Carbon::now()->format('ymdHis');

        if ($format == 'xlsx') {
            $name = $name.'.'.$format;
        }
        return \Excel::download(
            new GlobalExport($data, 'relatorios.parts.participativo.table'),
            $name,
            $formatType
        );
    }

    private static function coparticipacaoProcedimentosPlano($id_plano, $plano) {
        
        $procedimentos = $plano->procedimentos->map(function ($procedimento) use ($plano) {
            return [
                'nome_plano' => $plano->nome_plano,
                'id_procedimento' => $procedimento->id,
                'nome_procedimento' => $procedimento->nome_procedimento,
                'coparticipacao' => $procedimento->pivot->beneficio_valor,
            ];
        });
        return $procedimentos;
          
    }

    public function coparticipacaoProcedimentosPlanosDownload($id_plano)
    {
        $plano = Planos::find($id_plano);

        $data = [
            'procedimentos' => self::coparticipacaoProcedimentosPlano($id_plano, $plano),
            'exportar' => true
        ];

        return \Excel::download(
            new GlobalExport($data, 'relatorios.parts.planos.table'),
            $plano->nome_plano .'-coparticipação-' . Carbon::now()->format('ymdHis').'.xlsx',
            Excel::XLSX
        );
    }

    /**
     * Obtém os dados de sinistralidade do sistema permitindo filtros como:
     * - Intervalo de data
     * - Planos
     * - Clientes
     * - Status
     * - Tipo de autorização
     *
     * @param Request $request
     * @return array
     */
    private static function guiasRelatorioSinistralidade(Request $request)
    {
        ini_set('max_execution_time', 300);
        list($start, $end) = array_values(self::getDates($request));
        /**
         * @var \Illuminate\Database\Eloquent\Builder {$query
         */
        $query = \Modules\Guides\Entities\HistoricoUso::orderBy('created_at','DESC')
            ->where(function($query) use ($start, $end) {
                $query->where(function($query) use ($start, $end) {
                    $query->where('tipo_atendimento', "!=", HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->whereBetween('created_at', [$start, $end]);
                });
                $query->orWhere(function($query) use ($start, $end) {
                    $query->where('tipo_atendimento', HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->whereBetween('realizado_em', [$start, $end]);
                });
            });

        $filters = [
            'planos' => [
                'field' => 'id_plano',
                'operator' => 'IN'
            ],
            'procedimentos' => [
                'field' => 'id_procedimento',
                'operator' => 'IN'
            ],
            'clinicas' => 'id_clinica',
            'solicitantes' => 'id_solicitador',
            'glosado' => [
                'field' => 'glosado',
                'operator' => 'IN'
            ],
            'prestadores' => 'id_prestador',
            'clientes' => function($clientes) {
                return [
                    'field' => 'id_pet',
                    'operator' => 'IN',
                    'data' => \App\Models\Pets::whereIn('id_cliente', $clientes)->get(['id'])->toArray()
                ];
            },
            'status'   => 'status',
            'autorizacao' => 'autorizacao'
        ];

        $params = [];
        foreach ($filters as $filter => $key) {
            if($request->filled($filter)) {
                $f = $request->get($filter);
                $params[$filter] = $f;
                /**
                 * Mais de um valor possível
                 */
                if(is_callable($key)) {
                    $result = $key($f);
                    self::buildQuery($result, $result['data'], $query);
                } elseif(is_array($key)) {
                    self::buildQuery($key, $f, $query);
                } else {
                    if(is_array($f)) {
                        $query->whereIn($key, $f);
                    } else {
                        /**
                         * Operador e campo definidos explícitamente
                         * OU igualdade direta
                         */
                        self::buildQuery($key, $f, $query);
                    }
                }

            }
        }

        if($request->filled('especies')) {
            $especies = $request->get('especies');
            if(!is_array($especies)) {
                $especies = [$especies];
            }

            $params['especies'] =  $especies;
            $query->whereHas('pet', function($query) use ($especies) {
                $query->whereIn('tipo', $especies);
            });
        }



        $guias = $query->get([
            '*',
        ]);


        $guias = $guias->map(function($g) {
            $g->data = $g->created_at;
            if($g->tipo_atendimento == HistoricoUso::TIPO_ENCAMINHAMENTO) {
                $g->data = $g->realizado_em;
            }

            return $g;
        });

        return [
            'guias' => $guias,
            'params' => array_merge([
                'start' => $start->format('d/m/Y'),
                'end'   => $end->format('d/m/Y'),
            ], $params)
        ];
    }


    /**
     * Obtém os dados de sinistralidade do sistema permitindo filtros como:
     * - Intervalo de data
     * - Planos
     * - Clientes
     * - Status
     * - Tipo de autorização
     *
     * @param Request $request
     * @return array
     */
    private static function guiasRelatorioSinistralidadeGruposHospitalares(Request $request)
    {
        ini_set('max_execution_time', 300);
        list($start, $end) = array_values(self::getDates($request));
        /**
         * @var \Illuminate\Database\Eloquent\Builder {$query
         */
        $query = \Modules\Guides\Entities\HistoricoUso::orderBy('created_at','DESC')
            ->where(function($query) use ($start, $end) {
                $query->where(function($query) use ($start, $end) {
                    $query->where('tipo_atendimento', "!=", HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->whereBetween('created_at', [$start, $end]);
                });
                $query->orWhere(function($query) use ($start, $end) {
                    $query->where('tipo_atendimento', HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->whereBetween('realizado_em', [$start, $end]);
                });
            });

        $filters = [
            'planos' => [
                'field' => 'id_plano',
                'operator' => 'IN'
            ],
            'prestadores' => 'id_prestador',
            'clientes' => function($clientes) {
                return [
                    'field' => 'id_pet',
                    'operator' => 'IN',
                    'data' => \App\Models\Pets::whereIn('id_cliente', $clientes)->get(['id'])->toArray()
                ];
            },
            'grupos_hospitalares' => function($grupos) {
                return [
                    'field' => 'id_clinica',
                    'operator' => 'IN',
                    'data' => \App\Models\GruposClinicas::whereIn('id_grupo_hospitalar', $grupos)->get(['id'])->toArray()
                ];
            },
            'status'   => 'status',
            'autorizacao' => 'autorizacao'
        ];

        $params = [];
        foreach ($filters as $filter => $key) {
            if($request->filled($filter)) {
                $f = $request->get($filter);
                $params[$filter] = $f;
                /**
                 * Mais de um valor possível
                 */
                if(is_callable($key)) {
                    $result = $key($f);
                    self::buildQuery($result, $result['data'], $query);
                } elseif(is_array($key)) {
                    self::buildQuery($key, $f, $query);
                } else {
                    if(is_array($f)) {
                        $query->whereIn($key, $f);
                    } else {
                        /**
                         * Operador e campo definidos explícitamente
                         * OU igualdade direta
                         */
                        self::buildQuery($key, $f, $query);
                    }
                }

            }
        }

        if($request->filled('especies')) {
            $especies = $request->get('especies');
            if(!is_array($especies)) {
                $especies = [$especies];
            }

            $params['especies'] =  $especies;
            $query->whereHas('pet', function($query) use ($especies) {
                $query->whereIn('tipo', $especies);
            });
        }



        $guias = $query->get([
            '*',
        ]);


        $guias = $guias->map(function($g) {
            $g->data = $g->created_at;
            if($g->tipo_atendimento == HistoricoUso::TIPO_ENCAMINHAMENTO) {
                $g->data = $g->realizado_em;
            }

            return $g;
        });

        return [
            'guias' => $guias,
            'params' => array_merge([
                'start' => $start->format('d/m/Y'),
                'end'   => $end->format('d/m/Y'),
            ], $params)
        ];
    }

    /**
     * Obtém os dados de cancelamento permitindo filtros como:
     * - Data de cancelamento (período)
     * - Nome do cliente
     * - Nome do pet
     * - Planos
     * - Motivo do cancelamento
     * - Status Financeiro
     *
     * @param Request $request
     * @return array
     */
    private static function relatorioCancelamento(Request $request)
    {
        ini_set('max_execution_time', 300);
        list($start, $end) = array_values(self::getDates($request));
        
        $query = \App\Models\Cancelamento::ultimosPorPet()
            //->join('pets', 'pets.id', '=', 'cancelamentos.id_pet')
            ->whereBetween('data_cancelamento', [$start, $end])
            ->groupBy('id_pet');

        if($request->filled('cliente_nome')) {
            $query = $query->whereHas('pet.cliente', function ($query) use($request) {
                return $query->where('nome_cliente', 'LIKE', '%' . $request->cliente_nome . '%');
            });
        }

        if($request->filled('cliente_id_externo')) {
            $query = $query->whereHas('pet.cliente', function ($query) use($request) {
                return $query->where('id_externo', $request->cliente_id_externo);
            });
        }

        if($request->filled('cliente_nome')) {
            $query = $query->whereHas('pet.cliente', function ($query) use($request) {
                return $query->where('nome_cliente', 'LIKE', '%' . $request->cliente_nome . '%');
            });
        }

        if($request->filled('planos')) {
            $query = $query->whereHas('pet.petsPlanos.plano', function ($query) use($request) {
                return $query->whereIn('id', [$request->planos]);
            });
        }

        if($request->filled('motivo')) {
            $query = $query->where('motivo', $request->motivo);
        }

        $params = $request->all();
        
        
     
        $resultado = $query->get([
            '*',
        ]);

        $cancelamentos = [];
        $total = 0;
        foreach($resultado as $k => $r) {
            $pet = $r->pet;
            if(!$pet) {
                continue;
            }
            $cliente = $r->pet->cliente;
            $plano = $pet->plano();
            $petPlano = $pet->petsPlanosAtual()->first();

            if (empty($plano)) {
                continue;
            }

            $statusFinanceiro = $cliente ? $cliente->statusPagamento() : null;

            if(isset($params['status_financeiro']) && isset($statusFinanceiro)) {
                if($statusFinanceiro != $params['status_financeiro']) {
                    continue;
                }
            }

            $cancelamentos[] = [
                'data_cancelamento' => $r->data_cancelamento->format('d/m/Y'),
                'cliente' => ($cliente ? ['id' => $cliente->id, 'nome' => $cliente->nome_cliente, 'id_externo' => $cliente->id_externo, 'celular' => $cliente->celular, 'email' => $cliente->email, 'ativo' => $cliente->ativo] : null),
                'pet' => ($pet ? ['id' => $pet->id, 'nome' => $pet->nome_pet, 'regime' => $pet->regime, 'valor' => (!empty($petPlano->valor_momento) ?: 0), 'ativo' => $pet->ativo] : null),
                'plano' =>  ($plano->nome_plano ? $plano->nome_plano : '-'),
                'motivo' => $r->motivo,
                'status_financeiro' => $statusFinanceiro,
                'sinistralidade' => $pet->historicoUsos()->where('status', HistoricoUso::STATUS_LIBERADO)->sum('valor_momento')
            ];

            $total++;
        }

        return [
            'cancelamentos' => $cancelamentos,
            'total' => $total,
            'params' => array_merge([
                'start' => $start->format('d/m/Y'),
                'end'   => $end->format('d/m/Y'),
            ], $params)
        ];
    }

    public function cancelamentoDownload(Request $request)
    {
        if(!Entrust::can('relatorio_cancelamento_download')) {
            return self::notAllowed();
        }
        $params = self::relatorioCancelamento($request);
        $cancelamentos = $params['cancelamentos'];
        $total = $params['total'];

        $format = 'xlsx';
        if($request->filled('format')) {
            $format = $request->get('format');
            if(!in_array($format, ['pdf', 'xlsx', 'csv'])) {
                $format = 'xlsx';
            }
        }

        $formatType = ($format === 'pdf') ? Excel::DOMPDF : Excel::XLSX;
        $name = 'cancelamento-'. Carbon::now()->format('ymdHis');

        if ($format == 'xlsx') {
            $name = $name.'.'.$format;
        }

        $data = [
            'cancelamentos' => $cancelamentos,
            'total' => $total,
            'exportar' => true
        ];

        return \Excel::download(
            new GlobalExport($data, 'relatorios.parts.cancelamento.table'),
            $name,
            $formatType
        );
    }

    public function sinistralidadeGrupos(Request $request)
    {
        if(!Entrust::can('relatorio_sinistralidade')) {
            return self::notAllowed();
        }

        $params = self::guiasRelatorioSinistralidadeGruposHospitalares($request);
        return view('relatorios.sinistralidade_grupos')->with($params);
    }

    public function sinistralidade(Request $request)
    {
        if(!Entrust::can('relatorio_sinistralidade')) {
            return self::notAllowed();
        }

        $params = self::guiasRelatorioSinistralidade($request);
        return view('relatorios.sinistralidade')->with($params);
    }

    public function cancelamento(Request $request) {
        if(!Entrust::can('relatorio_cancelamento')) {
            return self::notAllowed();
        }

        $params = self::relatorioCancelamento($request);

        return view('relatorios.cancelamento')->with($params);
    }

    public function sinistralidadesDownload(Request $request)
    {
        if(!Entrust::can('relatorio_sinistralidade_download')) {
            return self::notAllowed();
        }
        $params = self::guiasRelatorioSinistralidade($request);
        $guias = $params['guias'];

        $format = 'xlsx';
        if($request->filled('format')) {
            $format = $request->get('format');
            if(!in_array($format, ['pdf', 'xlsx', 'csv'])) {
                $format = 'xlsx';
            }
        }

        $formatType = ($format === 'pdf') ? Excel::DOMPDF : Excel::XLSX;
        $name = 'sinistralidade-'. Carbon::now()->format('ymdHis');

        if ($format == 'xlsx') {
            $name = $name.'.'.$format;
        }

        $data = [
            'guias' => $guias,
            'exportar' => true
        ];

        return \Excel::download(
            new GlobalExport($data, 'relatorios.parts.sinistralidade.table'),
            $name,
            $formatType
        );
    }

    public function sinistralidadesGruposDownload(Request $request)
    {
        if(!Entrust::can('relatorio_sinistralidade_download')) {
            return self::notAllowed();
        }
        $params = self::guiasRelatorioSinistralidadeGruposHospitalares($request);
        $guias = $params['guias'];

        $format = 'xlsx';
        if($request->filled('format')) {
            $format = $request->get('format');
            if(!in_array($format, ['pdf', 'xlsx', 'csv'])) {
                $format = 'xlsx';
            }
        }

        $name = 'sinistralidade-'. Carbon::now()->format('ymdHis');

        if ($format == 'xlsx') {
            $name = $name.'.'.$format;
        }

        $formatType = ($format === 'pdf') ? Excel::DOMPDF : Excel::XLSX;

        $data = [
            'guias' => $guias,
            'exportar' => true
        ];

        return \Excel::download(
            new GlobalExport($data, 'relatorios.parts.sinistralidade.table_grupos'),
            $name,
            $formatType
        );
    }

    private static function buildQuery($key, $f, &$query)
    {
        if(is_array($key)) {
            $operator = isset($key['operator']) ? $key['operator'] : "=";
            if (array_key_exists($operator, ['=', '<', '>', '<=', '>='])) {
                $query->where($key['field'], $operator, $f);
            } elseif ($operator === 'LIKE') {
                $query->where($key['field'], $operator, "%$f%");
            } elseif ($operator === 'IN') {
                if(!is_array($f)) {
                    $f = [$f];
                }
                $query->whereIn($key['field'], $f);
            }
        } else {
            $query->where($key, $f);
        }
    }

    public static function setSelected($find, $params, $key) {
        $selected = "selected=selected";
        if(!isset($params[$key])) {
            return null;
        }
        if(is_array($params[$key])) {
            if(in_array($find, $params[$key])) {
                return $selected;
            }
        } else {
            if($find === $params[$key]) {
                return $selected;
            }
        }

        return null;
    }

    public static function getTimesheets(Request $request) {
        ini_set('max_execution_time', 300);
        list($start, $end) = array_values(self::getDates($request));

        /**
         * @var \Illuminate\Database\Eloquent\Builder {$query
         */
        $query = \App\Models\Timesheet::orderBy('timesheets.created_at','DESC')->
        join('tarefas as tr', 'timesheets.id_tarefa', '=', 'tr.id')->
        join('users as u', 'timesheets.id_usuario', '=', 'u.id')->
        join('projetos as p', 'tr.id_projeto', '=', 'p.id')->
        join('departamentos as d', 'p.id_departamento', '=', 'd.id')->
        groupBy('timesheets.id_tarefa')->
        groupBy('timesheets.id_usuario')->
        orderBy('u.name', 'ASC')->
        whereBetween('timesheets.created_at', [$start, $end])->
        select(
            'timesheets.id as id',
            'u.name as username',
            'tr.nome as tarefa',
            'p.nome as projeto',
            'd.nome as departamento',
            DB::raw('SUM(duracao) as duracao_total')
        );

        $filters = [
            'tarefas' => [
                'field' => 'tr.id',
                'operator' => 'IN'
            ],
            'projetos' => [
                'field' => 'p.id',
                'operator' => 'IN'
            ],
            'departamentos' => [
                'field' => 'd.id',
                'operator' => 'IN'
            ],
            'users' => [
                'field' => 'u.id',
                'operator' => 'IN'
            ]
        ];

        $params = [];
        self::setFilters($request, $filters, $query, $params);

        $sheets = $query->get();

        return [
            'sheets' => $sheets,
            'params' => array_merge([
                'start' => $start->format('d/m/Y'),
                'end'   => $end->format('d/m/Y'),
            ], $params)
        ];
    }

    public function reajustes(Request $request)
    {
        if(!Entrust::can('relatorio_reajustes')) {
            return self::notAllowed();
        }

        $params = self::getReajustes($request);
        return view('relatorios.reajustes')->with($params);
    }

    public static function getReajustes(Request $request) {

        ini_set('max_execution_time', 300);

        /**
         * @var \Illuminate\Database\Eloquent\Builder {$query
         */

        $data = $request->all();

        $data['ano'] = isset($data['ano']) ? $data['ano'] : Carbon::today()->year;
        $data['mes'] = isset($data['mes']) ? $data['mes'] : Carbon::today()->month + 1;

        $start = Carbon::create($data['ano']-1, $data['mes'])->startOfMonth();
        $end = Carbon::create($data['ano'], $data['mes']-1)->endOfMonth();

        $queryMesReajuste = \App\Models\Pets::where('ativo', '1')
                                            ->where('mes_reajuste', $data['mes'])
                                            ->orderBy('pets.regime', 'ASC')
                                            ->select('pets.*');

        $filters = [
        ];
        $params = [];

        self::setFilters($request, $filters, $queryMesReajuste, $params);
        $petsMesReajuste = $queryMesReajuste->get();

        $dados = [];

        foreach ($petsMesReajuste as $pet) {

            $valorBase = $pet->petsPlanosAtual()->first() ? $pet->petsPlanosAtual()->first()->valor_momento : $pet->valor;

            $valorPago = $pet->regime == 'MENSAL' ? $valorBase * 12 : $valorBase;
            $participacao = \App\Models\Participacao::where('id_pet', $pet->id)
                ->whereBetween('created_at', [$start, $end])
                ->sum('valor_participacao');

            $valorUtilizado = $pet->getValorUtilizado($start, $end);
            $valorPago = $valorPago + $participacao;
            $relacao_uso = round(($valorUtilizado / ($valorPago ?: 0.01) * 100), 2);
            if ($relacao_uso <= 100) {
                $reajuste = '7,54';
            } else if ($relacao_uso <= 200) {
                $reajuste = '18';
            } else {
                $reajuste = '24';
            }

            $dados[] = [
                'pet' => $pet,
                'plano' => $pet->plano(),
                'cliente' => $pet->cliente,
                'valorUtilizado' => $valorUtilizado,
                'valorPago' => $valorPago,
                'relacao_uso' => $relacao_uso,
                'reajuste' => $reajuste,
            ];
        }
        return [
            'dados' => $dados,
            'params' => array_merge([
                'start' => $start->format('d/m/Y'),
                'end'   => $end->format('d/m/Y'),
                'ano'   => $data['ano'],
                'mes'   => $data['mes'],
            ], $params)
        ];
    }

    public function reajustesDownload(Request $request)
    {
        if(!Entrust::can('relatorio_reajustes_download')) {
            return self::notAllowed();
        }
        $params = self::getTimesheets($request);
        $sheets = $params['sheets'];

        $format = 'xlsx';
        if($request->filled('format')) {
            $format = $request->get('format');
            if(!in_array($format, ['pdf', 'xlsx', 'csv'])) {
                $format = 'xlsx';
            }
        }

        $name = 'reajustes-'. Carbon::now()->format('ymdHis');

        if ($format == 'xlsx') {
            $name = $name.'.'.$format;
        }

        $formatType = ($format === 'pdf') ? Excel::DOMPDF : Excel::XLSX;

        $data = [
            'sheets' => $sheets,
            'exportar' => true
        ];

        return \Excel::download(
            new GlobalExport($data, 'relatorios.parts.reajustes.table'),
            $name,
            $formatType
        );
    }

    public function receitas(Request $request)
    {
        if(!Entrust::can('relatorio_reajustes')) {
            return self::notAllowed();
        }

        $params = self::getReceitas($request);
        return view('relatorios.receitas')->with($params);
    }

    public static function getReceitas(Request $request) {

        ini_set('max_execution_time', 300);

        $data = $request->all();
        $today = Carbon::today();

        $data['mes'] = isset($data['mes']) ? $data['mes'] : $today->month;
        $data['ano'] = isset($data['ano']) ? $data['ano'] : $today->year;

        $receitas = \App\Models\Pagamentos::select(
            'c.nome_cliente',
            'pets.id as id_pet',
            'pets.nome_pet',
            'p.id as id_plano',
            'p.nome_plano',
            'cob.competencia',
            'pagamentos.data_pagamento',
            'pagamentos.valor_pago',
            'pagamentos.complemento'
        )
        ->join('cobrancas as cob', 'cob.id', '=', 'pagamentos.id_cobranca')
        ->join('clientes as c', 'c.id', '=', 'cob.id_cliente')
        ->join('pets', 'pets.id_cliente', '=', 'c.id')
        ->join('pets_planos as pp', function($query) {
            $query->on('pets.id','=','pp.id_pet')
            ->whereRaw('pp.id IN (select MAX(pp2.id) from pets_planos as pp2 join pets as p2 on p2.id = pp2.id_pet group by p2.id)');
        })
        ->join('planos as p', 'p.id', '=', 'pp.id_plano')
        ->where('p.ativo', 1)
        ->where('cob.competencia', '=', $data['ano'] . '-' . $data['mes'])
        ->groupBy('c.nome_cliente')
        ->orderBy('c.nome_cliente', 'ASC');

        if(isset($data['tipoReceita'])){
            $receitas->where('pagamentos.complemento', 'LIKE', '%'. $data['tipoReceita'] .'%');

            if($data['tipoReceita'] !== 'Fatura') {
                $receitas->groupBy('pagamentos.complemento');
            }
        }

        if(isset($data['modalidade'])){
            $receitas->where('p.participativo', $data['modalidade']);
        }

        return [
            'receitas' => $receitas->get(),
            'params' => [
                'mes'   => $data['mes'],
                'ano'   => $data['ano'],
                'tipoReceita' => $data['tipoReceita'] ?? null,
                'modalidade' => $data['modalidade'] ?? null
            ]
        ];
    }

    public function receitasDownload(Request $request)
    {
        if(!Entrust::can('relatorio_reajustes_download')) {
            return self::notAllowed();
        }
        $params = self::getreceitas($request);
        $receitas = $params['receitas'];

        $format = 'xlsx';
        if($request->filled('format')) {
            $format = $request->get('format');
            if(!in_array($format, ['pdf', 'xlsx', 'csv'])) {
                $format = 'xlsx';
            }
        }

        $formatType = ($format === 'pdf') ? Excel::DOMPDF : Excel::XLSX;
        $name = 'receitas-'. Carbon::now()->format('ymdHis');

        if ($format == 'xlsx') {
            $name = $name.'.'.$format;
        }

        $data = [
            'receitas' => $receitas,
            'exportar' => true
        ];

        return \Excel::download(
            new GlobalExport($data, 'relatorios.parts.receitas.table'),
            $name,
            $formatType
        );
    }

    public function receitasPicpay(Request $request)
    {
        if(!Entrust::can('relatorio_reajustes')) {
            return self::notAllowed();
        }

        $params = self::getReceitasPicpay($request);
        return view('relatorios.receitas-picpay')->with($params);
    }

    public static function getReceitasPicpay(Request $request) {

        ini_set('max_execution_time', 300);

        $data = $request->all();
        $inicio = Carbon::now()->startOfMonth();
        $fim = Carbon::now()->endOfMonth();

        $data['inicio'] = isset($data['inicio']) ? Carbon::createFromFormat('d/m/Y', $data['inicio']) : $inicio;
        $data['fim'] = isset($data['fim']) ? Carbon::createFromFormat('d/m/Y', $data['fim']) : $fim;
        
        $receitas = \App\Models\Notas::select(
            'notas.corpo as descricao', 
            'notas.created_at',
            'c.nome_cliente'
        )
        ->join('clientes as c', 'c.id', '=', 'notas.cliente_id')
        ->whereBetween('notas.created_at', [$data['inicio'], $data['fim']])
        ->where('notas.corpo', 'LIKE', '%PICPAY%')
        ->orderBy('notas.created_at', 'ASC')
        ->get();

        foreach($receitas as $receita) {
            $receita['valor_pago'] = explode("R$ ", $receita->descricao);
            $receita['valor_pago'] = floatval($receita['valor_pago'][1]);
        }

        return [
            'receitas' => $receitas,
            'params' => [
                'inicio'   => $data['inicio']->format('d/m/Y'),
                'fim'   => $data['fim']->format('d/m/Y')
            ]
        ];
    }

    public function receitasPicpayDownload(Request $request)
    {
        if(!Entrust::can('relatorio_reajustes_download')) {
            return self::notAllowed();
        }
        $params = self::getReceitasPicpay($request);
        $receitas = $params['receitas'];

        $format = 'xlsx';
        if($request->filled('format')) {
            $format = $request->get('format');
            if(!in_array($format, ['pdf', 'xlsx', 'csv'])) {
                $format = 'xlsx';
            }
        }

        $formatType = ($format === 'pdf') ? Excel::DOMPDF : Excel::XLSX;
        $name = 'receitas-picpay-'. Carbon::now()->format('ymdHis');

        if ($format == 'xlsx') {
            $name = $name.'.'.$format;
        }

        $data = [
            'receitas' => $receitas,
            'exportar' => true
        ];

        return \Excel::download(
            new GlobalExport($data, 'relatorios.parts.receitas-picpay.table'),
            $name,
            $formatType
        );
    }

    public function receitasLinkPagamento(Request $request)
    {
        if(!Entrust::can('relatorio_reajustes')) {
            return self::notAllowed();
        }

        $params = self::getReceitasLinkPagamento($request);
        return view('relatorios.receitas-link-pagamento')->with($params);
    }

    public static function getReceitasLinkPagamento(Request $request) {

        ini_set('max_execution_time', 300);

        $data = $request->all();
        $inicio = Carbon::now()->startOfMonth();
        $fim = Carbon::now()->endOfMonth();

        $data['inicio'] = isset($data['inicio']) ? Carbon::createFromFormat('d/m/Y', $data['inicio']) : $inicio;
        $data['fim'] = isset($data['fim']) ? Carbon::createFromFormat('d/m/Y', $data['fim']) : $fim;
        
        $receitas = DB::table('links_pagamento as lp')
        ->select(
            'lp.updated_at', 
            'lp.valor',
            'lp.tags',
            'lp.descricao',
            'c.nome_cliente'
        )
        ->join('clientes as c', 'c.id', '=', 'lp.id_cliente')
        ->whereBetween('lp.updated_at', [$data['inicio'], $data['fim']])
        ->where('lp.status', '=', 'PAGO')
        ->orderBy('lp.updated_at')
        ->get();

        return [
            'receitas' => $receitas,
            'params' => [
                'inicio'   => $data['inicio']->format('d/m/Y'),
                'fim'   => $data['fim']->format('d/m/Y')
            ]
        ];
    }

    public function receitasLinkPagamentoDownload(Request $request)
    {
        if(!Entrust::can('relatorio_reajustes_download')) {
            return self::notAllowed();
        }
        $params = self::getReceitasLinkPagamento($request);
        $receitas = $params['receitas'];

        $format = 'xlsx';
        if($request->filled('format')) {
            $format = $request->get('format');
            if(!in_array($format, ['pdf', 'xlsx', 'csv'])) {
                $format = 'xlsx';
            }
        }

        $formatType = ($format === 'pdf') ? Excel::DOMPDF : Excel::XLSX;
        $name = 'receitas-link-pagamento-'. Carbon::now()->format('ymdHis');

        if ($format == 'xlsx') {
            $name = $name.'.'.$format;
        }
        $data = [
            'receitas' => $receitas,
            'exportar' => true
        ];

        return \Excel::download(
            new GlobalExport($data, 'relatorios.parts.receitas-link-pagamento.table'),
            $name,
            $formatType
        );
    }

    public static function getPetsSemMicrochip(Request $request) {
        ini_set('max_execution_time', 300);
        if ($request->get('start') && $request->get('end')) {
            list($start, $end) = array_values(self::getDates($request));
        } else {
            $start = Carbon::createFromFormat('Y-m-d', '2000-01-01');
            $end = Carbon::now();
        }

        /**
         * @var \Illuminate\Database\Eloquent\Builder {$query
         */
        $query = \App\Models\Pets::orderBy('pets.created_at','DESC')->
        join('pets_planos as pp', 'pets.id_pets_planos', '=', 'pp.id')->
        join('planos as p', 'pp.id_plano', '=', 'p.id')->
        join('clientes as c', 'pets.id_cliente', '=', 'c.id')->
        orderBy('pp.data_inicio_contrato', 'DESC')->
        whereBetween('pp.data_inicio_contrato', [$start, $end])->
        where('pets.ativo', '=', 1)->
        where(function($query) {
            $query->where('pets.numero_microchip', '');
            $query->orWhere('pets.numero_microchip', 0);
            $query->orWhereNull('pets.numero_microchip');
            $query->orWhereRaw("pets.numero_microchip REGEXP '[a-z]+'");
            $query->orWhereRaw("pets.numero_microchip REGEXP '[A-Z]+'");
        })->
        select(
            'pets.id as id',
            'pets.numero_microchip as numero_microchip',
            'pets.nome_pet as pet',
            'p.nome_plano as plano',
            'pp.data_inicio_contrato as data_inicio_contrato',
            'c.nome_cliente as cliente',
            'c.email as email',
            'c.celular as celular',
            'c.cidade as cidade',
            'c.estado as estado'
        );
        
        $filters = [];
        if ($request->get('planos')) {
            $filters['planos'] = [
                'field' => 'p.id',
                'operator' => 'IN'
            ];
        } 

        $params = [];
        self::setFilters($request, $filters, $query, $params);

        $pets = $query->get();

        return [
            'pets' => $pets,
            'params' => array_merge([
                'start' => $start->format('d/m/Y'),
                'end'   => $end->format('d/m/Y'),
            ], $params)
        ];
    }

    public function petsSemMicrochip(Request $request)
    {
        if(!Entrust::can('relatorio_pets_sem_microchip')) {
            return self::notAllowed();
        }

        $params = self::getPetsSemMicrochip($request);
        return view('relatorios.pets_sem_microchip')->with($params);
    }

    public function petsSemMicrochipDownload(Request $request)
    {
        if(!Entrust::can('relatorio_pets_sem_microchip')) {
            return self::notAllowed();
        }
        $params = self::getPetsSemMicrochip($request);
        $pets = $params['pets'];

        $format = 'xlsx';
        if($request->filled('format')) {
            $format = $request->get('format');
            if(!in_array($format, ['pdf', 'xlsx', 'csv'])) {
                $format = 'xlsx';
            }
        }

        $formatType = ($format === 'pdf') ? Excel::DOMPDF : Excel::XLSX;
        $name = 'pets_sem_microchip-'. Carbon::now()->format('ymdHis');

        if ($format == 'xlsx') {
            $name = $name.'.'.$format;
        }
        $data = [
            'pets' => $pets,
            'exportar' => true
        ];

        return \Excel::download(
            new GlobalExport($data, 'relatorios.parts.pets_sem_microchip.table'),
            $name,
            $formatType
        );
    }

    public function criarRelatorioPetsPlanosRetroativo() {
        $vidasAtivas = DadosTemporais::where('indicador', 'vidas_ativas')->orderBy('data_referencia', 'DESC')->get();

        $ultimoVidasAtivas = DadosTemporais::where('indicador', 'vidas_ativas')->orderBy('data_referencia', 'DESC')->first();
        $qtdeTotal = $ultimoVidasAtivas->valor_numerico;

        $i = 0;

        RelatorioPetsPlanos::truncate();
        foreach($vidasAtivas as $k => $v){
            
            $data = $v->data_referencia->addDays(1)->format('Y-m-d');
         
            $encerramentosDoDia = PetsPlanos::select('data_encerramento_contrato', DB::raw('COUNT(1) as total'), DB::raw("SUM(CASE WHEN p.regime = 'MENSAL' then valor_momento else valor_momento/12 end) as valor"))
                    ->from('pets_planos')
                    ->join(DB::raw('(SELECT id_pet, MAX(id) id FROM pets_planos WHERE pets_planos.deleted_at IS NULL GROUP BY id_pet) t2'), function($join) {
                        $join->on('pets_planos.id', '=', 't2.id');
                        $join->on('pets_planos.id_pet', '=', 't2.id_pet');
                    })
                    ->leftJoin('pets as p', 't2.id_pet', '=', 'p.id')
                    ->whereBetween('data_encerramento_contrato', [$data . ' 00:00:00', $data . ' 23:59:59'])
                    ->orderBy('data_encerramento_contrato', 'DESC')->first();
                           
            $encerradosDiaQtde = $encerramentosDoDia->total;
            $encerradosDiaValor = $encerramentosDoDia->valor;

            $queryInicio = PetsPlanos::select('data_inicio_contrato', DB::raw('COUNT(1) as total'), DB::raw("SUM(CASE WHEN p.regime = 'MENSAL' then valor_momento else valor_momento/12 end) as valor"))
            ->from('pets_planos')
            ->join(DB::raw("(SELECT id_pet, MAX(id) id FROM pets_planos WHERE pets_planos.status = 'P' AND pets_planos.deleted_at IS NULL GROUP BY id_pet) t2"), function($join) {
                $join->on('pets_planos.id', '=', 't2.id');
                $join->on('pets_planos.id_pet', '=', 't2.id_pet');
            })
            ->leftJoin('pets as p', 't2.id_pet', '=', 'p.id');

            $iniciadosDoDia = $queryInicio
                    ->whereBetween('data_inicio_contrato', [$data . ' 00:00:00', $data . ' 23:59:59'])
                    ->orderBy('data_inicio_contrato', 'DESC')->first();
                    
            $iniciadosDiaQtde = $iniciadosDoDia->total;
            $iniciadosDiaValor = $iniciadosDoDia->valor;

            if($i == 0) {
                $qtdeTotal = $qtdeTotal + $iniciadosDiaQtde - $encerradosDiaQtde;
            } else {
                $qtdeTotal = $qtdeTotal - $iniciadosDiaSeguinteQtde + $encerradosDiaSeguinteQtde;
            }
            

            RelatorioPetsPlanos::create([
                'data' => $data,
                'qtde_total' => $qtdeTotal,
                'qtde_total_iniciados' => 0,
                'valor_total_iniciados' => 0,
                'qtde_total_encerrados' => 0,
                'valor_total_encerrados' => 0,
                'qtde_dia_iniciados' => $iniciadosDiaQtde ?? 0,
                'valor_dia_iniciados' => $iniciadosDiaValor ?? 0,
                'qtde_dia_encerrados' => $encerradosDiaQtde ?? 0,
                'valor_dia_encerrados' => $encerradosDiaValor ?? 0,
                'qtde_dia_downgrades' => 0,
                'qtde_dia_upgrades' => 0
            ]);


            $i++;

            $iniciadosDiaSeguinteQtde = $iniciadosDiaQtde;
            $encerradosDiaSeguinteQtde = $encerradosDiaQtde;
        }
    }

     /**
     * Obtém os dados de clientes ativos:
     *
     * @param Request $request
     * @return array
     */
    private static function relatorioClientes(Request $request)
    {
        ini_set('max_execution_time', 300);
        list($start, $end) = array_values(self::getDates($request));

        $query = \App\Models\Clientes::whereBetween('created_at', [$start, $end])->orderBy('created_at', 'DESC');
    
        if($request->filled('ativo') && in_array($request->input('ativo'), [0,1])) {
            $query = $query->where('ativo', $request->input('ativo'));
        }

        if($request->filled('sexo') && in_array($request->input('sexo'), ['M','F'])) {
            $query = $query->where('sexo', $request->input('sexo'));
        }

        if($request->filled('pet_planos')) {
  
            $query = $query->whereHas('pets.petsPlanos.plano', function ($query) use($request) {
                return $query->whereIn('id', $request->pet_planos);
            });
        }
        
        // Procura os planos que o cliente contratou que fazem aniversário entre as datas escolhidas considerando o dia e o mês assinado
        if($request->filled('pet_plano_aniversario_inicio') || $request->filled('pet_plano_aniversario_fim')) {
           
            $query = $query->whereHas('pets.petsPlanos', function ($query) use($request) {

                if($request->filled('pet_plano_aniversario_inicio')) {
                    $petPlanoAniversarioPeriodo['inicio'] = Carbon::createFromFormat('d/m/Y', $request->input('pet_plano_aniversario_inicio'))->format('Y-m-d');
                }
        
                if($request->filled('pet_plano_aniversario_fim')) {
                    $petPlanoAniversarioPeriodo['fim'] = Carbon::createFromFormat('d/m/Y', $request->input('pet_plano_aniversario_fim'))->format('Y-m-d');
                }

                if(array_key_exists('inicio', $petPlanoAniversarioPeriodo) && array_key_exists('fim', $petPlanoAniversarioPeriodo)) {
                    $query->whereRaw(PetsPlanos::aniversarioPorPeriodoRawQuery($petPlanoAniversarioPeriodo['inicio'], $petPlanoAniversarioPeriodo['fim']));
                } elseif(array_key_exists('inicio', $petPlanoAniversarioPeriodo)) {
                    $query->whereRaw(PetsPlanos::aniversarioPorPeriodoRawQuery($petPlanoAniversarioPeriodo['inicio']));
                } else {
                    $query->whereRaw(PetsPlanos::aniversarioPorPeriodoRawQuery(null, $petPlanoAniversarioPeriodo['fim']));
                }
                
                // Se escolher clientes com status ativo, é buscado apenas de planos que ainda estão vigentes (sem data de encerramento)
                if($request->filled('ativo') && $request->input('ativo') == 1) {
                    $query = "AND data_encerramento_contrato IS NULL";
                }
                
            });
   
        }

        if($request->filled('pet_idade_de') || $request->filled('pet_idade_ate')) {
            
            $query = $query->whereHas('pets', function ($query) use($request) {
                
                if($request->filled('pet_idade_de')) {
                    $petAnoNascimentoDe = date('Y') - $request->input('pet_idade_de');
                    $petDataNascimentoDe = date("{$petAnoNascimentoDe}-m-d");
                    $query->whereRaw("data_nascimento <= '{$petDataNascimentoDe}'");
                }

                if($request->filled('pet_idade_ate')) {
                    $petAnoNascimentoAte = date('Y') - $request->input('pet_idade_ate');
                    $petDataNascimentAte = date("{$petAnoNascimentoAte}-m-d");
                    $query->whereRaw("data_nascimento >= '{$petDataNascimentAte}' ");
                }
            });
        }

        if($request->filled('pet_sexo') && in_array($request->input('pet_sexo'), ['M','F'])) {
            $query = $query->whereHas('pets', function ($query) use($request) {
                $query->where('sexo', $request->input('pet_sexo'));
            });
        }

        $resultado = $query->get([
            '*',
        ]);

        $params = $request->all();
        $params['start'] = $start->format('d/m/Y');
        $params['end'] = $end->format('d/m/Y');

        $clientes = [];
        $total = 0;
        foreach($resultado as $k => $cliente) {
 
            $statusFinanceiro = $cliente->statusPagamento();

            $clientes[] = [
                'data_cadastro' => $cliente->created_at->format('d/m/Y'),
                'id' => $cliente->id,
                'nome' => $cliente->nome_cliente,
                'cpf_cnpj' =>  $cliente->cpf,
                'email' =>  $cliente->email,
                'celular' => $cliente->celular,
                'telefone_fixo' => $cliente->telefone_fixo,
                'status_financeiro' => $statusFinanceiro,
                'dia_vencimento' => $cliente->dia_vencimento,
                'forma_pagamento' => $cliente->forma_pagamento,
                'quantidade_pets' => $cliente->pets()->count(),
            ];

            $total++;
        }

        return [
            'dados' => $clientes,
            'total' => $total,
            'params' => array_merge([
                'start' => $start->format('d/m/Y'),
                'end'   => $end->format('d/m/Y'),
            ], $params)
        ];
    }

    public function clientes(Request $request) {
        if(!Entrust::can('relatorio_clientes')) {
            return self::notAllowed();
        }
   
        $params = self::relatorioClientes($request);

        return view('relatorios.clientes')->with($params);
    }

    public function clientesDownload(Request $request)
    {
        if(!Entrust::can('relatorio_clientes_download')) {
            return self::notAllowed();
        }
        $params = self::relatorioClientes($request);
        $clientes = $params['dados'];
        $total = $params['total'];

        $data = [
            'dados' => $clientes,
            'total' => $total,
            'exportar' => true
        ];

        return \Excel::download(
            new GlobalExport($data, 'relatorios.parts.clientes.table'),
            'clientes-'. Carbon::now()->format('ymdHis').'.xlsx',
            Excel::XLSX
        );
    }

    public static function relatorioInadimplentes(Request $request)
    {
        ini_set('max_execution_time', 500);
        $inadimplentes = \App\Models\Cobrancas::where('cobrancas.status', 1)
            ->where('cobrancas.data_vencimento', '<', Carbon::now())
            ->whereNull('cobrancas.acordo')
            ->whereNull('cancelada_em')
            ->leftJoin('pagamentos as pg', 'pg.id_cobranca', '=', 'cobrancas.id')
            ->join('clientes as c', 'c.id', '=', 'cobrancas.id_cliente')
            ->orderBy('c.nome_cliente', 'ASC')
            ->selectRaw(
                'c.id as id_cliente,
                c.nome_cliente as nome_cliente,
                c.cpf as cpf_cnpj,
                c.celular as telefone,
                c.email as email,
                c.ativo as status,
                cobrancas.competencia as competencia,
                cobrancas.data_vencimento as data_vencimento,
                cobrancas.valor_original as valor'
            )
            ->with('cliente')
            ->havingRaw('COUNT(pg.id_cobranca) = 0')
            ->groupBy('cobrancas.id')
            ->get()
            ->map(function ($inadimplente) {
                return [
                    'id_cliente' => $inadimplente->id_cliente,
                    'nome_cliente' => $inadimplente->nome_cliente,
                    'cpf_cnpj' => $inadimplente->cpf_cnpj,
                    'telefone' => $inadimplente->telefone,
                    'email' => $inadimplente->email,
                    'status' => $inadimplente->status,
                    'competencia' => $inadimplente->competencia,
                    'data_vencimento' => $inadimplente->data_vencimento,
                    'valor' => $inadimplente->valor,
                    'statusFinanceiro' => $inadimplente->cliente ? $inadimplente->cliente->statusPagamento() : null
                ];
            });
            
        return [
            'clientesInadimplentes' => $inadimplentes,
            'total' => $inadimplentes->count()
        ];
    }

    public function inadimplentes(Request $request)
    {
        if(!Entrust::can('relatorio_clientes')) {
            return self::notAllowed();
        }
   
        $params = self::relatorioInadimplentes($request);

        return view('relatorios.inadimplentes')->with($params);
    }

    public function inadimplentesDownload(Request $request)
    {
        if(!Entrust::can('relatorio_clientes_download')) {
            return self::notAllowed();
        }

        $params = self::relatorioInadimplentes($request);
        $inadimplentes = $params['clientesInadimplentes'];
        $total = $params['total'];

        $data = [
            'clientesInadimplentes' => $inadimplentes,
            'total' => $total,
            'exportar' => true
        ];

        return \Excel::download(
            new GlobalExport($data, 'relatorios.parts.inadimplentes.table'),
            'inadimplentes-'. Carbon::now()->format('ymdHis').'.xlsx',
            Excel::XLSX
        );
    }

    public static function getPets(Request $request) {
        try {
            ini_set('max_execution_time', 300);
            list($start, $end) = array_values(self::getDates($request));

            /**
             * @var \Illuminate\Database\Eloquent\Builder {$query
             */
            $query = \App\Models\Pets::orderBy('pets.created_at', 'DESC')->
            join('pets_planos as pp', function ($query) {
                $query->on('pets.id', '=', 'pp.id_pet')
                    ->whereRaw('pp.id IN (select MAX(pp2.id) from pets_planos as pp2 join pets as p2 on p2.id = pp2.id_pet group by p2.id)');
            })->
            join('planos as pl', 'pp.id_plano', '=', 'pl.id')->
            join('clientes as c', 'pets.id_cliente', '=', 'c.id')->
            orderBy('c.nome_cliente', 'ASC')->
            whereBetween('pets.created_at', [$start, $end])->
            select(
                'pets.id as id',
                'pets.nome_pet as nome_pet',
                'pl.nome_plano as nome_plano',
                'pets.ativo as ativo',
                'pets.regime as regime',
                'pp.data_inicio_contrato as inicio_contrato',
                'pp.valor_momento as valor_plano',
                'c.nome_cliente as nome_cliente',
                'c.cpf as cpf',
                'c.cidade as cidade',
                'c.estado as uf',
                'pets.id_cliente as id_cliente',
                'c.dia_vencimento as dia_vencimento',
                'c.forma_pagamento as forma_pagamento'
            );

            $filters = [
                'clientes' => [
                    'field' => 'c.id',
                    'operator' => 'IN'
                ],
                'planos' => [
                    'field' => 'pl.id',
                    'operator' => 'IN'
                ],
                'ativo' => 'pets.ativo'
            ];

            $params = [];
            self::setFilters($request, $filters, $query, $params);

            $pets = $query->get();
            Log::error(sprintf($pets));
            return [
                'pets' => $pets,
                'total' => count($pets),
                'params' => array_merge([
                    'start' => $start->format('d/m/Y'),
                    'end' => $end->format('d/m/Y'),
                ], $params)
            ];
        }catch (\Exception $err){
            Log::error(sprintf($err));
            return [
                'pets' => [],
                'total' => 0,
                'params' => array_merge([
                    'start' => $start->format('d/m/Y'),
                    'end' => $end->format('d/m/Y'),
                ], $params)
            ];
        }
    }

    public function pets(Request $request)
    {
        if(!Entrust::can('relatorio_clientes')) {
            return self::notAllowed();
        }

        $params = self::getPets($request);
        return view('relatorios.pets')->with($params);
    }

    public function petsDownload(Request $request)
    {
        if(!Entrust::can('relatorio_clientes')) {
            return self::notAllowed();
        }
        $params = self::getPets($request);

        $format = 'xlsx';
        if($request->filled('format')) {
            $format = $request->get('format');
            if(!in_array($format, ['pdf', 'xlsx', 'csv'])) {
                $format = 'xlsx';
            }
        }

        $formatType = ($format === 'pdf') ? Excel::DOMPDF : Excel::XLSX;
        $name = 'pets-'. Carbon::now()->format('ymdHis');

        if ($format == 'xlsx') {
            $name = $name.'.'.$format;
        }
        $data = [
            'total' => $params['total'],
            'pets' => $params['pets'],
            'exportar' => true
        ];

        return \Excel::download(
            new GlobalExport($data, 'relatorios.parts.pets.table'),
            $name,
            $formatType
        );
    }

    public function compraRapida()
    {
        ini_set('max_execution_time', 300);
        $compras = LifepetCompraRapida::orderBy('id', 'desc')->get();

        return view('relatorios.compra-rapida', compact('compras'));
    }

    public function indicacoes()
    {
        ini_set('max_execution_time', 300);
        $indicacoes = Indicacoes::orderBy('id', 'desc')->get();

        return view('relatorios.indicacoes', compact('indicacoes'));
    }

    public function clientesSemFaturaCompetencia(Request $request)
    {
        ini_set('max_execution_time', 300);
        list($start, $end) = array_values(self::getDates($request));

        $statement = "SELECT
               pt.id,
               planos_correntes.data_inicio_contrato as 'inicio_do_contrato',
               p.nome_plano as 'plano',
               pt.regime as 'regime',
               pt.nome_pet as 'pet',
               c.nome_cliente as 'tutor',
               c.celular as 'celular',
               c.id as 'id_tutor',
               planos_correntes.data_encerramento_contrato as 'data_encerramento',
               cb.valor
        FROM
             pets pt
               INNER JOIN (
                SELECT pp.*
                          FROM pets p
                                 INNER JOIN (
                SELECT MAX(id) as id, id_pet FROM pets_planos
                                            GROUP BY id_pet
                                            ) mp
                                 INNER JOIN pets_planos pp ON mp.id = pp.id
                          GROUP BY pp.id_pet
                          ) planos_correntes
               ON planos_correntes.id_pet = pt.id
               INNER JOIN planos p ON p.id = planos_correntes.id_plano
               INNER JOIN clientes c ON c.id = pt.id_cliente
               LEFT JOIN (
                SELECT cobrancas.id_cliente, SUM(valor_original) as valor
                 FROM cobrancas
                 WHERE cobrancas.competencia = :competencia -- Competência de AGOSTO
                 GROUP BY cobrancas.id_cliente
               ) cb ON cb.id_cliente = c.id
        WHERE
            pt.ativo = 1  AND
            c.ativo = 1
            AND pt.deleted_at IS NULL
            AND c.deleted_at IS NULL
            AND p.deleted_at IS NULL
            AND pt.regime = 'MENSAL' -- Apenas mensais
            AND cb.valor IS NULL -- Sem valor de cobrança lançado
            AND p.id NOT IN (42, 43) -- Plano diferente do plano FREE
            AND c.id_conveniado IS NULL -- Sem convênio
            AND planos_correntes.data_inicio_contrato < :data_inicial_competencia
        GROUP BY pt.id
        ORDER BY tutor";

        $mes = sprintf("%02d", $request->get('mes', Carbon::now()->month));
        $ano = $request->get('ano', Carbon::now()->year);
        $competencia = "$ano". "-" . "$mes";
        $dataInicialCompetencia = Carbon::createFromFormat('Y-m', $competencia)->startOfMonth()->format('Y-m-d');

        $results = DB::select(DB::raw($statement), [
           'data_inicial_competencia' => $dataInicialCompetencia,
           'competencia' => $competencia
        ]);

        return view('relatorios.clientes-sem-fatura', [
            'results' => $results,
            'competencia' => $competencia
        ]);
    }
}