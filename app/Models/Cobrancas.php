<?php

namespace App\Models;

use App\Helpers\API\Superlogica\V2\Charge;
use App\Helpers\Utils;
use Carbon\Carbon;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;


class Cobrancas extends Model
{
    use SoftDeletes;

    public $table = 'cobrancas';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const LINK2VIA_FINANCEIRO = 'https://financeiro.lifepet.com.br/boletos/segundavia/';

    const PAGAMENTO_AUTOMATICO_PARTICIPACAO = 'Pagamento automático.';

    const COMPETENCE = Utils::COMPETENCE;

    const DRIVER__SUPERLOGICA_V1 = 'SUPERLOGICA_V1';
    const DRIVER__SUPERLOGICA_V2 = 'SUPERLOGICA_V2';
    const DRIVER__SF = 'SISTEMA_FINANCEIRO';
    const DRIVER_VINDI = "VINDI";

    protected $dates = ['deleted_at'];


    public $fillable = [
        'id_cliente',
        'competencia',
        'valor_original',
        'data_vencimento',
        'complemento',
        'status',
        'id_superlogica',
        'id_financeiro',
        'hash_boleto',
        'driver'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'competencia' => 'string',
        'valor_original' => 'float',
        'data_vencimento' => 'date',
        'cancelada_em' => 'datetime',
        'complemento' => 'string',
        'status' => 'integer',
        'hash_boleto' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function cliente()
    {
        return $this->belongsTo(\App\Models\Clientes::class, 'id_cliente');
    }

    public function pagamentos()
    {
        return $this->hasMany(\App\Models\Pagamentos::class, 'id_cobranca', 'id');
    }

    public function setDataVencimentoAttribute($value)
    {
        $this->attributes['data_vencimento'] = date('Y-m-d H:i:s', strtotime($value) );
    }


    public function segundaVia() 
    {
        /**
         * SE O BOLETO FOI GERADO NO SISTEMA NOVO
         */
        if(!empty($this->hash_boleto)) {
            return [
                'status'  => true,
                'message' => 'Cobrança encontrada',
                'data' => [
                    'link_2via' => self::LINK2VIA_FINANCEIRO . $this->hash_boleto
                ]
            ];
        }

        if(!$this->id_superlogica) {
            return [
                'status'   => false,
                'message'  => 'Cobrança não vinculada com a geração de boletos.'
            ];
        }

        /**
         * Novo sistema Superlógica
         */
        $chargeService = new Charge();
        $charge = $chargeService->getCharge($this->id_superlogica);
        if($charge) {
            return [
                'status'   => true,
                'message'  => 'Cobrança encontrada',
                'data' => [
                    'link_2via' => $charge->link_2via
                ]
            ];
        }

        $invoicesManager = new \App\Helpers\API\Superlogica\Invoice();
        $invoices = collect($invoicesManager->get($this->id_superlogica));
        if($invoices->count() == 0) {
            return [
                'status'   => false,
                'message'  => 'Cobrança não encontrada no sistema financeiro.'
            ];
        }

        $invoice = $invoices->first();
        return [
            'status'  => true,
            'message' => 'Cobrança encontrada',
            'data' => [
                'link_2via' => $invoice->link_2via
            ]
        ];
    }

    public function linkSegundaVia()
    {
        $segundaVia = $this->segundaVia();
        if($segundaVia['status']) {
            return $segundaVia['data']['link_2via'];
        }

        return null;
    }

    public function baixaManual($complemento = null, $idExternoPagamento = null, $dataPagamento = null) {
        try {
            $hoje = Carbon::now()->format('d/m/Y');
            if (!$complemento) {
                $complemento = "BAIXA MANUAL: ($hoje)";
            }

            $pagamento = Pagamentos::where('id_financeiro', 'PAYMENT-' . $idExternoPagamento)->first();
            if (!$pagamento) {
                $pagamento = Pagamentos::create([
                    'id_cobranca' => $this->id,
                    'data_pagamento' => date('Y-m-d'),
                    'complemento' => "Pagamento da assinatura processado pela Vindi",
                    'valor_pago' => $this->valor_original,
                    'id_financeiro' => $idExternoPagamento,
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                    'forma_pagamento' => 0
                ]);
            } else {
                $pagamento->complemento .= "\n$complemento";
                $pagamento->update();
            }
        } catch (\Exception $e){
            Log::error(sprintf("Error save payment WebHook :".$e));
        }

        return $pagamento;
    }

    public static function cobrancaAutomatica(Clientes $cliente, $valor_original, $complemento = '', $data_vencimento = null, $competencia = null, $id_financeiro = null, $pago = true, $idExternoPagamento = null, $dataPagamento = null) {
        if(!$data_vencimento) {
            $data_vencimento = new Carbon();
        }

        if(!$competencia) {
            $competencia = Carbon::now()->format('Y-m');
        }
        $cobranca = null;
        if($id_financeiro) {
            $cobranca = self::where('id_financeiro', $id_financeiro)->first();
        }
        if(!$cobranca) {
            /**
             * @var Cobrancas $cobranca
             */
            $cobranca = self::create([
                'id_cliente' => $cliente->id,
                'data_vencimento' => $data_vencimento,
                'valor_original' => $valor_original,
                'status' => 1,
                'competencia' => $competencia,
                'id_financeiro' => $id_financeiro
            ]);
        }


        if($pago) {
            if($complemento) {
                $complemento = self::PAGAMENTO_AUTOMATICO_PARTICIPACAO . "\n" . $complemento;
            } else {
                $complemento = self::PAGAMENTO_AUTOMATICO_PARTICIPACAO;
            }

            $cobranca->baixaManual($complemento, $idExternoPagamento, $dataPagamento);
        }

        return $cobranca;
    }
}
