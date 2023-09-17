<?php
/**
 * Created by PhpStorm.
 * User: lifepet
 * Date: 15/09/17
 * Time: 14:52
 */

namespace App\Helpers\API\Superlogica\Webhook;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Route;

class Adapter
{
    public function __construct()
    {
        $this->clientMapping = [
            'st_nome_sac' => 'nome_cliente',
            'st_cgc_sac' => 'cpf',
            'st_email_sac' => 'email',
            'st_telefone_sac' => 'telefone_fixo',
            'st_endereco_sac' => 'rua',
            'st_complemento_sac' => 'complemento_endereco',
            'st_cidade_sac' => 'cidade',
            'st_estado_sac' => 'estado',
            'st_cep_sac' => 'cep',
            'st_diavencimento_sac' => 'vencimento',
            'dt_cadastro_sac' => 'created_at',
            'id_sacado_sac' => 'id_superlogica',
            'st_sexo_sac' => 'sexo',
            'dt_nascimento_sac' => 'data_nascimento',
            'st_celular_sac' => 'celular',
            'st_rg_sac' => 'rg',
            'st_bairro_sac' => 'bairro',
        ];
        $this->cobrancasMapping = [
            'id_recebimento_recb'   => 'id_superlogica',
            'dt_vencimento_recb'    => [
                'field'         => 'data_vencimento',
                'formatter' => function($input) {
                    if(!$input) {
                        return null;
                    }
                    return Carbon::createFromFormat('m/d/Y', $input);
                }
            ],
            'id_sacado_sac'         => 'id_cliente_superlogica',
            'fl_status_recb'        => 'status_recebimento',
            'st_observacao_recb'    => 'complemento',
            'vl_total_recb'         => 'valor_original',
            'dt_cancelamento_recb'  => 'cancelamento',
            'dt_competencia_recb'   => [
                'field'         => 'competencia',
                'formatter' => function($input) {
                    if(!$input) {
                        return null;
                    }
                    return Carbon::createFromFormat('m/d/Y', $input);
                }
            ],
            'dt_liquidacao_recb' => [
                'field' => 'data_pagamento',
                'formatter' => function($input) {
                    if(!$input) {
                        return null;
                    }
                    return Carbon::createFromFormat('m/d/Y', $input);
                }
            ],
            'id_formapagamento_recb' => 'forma_pagamento',
            'compo_recebimento'     => [
                'field' => 'composicao',
                'formatter' => function($input) {
                    $pagamentos = [];
                    if(is_array($input)) {
                        foreach($input as $p) {
                            $pagamentos[] = [
                                'id_pagamento_superlogica' => isset($p['id_composicao_comp']) ? $p['id_composicao_comp'] : null,
                                'complemento' => isset($p['st_complemento_comp']) ? $p['st_complemento_comp'] : null,
                                'valor_pago' => isset($p['st_valor_comp']) ? $p['st_valor_comp'] : null,
                            ];
                        }

                        return $pagamentos;
                    }

                    return $input;
                }
            ]
        ];
        $this->despesasMapping = [
            'vl_valor_mov' => 'valor_total',
            'st_descricao_cc' => 'descricao',
            'nm_participacao_movc' => 'porcentagem_participacao',
            'id_centrocusto_cc' => 'id_centrocusto',
            'valor' => 'valor',
            'st_observacoes_mov' => 'observacoes',
            'st_label_mov' => 'label',
            'st_historico_mov' => 'historico',
            'dt_emissao_doc' => 'data_emissao',
            'dt_ordenacao' => 'data_ordenacao',
            'dt_previsaocredito_mov' => 'data_previsaocredito',
        ];
    }

    private static function map(Request $request, array $mapping)
    {
        $mapped = [];
        $data = collect($request->get('data'));

        foreach($mapping as $from => $to) {
            if($data->has($from)) {
                if(is_array($to)) {
                    $formatter = $to['formatter'];
                    $mapped[$to['field']] = $formatter($data->get($from));
                } else {
                    $mapped[$to] = $data->get($from);
                }
            }
        }

        return $mapped;
    }

    public static function adapt(Request $request, $modelName)
    {
        $suffix = 'Mapping';
        $modelName .= $suffix;
        $adapter = new self();

        if(isset($adapter->$modelName)) {
            return self::map($request, $adapter->$modelName);
        }
        return [];
    }

    public static function routes()
    {
        self::alterarDespesa();
        self::cadastrarCobranca();
        self::atualizarCobranca();
        self::liquidarCobranca();
        self::invalidarCobranca();
    }

    private static function invalidarCobranca()
    {
        Route::post('superlogica/cobranca/invalidar', function(Request $request) {
            $token = Config::get('settings.superlogica.webhook.token');
            if($request->get('validationtoken') != $token) {
                return [
                    'status' => 500,
                    'message' => "Token inválido"
                ];
            }

            $adapted = \App\Helpers\API\Superlogica\Webhook\Adapter::adapt($request, 'cobrancas');

            if(!\App\Models\Cobrancas::where('id_superlogica', '=', $adapted['id_superlogica'])->exists()) {
                return ['status' => 500, 'message' => 'Cobrança não encontrada'];
            } else {
                $cobranca = \App\Models\Cobrancas::where('id_superlogica', '=', $adapted['id_superlogica'])->first();
                $cobranca->status = 0;
                $cobranca->deleted_at = new Carbon\Carbon();
                $updated = $cobranca->update();
            }

            if($updated) {
                return ['status' => 200];
            }
        })->name('superlogica.cobranca.invalidar');
    }

    private static function liquidarCobranca()
    {
        Route::post('superlogica/cobranca/liquidar', function (Request $request) {
            $token = Config::get('settings.superlogica.webhook.token');
            if ($request->get('validationtoken') != $token) {
                return [
                    'status' => 500,
                    'message' => "Token inválido"
                ];
            }

            $adapted = \App\Helpers\API\Superlogica\Webhook\Adapter::adapt($request, 'cobrancas');

            $cliente = \App\Models\Clientes::where('id_superlogica', $adapted['id_cliente_superlogica'])->first();
            if (!$cliente) {
                return [
                    'status' => 500,
                    'message' => 'Cliente não vinculado'
                ];
            }

            if (!\App\Models\Cobrancas::where('id_superlogica', '=', $adapted['id_superlogica'])->exists()) {
                return ['status' => 500, 'message' => 'Cobrança não encontrada'];
            } else {
                $cobranca = \App\Models\Cobrancas::where('id_superlogica', '=', $adapted['id_superlogica'])->first();
                $cobranca->fill($adapted);
                $cobranca->update();

                //Gerar pagamentos
                foreach ($adapted['composicao'] as $composicao) {
                    if (floatval($composicao['valor_pago']) < 0) {
                        continue;
                    }
                    $pagamento = [
                        'id_cobranca' => $cobranca->id,
                        'data_pagamento' => $adapted['data_pagamento'],
                        'valor_pago' => $composicao['valor_pago'],
                        'complemento' => $composicao['complemento'],
                        'forma_pagamento' => $adapted['forma_pagamento'],
                        'id_pagamento_superlogica' => $composicao['id_pagamento_superlogica']
                    ];

                    if (!\App\Models\Pagamentos::where('id_pagamento_superlogica', $composicao['id_pagamento_superlogica'])->exists()) {
                        $p = \App\Models\Pagamentos::create($pagamento);
                    } else {
                        $p = \App\Models\Pagamentos::where('id_pagamento_superlogica', $composicao['id_pagamento_superlogica'])->first();
                        $p->fill($pagamento);
                        $p->update();
                    }
                }
            }

            if ($cobranca) {
                return ['status' => 200];
            }
        });
    }

    private static function atualizarCobranca()
    {
        Route::post('superlogica/cobranca/atualizar', function (Request $request) {
            $token = Config::get('settings.superlogica.webhook.token');
            if ($request->get('validationtoken') != $token) {
                return [
                    'status' => 500,
                    'message' => "Token inválido"
                ];
            }

            $adapted = \App\Helpers\API\Superlogica\Webhook\Adapter::adapt($request, 'cobrancas');

            $cliente = \App\Models\Clientes::where('id_superlogica', $adapted['id_cliente_superlogica'])->first();
            if (!$cliente) {
                return [
                    'status' => 500,
                    'message' => 'Cliente não vinculado'
                ];
            }
            $insert = [
                'id_cliente' => $cliente->id,
                'complemento' => $adapted['complemento'],
                'valor_original' => $adapted['valor_original'],
                'data_vencimento' => $adapted['data_vencimento'],
                'status' => 1,
                'competencia' => $adapted['competencia']->format('Y-m'),
                'id_superlogica' => intval($adapted['id_superlogica'])
            ];

            $cobranca = null;
            if (\App\Models\Cobrancas::where('id_superlogica', '=', $insert['id_superlogica'])->exists()) {
                $cobranca = \App\Models\Cobrancas::where('id_superlogica', '=', $insert['id_superlogica'])->first();
                $cobranca->fill($insert);
                $cobranca->update();
            }

            if ($cobranca) {
                return ['status' => 200];
            } else {
                return ['status' => 500, 'message' => 'Cobrança não encontrada'];
            }
        });
    }

    private static function cadastrarCobranca()
    {
        Route::post('superlogica/cobranca/cadastrar', function (Request $request) {
            $token = Config::get('settings.superlogica.webhook.token');
            if ($request->get('validationtoken') != $token) {
                return [
                    'status' => 500,
                    'message' => "Token inválido"
                ];
            }

            $adapted = \App\Helpers\API\Superlogica\Webhook\Adapter::adapt($request, 'cobrancas');

            $cliente = \App\Models\Clientes::where('id_superlogica', $adapted['id_cliente_superlogica'])->first();
            if (!$cliente) {
                return [
                    'status' => 500,
                    'message' => 'Cliente não vinculado'
                ];
            }
            $insert = [
                'id_cliente' => $cliente->id,
                'complemento' => $adapted['complemento'],
                'valor_original' => $adapted['valor_original'],
                'data_vencimento' => $adapted['data_vencimento'],
                'status' => 1,
                'competencia' => $adapted['competencia'],
                'id_superlogica' => intval($adapted['id_superlogica'])
            ];
            if (!\App\Models\Cobrancas::where('id_superlogica', '=', $insert['id_superlogica'])->exists()) {
                $cobranca = \App\Models\Cobrancas::create($insert);
            } else {
                $cobranca = \App\Models\Cobrancas::where('id_superlogica', '=', $insert['id_superlogica'])->first();
                $cobranca->fill($insert);
                $cobranca->update();
            }

            if ($cobranca) {
                return ['status' => 200];
            }
        });
    }

    private static function alterarDespesa()
    {
        Route::post('superlogica/despesa/alterada', function (Request $request) {
            $token = Config::get('settings.superlogica.webhook.token');
            if ($request->get('validationtoken') != $token) {
                return [
                    'status' => 500,
                    'message' => "Token inválido"
                ];
            }
            $data = $request->get('data');
//    $data = $data[0];
            $idDespesa = $data['id_contabanco_mov'];

            $client = new \App\Helpers\API\Superlogica\Client();
            $despesa = $client->caixa([
                'id' => $idDespesa
            ])[0];

            if ($despesa) {

                $centrosDeCusto = $despesa->centro_de_custo;
                foreach ($centrosDeCusto as $cc) {
                    $despesaCC = [
                        'id_superlogica' => $despesa->id_contabanco_mov,
                        'valor_total' => abs($despesa->vl_valor_mov),
                        'forma_pagamento' => $despesa->st_forma_pag,
                        'id_centrocusto_superlogica' => $cc->idcentrocusto,
                        'nome_centrocusto' => $cc->st_descricao_cc,
                        'porcentagem_participacao' => $cc->vlparticipacao,
                        'valor_participacao' => abs($despesa->vl_valor_mov * ($cc->vlparticipacao / 100)),
                        'data_ordenacao' => Carbon\Carbon::createFromFormat('d/m/Y', $despesa->dt_ordenacao)->format('Y-m-d'),
                        'data_previsaocredito' => Carbon\Carbon::createFromFormat('d/m/Y', $despesa->dt_previsaocredito_mov)->format('Y-m-d'),
                        'observacoes' => $despesa->st_observacoes_mov,
                        'historico' => $despesa->st_historico_mov,
                        'label' => $despesa->st_label_mov,
                        'data_emissao' => Carbon\Carbon::createFromFormat('d/m/Y', $despesa->dt_emissao_doc)->format('Y-m-d'),
                    ];

                    $alterDespesa = \App\Models\Despesas::where('id_superlogica', $despesaCC['id_superlogica'])
                        ->where('id_centrocusto_superlogica', $despesaCC['id_centrocusto_superlogica'])->first();

                    if (!$alterDespesa) {
                        $alterDespesa = new \App\Models\Despesas();
                    }

                    $alterDespesa->fill($despesaCC);
                    $alterDespesa->save();
                }

                return ['status' => 200];
            } else {
                return ['status' => 500, 'message' => 'Despesa não encontrada'];
            }
        });
    }
}