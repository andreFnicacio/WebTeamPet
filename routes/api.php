<?php

use App\Helpers\API\Zenvia\Webhook\Adapter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('especialidades', 'EspecialidadesAPIController');

Route::post('superlogica/webhook/active', '\App\Http\Controllers\API\SuperLogicaWebhookAPIController@active');

//Route::resource('clientes', 'ClientesAPIController');

// Endpoints for Site sync
Route::post('/clientes', '\App\Http\Controllers\API\ClientesAPIController@store');
Route::post('/pets', '\App\Http\Controllers\API\PetsAPIController@store');
Route::get('/accredited/site', '\App\Http\Controllers\API\SiteAPIController@index');
// End site sync endpoints

Route::get('cpf/{cpf}', function($cpf) {
    $cpf = str_replace('-', '', $cpf);
    $cpf = str_replace('.', '', $cpf);
    $cpf = str_replace(' ', '', $cpf);
    return [
        'exists' => \App\Models\Clientes::where('cpf', $cpf)->exists()
    ];
});

Route::get('/sync/cobranca/{financial_id}/{status}', '\App\Http\Controllers\API\CobrancasController@sync');

Route::get('/sync/client/{financial_id}/subscription', '\App\Http\Controllers\API\CobrancasController@syncClientSubscription');

Route::get('/sync/client/{id}/document', '\App\Http\Controllers\API\CobrancasController@searchClientDocument');

Route::get('email/{email}', function($email) {
    return [
        'exists' => \App\User::where('email', $email)->exists()
    ];
});

Route::post('esquecisenha/enviaremail', function(Request $request) {
    $dados = $request->all();
    $email = $dados['email'];

    $user = \App\User::where('email', $email)->first();
    $exists = $user->exists();

    $credenciadoAtivo = true;

    if(\Modules\Clinics\Entities\Clinicas::where('id_usuario', $user->id)->exists()) {
        $credenciadoAtivo = \Modules\Clinics\Entities\Clinicas::where('id_usuario', $user->id)->where('ativo', 1)->exists();
    }

    if ($exists && $credenciadoAtivo) {
        $response = Password::sendResetLink(['email' => $email], function (Illuminate\Mail\Message $message) {
            $message->subject('Recuperação de Senha');
        });

        switch ($response) {
            case Password::RESET_LINK_SENT:
                return ['exists' => true];
            case Password::INVALID_USER:
                return ['exists' => false];
        }
    }
    return ['exists' => false];
});

Route::get('/searchables', function(Request $request) {
    return [
        [
            'name'  => 'clientes',
            'url'   => route('clientes.index'),
            'title' => 'Clientes',
            'icon'  => 'ion-android-contacts'
        ],
        [
            'name'  => 'especialidades',
            'url'   => route('especialidades.index'),
            'title' => 'Especialidades',
            'icon'  => 'ion-erlenmeyer-flask'
        ],
        [
            'name'  => 'pets',
            'url'   => route('pets.index'),
            'title' => 'Pets',
            'icon'  => 'ion-ios-paw'
        ],
        [
            'name'  => 'planos',
            'url'   => route('planos.index'),
            'title' => 'Planos',
            'icon'  => 'fa fa-book'
        ]
    ];
});


//Observação: Essa não é a rota para emissão de guias!
Route::get('pet/{microchip}', function($microchip) {
    return \App\Models\Pets::where('numero_microchip', $microchip)->get()->map(function(\App\Models\Pets $pet) {
        $publicPet = new \stdClass();
        $publicPet->id = $pet->id;
        $publicPet->nome_pet = $pet->nome_pet;
        $publicPet->id_cliente = $pet->id_cliente;
        $publicPet->nome_cliente = $pet->cliente()->first()->nome_cliente;
        $publicPet->ativo = $pet->ativo;
        $publicPet->statusPagamento = $pet->statusPagamento();
        $publicPet->bichos = $pet->isBichos();
        $publicPet->doencasPreExistentes = $pet->doencas_pre_existentes;
        $publicPet->id_plano = $pet->plano()->id;
        $publicPet->nome_plano = $pet->plano()->nome_plano;

        return $publicPet;
    });
});

Route::get('roles/user/{id}', function($id) {
    return \Illuminate\Support\Facades\DB::table('role_user')
        ->join('role', 'role_user.role_id', '=', 'role.id')
        ->where('role_user.user_id', $id)
        ->get();
});

Route::get('prestador/{crmv}', function($crmv) {
    return \Modules\Veterinaries\Entities\Prestadores::where('crmv', $crmv)->get()->map(function(\Modules\Veterinaries\Entities\Prestadores $p) {
        $public = new \stdClass();
        $public->id = $p->id;
        $public->nome = $p->nome;

        return $public;
    });
});

Route::get('buscaPrestador', function(Request $request) {
    return \Modules\Veterinaries\Entities\Prestadores::select(['prestadores.*', 'especialidades.nome as nome_especialidade'])
    ->where('crmv', $request->get('crmv'))
    ->where('crmv_uf', $request->get('crmv_uf'))
    ->leftJoin('especialidades', 'especialidades.id', 'prestadores.id_especialidade')
    ->first();
});

Route::get('freeinadbrasil', function(Request $request) {
    $statusPagEmDia = (new \App\Models\Clientes())::PAGAMENTO_EM_DIA;
    $clientes = (new \App\Models\PetsPlanos())->where('id_plano', 43)->get()->map(function ($pp) use ($statusPagEmDia) {
        $cliente = $pp->pet->cliente;
        $statusPagamento = $cliente->statusPagamento();
        if (in_array($cliente->estado, ['ES', 'ES Santo', 'Espírito Santo'])) {
            if ($statusPagamento == $statusPagEmDia) {
                return [
                    'id' => $cliente->id,
                    'nome' => $cliente->nome_cliente,
                    'email' => $cliente->email,
                    'celular' => $cliente->celular,
                    'estado' => $cliente->estado,
                ];
            }
        }
        return null;
    })->toArray();
    $clientes = array_filter($clientes, function($value) { return !is_null($value) && $value !== ''; });
    return $clientes;
});

Route::get('atualizarPrestadoresCrmv', function(Request $request) {
    $prestadores = (new \Modules\Veterinaries\Entities\Prestadores())->all();
    $crmv_att = [];
    foreach($prestadores as $p) {
        $cpf = $p->cpf;
        $cpf = str_replace('-', '', $cpf);
        $cpf = str_replace('.', '', $cpf);
        $cpf = str_replace(' ', '', $cpf);

        $crmv = $p->crmv;
        $crmv = str_replace('CRMV', '', $crmv);
        $crmv = str_replace(' ', '', $crmv);
        $crmv = str_replace('-', '', $crmv);
        $crmv = str_replace('/', '', $crmv);
        preg_match_all('!\d+!', $crmv, $crmv_numeros);
        $crmv_numeros = implode('', $crmv_numeros[0]);
        $crmv_uf = str_replace($crmv_numeros, '', $crmv);

        $p->crmv = $crmv_numeros;
        if ($crmv_uf != '' && $crmv_uf != 'VP' && $crmv_uf != 'VS') {
            $p->crmv_uf = $crmv_uf;
        }
        $p->cpf = $cpf;
        $p->nome = ucwords(mb_strtolower($p->nome));
        $p->update();

        $crmv_att[] = [
            'prestador' => $p->nome,
            'crmv' => $p->crmv,
            'crmv_uf' => $p->crmv_uf,
            'cpf' => $p->cpf,
        ];
    }

    return json_encode($crmv_att);
});


Route::get('cobrancas/{idCliente}', function($idCliente) {
    return \App\Models\Cobrancas::where('id_cliente', $idCliente)->orderBy('competencia', 'DESC')->get()->map(function(\App\Models\Cobrancas $j) {
        $public = new \stdClass();
        $public->id = $j->id;
        $public->competencia = $j->competencia;
        $public->data_vencimento = $j->data_vencimento;

        return $public;
    });
})->name('getCobrancas');


/**
 * Rotas de Webhook do Superlógica
 */
// \App\Helpers\API\Superlogica\Webhook\Adapter::routes();
Route::post('superlogica/cobranca/sincronizar', function(Request $request) {
    $input = $request->all();
    $cobranca = (new \App\Models\Cobrancas())->find($input['id_cobranca']);
    $curl = new \App\Helpers\API\Superlogica\Curl();
    $url = 'https://api.superlogica.net/v2/financeiro/cobranca?' . http_build_query([
            'exibirComposicaoDosBoletos' => '1',
            'id' => $input['old_superlogica_id']
        ]);

    $curl->getDefaults($url);
    $response = $curl->execute();
    $curl->close();

    if(is_array($response)) {
        $response = $response[0];
    }

    if ($cobranca->pagamentos()->count() === 0 && $response->dt_liquidacao_recb !== '') {
        foreach ($response->compo_recebimento as $pag) {
            (new \App\Models\Pagamentos())->create([
                'id_cobranca' => $cobranca->id,
                'data_pagamento' => (new \Carbon\Carbon())->createFromFormat('m/d/Y', $response->dt_liquidacao_recb),
                'complemento' => $pag->st_complemento_comp,
                'forma_pagamento' => $response->id_formapagamento_recb,
                'valor_pago' => $pag->st_valor_comp
            ]);
        }
    } elseif ($cobranca->pagamentos()->count() === 0 && $response->dt_cancelamento_recb !== '') {
        $cobranca->cancelada_em = new Carbon();
        $cobranca->justificativa = 'Cancelada e removida via sincronização manual';
        $cobranca->status = 0;
        $cobranca->update();
        $cobranca->delete();
    }

    return back();
})->name('superlogica.cobranca.sincronizar');

Route::post('superlogica/cobranca/invalidar', function(Request $request) {
    $token = Config::get('settings.superlogica.webhook.token');
    if($request->get('validationtoken') != $token) {
        return [
            'status' => 500,
            'message' => "Token inválido"
        ];
    }

    $adapted = \App\Helpers\API\Superlogica\Webhook\Adapter::adapt($request, 'cobrancas');

    if(!\App\Models\Cobrancas::where('old_superlogica_id', '=', $adapted['id_superlogica'])->exists()) {
        return ['status' => 500, 'message' => 'Cobrança não encontrada'];
    } else {
        $cobranca = \App\Models\Cobrancas::where('old_superlogica_id', '=', $adapted['id_superlogica'])->first();
        $cobranca->status = 0;
        $cobranca->deleted_at = new Carbon();
        $updated = $cobranca->update();
    }

    if($updated) {
        return ['status' => 200];
    }
})->name('superlogica.cobranca.invalidar');

Route::post('superlogica/cobranca/liquidar', function (Request $request) {
    $token = Config::get('settings.superlogica.webhook.token');;
    if ($request->get('validationtoken') != $token) {
        return [
            'status' => 500,
            'message' => "Token inválido"
        ];
    }

    $adapted = \App\Helpers\API\Superlogica\Webhook\Adapter::adapt($request, 'cobrancas');

    $cliente = \App\Models\Clientes::where('id_externo', $adapted['id_cliente_superlogica'])->first();
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
        'competencia' => $adapted['competencia']->format('Y-m'),
        'old_superlogica_id' => intval($adapted['id_superlogica']),
        'driver' => \App\Models\Cobrancas::DRIVER__SUPERLOGICA_V1
    ];

    if (!\App\Models\Cobrancas::where('old_superlogica_id', '=', $adapted['id_superlogica'])->exists()) {
        return ['status' => 500, 'message' => 'Cobrança não encontrada'];
    } else {
        $cobranca = \App\Models\Cobrancas::where('old_superlogica_id', '=', $adapted['id_superlogica'])->first();
        $cobranca->fill($insert);
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

Route::post('superlogica/cobranca/atualizar', function (Request $request) {
    $token = Config::get('settings.superlogica.webhook.token');
    if ($request->get('validationtoken') != $token) {
        return [
            'status' => 500,
            'message' => "Token inválido"
        ];
    }

    $adapted = \App\Helpers\API\Superlogica\Webhook\Adapter::adapt($request, 'cobrancas');

    $cliente = \App\Models\Clientes::where('id_externo', $adapted['id_cliente_superlogica'])->first();
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
        'old_superlogica_id' => intval($adapted['id_superlogica']),
        'driver' => \App\Models\Cobrancas::DRIVER__SUPERLOGICA_V1
    ];

    $cobranca = null;
    if (\App\Models\Cobrancas::where('old_superlogica_id', '=', $insert['old_superlogica_id'])->exists()) {
        $cobranca = \App\Models\Cobrancas::where('old_superlogica_id', '=', $insert['old_superlogica_id'])->first();
        $cobranca->fill($insert);
        $cobranca->update();
    }

    if ($cobranca) {
        return ['status' => 200];
    } else {
        return ['status' => 500, 'message' => 'Cobrança não encontrada'];
    }
});

Route::post('superlogica/cobranca/cadastrar', function (Request $request) {
    $token = Config::get('settings.superlogica.webhook.token');
    if ($request->get('validationtoken') != $token) {
        return [
            'status' => 500,
            'message' => "Token inválido"
        ];
    }

    $adapted = \App\Helpers\API\Superlogica\Webhook\Adapter::adapt($request, 'cobrancas');

    $cliente = \App\Models\Clientes::where('id_externo', $adapted['id_cliente_superlogica'])->first();
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
        'id_superlogica' => intval($adapted['id_superlogica']),
        'driver' => \App\Models\Cobrancas::DRIVER__SUPERLOGICA_V1
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
/**
 * Rotas de Webhook do  - Versão 2
 */
// \App\Helpers\API\Superlogica\Webhook\Adapter::routes();
Route::post('superlogica/v2/cobranca/sincronizar', function(Request $request) {
    $input = $request->all();
    $cobranca = (new \App\Models\Cobrancas())->find($input['id_cobranca']);
    $curl = new \App\Helpers\API\Superlogica\Curl();
    $url = 'https://api.superlogica.net/v2/financeiro/cobranca?' . http_build_query([
        'exibirComposicaoDosBoletos' => '1',
        'id' => $input['id_superlogica']
    ]);

    $curl->getDefaults($url);
    $response = $curl->execute();
    $curl->close();

    if(is_array($response)) {
        $response = $response[0];
    }

    if($cobranca->id_superlogica !== $response->id_recebimento_recb) {
        abort(422, 'O ID de sincronia da cobrança não confere. ID registrado localmente: ' . $cobranca->id_superlogica);
    }

    if ($cobranca->pagamentos()->count() === 0 && $response->dt_liquidacao_recb !== '') {
        foreach ($response->compo_recebimento as $pag) {
            (new \App\Models\Pagamentos())->create([
                'id_cobranca' => $cobranca->id,
                'data_pagamento' => (new \Carbon\Carbon())->createFromFormat('m/d/Y', $response->dt_liquidacao_recb),
                'complemento' => $pag->st_complemento_comp,
                'forma_pagamento' => $response->id_formapagamento_recb,
                'valor_pago' => $pag->st_valor_comp,
                'id_pagamento_superlogica' => $pag->id_composicao_comp
            ]);
        }
    } elseif ($cobranca->pagamentos()->count() === 0 && $response->dt_cancelamento_recb !== '') {
        $cobranca->cancelada_em = new Carbon();
        $cobranca->justificativa = 'Cancelada e removida via sincronização manual';
        $cobranca->status = 0;
        $cobranca->update();
        $cobranca->delete();
    }

    return back();
})->name('superlogica.v2.webhooks.cobranca.sincronizar');

Route::post('superlogica/v2/cobranca/invalidar', function(Request $request) {
    $token = env('SUPERLOGICA_WEBHOOK_VALIDATION_TOKEN');
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
        $cobranca->deleted_at = new Carbon();
        $updated = $cobranca->update();

        //Verificar primeiro pagamento:
        $data = $request->get('data');
        $primeiroPagamento = false;
        if(!empty($data->id_adesao_plc)) {
            $primeiroPagamento = true;

            //Verificar motivo
            if($data->fl_motivocancelar_recb == \App\Helpers\API\Superlogica\V2\Charge::INVALIDATE_REASON__SIGNATURE_ATIVATION ||
               $data->fl_motivocancelar_recb == \App\Helpers\API\Superlogica\V2\Charge::INVALIDATE_REASON__BONUS) {
                //Ativar assinatura
                foreach ($adapted['composicao'] as $composicao) {

                    //Find pet from complement
                    $composicaoRecebimento = explode("_", $composicao['complemento']);
                    if(isset($composicaoRecebimento[3])) {
                        $idPet = $composicaoRecebimento[3];
                        $pet = \App\Models\Pets::find($idPet);
                        if($pet) {
                            $pet->ativo = 1;
                            $pet->update();
                        }
                    }
                }
            }
        }
    }

    if($updated) {
        return ['status' => 200];
    }
})->name('superlogica.v2.webhooks.cobranca.invalidar');

Route::post('superlogica/v2/cobranca/liquidar', function (Request $request) {
    $token = env('SUPERLOGICA_WEBHOOK_VALIDATION_TOKEN');
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

        //Verificar primeiro pagamento:
        $data = $request->get('data');
        $primeiroPagamento = false;
        if(!empty($data->id_adesao_plc)) {
            $primeiroPagamento = true;
            $cliente->ativo = 1;
            $cliente->update();
        }

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

            //Find pet from complement
            $composicaoRecebimento = explode("_", $composicao['complemento']);
            if(isset($composicaoRecebimento[3])) {
                $idPet = $composicaoRecebimento[3];
                $pet = \App\Models\Pets::find($idPet);
                $pet->ativo = 1;
                $pet->update();
            }
        }
    }

    if ($cobranca) {
        return ['status' => 200];
    }
})->name('superlogica.v2.webhooks.cobranca.liquidar');

Route::post('superlogica/v2/cobranca/atualizar', function (Request $request) {
    $token = env('SUPERLOGICA_WEBHOOK_VALIDATION_TOKEN');
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
        'id_superlogica' => intval($adapted['id_superlogica']),
        'driver' => \App\Models\Cobrancas::DRIVER__SUPERLOGICA_V2
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
})->name('superlogica.v2.webhooks.cobranca.atualizar');

Route::post('superlogica/v2/cobranca/cadastrar', function (Request $request) {
    $token = env('SUPERLOGICA_WEBHOOK_VALIDATION_TOKEN');
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
        'id_superlogica' => intval($adapted['id_superlogica']),
        'driver' => \App\Models\Cobrancas::DRIVER__SUPERLOGICA_V2
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

Route::post('superlogica/v2/cobranca/reativar', function(Request $request) {
    $token = env('SUPERLOGICA_WEBHOOK_VALIDATION_TOKEN');
    if($request->get('validationtoken') != $token) {
        return [
            'status' => 500,
            'message' => "Token inválido"
        ];
    }

    $adapted = \App\Helpers\API\Superlogica\Webhook\Adapter::adapt($request, 'cobrancas');

    if(!\App\Models\Cobrancas::withTrashed()->where('id_superlogica', '=', $adapted['id_superlogica'])->exists()) {
        return ['status' => 500, 'message' => 'Cobrança não encontrada'];
    } else {
        $cobranca = \App\Models\Cobrancas::withTrashed()->where('id_superlogica', '=', $adapted['id_superlogica'])->first();
        $cobranca->status = 1;
        $cobranca->deleted_at = null;
        $updated = $cobranca->update();
    }

    if($updated) {
        return ['status' => 200];
    }
})->name('superlogica.v2.webhooks.cobranca.reativar');

/**
 * WEBHOOK SISTEMA NOVO
 */
Route::post('financeiro/cobranca/cadastrar', function (Request $request) {
    $token = Config::get('settings.superlogica.webhook.token');
    if ($request->get('validationtoken') != $token) {
        return [
            'status' => 500,
            'message' => "Token inválido"
        ];
    }

    if(!isset($request['ref_code'])){
        return [
            'status' => 500,
            'message' => 'Código de referência não informado'
        ];
    }

    $cliente = \App\Models\Clientes::where('id_externo', $request['ref_code'])
        ->where('ativo', 1)->first();
    if (!$cliente) {
        return [
            'status' => 500,
            'message' => 'Cliente não vinculado'
        ];
    }

    $insert = [
        'id_cliente' => $cliente->id,
        'complemento' => $request['complemento'] ?? NULL,
        'valor_original' => $request['valor'],
        'data_vencimento' => $request['dt_vencimento'],
        'status' => 1,
        'competencia' => $request['competencia'],
        'id_superlogica' => NULL,
        'hash_boleto' => $request['hash'] ?? NULL,
        'id_financeiro' => $request['id_financeiro'] ?? NULL,
        'driver' => \App\Models\Cobrancas::DRIVER__SF
    ];

    if(isset($insert['hash_boleto']) && !empty($insert['hash_boleto'])) {
        $checkExists = \App\Models\Cobrancas::where('hash_boleto', '=', $insert['hash_boleto'])->exists();
        $cobranca = \App\Models\Cobrancas::where('hash_boleto', '=', $insert['hash_boleto'])->first();
    }

    if(isset($request['id_financeiro']) && !empty($request['id_financeiro'])) {
        $checkExists = \App\Models\Cobrancas::where('id_financeiro', '=', $request['id_financeiro'])->exists();
        $cobranca = \App\Models\Cobrancas::where('id_financeiro', '=', $request['id_financeiro'])->first();
    }


    if (!$checkExists) {
        $cobranca = \App\Models\Cobrancas::create($insert);
    } else {
        //$cobranca = \App\Models\Cobrancas::where('hash_boleto', '=', $insert['hash_boleto'])->first();
        $cobranca->fill($insert);
        $cobranca->update();
    }

    if ($cobranca) {
        return ['status' => 200];
    }
})->middleware('throttle:500');

Route::post('financeiro/cobranca/cancelar', function (Request $request) {
    $token = Config::get('settings.superlogica.webhook.token');
    if ($request->get('validationtoken') != $token) {
        return [
            'status' => 500,
            'message' => "Token inválido"
        ];
    }

    $checkExists = false;

    if(!isset($request['id_financeiro'])){
        return [
            'status' => 500,
            'message' => "O id_financeiro deve ser informado"
        ];
    }

    if(isset($request['id_financeiro']) && !empty($request['id_financeiro'])) {
        $checkExists = \App\Models\Cobrancas::where('id_financeiro', '=', $request['id_financeiro'])->exists();
        $cobranca = \App\Models\Cobrancas::where('id_financeiro', '=', $request['id_financeiro'])->first();
    }

    if (!$checkExists) {
        return ['status' => 500, 'message' => 'Cobrança não encontrada'];
    } else {
        $cobranca->cancelada_em = new Carbon();
        $cobranca->justificativa = 'Cancelada e removida via sincronização com sistema financeiro';
        $cobranca->status = 0;
        $cobranca->update();
        $cobranca->delete();
    }

    if ($cobranca) {
        return ['status' => 200];
    }
})->middleware('throttle:500');

Route::post('financeiro/cobranca/liquidar', function (Request $request) {
    $token = Config::get('settings.superlogica.webhook.token');
    if ($request->get('validationtoken') != $token) {
        return [
            'status' => 500,
            'message' => "Token inválido"
        ];
    }

    if(isset($request['hash_boleto']) && !empty($request['hash_boleto'])) {
        $checkExists = \App\Models\Cobrancas::where('hash_boleto', '=', $request['hash_boleto'])->exists();
        $cobranca = \App\Models\Cobrancas::where('hash_boleto', '=', $request['hash_boleto'])->first();
    }

    if(isset($request['id_financeiro']) && !empty($request['id_financeiro'])) {
        $checkExists = \App\Models\Cobrancas::where('id_financeiro', '=', $request['id_financeiro'])->exists();
        $cobranca = \App\Models\Cobrancas::where('id_financeiro', '=', $request['id_financeiro'])->first();
    }

    if (!$checkExists) {
        return ['status' => 500, 'message' => 'Cobrança não encontrada'];
    } else {
        //Gerar pagamentos

        foreach ($request['itens'] as $item) {

            if (floatval($item['valor_pago']) < 0) {
                continue;
            }

            $pagamento = [
                'id_cobranca' => $cobranca->id,
                'data_pagamento' => $request['data_pagamento'],
                'valor_pago' => $item['valor_pago'],
                'complemento' => $item['complemento'],
                'forma_pagamento' => $request['forma_pagamento'],
                'id_financeiro' => $item['id_item']
            ];

            if (!\App\Models\Pagamentos::where('id_financeiro', $item['id_item'])->exists()) {
                $p = \App\Models\Pagamentos::create($pagamento);
            } else {
                $p = \App\Models\Pagamentos::where('id_financeiro', $item['id_item'])->first();
                $p->fill($pagamento);
                $p->update();
            }
        }
    }

    if ($cobranca) {
        return ['status' => 200];
    }
})->middleware('throttle:500');


Route::post('superlogica/despesa/alterada', function (Request $request) {
    $token = Config::get('settings.superlogica.webhook.token');
    if ($request->get('validationtoken') != $token) {
        return [
            'status' => 500,
            'message' => "Token inválido"
        ];
    }
    $data = $request->get('data');
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
                'data_ordenacao' => \Carbon\Carbon::createFromFormat('d/m/Y', $despesa->dt_ordenacao)->format('Y-m-d'),
                'data_previsaocredito' => \Carbon\Carbon::createFromFormat('d/m/Y', $despesa->dt_previsaocredito_mov)->format('Y-m-d'),
                'observacoes' => $despesa->st_observacoes_mov,
                'historico' => $despesa->st_historico_mov,
                'label' => $despesa->st_label_mov,
                'data_emissao' => \Carbon\Carbon::createFromFormat('d/m/Y', $despesa->dt_emissao_doc)->format('Y-m-d'),
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


/**
 * Rotas de Webhook do Zenvia
 */
// \App\Helpers\API\Zenvia\Webhook\Adapter::routes();
Route::post('/zenvia/respostaSms', function (Request $request) {
    $data = $request->callbackMoRequest;

    $sms = Sms::where('id', $data->id);
    if($sms) {
        $sms->response = $data->body;
        $sms->update();
    }

    if($sms->finalidade === "avaliacao_credenciado") {
        $zenvia = (new Adapter());
        //$zenvia::registrarAvaliacaoCredenciado($sms);
        // self::registrarAvaliacaoCredenciado($sms);
    }
})->name('zenvia.resposta');

Route::group(['middleware' => \Barryvdh\Cors\HandleCors::class], function() {
    Route::get('planos', function(Request $request) {
        $planos = \App\Models\Planos::where('ativo', 1)->get();

        $mapped = $planos->map(function($item) {
            $p = new \stdClass();

            $p->id = $item->id;
            $p->nome = explode(' ', $item->nome_plano)[0];
            if($item->display_name) {
                $p->nome = $item->display_name;
            }
            $p->individual = $item->preco_plano_individual;
            $p->familiar = $item->preco_plano_familiar;
            $p->preco_participativo = $item->preco_participativo;
            $p->coberturas = $item->imagem_carencias;
            $p->adesao = \App\Http\Controllers\EcommerceController::VALOR_ADESAO ?: 0;
            $p->adesao_participativo = \App\Http\Controllers\EcommerceController::VALOR_ADESAO_PARTICIPATIVO ?: 0;

//            if ($p->id == 37) {
//
//                    $plano = 37;
//                    $valor = 9.9;
//                    $qtd = \App\Models\PetsPlanos::where('pets_planos.created_at' , '>=', \Carbon\Carbon::parse('2019-07-11 18:30:00')->format('Y-m-d H:i'))
//                        ->join('pets', 'pets.id', '=', 'pets_planos.id_pet')
//                        ->where('pets_planos.id_plano', $plano)
//                        ->where('pets.regime', 'MENSAL')
//                        ->where('pets.valor', $valor)
//                        ->count();
//
//                    if ($qtd < 10) {
//                        $p->individual = 9.9;
//                        $p->adesao = 0;
//                    }
//
//            }

            return $p;
        });

        return $mapped;
    });


    Route::get('racas', function(Request $request) {
        $racas = (new \App\Models\Raca)->select(['id', 'nome']);
        $tipo = $request->get('tipo', null);
        if ($tipo) {
            $racas->whereIn('tipo', ["TODOS", $tipo]);
        }
        $racas->orderBy('tipo', 'DESC');
        return $racas->get();
    })->name('racas');

    Route::post('pre_cadastro', function(Request $request) {

        $input = $request->all();

        if (!isset($input['cpf'])) {
            return response()->json(["msg" => "CPF não informado!"], 404);
        }

        $input['cpf'] = str_replace('.', '', str_replace('-', '', $input['cpf']));
        $input['data_nascimento'] = (new Carbon())->createFromFormat('d/m/Y', $input['data_nascimento'])->format('Y-m-d');
        $existePreCadastro = (new \App\Models\PreCadastros())->where('cpf', $input['cpf'])->exists();
        if (!$existePreCadastro) {
            $pc = (new \App\Models\PreCadastros())->create($input);
        }
    });
});


//Cria um novo lead

Route::group([
    'middleware' => \Barryvdh\Cors\HandleCors::class,
    'prefix' => 'ecommerce'
], function() {
    Route::post('lead', function(Request $request) {
        $c = new \App\Http\Controllers\LeadsController();
        return $c->store($request);
    });
    Route::post('assinar', function(Request $request) {
        $c = new \App\Http\Controllers\EcommerceController();
        return $c->assinar($request);
    });
    Route::post('dados_adicionais', function(Request $request) {
        $c = new \App\Http\Controllers\EcommerceController();
        return $c->saveAdditionalData($request);
    });

    Route::get('getPlanosVendidos', function(Request $request) {

//        $plano = 35;
//        $valor = 100;
        $plano = 36;
        $valor = 9.9;

        $qtd = \App\Models\PetsPlanos::where('pets_planos.created_at' , '>=', \Carbon\Carbon::parse('2018-07-11 18:30:00')->format('Y-m-d H:i'))
            ->join('pets', 'pets.id', '=', 'pets_planos.id_pet')
            ->where('pets_planos.id_plano', $plano)
            ->where('pets.regime', 'MENSAL')
            ->where('pets.valor', $valor)
            ->count();

        return $qtd;
    })->name('ecommerce.getPlanosVendidos');

});
//
Route::group([
    'prefix' => 'planosProcedimentos'
], function() {
    Route::post('desvincular', function(Request $request) {
        return (new \App\Http\Controllers\PlanosProcedimentosAPIController())->removeVinculo($request);
    });
    Route::post('vincular', function(Request $request) {
        return (new \App\Http\Controllers\PlanosProcedimentosAPIController())->storeVinculo($request);
    });
    Route::get('findByProcedimento/{id}', function(Request $request, $id) {
        return (new \App\Http\Controllers\PlanosProcedimentosAPIController)->findByProcedimento($id);
    });
    Route::get('findByVinculo/{id_planos_procedimentos}', function(Request $request, $id) {
        return (new \App\Http\Controllers\PlanosProcedimentosAPIController)->findByVinculo($id);
    });
});

Route::group([
    'prefix' => 'homologacao'
], function() {
    Route::post('cron', function(Request $request) {
        $status = \App\Http\Util\Logger::log(\App\Http\Util\LogMessages::EVENTO['CRIACAO'], 'cron',
            \App\Http\Util\LogMessages::IMPORTANCIA['BAIXA'], "Os dados do dia " . (new \Carbon\Carbon())->format('d/m/Y') . " foram gravados com sucesso.");
    });
});

Route::group([
    'middleware' => \Barryvdh\Cors\HandleCors::class,
    'prefix' => 'credenciados'
], function() {

    Route::group([
        'prefix' => 'novo-site'
    ], function() {
        Route::get('/tipo', function() { 
            $types = \Modules\Clinics\Entities\Clinicas::all()
                ->pluck('tipo')
                ->unique()
                ->toArray();
                
            $types = array_map(function($type) {
                return ucwords(mb_convert_case(strtolower($type), MB_CASE_TITLE));
            }, $types);

            return array_values($types);
        });

        Route::get('/', function(Request $request) {
            $clinicas = \Modules\Clinics\Entities\Clinicas::where('ativo', 1)
                ->where('exibir_site', 1)
                ->whereNotNull('nome_site')
                ->whereNotNull('lat')
                ->whereNotNull('lng')
                ->get()->map(function(\Modules\Clinics\Entities\Clinicas $clinica) use ($request) {
                    if($request['plans']){
                        $atende = $clinica->checkPlanoCredenciado($request['plans']);
                        if ($atende === false) {
                            return null;
                        }
                    }

                    if($request['cities']) {
                        $cidade = strtoupper(\App\Helpers\Utils::remove_accents($clinica->cidade));
                        if($cidade != strtoupper(\App\Helpers\Utils::remove_accents($request['cities']))){
                            return null;
                        }
                    }

                    if($request['states']) {
                        $estado = strtoupper($clinica->estado);
                        if($estado != strtoupper($request['states'])){
                            return null;
                        }
                    }

                    if($request['types']) {
                        $tipo = strtoupper(\App\Helpers\Utils::remove_accents($clinica->tipo));
                        if($tipo != strtoupper(\App\Helpers\Utils::remove_accents($request['types']))){
                            return null;
                        }
                    }

                    $publicClinica = new \stdClass();
                    $publicClinica->id = $clinica->id;
                    $publicClinica->nome = $clinica->nome_site;
    
                    $publicClinica->endereco = ($clinica->rua ? $clinica->rua : '') .
                        ($clinica->numero_endereco ? ', ' . $clinica->numero_endereco : '') .
                        ($clinica->bairro ? ' - ' . $clinica->bairro : '') .
                        ($clinica->cidade ? ', ' . $clinica->cidade : '') .
                        ($clinica->estado ? ' - ' . $clinica->estado : '') .
                        ($clinica->cep ? ', ' . $clinica->cep : '');
                    $publicClinica->endereco = trim(trim(trim($publicClinica->endereco, ','), '-'));
    
                    $publicClinica->lat = $clinica->lat;
                    $publicClinica->lng = $clinica->lng;
    
                    $publicClinica->email_site = $clinica->email_site ? $clinica->email_site : '';
                    $publicClinica->telefone_site = '';
                    if ($clinica->telefone_site) {
                        $publicClinica->telefone_site = \App\Helpers\Utils::formataTelefone($clinica->telefone_site);
                    }
                    $publicClinica->celular_site = '';
                    if ($clinica->celular_site) {
                        $publicClinica->celular_site = \App\Helpers\Utils::formataTelefone($clinica->celular_site);
                    }
    
                    $publicClinica->atendimento_tags = $clinica->tagsSelecionadas()->with('tag')->get()->pluck('tag.nome')->toArray();
                    
                    $planos = (new \App\Models\PlanosCredenciados())
                        ->where('id_clinica', $clinica->id)
                        ->whereIn('id_plano', [74, 75, 76])
                        ->where('habilitado', 1)
                        ->with('plano')
                        ->get()
                        ->map(function ($planoCredenciado) {
                            $plano = $planoCredenciado->plano;
                            if ($plano) {
                                return $plano->display_nome ?: $plano->nome_plano;
                            }
                        })->toArray();  
                    $planos = array_filter($planos);
                    if (!$planos) {
                        return null;
                    }
                    $publicClinica->planos = $planos;
    
                    return $publicClinica;
                })->toArray();
            $clinicas = array_values(array_filter($clinicas));
            return $clinicas;
        });
    });

    Route::group([
        'prefix' => 'site'
    ], function() {
        Route::get('/buscarEstados', function(Request $request) {
            $estados = \Modules\Clinics\Entities\Clinicas::where('ativo', 1)
                ->where('exibir_site', 1)
                ->where('cidade', '!=', '')
                ->where('estado', '!=', '')
                ->groupBy('estado')
                ->orderBy('estado', 'ASC')
                ->get()->map(function(\Modules\Clinics\Entities\Clinicas $clinica) {
                    return $clinica['estado'];
                });
            return $estados;
        });
        Route::get('/{uf}/buscarCidades', function(Request $request, $uf) {
            $cidades = \Modules\Clinics\Entities\Clinicas::where('ativo', 1)
                ->where('exibir_site', 1)
                ->where('cidade', '!=', '')
                ->where('estado', $uf)
                ->groupBy('cidade')
                ->orderBy('cidade', 'ASC')
                ->get()->map(function(\Modules\Clinics\Entities\Clinicas $clinica) {
                    return ucwords(mb_convert_case(strtolower($clinica['cidade']), MB_CASE_TITLE));
                });
            return $cidades;
        });
        Route::get('/{uf}/{cidade}/buscarPlanos', function(Request $request, $uf, $cidade) {

            $planosAgrupados = [];
            $planos = [];

            $clinicas = \Modules\Clinics\Entities\Clinicas::where('ativo', 1)
                ->where('exibir_site', 1)
                ->where('estado', $uf)
                ->where('cidade', $cidade)
                ->get();

            foreach ($clinicas as $clinica) {
                $planosCredenciados = \App\Models\PlanosCredenciados::where('id_clinica', $clinica->id)->get();
                foreach ($planosCredenciados as $pc) {
                    if ($pc && $pc->habilitado) {
                        $plano = (new \App\Models\Planos())->find($pc->id_plano);
                        if ($plano) {

                            $nome = $plano->display_nome ?: $plano->nome_plano;
                            $nome = explode(' TAB', strtoupper($nome))[0];
                            $nome = str_replace('Plano ', '', $nome);
                            $nome = str_replace('PLANO ', '', $nome);
                            if (!isset($planos[$nome])) {
                                $planos[$nome] = [];
                            }
                            if (!in_array($plano->id, $planos[$nome])) {
                                $planos[$nome][] = $plano->id;
                            }

                        }
                    }
                }
            }
            return $planos;

            // $planos = \App\Models\Planos::where('nome_plano', '!=', '')
            //     ->orderBy('nome_plano', 'ASC')
            //     ->get();

            foreach ($planos as $plano) {
                $nome = $plano->display_nome ?: $plano->nome_plano;
                $nome = explode(' TAB', strtoupper($nome))[0];
                $nome = str_replace('Plano ', '', $nome);
                $nome = str_replace('PLANO ', '', $nome);
                if (!in_array($nome, $planosAgrupados)) {
                    $planosAgrupados[$nome][] = $plano->id;
                }
            }
            return $planosAgrupados;
        });
    });


    Route::get('/', function(Request $request) {
        $clinicas = \Modules\Clinics\Entities\Clinicas::where('ativo', 1)
            ->where('exibir_site', 1)
            ->whereNotNull('nome_site')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->get()->map(function(\Modules\Clinics\Entities\Clinicas $clinica) use ($request) {
                if($request['plano']){
                    $planos = explode(',', $request['plano']);
                    $atende = false;
                    foreach ($planos as $plano) {
                        if($clinica->checkPlanoCredenciado($plano)){
                            $atende = true;
                            break;
                        }
                    }
                    if ($atende == false) {
                        return null;
                    }
                }
                if($request['cidade']) {
                    $cidade = strtoupper(\App\Helpers\Utils::remove_accents($clinica->cidade));
                    if($cidade != strtoupper(\App\Helpers\Utils::remove_accents($request['cidade']))){
                        return null;
                    }
                }
                $publicClinica = new \stdClass();
                $publicClinica->id = $clinica->id;
                $publicClinica->nome = $clinica->nome_site;

                $publicClinica->endereco = ($clinica->rua ? $clinica->rua : '') .
                    ($clinica->numero_endereco ? ', ' . $clinica->numero_endereco : '') .
                    ($clinica->bairro ? ' - ' . $clinica->bairro : '') .
                    ($clinica->cidade ? ', ' . $clinica->cidade : '') .
                    ($clinica->estado ? ' - ' . $clinica->estado : '') .
                    ($clinica->cep ? ', ' . $clinica->cep : '');
                $publicClinica->endereco = trim(trim(trim($publicClinica->endereco, ','), '-'));

                $publicClinica->lat = $clinica->lat;
                $publicClinica->lng = $clinica->lng;

                $publicClinica->email_site = $clinica->email_site ? $clinica->email_site : '';
                $publicClinica->telefone_site = $clinica->telefone_site ? $clinica->telefone_site : '';
                $publicClinica->celular_site = $clinica->celular_site ? $clinica->celular_site : '';

                $publicClinica->atendimento_tags = $clinica->tagsSelecionadas()->with('tag')->get()->pluck('tag.nome')->toArray();
                
                $planos = (new \App\Models\PlanosCredenciados())->where('id_clinica', $clinica->id)->where('habilitado', 1)->get()->map(function ($clinica) {
                    $plano = (new \App\Models\Planos())->find($clinica->id_plano);
                    if ($plano) {
                        return [
                            'nome' => $plano->display_nome ?: $plano->nome_plano,
                            'img_carteirinha' => $plano->getImagemCarteirinha()
                        ];
                    }
                })->toArray();
                $planos = array_filter($planos);
                $planosAgrupados = [];
                foreach ($planos as $plano) {
                    $nome = $plano['nome'];
                    $nome = explode(' TAB', strtoupper($nome))[0];
                    $nome = str_replace('Plano ', '', $nome);
                    $nome = str_replace('PLANO ', '', $nome);
                    if (!in_array($nome, $planosAgrupados)) {
                        $planosAgrupados[] = $nome;
                        $publicClinica->planos[] = [
                            'nome' => $nome,
                            'img_carteirinha' => $plano['img_carteirinha']
                        ];
                    }
                }

                return $publicClinica;
            })->toArray();
        $clinicas = array_values(array_filter($clinicas));
        return json_encode($clinicas);
    });
    
    Route::get('/planos', function(Request $request) {
        $planos = \App\Models\Planos::where('nome_plano', '!=', '')
            ->orderBy('nome_plano', 'ASC')
            ->get();
        $planosAgrupados = [];
        foreach ($planos as $plano) {
            $nome = $plano->nome_plano;
            $nome = explode(' TAB', strtoupper($nome))[0];
            if (!in_array($nome, $planosAgrupados)) {
                $planosAgrupados[$nome][] = $plano->id;
            }
        }
        return $planosAgrupados;
    });
    Route::get('/cidades', function(Request $request) {
        $cidades = \Modules\Clinics\Entities\Clinicas::where('exibir_site', 1)
            ->where('cidade', '!=', '')
            ->groupBy('cidade')
            ->orderBy('cidade', 'ASC')
            ->get()->map(function(\Modules\Clinics\Entities\Clinicas $clinica) {
                return $clinica['cidade'];
            });
        return $cidades;
    });

    Route::post('/agendamentos/atribuir', function(Request $request) {
        $v = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'id_guia' => 'required',
            'id_credenciado' => 'exists:clinicas,id',
        ]);

        if ($v->fails()) {
            return abort(500, 'Parâmetros inválidos');
        }

        $guias = \Modules\Guides\Entities\HistoricoUso::where('id', $request->get('id_guia'))->get();
        foreach($guias as $g) {
            $g->id_clinica = $request->get('id_credenciado');
            $g->update();
        }

        return [
            'status' => 200,
            'message' => 'Atribuído com sucesso'
        ];
    });
    Route::post('/credenciamentoD4sign', function(Request $request) {
        $dadosCredenciado = $request->all();

        try{
            $client = new \App\Helpers\API\D4sign\Client();

            $name_document = "Contrato de Credenciamento - " . $dadosCredenciado['nome_credenciado'];
            $uuid_cofre = '41e142b6-d8ea-4a6d-8da0-98735e457367';
            $uuid_template = 'Mzk3NQ==';

            // Dev Test
//            $client->setUrl('http://demo.d4sign.com.br/api/');
//            $client->setAccessToken("live_f405f89b7dc3a39a178298b5f43d6d48f8d39c50b96938857d61eb471e3fb98b");
//            $client->setCryptKey("live_crypt_kjBysuSID9HBXX44EqUQC1pkjrZalee7");
//            $uuid_cofre = '09491703-8bba-4106-aa57-e430f3941925';
//            $uuid_template = 'Mzk0Ng==';
            // End Dev Test

            $dadosCredenciado['dia'] = \Carbon\Carbon::today()->format('d');
            $dadosCredenciado['mes'] = \App\Helpers\Utils::getMonthName(\Carbon\Carbon::today()->format('m'));
            $dadosCredenciado['nome_credenciado_topo'] = $dadosCredenciado['nome_credenciado'];

            $templates = array(
                $uuid_template => $dadosCredenciado
            );

            $document = $client->documents->makedocumentbytemplate($uuid_cofre, $name_document, $templates);

            $signers = array(
                array(
                    "email" => $dadosCredenciado['email_credenciado'],
                    "act" => '1',
                    "foreign" => '0',
                    "certificadoicpbr" => '0',
                    "assinatura_presencial" => '0',
                    "after_position" => '0'
                ),
                array(
                    "email" => $dadosCredenciado['email_testemunha'],
                    "act" => '5',
                    "foreign" => '0',
                    "certificadoicpbr" => '0',
                    "assinatura_presencial" => '0',
                    "after_position" => '1'
                ),
                array(
                    "email" => "juridico@lifepet.com.br",
                    "act" => '5',
                    "foreign" => '0',
                    "certificadoicpbr" => '0',
                    "assinatura_presencial" => '0',
                    "after_position" => '2'
                ),
                array(
                    "email" => "thiago@lifepet.com.br",
                    "act" => '3',
                    "foreign" => '0',
                    "certificadoicpbr" => '0',
                    "assinatura_presencial" => '0',
                    "after_position" => '3'
                ),
            );

            \Illuminate\Support\Facades\Mail::send('mail.credenciados.credenciamento', [
                'titulo' => 'Nova Solicitação de Credenciamento - São Paulo',
                'dadosCredenciamento' => [
                    'Nome do Credenciado' => $dadosCredenciado['nome_credenciado'],
                    'Email do Credenciado' => $dadosCredenciado['email_credenciado'],
                    'Telefone do Credenciado' => $dadosCredenciado['telefone_credenciado'],
                    'CPF/CNPJ do Credenciado' => $dadosCredenciado['cpf_cnpj_credenciado'],
                    'Endereco do Credenciado' => $dadosCredenciado['endereco_credenciado'],
                    'Bairro do Credenciado' => $dadosCredenciado['bairro_credenciado'],
                    'Cidade do Credenciado' => $dadosCredenciado['cidade_credenciado'],
                    'Cep do Credenciado' => $dadosCredenciado['cep_credenciado'],
                    'Nome do Responsável' => $dadosCredenciado['nome_responsavel'],
                    'CPF do Responsável' => $dadosCredenciado['cpf_responsavel'],
                    'Celular do Responsável' => $dadosCredenciado['celular_responsavel'],
                    'Nome da Testemunha' => $dadosCredenciado['nome_testemunha'],
                    'CPF da Testemunha' => $dadosCredenciado['cpf_testemunha'],
                    'Email da Testemunha' => $dadosCredenciado['email_testemunha'],
                    'Data da Solicitação' => \Carbon\Carbon::now()->format('d/m/Y H:i')
                ],
            ], function($message) {
                $message->to("ramon@lifepet.com.br")
                    ->cc("thiago@lifepet.com.br")
                    ->cc("alexandre@lifepet.com.br")
                    ->subject('Nova Solicitação de Credenciamento - São Paulo');
            });

            $client->documents->createList($document->uuid, $signers);
            $client->documents->sendToSigner($document->uuid, "Segue documento para assinatura.", 1, 0);

            return [
                'status' => true
            ];

        } catch (Exception $e) {
            return [
                'status' => false,
                'msg' => $e->getMessage()
            ];
        }
    });

    Route::post('/avaliacao', function(Request $request) {
        // $nota = $request->get('nota', 1);
        $numeroGuia = $request->get('numero_guia');
        $guia = \Modules\Guides\Entities\HistoricoUso::where('numero_guia', $numeroGuia)->first();
        $input = $request->all();

        $input['id_cliente'] = $guia->pet->cliente->id;
        $input['id_prestador'] = $guia->prestador->id;
        $input['id_clinica'] = $guia->clinica->id;
        $input['id_pet'] = $guia->pet->id;
        $input['publico'] = 1;

        if(!$guia) {
            abort(404);
        }

        (new \Modules\Veterinaries\Entities\AvaliacoesPrestadores())->create($input);

        // (new \App\Helpers\GamificationCredenciados($numeroGuia))->applyGameficationAvaliacaoCredenciado($nota);

        return view('clinicas.avaliacao.sucesso');
    })->name('credenciados.avaliacao.avaliar');

    Route::get('/avaliacao/sucesso', function() {
        return view('clinicas.avaliacao.sucesso');
    });
    Route::get('/avaliacao/{numeroGuia}', function(Request $request, $numeroGuia) {
        $hu = new \Modules\Guides\Entities\HistoricoUso();
        $guia = $hu::where('numero_guia', $numeroGuia)
                    // ->where('status', '!=', $hu::STATUS_RECUSADO)
                    ->first();

        if(!$guia) {
            abort(404);
        }

        $avaliacaoExists = (new \Modules\Veterinaries\Entities\AvaliacoesPrestadores())->where('numero_guia', $guia->numero_guia)->exists();

        $avaliacaoInvalida = false;
        if ($guia->status == $hu::STATUS_RECUSADO || $avaliacaoExists) {
            $avaliacaoInvalida = true;
        }

        return view('clinicas.avaliacao.avaliar', [
            'guia' => $guia,
            'avaliacaoInvalida' => $avaliacaoInvalida
        ]);
    })->name('credenciados.avaliacao.formulario');

    Route::get('/avaliacao/{numeroGuia}/email', function(Request $request, $numeroGuia) {
        return view('mail.credenciados.avaliacao', [
            'guia' => \Modules\Guides\Entities\HistoricoUso::where('numero_guia', $numeroGuia)->first()
        ]);
    });

});


Route::group([
    'middleware' => \Barryvdh\Cors\HandleCors::class,
    'prefix' => 'geral'
], function() {
    Route::get('/pets', function(Request $request) {
        if ($request->get('ativo')) {
            $pets = \App\Models\Pets::where('ativo', $request->get('ativo'))->get();
        } else {
            $pets = \App\Models\Pets::all();
        }

        $data = [];
        foreach($pets as $pet) {

            $cliente = $pet->cliente;
            $plano = $pet->plano();
            $raca = $pet->raca;

            $data[] = [
                'pet_id' => $pet->id,
                'pet_nome' => $pet->nome_pet,
                'pet_ativo' => $pet->ativo,
                'pet_valor' => $pet->valor,
                'pet_regime' => $pet->regime,
                'pet_familiar' => $pet->familiar,
                'pet_participativo' => $pet->participativo,
                'pet_mes_reajuste' => $pet->mes_reajuste,
                'pet_sexo' => $pet->sexo,
                'pet_data_nascimento' => $pet->data_nascimento->format('Y-m-d'),
                'pet_data_nascimento1' => $pet->data_nascimento->format('d/m/Y'),
                'pet_data_nascimento3' => $pet->data_nascimento->toDateTimeString(),
                'pet_data_nascimento4' => $pet->data_nascimento->toW3cString(),
                'pet_idade' => \Carbon\Carbon::parse($pet->data_nascimento)->age,
                'pet_tipo' => $pet->tipo,
                'raca_id' => $raca->id,
                'raca_nome' => $raca->nome,
                'plano_id' => $plano->id,
                'plano_nome' => $plano->nome_plano,
                'cliente_id' => $pet->id_cliente,
                'cliente_nome' => $cliente->nome_cliente,
                'cliente_cep' => $cliente->cep,
                'cliente_bairro' => $cliente->bairro,
                'cliente_cidade' => $cliente->cidade,
                'cliente_estado' => $cliente->estado,
                'cliente_sexo' => $cliente->sexo,
                'cliente_estado_civil' => $cliente->estado_civil,
            ];
        }

        return $data;
    });

    Route::get('/clientes', function(Request $request) {
        if ($request->get('ativo')) {
            $clientes = \App\Models\Clientes::where('ativo', $request->get('ativo'))->get();
        } else {
            $clientes = \App\Models\Clientes::all();
        }

        $data = [];
        foreach($clientes as $cliente) {

            $pets = $cliente->pets();

            $data[] = [
                'pets_quantidade' => $pets->count(),
                'cliente_id' => $cliente->id,
                'cliente_nome' => $cliente->nome_cliente,
                'cliente_ativo' => $cliente->ativo,
                'cliente_cep' => $cliente->cep,
                'cliente_bairro' => $cliente->bairro,
                'cliente_cidade' => $cliente->cidade,
                'cliente_estado' => $cliente->estado,
                'cliente_sexo' => $cliente->sexo,
                'cliente_estado_civil' => $cliente->estado_civil,
                'cliente_data_nascimento' => $cliente->data_nascimento->toIso8601ZuluString(),
                'cliente_data_nascimento1' => $cliente->data_nascimento->format('Y-m-d'),
                'cliente_data_nascimento2' => $cliente->data_nascimento->format('d/m/Y'),
                'cliente_data_nascimento3' => $cliente->data_nascimento->toDateTimeString(),
                'cliente_data_nascimento4' => $cliente->data_nascimento->toW3cString(),
                'cliente_idade' => \Carbon\Carbon::parse($cliente->data_nascimento)->age,
                'cliente_vencimento' => $cliente->vencimento,
                'cliente_status_pagamento' => strtoupper($cliente->status_pagamento),
            ];
        }

        return $data;
    });

    Route::get('/cliente/inadimplente', function(Request $request) {

        if (!$request->get('cpf')) {
            return ['response' => null];
        }

        $cpf = preg_replace( '/[^0-9]/', '', $request->get('cpf'));

        $cliente = \App\Models\Clientes::where('cpf', $cpf)->first();

        if (!$cliente || $cliente->status_pagamento !== \App\Models\Clientes::INADIMPLENTE_60_DIAS) {
            return response()->json(["response" => ""], 404);
        }

        return response()->json(["response" => true]);
    });

    Route::get('/vendas', function(Request $request) {
        $vendedoresPontuacao = \App\Models\VendedoresPontuacao::all();

        $data = [];
        foreach($vendedoresPontuacao as $vp) {

            $vendedor = $vp->vendedor;
            $venda = $vp->venda;
            $pet = $venda->pet;
            $cliente = $venda->cliente;
            $plano = $venda->plano;

            $data[] = [
                'vendedor_id' => $vendedor->id,
                'vendedor_nome' => $vendedor->nome,
                'pet_id' => $pet->id,
                'pet_nome' => $pet->nome_pet,
                'cliente_id' => $cliente->id,
                'cliente_nome' => $cliente->nome_cliente,
                'plano_id' => $plano->id,
                'plano_nome' => $plano->nome_plano,
                'venda_pontuacao' => $vp->pontuacao,
                'venda_adesao' => $venda->adesao,
                'venda_valor' => $venda->valor,
                'venda_comissao' => $venda->comissao,
                'venda_data_inicio_contrato' => \Carbon\Carbon::parse($venda->data_inicio_contrato)->toIso8601ZuluString(),
            ];
        }

        return $data;
    });

    Route::get('/planos', function(Request $request) {
        $planos = \App\Models\Planos::all();

        $data = [];
        foreach($planos as $plano) {

            $petsPlanos = $plano->petsPlanos();

            $qtdCaesAtivos = $plano->petsPlanos()->whereHas('pet', function ($query) {
                $query->where('ativo', 1);
                $query->where('tipo', 'CACHORRO');
            })->get()->count();

            $qtdGatosAtivos = $plano->petsPlanos()->whereHas('pet', function ($query) {
                $query->where('ativo', 1);
                $query->where('tipo', 'GATO');
            })->get()->count();

            $mediaDiasPermanencia = 0;
            $petsPlanosEncerrados = $plano->petsPlanos()
                ->whereYear('data_encerramento_contrato', '>', 2000)
                ->where('data_encerramento_contrato', '!=', null)
                ->where('data_encerramento_contrato', '!=', '')
                ->where('data_encerramento_contrato', '!=', '0000-00-00')
                ->whereYear('data_inicio_contrato', '>', 2000)
                ->where('data_inicio_contrato', '!=', null)
                ->where('data_inicio_contrato', '!=', '')
                ->where('data_inicio_contrato', '!=', '0000-00-00');
            if($petsPlanosEncerrados->count()) {
                foreach ($petsPlanosEncerrados->get() as $pp) {
                    $mediaDiasPermanencia += $pp->data_encerramento_contrato->diffInDays($pp->data_inicio_contrato);
                }
                $mediaDiasPermanencia /= $petsPlanosEncerrados->count();
            }

            $data[] = [
                'plano_id' => $plano->id,
                'plano_nome' => $plano->nome_plano,
                'qtd_total' => $petsPlanos->count(),
                'qtd_ativos' => $qtdCaesAtivos + $qtdGatosAtivos,
                'qtd_caes_ativos' => $qtdCaesAtivos,
                'qtd_gatos_ativos' => $qtdGatosAtivos,
                'mediaDiasPermanencia' => $mediaDiasPermanencia,
            ];
        }

        return $data;
    });

    Route::get('/pets-ltv', function(Request $request) {

        $ativo = $request->get('ativo') ?: 0;
        $pets = (new \App\Models\Pets)->where('ativo', $ativo)->get();

        foreach ($pets as $pet) {
            $ltv = $pet->getLTV($ativo);
            if ($ltv) {
                $data['pets'][] = [
                    'pet' => $pet->nome_pet,
                    'ltv' => $ltv,
                ];
            }
        }

        $total = array_sum(array_map(function($pet) {
            return $pet['ltv'];
        }, $data['pets']));
        $total /= count($data['pets']);

        $data['total'] = $total;
        return $data;
    });

    Route::get('/pets/lpt/ltv', function(Request $request) {
        $data = [
            'pets' => [],
        ];
        $ativo = $request->get('ativo', 0);
        $pets = (new \App\Models\Pets)
                    ->where('pets.ativo', $ativo)
                    ->LPT()
                    ->get();

        foreach ($pets as $pet) {
            $ltv = $pet->getLTV();

            if ($ltv) {
                $data['pets'][] = [
                    'pet' => $pet->nome_pet,
                    'ltv' => $ltv,
                ];
            }
        }

        if(count($data['pets']) == 0) {
            return [
                'total' => 0,
                'pets' => []
            ];
        }

        $total = array_sum(array_map(function($pet) {
            return $pet['ltv'];
        }, $data['pets']));
        $total /= count($data['pets']);

        $data['total'] = $total;
        return $data;
    });
});

/**
 * GeckoBoard Dashboards
 */
Route::group([
    'middleware' => \Barryvdh\Cors\HandleCors::class,
    'prefix' => 'geckoBoard'
], function() {

    Route::post('/atendimento/chamada_iniciada', function (Request $request) {
//        747816-b81da500-6923-0137-d418-022d23d9b2a0
//        $json = json_decode(file_get_contents('https://push.geckoboard.com/v1/send/747816-b81da500-6923-0137-d418-022d23d9b2a0'), true);
        var_dump($request->all());
        return "teste atende simples";
    });

    Route::group([
        'prefix' => 'auditoria'
    ], function() {

        Route::get('/guias_hoje', function (Request $request) {
            $start = \Carbon\Carbon::today()->startOfDay();
            $end = \Carbon\Carbon::today()->endOfDay();

            $guias = \Modules\Guides\Entities\HistoricoUso::where(function ($query) use ($start, $end) {
                $query->where(function ($query) use ($start, $end) {
                    $query->where('historico_uso.tipo_atendimento', "!=", \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->whereBetween('historico_uso.created_at', [$start, $end]);
                });
                $query->orWhere(function ($query) use ($start, $end) {
                    $query->where('historico_uso.tipo_atendimento', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->whereBetween('historico_uso.realizado_em', [$start, $end]);
                });
            })
                ->where('status', '=', \Modules\Guides\Entities\HistoricoUso::STATUS_LIBERADO)
                ->get()
                ->count();

            $data["item"][] = [
                "value" => $guias,
                "text" => "Guias emitidas hoje"
            ];
            return $data;
        });
        Route::get('/guias_mes', function (Request $request) {
            $start = \Carbon\Carbon::today()->startOfMonth();
            $end = \Carbon\Carbon::today()->endOfMonth();

            $guias = \Modules\Guides\Entities\HistoricoUso::where(function ($query) use ($start, $end) {
                $query->where(function ($query) use ($start, $end) {
                    $query->where('historico_uso.tipo_atendimento', "!=", \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->whereBetween('historico_uso.created_at', [$start, $end]);
                });
                $query->orWhere(function ($query) use ($start, $end) {
                    $query->where('historico_uso.tipo_atendimento', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->whereBetween('historico_uso.realizado_em', [$start, $end]);
                });
            })
                ->where('status', '=', \Modules\Guides\Entities\HistoricoUso::STATUS_LIBERADO)
                ->get()
                ->count();

            $data["item"][] = [
                "value" => $guias,
                "text" => "Guias emitidas no mês"
            ];
            return $data;
        });
        Route::get('/rentabilidade', function (Request $request) {
            $start = \Carbon\Carbon::today()->startOfMonth();
            $end = \Carbon\Carbon::today()->endOfMonth();

            $planos = \App\Models\Planos::all();
            foreach ($planos as $plano) {
                $recebimentos = $plano->recebimentos();
                $participado = \App\Models\Participacao::participadoPlano($plano->id, $start, $end);
                $sinistralidade = $plano->sinistralidade($start, $end);
                $ratio = 0;
                if ($sinistralidade && $recebimentos) {
                    $ratio = $sinistralidade / ($recebimentos + $participado);
                }

                $dados[] = [
                    'label' => $plano->nome_plano,
                    'value' => $ratio
                ];
            }

            $dados = collect($dados)->sortBy('value')->reverse()->toArray();
            foreach ($dados as $d) {
                $data["items"][] = [
                    'label' => $d['label'],
                    'value' =>  \App\Helpers\Utils::ratio($d['value'] * 100)
                ];
            }

            return $data;
        });
        Route::get('/planos_consumo', function (Request $request) {
            $start = \Carbon\Carbon::today()->startOfMonth();
            $end = \Carbon\Carbon::today()->endOfMonth();

            $total = \Modules\Guides\Entities\HistoricoUso::where(function ($query) use ($start, $end) {
                $query->where(function ($query) use ($start, $end) {
                    $query->where('historico_uso.tipo_atendimento', "!=", \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->whereBetween('historico_uso.created_at', [$start, $end]);
                });
                $query->orWhere(function ($query) use ($start, $end) {
                    $query->where('historico_uso.tipo_atendimento', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->whereBetween('historico_uso.realizado_em', [$start, $end]);
                });
            })
                ->where('status', '=', \Modules\Guides\Entities\HistoricoUso::STATUS_LIBERADO)
                ->get()
                ->sum('valor_momento');

            $guias = \Modules\Guides\Entities\HistoricoUso::selectRaw('pl.nome_plano, sum(historico_uso.valor_momento) as valor')
                ->join('planos as pl', 'historico_uso.id_plano', '=', 'pl.id')
                ->where(function ($query) use ($start, $end) {
                    $query->where(function ($query) use ($start, $end) {
                        $query->where('historico_uso.tipo_atendimento', "!=", \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                            ->whereBetween('historico_uso.created_at', [$start, $end]);
                    });
                    $query->orWhere(function ($query) use ($start, $end) {
                        $query->where('historico_uso.tipo_atendimento', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                            ->whereBetween('historico_uso.realizado_em', [$start, $end]);
                    });
                })
                ->where('status', '=', \Modules\Guides\Entities\HistoricoUso::STATUS_LIBERADO)
                ->groupBy('id_plano')
                ->orderByRaw('sum(historico_uso.valor_momento)', 'DESC')
                ->get()
                ->reverse();


            $data['y_axis'] = [
                'format' => 'percent',
            ];
            foreach ($guias as $guia) {

                $data['x_axis']['labels'][] = $guia->nome_plano;
                $data['series'][0]['data'][] = floatval(substr($guia->valor / $total, 0, 6));

            }
            return $data;

            $data = [];
            foreach ($usos as $u) {
                $data[] = [
                    'plano' => $u['nome_plano'],
                    'valor' => $u['valor'] / $total
                ];
            }

            return array_slice($data, 0, 10, true);

        });
        Route::get('/procedimentos', function (Request $request) {
            $start = \Carbon\Carbon::today()->startOfMonth();
            $end = \Carbon\Carbon::today()->endOfMonth();

            $guias = \Modules\Guides\Entities\HistoricoUso::selectRaw('procedimentos.nome_procedimento, count(historico_uso.id_procedimento) as valor')->
            where(function ($query) use ($start, $end) {
                $query->where(function ($query) use ($start, $end) {
                    $query->where('historico_uso.tipo_atendimento', "!=", \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->whereBetween('historico_uso.created_at', [$start, $end]);
                });
                $query->orWhere(function ($query) use ($start, $end) {
                    $query->where('historico_uso.tipo_atendimento', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->whereBetween('historico_uso.realizado_em', [$start, $end]);
                });
            })->
            join('procedimentos', 'historico_uso.id_procedimento', '=', 'procedimentos.id')->
            where('status', \Modules\Guides\Entities\HistoricoUso::STATUS_LIBERADO)->
            groupBy('id_procedimento')->
            orderByRaw('COUNT(id_procedimento) DESC')->
            limit(10)->
            get();

            $data['y_axis'] = [
                'format' => 'decimal',
            ];
            foreach ($guias as $guia) {

                $data['x_axis']['labels'][] = $guia->nome_procedimento;
                $data['series'][0]['data'][] = $guia->valor;

            }

            return $data;
        });
        Route::get('/sinistralidade_mensal', function (Request $request) {
            $start = \Carbon\Carbon::today()->startOfMonth();
            $end = \Carbon\Carbon::today()->endOfMonth();

            $sinistralidade = \Modules\Guides\Entities\HistoricoUso::whereBetween('created_at', [$start, $end])
                ->where('status', \Modules\Guides\Entities\HistoricoUso::STATUS_LIBERADO)
                ->sum('valor_momento');

            $faturamentoMensal = \App\Models\Pets::where('ativo', 1)
                ->where('regime', \App\Models\Pets::REGIME_MENSAL)
                ->sum('valor');

            $ratio = (($sinistralidade*100)/$faturamentoMensal);
            if($faturamentoMensal > 0) {
                $ratio = number_format($ratio, 2);
            }

            $data["item"][] = [
                "value" => $ratio,
                "text" => "Sinistralidade Mensal",
                "prefix" => "%"
            ];

            return $data;
        });
        Route::get('/sinistralidade_credenciada', function (Request $request) {
            $start = \Carbon\Carbon::today()->startOfMonth();
            $end = \Carbon\Carbon::today()->endOfMonth();

            $query = \Modules\Guides\Entities\HistoricoUso::whereBetween('historico_uso.created_at', [$start, $end])->where('status', \Modules\Guides\Entities\HistoricoUso::STATUS_LIBERADO);
            $total = $query->sum('valor_momento');
            if($total == 0) {
                return [];
            }

            $top = $query->groupBy('historico_uso.id_clinica')
                ->selectRaw('SUM(historico_uso.valor_momento) as valor, c.nome_clinica as nome')
                ->join('clinicas as c', 'historico_uso.id_clinica', '=', 'c.id')
                ->limit(5)
                ->orderByRaw('SUM(historico_uso.valor_momento) DESC')
                ->get();



            $data["items"] = $top->map(function($t) use ($total) {
                return [
                    'label' => $t['nome'],
                    'value' => \App\Helpers\Utils::ratio(($t['valor'] * 100) / $total)
                ];
            });

            return $data;

        });
    });

    Route::get('/financeiro/inadimplencia', function (Request $request) {
        $total_liquidado = (new \App\Models\DadosTemporais)->where('indicador', 'superlogica_total_liquidado')
            ->where('data_referencia', \Carbon\Carbon::today()->format('Y-m-d'))->first()['valor_numerico'];
        $total_faturado = (new \App\Models\DadosTemporais)->where('indicador', 'superlogica_total_faturado')
            ->where('data_referencia', \Carbon\Carbon::today()->format('Y-m-d'))->first()['valor_numerico'];
        $total_vencer = (new \App\Models\DadosTemporais)->where('indicador', 'superlogica_total_vencer')
            ->where('data_referencia', \Carbon\Carbon::today()->format('Y-m-d'))->first()['valor_numerico'];
        if ($total_liquidado && $total_faturado) {
            $valor = (1 - ($total_liquidado / ($total_faturado - $total_vencer))) * 100;
        } else {
            $valor = 0;
        }
        $data["item"][] = [
            "value" => $valor,
            "text" => "Inadimplência",
            "prefix" => "%"
        ];
        return $data;
    });

    Route::get('/superlogica/{indicador}', function (Request $request, $indicador) {
        // superlogica_total_faturado
        // superlogica_total_liquidado
        // superlogica_total_creditado
        // superlogica_total_vencer
        // superlogica_total_atrasado
        // superlogica_total_atrasado_acumulado
        $result = (new \App\Models\DadosTemporais)->where('indicador', 'superlogica_'.$indicador)
            ->where('data_referencia', \Carbon\Carbon::today()->format('Y-m-d'))->first();
        $valor = "Indefinido";
        if($result && $result['valor_numerico'] > 0) {
            $valor = $result['valor_numerico'];
        }

        $texto = ucwords(mb_strtolower(str_replace('_', ' ', $indicador)));

        $data["item"][] = [
            "value" => $valor,
            "text" => $texto,
            "prefix" => "R$"
        ];
        return $data;
    });

    Route::group([
        'prefix' => 'presidencia'
    ], function() {
        Route::get('/petsPlanos/{status}/{tempo}', function (Request $request, $status, $tempo) {
            if ($tempo == 'mes') {
                $start = \Carbon\Carbon::today()->startOfMonth();
                $end = \Carbon\Carbon::today()->endOfMonth();
            } else {
                $start = \Carbon\Carbon::today()->startOfDay();
                $end = \Carbon\Carbon::today()->endOfDay();
            }

            $statusText = [
                'P' => 'Vendas' . ($tempo == 'mes' ? ' do mês' : ' do dia'),
                'U' => 'Upgrades',
                'D' => 'Downgrades',
                'R' => 'Renovações'
            ];

            $petsPlanos = \App\Models\PetsPlanos::whereBetween('data_inicio_contrato', [$start, $end])->where('status', $status)->count();

            $data["item"][] = [
                "value" => $petsPlanos,
                "text" => $statusText[$status]
            ];
            return $data;
        });

        Route::get('/cancelamentos', function (Request $request) {
            $start = \Carbon\Carbon::today()->startOfMonth();
            $end = \Carbon\Carbon::today()->endOfMonth();
            $cancelamentos = \App\Models\PetsPlanos::whereBetween('data_encerramento_contrato',[$start, $end])
                ->join('pets', 'pets.id', '=', 'pets_planos.id_pet')
                ->where('pets.ativo', 0)
                ->count('pets_planos.id');

            $registro = \App\Models\DadosTemporais::where('indicador', 'vidas_ativas')->where('data_referencia', \Carbon\Carbon::today()->subMonth()->endOfMonth()->format('Y-m-d'))->first();
            $vidasAtivas = $registro ? $registro->valor_numerico : 0;

            $ratio = $vidasAtivas ? (($cancelamentos * 100) / $vidasAtivas) : 0;

            $data["item"][] = [
                "value" => $ratio,
                "text" => "Cancelamentos",
                "prefix" => "%"
            ];
            return $data;
        });

        Route::get('/vidasAtivas/{tipo}', function (Request $request, $tipo) {

            if ($tipo == 'serial') {
                $start = \Carbon\Carbon::today()->subDays(30);
                $end = \Carbon\Carbon::today();

                $response = [];
                for($i = 0; $i <= $start->diffInDays($end); $i++) {
                    $data_vidas = [];
                    $date = $end->copy()->subDays($i);
                    $data_vidas['nome'] = $date->format('Y-m-d');
                    $registro = \App\Models\DadosTemporais::where('indicador', 'vidas_ativas')->where('data_referencia', $date->format('Y-m-d'))->first();
                    $data_vidas['valor'] = $registro ? $registro->valor_numerico : 0;

                    if($date->isToday()) {
                        $data_vidas['valor'] = \App\Models\Pets::where('ativo', 1)->count('id');
                    }

                    $response[] = $data_vidas;
                }

                $data['x_axis']['type'] = 'datetime';
                $series = new stdClass();
                foreach ($response as $resp) {
                    $series->data[] = [$resp['nome'], $resp['valor']];
                }
                $series->name = "Vidas ativas (últimos 30 dias)";
                $series->incomplete_from = $end->format('Y-m-d');
                $data['series'] = [$series];
            } else {
                $vidas = \App\Models\Pets::where('ativo','1')->count('id');
                $data["item"][] = [
                    "value" => $vidas,
                    "text" => "Vidas Ativas"
                ];
            }

            return $data;
        });

    });


    Route::get('/vidasAtivas/{tipo}/{meses}', 'GeckoBoardController@vidasAtivas');
    Route::get('/novasVidas/{tipo}/{plan}', 'GeckoBoardController@novasVidas');
    Route::get('/novasVidasAmount', 'GeckoBoardController@novasVidasAmount');
    Route::get('/novasVidasSite', 'GeckoBoardController@novasVidasSite');
    Route::get('/novasVidasInsideSales', 'GeckoBoardController@novasVidasInsideSales');
    Route::get('/subscriptionsByPlan/{plan}/{isPaid}/{periodicity}/{active}', 'GeckoBoardController@subscriptionsByPlan');
    Route::get('/activeSubscriptionsCurrentMonth', 'GeckoBoardController@activeSubscriptionsCurrentMonth');
    Route::get('/subscriptionsAmount/{periodicity}', 'GeckoBoardController@subscriptionsAmount');
    Route::get('/novasVidasTotalMensal/{meses}', 'GeckoBoardController@novasVidasTotalMensal');
    Route::get('/novasVidasIntegralMensal/{meses}', 'GeckoBoardController@novasVidasIntegralMensal');
    Route::get('/novasVidasNaoPagantesMensal/{tipo}/{meses}', 'GeckoBoardController@novasVidasNaoPagantesMensal');
    Route::get('/novasVidasPagantesMensal/{tipo}/{meses}', 'GeckoBoardController@novasVidasPagantesMensal');
    Route::get('/cancelamentos/{tipo}', 'GeckoBoardController@cancelamentos');
    Route::get('/receitaTotal/{tipo}/{meses}', 'GeckoBoardController@receitaTotal');
    Route::get('/receitaCoparticipacao/{tipo}/{meses}', 'GeckoBoardController@receitaCoparticipacao');
    Route::get('/receitaRecorrencia/{tipo}/{meses}', 'GeckoBoardController@receitaRecorrencia');
    Route::get('/receitaRecorrenciaParticipativos/{tipo}/{meses}', 'GeckoBoardController@receitaRecorrenciaParticipativos');
    Route::get('/receitaTotalParticipativos/{tipo}/{meses}', 'GeckoBoardController@receitaTotalParticipativos');
    Route::get('/cancelamentosParticipativos/{tipo}', 'GeckoBoardController@cancelamentosParticipativos');
    Route::get('/vidasAtivasParticipativos/{tipo}/{meses}', 'GeckoBoardController@vidasAtivasParticipativos');
    Route::get('/vidasAtivasIntegral/{meses}', 'GeckoBoardController@vidasAtivasIntegral');
    Route::get('/vidasAtivasTotal/{meses}', 'GeckoBoardController@vidasAtivasTotal');
    Route::get('/cancelamentosParticipativosMensal/{tipo}/{meses}', 'GeckoBoardController@cancelamentosParticipativosMensal');
    Route::get('/cancelamentosIntegralMensal/{tipo}/{meses}', 'GeckoBoardController@cancelamentosIntegralMensal');
    Route::get('/churnParticipativosPeriodo/{meses}', 'GeckoBoardController@churnParticipativosPeriodo');
    Route::get('/petsPagantesParticipativos/{tipo}/{meses}', 'GeckoBoardController@petsPagantesParticipativos');
    Route::get('/petsNaoPagantesParticipativos/{tipo}/{meses}', 'GeckoBoardController@petsNaoPagantesParticipativos');
    Route::get('/migracoesMensal/{meses}', 'GeckoBoardController@migracoesMensal');
    Route::get('/migracoesPagantesMensal/{meses}', 'GeckoBoardController@migracoesPagantesMensal');
    Route::get('/migracoesNaoPagantesMensal/{meses}', 'GeckoBoardController@migracoesNaoPagantesMensal');

});

/**
 * PowerBI Dashboards
 */
Route::group([
    'middleware' => \Barryvdh\Cors\HandleCors::class,
    'prefix' => 'powerBI'
], function() {

    /**
     * Rentabilidade do mês
     */
    Route::get('/auditoria/rentabilidade', function (Request $request) {
        $start = \Carbon\Carbon::today()->startOfMonth();
        $end = \Carbon\Carbon::today()->endOfMonth();

        $planos = \App\Models\Planos::all();
        foreach ($planos as $plano) {
            $recebimentos = $plano->recebimentos();
            $participado = \App\Models\Participacao::participadoPlano($plano->id, $start, $end);
            $sinistralidade = $plano->sinistralidade($start, $end);
            $ratio = 0;
            if ($sinistralidade && $recebimentos) {
                $ratio = $sinistralidade / ($recebimentos + $participado);
            }

            $rentabilidade[] = [
                'plano' => $plano->nome_plano,
                'rentabilidade' => $ratio * 100
            ];
        }
        $data = $rentabilidade;
        return $data;
    });

    /**
     * Top 10 Planos com maior consumo do mês
     */
    Route::get('/auditoria/planos_consumo', function (Request $request) {
        $start = \Carbon\Carbon::today()->startOfMonth();
        $end = \Carbon\Carbon::today()->endOfMonth();

        $total = \Modules\Guides\Entities\HistoricoUso::where(function ($query) use ($start, $end) {
            $query->where(function ($query) use ($start, $end) {
                $query->where('historico_uso.tipo_atendimento', "!=", \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                    ->whereBetween('historico_uso.created_at', [$start, $end]);
            });
            $query->orWhere(function ($query) use ($start, $end) {
                $query->where('historico_uso.tipo_atendimento', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                    ->whereBetween('historico_uso.realizado_em', [$start, $end]);
            });
        })
            ->where('status', '=', \Modules\Guides\Entities\HistoricoUso::STATUS_LIBERADO)
            ->get()
            ->sum('valor_momento');

        $usos = \Modules\Guides\Entities\HistoricoUso::selectRaw('pl.nome_plano, sum(historico_uso.valor_momento) as valor')
            ->join('planos as pl', 'historico_uso.id_plano', '=', 'pl.id')
            ->where(function ($query) use ($start, $end) {
                $query->where(function ($query) use ($start, $end) {
                    $query->where('historico_uso.tipo_atendimento', "!=", \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->whereBetween('historico_uso.created_at', [$start, $end]);
                });
                $query->orWhere(function ($query) use ($start, $end) {
                    $query->where('historico_uso.tipo_atendimento', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->whereBetween('historico_uso.realizado_em', [$start, $end]);
                });
            })
            ->where('status', '=', \Modules\Guides\Entities\HistoricoUso::STATUS_LIBERADO)
            ->groupBy('id_plano')
            ->orderByRaw('sum(historico_uso.valor_momento)', 'DESC')
            ->get()
            ->reverse()
            ->toArray();

        $data = [];
        foreach ($usos as $u) {
            $data[] = [
                'plano' => $u['nome_plano'],
                'valor' => $u['valor'] / $total
            ];
        }

        return array_slice($data, 0, 10, true);

    });

    /**
     * Guias de Hoje
     */
    Route::get('/auditoria/guias_hoje', function (Request $request) {
        $start = \Carbon\Carbon::today()->startOfDay();
        $end = \Carbon\Carbon::today()->endOfDay();

        $guias = \Modules\Guides\Entities\HistoricoUso::where(function ($query) use ($start, $end) {
            $query->where(function ($query) use ($start, $end) {
                $query->where('historico_uso.tipo_atendimento', "!=", \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                    ->whereBetween('historico_uso.created_at', [$start, $end]);
            });
            $query->orWhere(function ($query) use ($start, $end) {
                $query->where('historico_uso.tipo_atendimento', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                    ->whereBetween('historico_uso.realizado_em', [$start, $end]);
            });
        })
            ->where('status', '=', \Modules\Guides\Entities\HistoricoUso::STATUS_LIBERADO)
            ->get()
            ->toArray();

        return $guias;
    });

    /**
     * Top 10 Procedimentos mais requisitados
     */
    Route::get('/auditoria/procedimentos', function (Request $request) {
        $start = \Carbon\Carbon::today()->startOfMonth();
        $end = \Carbon\Carbon::today()->endOfMonth();

        $data = \Modules\Guides\Entities\HistoricoUso::selectRaw('procedimentos.nome_procedimento, count(historico_uso.id_procedimento) as valor')->
        where(function ($query) use ($start, $end) {
            $query->where(function ($query) use ($start, $end) {
                $query->where('historico_uso.tipo_atendimento', "!=", \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                    ->whereBetween('historico_uso.created_at', [$start, $end]);
            });
            $query->orWhere(function ($query) use ($start, $end) {
                $query->where('historico_uso.tipo_atendimento', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                    ->whereBetween('historico_uso.realizado_em', [$start, $end]);
            });
        })->
        join('procedimentos', 'historico_uso.id_procedimento', '=', 'procedimentos.id')->
        where('status', \Modules\Guides\Entities\HistoricoUso::STATUS_LIBERADO)->
        groupBy('id_procedimento')->
        orderByRaw('COUNT(id_procedimento) DESC')->
        limit(10)->
        get()->
        toArray();

        return $data;
    });

    /**
     * Sinistralidade Mensal
     */
    Route::get('/auditoria/sinistralidadeMensal', function (Request $request) {
        $start = \Carbon\Carbon::today()->startOfMonth();
        $end = \Carbon\Carbon::today()->endOfMonth();

        $sinistralidade = \Modules\Guides\Entities\HistoricoUso::whereBetween('created_at', [$start, $end])
            ->where('status', \Modules\Guides\Entities\HistoricoUso::STATUS_LIBERADO)
            ->sum('valor_momento');

        $faturamentoMensal = \App\Models\Pets::where('ativo', 1)
            ->where('regime', \App\Models\Pets::REGIME_MENSAL)
            ->sum('valor');

        $ratio = (($sinistralidade*100)/$faturamentoMensal);
        if($faturamentoMensal > 0) {
            $ratio = number_format($ratio, 2);
        }

        $data = [
            [
                'valor' => \App\Helpers\Utils::ratio($ratio)
            ]
        ];

        return $data;
    });
});

Route::get('/send_mass_push', function (Request $request) {
    $data = $request->all();
    $limit = 20;
    $page = ($data['page'] - 1) * $limit;
    if ($data['pass'] == '1901') {

        $pushs = \App\Models\Clientes::orderBy('id', 'asc')
            ->where('farmapet', 0)
            ->where('ativo', 1)
            ->whereNotNull('token_firebase')
            // ->whereRaw('nome_cliente LIKE "%Ramon Penna%"')
            ->skip($page)
            ->take($limit)
            ->get()
            ->map(function ($cliente) {
                dump($cliente->nome_cliente);
                $pushNotification = (new \Modules\Mobile\Services\PushNotificationService(
                    $cliente,
                    "Com a Farma Pet...",
                    "Você tem R$ 500 de crédito em medicamentos manipulados para o seu pet pagando apenas R$ 100/ano. Comece a economizar agora. Acesse o app e assine! " . "\u{1F63B} \u{1F43E}",
                    []
                ));
                $push = $pushNotification->send();
                dump($push);
            });

        $data = $pushs;

    } else {
        return response()->json(["msg" => "Forbidden!"], 403);
    }
    return response()->json(["msg" => "Notificações enviadas!"], 200);
});

Route::get('/relatorio_reajustes', function (Request $request) {
    $pets = \App\Models\Pets::whereHas('petsPlanos')->get();
    dd($pets->count());
    return $pets;
});

Route::group([
    'middleware' => [\Barryvdh\Cors\HandleCors::class, 'allow-cookies-sessions']
    
], function() {
    Route::post('nps', 'NpsAPIController@store');
    Route::post('public-nps', 'NpsAPIController@publicStore');
    Route::get('nps', 'NpsAPIController@getNps');
    Route::get('nps/token', 'NpsAPIController@getToken');
    Route::get('nps/verificar-cliente', 'NpsAPIController@verificarCliente');
});

Route::group([
    'middleware' => \Barryvdh\Cors\HandleCors::class,
    'prefix' => 'dados'
], function() {

    Route::group(['prefix' => 'stat'], function () {
        Route::get('/credenciados', 'DashboardsAPIController@statCredenciados');
        Route::get('/vidas-ativas', 'DashboardsAPIController@statVidasAtivas');
        Route::get('/novas-vidas', 'DashboardsAPIController@statNovasVidas');
        Route::get('/cancelamentos', 'DashboardsAPIController@statCancelamentos');
        Route::get('/qtd-glosas', 'DashboardsAPIController@statQtdGlosas');
        Route::get('/valor-glosas', 'DashboardsAPIController@statValorGlosas');
        Route::get('/upgrades', 'DashboardsAPIController@statUpgrades');

        Route::group(['prefix' => 'superlogica'], function () {
            Route::get('/metricas', 'DashboardsAPIController@superlogicaMetricas');
            Route::get('/ltv', 'DashboardsAPIController@superlogicaLtv');
            Route::get('/churn', 'DashboardsAPIController@superlogicaChurn');
            Route::get('/churn-valor', 'DashboardsAPIController@superlogicaChurnValor');
            Route::get('/ticket', 'DashboardsAPIController@superlogicaTicket');
            Route::get('/inadimplencia', 'DashboardsAPIController@superlogicaInadimplencia');
        });
    });

    Route::group(['prefix' => 'line'], function () {
        Route::get('/vidas-ativas', 'DashboardsAPIController@lineVidasAtivas');
        Route::get('/novas-vidas', 'DashboardsAPIController@lineNovasVidas');
        Route::get('/cancelamentos', 'DashboardsAPIController@lineCancelamentos');
        Route::get('/sinistralidade/planos', 'DashboardsAPIController@lineSinistralidadePlanos');
        Route::get('/sinistralidade/credenciados', 'DashboardsAPIController@lineSinistralidadeCredenciados');
        Route::get('/sinistralidade/procedimentos', 'DashboardsAPIController@lineSinistralidadeProcedimentos');
    });

  

    Route::group(['prefix' => 'gauge'], function () {
        Route::get('/nps', 'DashboardsAPIController@gaugeNps');
    });

    Route::group(['prefix' => 'multi-line'], function () {
        Route::get('/vidas-cancelamentos', 'DashboardsAPIController@multilineVidasCancelamentos');
    });

    // [X][line ] - Novas vidas (crescimento)
    // [x][valor] - Sinistralidade (Produto, Rede, Procedimento)
    // [X][valor] - Churn-rate (vida e $)                                   superlogica/metricas->desativadas e churn mrr
    // [X][valor] - LTV (Lifetime Value)                                    superlogica/metricas->ltv
    // [X][valor] - Ticket Médio                                            superlogica/metricas->ticket
    // [X][valor] - Vidas
    // [X][valor] - Qtt de Glosas (Valor glosado)
    // [X][valor] - Rede c/ terceiros
    // [X][valor] - Rede própria (Lucro bruto/líquido)
    // [X][valor] - UpGrade
    // [X][valor] - Inadimplência                                           superlogica/metricas->
    // [-][valor] - [Indicador] NPS (Avisar Alexandre quando terminar)

});


Route::group([
    'middleware' => \Barryvdh\Cors\HandleCors::class,
    'prefix' => 'indicadores'
], function() {
    Route::get('/vidas-ativas', 'IndicadoresAPIController@vidasAtivas');
    
    Route::group(
        ['prefix' => 'nps'], 
        function() {
            Route::get('acumulado-diario', 'IndicadoresAPIController@npsAcumuladoDia');
            Route::get('acumulado-mensal', 'IndicadoresAPIController@npsAcumuladoMes');
            Route::get('mensal', 'IndicadoresAPIController@npsMes');
    });

    Route::group(
        ['prefix' => 'churn-rate'],
        function() {
            Route::get('mensal', 'IndicadoresAPIController@churnRateMensal');
        }
    );

    Route::group(
        ['prefix' => 'upgrade'],
        function() {
            Route::get('mensal', 'IndicadoresAPIController@upgradeMensal');
        }
    );
});

Route::group([
    'prefix' => 'cron/rd_station'
], function() {
    Route::get('/pet_aniversario', 'RDStationAPIController@enviarEmailPetsAniversariantes');
});

Route::group(['prefix' => 'telemedicina', 'middleware' => []], function() {
    Route::get('/cliente/{cpf}', function(Request $request, $cpf) {

        $cpf = preg_replace( '/[^0-9]/', '', $cpf);
        $c = \App\Models\Clientes::where('cpf', $cpf)->where('ativo', 1)->first();

        return [
            'status' => $c ? $c->statusPagamento() === \App\Models\Clientes::PAGAMENTO_EM_DIA : false,
            'exists' => !is_null($c)
        ];
    });
});

Route::group(['prefix' => 'assinaturas', 'middleware' => []], function () {
    Route::get('/cliente/{cpf}', function(Request $request, $cpf) {
        header('Access-Control-Allow-Origin: https://lifepet.com.br');
        $cpf = preg_replace( '/[^0-9]/', '', $cpf);
        $exists = \App\Models\Clientes::where('cpf', $cpf)->exists();
        return [
            'exists' => $exists
        ];
    });

    Route::get('/concluir/{hash}', function(Request $request, $hash) {
       $compraRapida = \App\LifepetCompraRapida::where('hash', $hash)->
                                                 where('concluido', 0)->
                                                 where('pagamento_confirmado', 1)->
                                                 orderBy('created_at', 'DESC')->first();
       if(!$compraRapida) {
           return view('assinaturas.oops');
       }

       $racas = \App\Models\Raca::all();

       return view('assinaturas.concluir', ['compraRapida' => $compraRapida, 'racas' => $racas]);
    })->name('assinaturas.concluir');

    Route::post('/concluir/{hash}', function(Request $request, $hash) {
        $controller = new \App\Http\Controllers\API\AssinaturasAPIController();

        return $controller->sign($request, $hash);
    })->name('assinaturas.salvar');

    Route::get('/finalizado', function() {
        return view('assinaturas.finalizado');
    });

//    Route::post('/reprocessar', function(Request $request) {
//        $financeiro = new \App\Helpers\API\Financeiro\Financeiro();
//        $logger = new \App\Http\Util\Logger('ecommerce');
//
//        $eligible = \App\LifepetCompraRapida::where('concluido', 1)->where('pagamento_confirmado', 0)->get();
//
//        foreach($eligible as $e) {
//            if(!\App\LifepetCompraRapida::where('cpf', $e->cpf)->where('pagamento_confirmado',1)->exists()) {
//               $customer = $financeiro->customer($e->cpf);
//               $form = [
//                   'name' => $e->nome,
//                   'email' => $e->email,
//                   'address[0][zipcode]' => $e->cep,
//                   'address[0][address1]' =>  $e->rua,
//                   'address[0][number]' => $e->numero,
//                   'address[0][address2]' => $e->bairro,
//                   'address[0][city]' =>  $e->cidade,
//                   'address[0][country]' => 'Brasil',
//                   'address[0][state]' =>  $e->estado,
//                   'payment_type' => 'creditcard',
//                   'cpf_cnpj' => $e->cpf,
//                   'status' => 'A',
//                   'financial_status' => 1,
//               ];
//               try {
//                   $financeiro->post("customer/{$customer->id}", $form);
//               } catch (Exception $e) {
//                   $logger->register(\App\Http\Util\LogEvent::NOTICE, \App\Http\Util\LogPriority::MEDIUM,
//                       "Não foi possível atualizar os dados do cliente no SF na tentativa de reprocessamento de cobranças do Lifepet Compra Rápida."
//                   );
//               }
//            }
//        }
//    });

    Route::post('/assinar', function(Request $request) {
        $origin = request()->headers->get('origin');
        $allowed = [
            'http://lifepet.com.br',
            'https://lifepet.com.br',
            'http://app.lifepet.com.br',
            'https://app.lifepet.com.br',
            'http://manager.lifepet'
        ];
        if(is_null($origin) || !in_array($origin, $allowed)) {
            return abort(403, 'Não autorizado.');
        }

        header('Access-Control-Allow-Origin: https://lifepet.com.br');
        /**
         * ETAPA DE INICIALIZAÇÃO E OBTENÇÃO/ORGANIZAÇÃO DE DADOS
         */
        $tags = [];
        $finance = new \App\Helpers\API\Financeiro\Financeiro();
        $logger = new \App\Http\Util\Logger('ecommerce');

        $v = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'id_plano'      => 'required',
            'name'          => 'required',
            'email'         => 'required|email',
            'celular'       => 'required',
            'cpf'           => 'required',
            'country'       => 'required',
            'state'         => 'required',
            'city'          => 'required',
            'neighbourhood' => 'required',
            'street'        => 'required',
            'brand'         => 'sometimes|required',
            'card_number'   => 'sometimes|required',
            'expires_in'    => 'sometimes|required',
            'ccv'           => 'sometimes|required',
            'regime'        => 'required',
            'recaptcha'     => 'required'
        ]);

        $input = $request->all();
        $input['origin'] = $origin;

        $dadosCliente = \App\Http\Controllers\API\AssinaturasAPIController::formatClientDataToLog($input);

        if ($v->fails()) {
            $messages = join("\n", $v->getMessageBag()->all());
            $logger->register(\App\Http\Util\LogEvent::NOTICE, \App\Http\Util\LogPriority::MEDIUM,
                "Dados requeridos estão incompletos. Verifique o preenchiemento do formulário. Detalhes: $messages \nOs dados recebidos foram: \n{$dadosCliente}"
            );

            return [
                'erro' => 'Muitas tentativas consecutivas',
                'message' => "Dados requeridos estão incompletos. Verifique o preenchiemento do formulário. Detalhes: $messages",
                'status' => false
            ];
        }

        if(!\App\Http\Controllers\LifepetParaTodosController::googleCaptcha($request->get('recaptcha'))) {
            $dadosRequisicao = json_encode([
                '_REQUEST' => \App\Http\Controllers\API\AssinaturasAPIController::formatClientDataToLog($_REQUEST),
                '_SERVER'  => \App\Helpers\Utils::getServerInfoToLog($_SERVER)
            ]);

            $logger->register(\App\Http\Util\LogEvent::WARNING, \App\Http\Util\LogPriority::MEDIUM,
                "Uma tentativa de compra foi barrada após ser identificada como ação fraudulenta. Dados de requisição: \n{$dadosRequisicao}"
            );

            return [
                'erro' => 'Erro.',
                'message' => "Não foi possível garantir a autenticidade da requisição.",
                'status' => false
            ];
        }


        $plan = (int) $request->get('id_plano', 64);
        $cpf = $request->get('cpf');
        $cpf = preg_replace( '/[^0-9]/', '', $cpf);

        $cliente = \App\Models\Clientes::cpf($cpf)->first();
        if($cliente && $cliente->statusPagamento() !== \App\Models\Clientes::PAGAMENTO_EM_DIA) {
            return [
                'erro' => 'Erro.',
                'message' => "Entre em contato com o atendimento da Lifepet.",
                'status' => false
            ];
        }

        $logger->register(\App\Http\Util\LogEvent::NOTICE, \App\Http\Util\LogPriority::MEDIUM,
            "Houve uma nova tentativa de contratação do plano #{$plan}. Identificação: {$cpf} \nOs dados recebidos foram: \n{$dadosCliente}"
        );

        $query = \App\LifepetCompraRapida::where('cpf', $cpf);
        if($query->exists()) {
            $compraRapida = $query->first();

            $query->where('pagamento_confirmado', 0);

            if($query->exists()) {
                $compraRapida = $query->first();
            }
            //Verifica se a última tentativa tem mais de 3 minutos
            if(Carbon::now()->addMinutes(-3)->gte($compraRapida->updated_at)) {
                //Caso positivo, atualiza a base e continua o processo
                $compraRapida->updated_at = Carbon::now();
                $compraRapida->update();
            } else {
                $logger->register(\App\Http\Util\LogEvent::WARNING, \App\Http\Util\LogPriority::MEDIUM,
                    "O cliente tentou fazer uma nova compra em menos de 3 minutos. Identificação: {$cpf} \nOs dados recebidos foram: \n{$dadosCliente}"
                );
                //Caso negativo, retorna um erro pedindo para aguardar 10 minutos até a próxima tentativa
                return [
                    'erro' => 'Muitas tentativas consecutivas',
                    'message' => 'Encontramos uma compra recente com os seus dados. No caso de uma nova compra, pedimos para que aguarde 3 minutos antes da próxima tentativa. Cheque o seu email para mais orientações após a compra.',
                    'status' => false
                ];
            }

            $compraRapida->addTentativa();
        } else {
            $compraRapida = new \App\LifepetCompraRapida();
            $compraRapida->addTentativa();
        }

        $amount = $request->get('amount', 1);
        $installments = $request->get('parcelas', 1);
        $plano = \App\Models\Planos::find($plan);

        $tags[] = "parcelas:$installments";
        $tags[] = $regime = strtoupper($request->get('regime', 'MENSAL'));
        $tags[] = 'lpt#' . $plan;

        /**
         * ETAPA DE PRECIFICAÇÃO
         */
        $priceToPay = \App\Http\Controllers\LifepetParaTodosController::obterPrecoPlano($plano, $amount, $regime);

        if(!$priceToPay) {
            $prices = \App\Http\Controllers\API\AssinaturasAPIController::getPrice($plan);
            $priceToPay = $prices[$amount];
        }
        //Aplicar códigos de desconto.
        if($request->get('id_cupom')) {
            /**
             * @var \App\Models\LPTCodigosPromocionais $cupom
             */
            $cupom = \App\Models\LPTCodigosPromocionais::find($request->get('id_cupom'));
            if($cupom && $cupom->regimeAplicavel($regime)) {
                $logger->register(\App\Http\Util\LogEvent::NOTICE, \App\Http\Util\LogPriority::HIGH,
                    "O cupom {$cupom->codigo} acaba de ser utilizado. Identificação: {$cpf}"
                );
                $tags[] = "cupom:{$cupom->codigo}";
                $priceToPay = $cupom->aplicar($priceToPay);
            }
        }

        if($priceToPay < 1) {
            $priceToPay = 0;
            $logger->register(\App\Http\Util\LogEvent::NOTICE, \App\Http\Util\LogPriority::HIGH,
                "O plano será contratado por R$ 0,00. Identificação: {$cpf} \nOs dados recebidos foram: \n{$dadosCliente}"
            );
        }

        /**
         * ETAPA DE CADASTRO FINANCEIRO DO CLIENTE
         */
        //Creates customer
        $customer = null;
        try {
            $customer = $finance->createBasicCustomer($request->all());
        } catch (\Exception $e) {
            $logger->register(\App\Http\Util\LogEvent::ERROR, \App\Http\Util\LogPriority::HIGH,
                "Houve um erro ao tentar cadastrar o cliente no SF. Identificação: {$cpf} \nExceção:\n{$e->getMessage()}\nOs dados recebidos foram: \n{$dadosCliente}"
            );

            return [
                'erro' => $e->getMessage(),
                'message' => 'Houve um problema ao tentar cadastrar os seus dados no sistema. Verifique se todos os campos requeridos estão preenchidos. Caso o erro persista, entre em contato com nosso suporte.',
                'status' => false,
            ];
        }

        if(!$customer) {
            $logger->register(\App\Http\Util\LogEvent::ERROR, \App\Http\Util\LogPriority::HIGH,
                "Houve um erro desconhecido ao tentar cadastrar o cliente no SF. Identificação: {$cpf} \nOs dados recebidos foram: \n{$dadosCliente}"
            );
            return [
                'erro' => '',
                'message' => 'Houve um problema ao tentar cadastrar os seus dados no sistema. Verifique se todos os campos requeridos estão preenchidos. Caso o erro persista, entre em contato com nosso suporte.',
                'status' => false,
            ];
        }

        /**
         * ETAPA DE PAGAMENTO
         */
        //Adds credit card and signature
        $card = null;
        if($priceToPay) {
            try {
                $card = $finance->addCreditCard($customer, $request->all());
            } catch (Exception $e) {
                $logger->register(\App\Http\Util\LogEvent::ERROR, \App\Http\Util\LogPriority::HIGH,
                    "Não foi possível salvar o cartão de crédito. O número do cartão informado é inválido. Identificação: {$cpf} \nExceção\n{$e->getMessage()}\nOs dados recebidos foram: \n{$dadosCliente}"
                );

                return [
                    'erro' => $e->getMessage(),
                    'message' => 'O número do cartão informado é inválido.',
                    'status' => false,
                ];
            }
        }


        if(!$card && $priceToPay) {
            $logger->register(\App\Http\Util\LogEvent::ERROR, \App\Http\Util\LogPriority::HIGH,
                "Não foi possível salvar o cartão de crédito. Identificação: {$cpf} \nOs dados recebidos foram: \n{$dadosCliente}"
            );
            throw new Exception('Não foi possível realizar a compra com o cartão informado.');
        }

        $payment = new stdClass();
        $payment->status = 'PENDING';

        if($priceToPay) {
            try {
                $payment = $finance->pay([
                    'amount' => number_format($priceToPay, 2),
                    'installments' => $installments,
                    'customer_id' => $customer->id,
                    'due_date' => Carbon::now()->format('Y-m-d'),
                    'type' => 'creditcard',
                    'fingerprint_ip' => $request->ip(),
                    'fingerprint_session' => $request->get('fingerprint_session'),
                    'tags' => join(';', array_merge($tags, ['e-commerce', 'lifepet-para-todos', 'compra-rapida', 'venda']))
                ]);
            } catch (Exception $e) {
                $exception = "{$e->getMessage()}\n{$e->getFile()}:{$e->getLine()}";
                $logger->register(\App\Http\Util\LogEvent::ERROR, \App\Http\Util\LogPriority::HIGH,
                    "Houve um erro ao tentar processar o pagamento do cliente no SF.\nIdentificação: {$cpf}.\nExceção: {$exception} \nOs dados recebidos foram: \n{$dadosCliente}"
                );

                $compraRapida->adapt($request);
                $compraRapida->pagamento_confirmado = false;
                $compraRapida->setPagamentos($payment);
                $compraRapida->save();

                return [
                    'erro' => $e->getMessage(),
                    'message' => 'Encontramos um erro ao tentar processar o pagamento no seu cartão. Verifique o limite do seu cartão ou tente novamente mais tarde.',
                    'status' => false
                ];
            }
        } else {
            $payment->status = 'AVAILABLE';
            $payment->description = 'Pagamento de R$ 0,00 autorizado sem cobrança via SF.';
        }

        if($payment->status == 'AVAILABLE') {

            $logger->register(\App\Http\Util\LogEvent::NOTICE, \App\Http\Util\LogPriority::HIGH,
                "O pagamento do cliente foi confirmado.\n Identificação: {$cpf}.\nOs dados recebidos foram: \n{$dadosCliente}"
            );

            //Create email trigger - Success
            $compraRapida->adapt($request);
            $compraRapida->pagamento_confirmado = true;
            $compraRapida->setPagamentos($payment);

            if(isset($cupom) && $cupom) {
                $compraRapida->id_cupom = $cupom->id;
            }

            $compraRapida->save();

            $rd = new \App\Helpers\API\RDStation\Services\RDCompraParaTodosContinuarCadastroService($plano);
            $rd->process($compraRapida);

            $linkConclusao = route('api.assinaturas.concluir', ['hash' => $compraRapida->hash]);

            $mensagem = "Você está quase lá! Estamos processando seu pagamento. Enviamos informações importantes para o seu e-mail para finalizar seu cadastro e conhecer seus pets. Mas não se preocupe, você também pode concluir o seu cadastro pelo seguinte link: $linkConclusao";
            $whatsapp = new \App\Helpers\API\SimpleChat\Message($compraRapida->nome, $mensagem, $compraRapida->celular);

            try {
                $whatsapp->send();
            } catch (Exception $e) {
                \App\Http\Util\Logger::log(
                    \App\Http\Util\LogEvent::WARNING,
                    'ecommerce',
                    \App\Http\Util\LogPriority::LOW,
                    'Houve uma falha na tentativa de enviar o link de conclusão ao cliente via Whatsapp. '
                );
            }

            return [
                'link' => $linkConclusao,
                'message' => 'Você está quase lá! Estamos processando seu pagamento. Agora é necessário que você vá até seu e-mail. Enviamos informações importantes para finalizar seu cadastro e conhecer seus pets.',
                'status' => true,
                'hash' => $compraRapida->hash
            ];
        } else {
            //Create email trigger - Failure
            $rd = new \App\Helpers\API\RDStation\Services\RDCompraParaTodosFalhaPagamentoService($plano);
            $rd->process($compraRapida);

            //Enviar um email notificando problema na compra. (Thiago, Atendimento, Alexandre)
            $logger->register(\App\Http\Util\LogEvent::WARNING, \App\Http\Util\LogPriority::HIGH,
                "O pagamento do cliente NÃO foi confirmado.\nIdentificação: {$cpf}.\nOs dados recebidos foram: \n{$dadosCliente}"
            );

            return [
                'message' => 'Encontramos um erro ao tentar processar o pagamento no seu cartão. Verifique o limite do seu cartão e tente novamente.',
                'status' => false,
            ];
        }
    })->name('assinaturas.assinar');

    Route::post('/v2/order', function(Request $request) {
        header('Access-Control-Allow-Origin: https://lifepet.com.br');
        $logger = new \App\Http\Util\Logger('ecommerce');

        $plan = (int) $request->get('id_plano');
        $cpf = $request->get('cpf');
        $cpf = preg_replace( '/[^0-9]/', '', $cpf);

        $dadosCliente = \App\Http\Controllers\API\AssinaturasAPIController::formatClientDataToLog($request->all());

        $logger->register(\App\Http\Util\LogEvent::NOTICE, \App\Http\Util\LogPriority::MEDIUM,
            "API v2\nHouve uma nova tentativa de contratação do plano #{$plan}. Identificação: {$cpf} \nOs dados recebidos foram: \n{$dadosCliente}"
        );

        $prices = \App\Http\Controllers\API\AssinaturasAPIController::getPrice($plan);

        $amount = $request->get('amount', 1);
        $priceToPay = $prices[$amount];
        if($priceToPay < 1) {
            throw new Exception('Não foi possível realizar a compra. Valor incorreto.');
        }

        //Create email trigger - Success
        $compraRapida = new \App\LifepetCompraRapida();
        $compraRapida->adapt($request);
        $compraRapida->pagamento_confirmado = false;
        $compraRapida->save();

        return [
            'status' => true,
            'order_id' => $compraRapida->id,
            'plan_id' => $plan,
            'price' => $priceToPay
        ];
    })->name('assinaturas.v2.order');

    Route::get('v2/order/{id}', function(Request $request, $id) {
        header('Access-Control-Allow-Origin: https://lifepet.com.br');
        $logger = new \App\Http\Util\Logger('ecommerce');

        $compraRapida = \App\LifepetCompraRapida::find($id);

        if(!$compraRapida) {
            $logger->register(\App\Http\Util\LogEvent::NOTICE, \App\Http\Util\LogPriority::MEDIUM,
                "Não foi possível encontrar a ordem de número #{$id}"
            );

            return [
                'status' => false,
                'message' => "Não foi possível encontrar a ordem de número #{$id}"
            ];
        }

        if($compraRapida->pagamento_confirmado) {
            return redirect("https://lifepet.com.br/paratodos-sucesso/?hash=" . $compraRapida->hash);
        }

        $compraRapida->pagamento_confirmado = true;
        $compraRapida->save();

        $logger->register(\App\Http\Util\LogEvent::NOTICE, \App\Http\Util\LogPriority::HIGH,
            "API v2\nO pagamento do cliente foi confirmado.\n Identificação: {$compraRapida->nome} ({$compraRapida->cpf}). Pedido #{$compraRapida->id}"
        );

        $rd = new \App\Helpers\API\RDStation\Services\RDCompraParaTodosContinuarCadastroService();
        $rd->process($compraRapida);

        $linkConclusao = route('api.assinaturas.concluir', ['hash' => $compraRapida->hash]);

        $mensagem = "Você está quase lá! Estamos processando seu pagamento. Enviamos informações importantes para o seu e-mail para finalizar seu cadastro e conhecer seus pets. Mas não se preocupe, você também pode concluir o seu cadastro pelo seguinte link: $linkConclusao";
        $whatsapp = new \App\Helpers\API\SimpleChat\Message($compraRapida->nome, $mensagem, $compraRapida->celular);

        try {
            $whatsapp->send();
        } catch (Exception $e) {
            \App\Http\Util\Logger::log(
                \App\Http\Util\LogEvent::WARNING,
                'ecommerce',
                \App\Http\Util\LogPriority::LOW,
                'Houve uma falha na tentativa de enviar o link de conclusão ao cliente via Whatsapp. '
            );
        }

        return redirect("https://lifepet.com.br/paratodos-sucesso/?hash=" . $compraRapida->hash);
    })->name('assinaturas.v2.confirm');

    Route::post('v3/pagseguro/checkout-code', function(Request $request) {
        header('Access-Control-Allow-Origin: https://lifepet.com.br');
        $logger = new \App\Http\Util\Logger('ecommerce');

        $email = 'lifepet@lifepet.com.br';
        $token = '61fc8053-2c59-4d9e-9dc8-96aed89e1bc45ce2036e4329bd34a74a15930b8779354df2-0e13-4863-b947-a6ffe4977716';

        $plan = $request->get('id_plano');
        $cpf = $request->get('cpf');
        $dadosCliente = \App\Http\Controllers\API\AssinaturasAPIController::formatClientDataToLog($request->all());

        $prices = \App\Http\Controllers\API\AssinaturasAPIController::getPrice($plan);

        $amount = $request->get('amount', 1);
        $priceToPay = $prices[$amount];
        if($priceToPay < 1) {
            throw new Exception('Não foi possível realizar a compra. Valor incorreto.');
        }

        $logger->register(\App\Http\Util\LogEvent::NOTICE, \App\Http\Util\LogPriority::MEDIUM,
            "API v2\nHouve uma nova tentativa de contratação do plano #{$plan}. Identificação: {$cpf} \nOs dados recebidos foram: \n{$dadosCliente}"
        );

        //Create email trigger - Success
        $compraRapida = new \App\LifepetCompraRapida();
        $compraRapida->adapt($request);
        $compraRapida->pagamento_confirmado = false;
        $compraRapida->save();

        $form = [
            "currency" => "BRL",
            "itemId1" => $compraRapida->id,
            "itemDescription1" => "Lifepet - Plano #{$plan}",
            "itemAmount1" => number_format($priceToPay, 2),
            "itemQuantity1" => 1,
            "itemWeight1" => 1,
            "reference" => "LPT-{$plan}",
            "senderName" => $request->get('name'),
            "senderAreaCode" => 27,
            "senderPhone" => $request->get('phone'),
            "senderCPF" => $request->get('cpf'),
            "senderBornDate" => null,
            "senderEmail" => $request->get('email', "email@sandbox.pagseguro.com.br"),
            "shippingType" => 1,
            "shippingAddressStreet" => $request->get('street'),
            "shippingAddressNumber" => $request->get('address_number'),
            "shippingAddressComplement" => '',
            "shippingAddressDistrict" => $request->get('neighbouhood'),
            "shippingAddressPostalCode" => $request->get('cep'),
            "shippingAddressCity" => $request->get('city'),
            "shippingAddressState" => $request->get('state'),
            "shippingAddressCountry" => 'BRA',
            "extraAmount" => null,
            "notificationURL" => null,
            "maxUses" => 2,
            "maxAge" => 3000,
            "shippingCost" => null,
            "redirectURL" => route('api.assinaturas.v2.confirm', ['id' => $compraRapida->id]),
            "excludePaymentMethodGroup" => "BOLETO"
        ];


        $http = new \GuzzleHttp\Client([
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]
        ]);
        try {
            $response = $http->request('POST',"https://ws.pagseguro.uol.com.br/v2/checkout/?email={$email}&token={$token}", [
                'form_params' => $form
            ]);

            $contents = $response->getBody()->getContents();
            $xml = simplexml_load_string($contents, "SimpleXMLElement", LIBXML_NOCDATA);
            $json = json_encode($xml);
            $array = json_decode($json,TRUE);
            $array['order_id'] = $compraRapida->id;
            $array['status'] = true;
            return $array;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    });

    /**
     * Superlógica V2
     */
    Route::group(['prefix' => 'v4'], function() {
        Route::post('/assinar', '\App\Http\Controllers\Superlogica\LifepetParaTodosController@assinar')->name('assinaturas.v4.assinar');
    });
});

Route::group(['prefix' => 'message', 'middleware' => []], function() {
   Route::group(['prefix' => 'whatsapp'], function() {
      Route::post('/boleto-atraso', function(Request $request) {

          $leads = $request->get('leads');

          $idBoleto = $leads[0]["custom_fields"]["IdBoleto"];
          $dataVencimento = $leads[0]["custom_fields"]["DataVencimento"];
          $valorBoleto = $leads[0]["custom_fields"]["ValorBoleto"];
          $email = $leads[0]["email"];

          $cliente = \App\Models\Clientes::where('email', '=', $email)->first();
          if(!$cliente || !$cliente->celular) {
              throw new Exception('Cliente não encontrado.');
          }

          $message = "Olá,<br>" .
                     "Verificamos que o boleto do plano do seu pet já venceu, mas o pagamento ainda não foi realizado.<br>" .
                     "Queremos saber o que aconteceu e como podemos ajudar.<br>" .
                     "Lembramos que é muito importante manter o pagamento em dia para que seu pet continue seguro e você livre de surpresas financeiras.<br>" .
                     "O valor do seu boleto é de R\$ {$valorBoleto} e venceu dia {$dataVencimento}.<br>" .
                     "Para sua comodidade, segue o link com a segunda-via do boleto: https://financeiro.lifepet.com.br/boletos/segundavia/{$idBoleto}<br>" .
                     "O pagamento pode demorar até 72h pode ser compensado. Caso já tenha pago, favor desconsiderar essa mensagem.<br>" .
                     "Caso necessite de suporte, digite \"Menu\".";
          $message = str_replace(["<br>", "<br />"], "\r\n", $message);

          $messenger = new App\Helpers\API\SimpleChat\Message($cliente->nome_cliente, $message, $cliente->celular);
          $response = $messenger->send();
          return [
              'status' => $response->getStatusCode(),
              'body' => $response->getBody()
          ];
      });

      Route::post('/batch', function(Request $request) {
          ini_set('max_execution_time', 400 * 30);
          $logger = new \App\Http\Util\Logger('comunicacao');

          $responses = [];
          $phones = [];
          $rawPhones = $request->get('phones');
          if($rawPhones) {
              $phones = explode(',', $rawPhones);
          }

          $file = $request->get('file', null);

          foreach ($phones as $phone) {
              $imageResponse = null;
              if($file) {
                  $messenger = new App\Helpers\API\SimpleChat\Message('Lifepet', $request->get('image_message'), $phone, $file);
                  $response = $messenger->send();
                  $imageResponse =  [
                      'phone' => $phone,
                      'status' => $response->getStatusCode(),
                      'body' => $response->getBody()->getContents()
                  ];
              }


              $messenger = new App\Helpers\API\SimpleChat\Message('Lifepet', $request->get('text_message'), $phone);
              $response = $messenger->send();
              $textResponse =  [
                  'phone' => $phone,
                  'status' => $response->getStatusCode(),
                  'body' => $response->getBody()->getContents()
              ];

              $responses[] = [
                  'image' => $imageResponse,
                  'text' => $textResponse
              ];

              $logger->register(\App\Http\Util\LogEvent::NOTICE, \App\Http\Util\LogPriority::LOW,
                  "Whatsapp enviado para o cliente {$phone}"
              );
          }

          return $responses;
      });
   });
});

Route::post('/teste-whatsapp', function(Request $request) {
    ob_start();
    var_dump($request->all());
    $date = Carbon::now()->format('Y-m-d h:i:s');
    $data = $date . ":\n\n";
    $data = $data . ob_get_clean();

    $contents = file_get_contents(storage_path('logs/requests.log'));

    $contents = $contents . $data;

    return file_put_contents(storage_path('logs/requests.log'), $contents);
});

Route::group(['prefix' => 'assets/images/campaign'], function() {
   Route::get('{year}/{file}', function(Request $request, $year, $file) {
       header("Access-Control-Allow-Origin: *");
       header("Access-Control-Allow-Headers: *");
       header("Content-Type: image/jpeg");

       $s = DIRECTORY_SEPARATOR;
       $uri = public_path() . "{$s}assets{$s}images{$s}campaign{$s}{$year}{$s}{$file}";

       if(!file_exists($uri)) {
           abort(404);
           return;
       }

       echo file_get_contents($uri);
   });
});

Route::get('links-pagamento/{id}/dispatch', function($id) {
    return \App\LinkPagamento::find($id)->dispatch();
});
Route::post('renovacao/api/callback/{id}', '\App\Http\Controllers\RenovacaoController@callbackPagamento')->name('renovacao.callback');

Route::group(['prefix' => 'relatorios/'], function() {
    Route::get('cenario2', function() {

        $relatorioPets = App\Models\Pets::where('ativo', 1)
        ->whereHas('cliente', function ($cliente) {
            $cliente->where('ativo', 1);
        })
        ->with([
            'cliente',
            'petsPlanos.plano'
        ])
        ->get();

        $resultado = [];
        foreach ($relatorioPets as $pet) {
            $ultimoPlano = $pet->petsPlanos->last();
            if (!$ultimoPlano) {
                continue;
            }
            
            $penultimoPlano = $pet->petsPlanos
                ->where('id', '!=', $ultimoPlano->id)
                ->last();
            if (!$penultimoPlano) {
                continue;
            }

            if (
                in_array($ultimoPlano->id_plano, [70, 71, 72, 73]) &&
                $ultimoPlano->valor_momento == 0 &&
                $penultimoPlano->plano->participativo == 0
            ) {
                $resultado[] = [
                    'id_pets_planos' => $ultimoPlano->id,
                    'id_pet' => $pet->id,
                    'nome_pet' => $pet->nome_pet,
                    'id_cliente' => $pet->id_cliente,
                    'nome_cliente' => $pet->cliente->nome_cliente,
                    'cpf' => $pet->cliente->cpf,
                    'valor_momento' => $ultimoPlano->valor_momento,
                    'id_plano' => $ultimoPlano->id_plano,
                    'nome_plano' => $ultimoPlano->plano->nome_plano,
                    'regime' => $pet->regime
                ];
            }
        }
        return \Excel::create('cenario2-'. \Carbon\Carbon::now()->format('ymdHis'), function(\Maatwebsite\Excel\Writers\LaravelExcelWriter $excel) use ($resultado) {
            $excel->sheet('Pasta1', function($sheet) use ($resultado) {
                $sheet->fromArray($resultado);
            });
        })->download('csv');
    });

    Route::get('cenario3', function() {
        $relatorioPets = App\Models\Pets::where('ativo', 1)
        ->whereHas('cliente', function ($cliente) {
            $cliente->where('ativo', 1);
        })
        ->with([
            'cliente.pets',
            'petsPlanos.plano'
        ])
        ->groupBy('id')
        ->get();

        $resultado = [];
        foreach ($relatorioPets as $pet) {
            $ultimoPlano = $pet->petsPlanos->last();
            if (!$ultimoPlano) {
                continue;
            }

            if (
                $pet->cliente->pets()->where('ativo', 1)->get()->count() > 1 &&
                $ultimoPlano->plano && 
                $ultimoPlano->plano->participativo == 1 &&
                $pet->regime == 'MENSAL' &&
                $ultimoPlano->valor_momento != 0 &&
                !in_array($ultimoPlano->id_plano, [74, 75, 76])
            ) {
                $resultado[] = [
                    'id_pet' => $pet->id,
                    'nome_pet' => $pet->nome_pet,
                    'id_cliente' => $pet->id_cliente,
                    'nome_cliente' => $pet->cliente->nome_cliente,
                    'cpf' => $pet->cliente->cpf,
                    'valor_momento' => $ultimoPlano->valor_momento,
                    'nome_plano' => $ultimoPlano->plano->nome_plano,
                    'regime' => $pet->regime,
                    'quatidade_pets' => $pet->cliente->pets->count()
                ];
            }
        }
        return \Excel::create('cenario3-'. \Carbon\Carbon::now()->format('ymdHis'), function(\Maatwebsite\Excel\Writers\LaravelExcelWriter $excel) use ($resultado) {
            $excel->sheet('Pasta1', function($sheet) use ($resultado) {
                $sheet->fromArray($resultado);
            });
        })->download('csv');

    });

    Route::get('cenario5', function() {
        $relatorioPets = App\Models\Pets::where('ativo', 1)
        ->whereHas('cliente', function ($cliente) {
            $cliente->where('ativo', 1);
        })
        ->with([
            'cliente.pets',
            'petsPlanos.plano'
        ])
        ->get();

        $resultado = [];
        foreach ($relatorioPets as $pet) {
            $ultimoPlano = $pet->petsPlanos->last();
            if (!$ultimoPlano) {
                continue;
            }
            
            $penultimoPlano = $pet->petsPlanos
                ->where('id', '!=', $ultimoPlano->id)
                ->last();
            if (!$penultimoPlano) {
                continue;
            }

            if (
                in_array($ultimoPlano->id_plano, [70, 71, 72]) &&
                $pet->cliente->pets->count() > 1 &&
                $penultimoPlano->plano->participativo == 0
            ) {
                $resultado[] = [
                    'id_pet' => $pet->id,
                    'nome_pet' => $pet->nome_pet,
                    'id_cliente' => $pet->id_cliente,
                    'nome_cliente' => $pet->cliente->nome_cliente,
                    'cpf' => $pet->cliente->cpf,
                    'valor_momento' => $ultimoPlano->valor_momento,
                    'nome_plano' => $ultimoPlano->plano->nome_plano,
                    'regime' => $pet->regime,
                ];
            }
        }
        return \Excel::create('cenario5-'. \Carbon\Carbon::now()->format('ymdHis'), function(\Maatwebsite\Excel\Writers\LaravelExcelWriter $excel) use ($resultado) {
            $excel->sheet('Pasta1', function($sheet) use ($resultado) {
                $sheet->fromArray($resultado);
            });
        })->download('csv');

    });
});