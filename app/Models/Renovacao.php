<?php

namespace App\Models;

use App\Helpers\API\Financeiro\Financeiro;
use App\Helpers\API\RDStation\Services\RDRenovacaoConversaoParaMensalService;
use App\Helpers\Utils;
use App\Http\Util\LogEvent;
use App\Http\Util\Logger;
use App\Http\Util\LogMessages;
use App\Http\Util\LogPriority;
use App\LinkPagamento;
use Carbon\Carbon;
use Composer\Package\Link;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use OpenApi\Util;

/**
 * Class Renovacao
 * @package App\Models
 * @property Pets $pet
 * @property Clientes $cliente
 * @property Planos $plano
 * @property string $regime
 * @property string $status
 * @property float $valor Valor da renovação com desconto
 * @property float $valor_original Valor original sem desconto do plano atual do cliente
 * @property float $valor_bruto Valor reajustado sem desconto
 * @property float $desconto
 * @property Carbon $paid_at
 * @property float $reajuste
 * @property int $id
 * @property LinkPagamento $link
 * @property string $competencia_mes
 * @property string $competencia_ano

 */

class Renovacao extends Model
{
    public $table = 'renovacoes';

    const LOG_AREA = 'renovacoes';
    const LOG_TABLE = 'renovacoes';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const STATUS_NOVO = 'NOVO';
    const STATUS_EM_NEGOCIACAO = 'EM_NEGOCIACAO';
    const STATUS_PAGO = 'PAGO';
    const STATUS_CANCELADO = 'CANCELADO';
    const STATUS_NAO_OPTANTE = 'NAO_OPTANTE';
    const STATUS_AGENDADO = 'AGENDADO';
    const STATUS_ATUALIZADO = 'ATUALIZADO';
    const STATUS_CONVERTIDO = 'CONVERTIDO';

    protected $dates = ['deleted_at', 'paid_at'];


    public $fillable = [
        'id_cliente',
        'id_pet',
        'id_plano',
        'status',
        'id_link_pagamento',
        'paid_at',
        'regime',
        'valor',
        'valor_original',
        'desconto',
        'competencia_mes',
        'competencia_ano',
        'valor_bruto',
        'reajuste',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_pet' => 'integer',
        'id_plano' => 'integer',
        'status' => 'string',
        'id_link_pagamento' => 'integer',
        'regime' => 'string',
        'valor_bruto' => 'float',
        'reajuste' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'id_cliente' => 'numeric|required|exists:clientes,id',
        'id_pet' => 'numeric|required|exists:pets,id',
        'id_plano' => 'numeric|required|exists:planos,id',
        'status' => 'required|in:NOVO,EM_NEGOCIACAO,PAGO,CANCELADO',
        'id_link_pagamento' => 'numeric|required|exists:planos,id',
        'regime' => 'required|in:ANUAL,MENSAL',
        'valor' => 'required|numeric',
        'valor_original' => 'required|numeric',
        'desconto' => 'required|numeric'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'id_cliente');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function link()
    {
        return $this->belongsTo(LinkPagamento::class, 'id_link_pagamento');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function pet()
    {
        return $this->belongsTo(Pets::class, 'id_pet');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function plano()
    {
        return $this->belongsTo(Planos::class, 'id_plano');
    }

    public function scopeAberta($query)
    {
        return $query->whereIn('status', [
            self::STATUS_NOVO,
            self::STATUS_EM_NEGOCIACAO
        ]);
    }

    public function renovar()
    {
        //Criar nota
        $valorRenovacao = Utils::money($this->valor);
        $corpoNota = "A renovação do PET {$this->pet->nome_pet} de TUTOR {$this->pet->cliente->nome_cliente} no valor de {$valorRenovacao} foi concluída.";
        $this->cliente->addNota($corpoNota);


        $this->status = self::STATUS_ATUALIZADO;
        $this->update();
        return $this->pet->renovar($this);
    }

    public function agendar()
    {
        $valorRenovacao = Utils::money($this->valor);
        $corpoNota = "A renovação do PET {$this->pet->nome_pet} de TUTOR {$this->pet->cliente->nome_cliente} no valor de {$valorRenovacao} foi AGENDADA para o dia do vencimento do contrato.";
        $this->cliente->addNota($corpoNota);

        $this->status = self::STATUS_AGENDADO;
        $this->update();
        return $this;
    }

    public function converter()
    {
        $this->status = self::STATUS_CONVERTIDO;
        $this->pet->regime = Pets::REGIME_MENSAL;

        $petsPlanos = $this->pet->petsPlanosAtual()->first();
        $valorBruto = $this->valor_bruto;
        if($this->valor_bruto === $this->valor_original && $this->reajuste > 0) {
            $valorBruto = $valorBruto * (1 + $this->reajuste);
        }
        $petsPlanos->valor_momento = $valor = (float) number_format($valorBruto / 12, 2);
        $planoConvertido = clone $petsPlanos;
        $planoConvertido->id = null;
        $planoConvertido->status = PetsPlanos::STATUS_RENOVACAO;
        $planoConvertido->id_vendedor = 1;

        $planoConvertido->save();

        $pet = $this->pet->nome_pet;
        $valor = Utils::money($valor);

        $mensagem = "O plano do PET $pet foi convertido para o regime MENSAL de forma automática com o valor de R$ $valor.";
        $this->cliente->addNota($mensagem);

        Logger::log(LogEvent::NOTIFY, 'renovacoes', LogPriority::HIGH, $mensagem, 1);

        (new RDRenovacaoConversaoParaMensalService())->process($this);

        $this->pet->update([
            'id_pets_planos' => $planoConvertido->id,
            'regime'         => Pets::REGIME_MENSAL
        ]);

        $this->update();
        return $this;
    }

    public function getValorBrutoAttribute()
    {
        if(isset($this->attributes['valor_bruto'])) {
            return $this->attributes['valor_bruto'];
        }
        $percentualDesconto = 0;
        if($this->desconto) {
            $percentualDesconto = floatval($this->desconto/100);
        }
        return floatval($this->valor)/(1 - $percentualDesconto);
    }

    public function getReajusteAttribute()
    {
        if(isset($this->attributes['reajuste'])) {
            return $this->attributes['reajuste'];
        }

        if(!$this->valor_original) {
            return 0;
        }

        return ($this->valor_bruto*100/$this->valor_original) - 100;
    }

    public function atualizarFaturaAberta()
    {
        $financeiro = new Financeiro();
        /**
         * @var Clientes $cliente
         */
        $cliente = $this->cliente;

        $cliente->syncWithFinance();

        try {
            $customer = $financeiro->get('customer/refcode/' . $cliente->id_externo);
        } catch (\Exception $e) {
            $mensagemLog = "[RENOVAÇÃO]: O cliente {$cliente->nome_cliente} não pôde ser encontrado no SF com o 'refcode' informado. Não foi possível lançar a diferença da renovação na fatura.";

            Logger::log(
                LogEvent::WARNING,
                'clientes',
                LogPriority::MEDIUM,
                $mensagemLog,
                null,
                'renovacoes',
                $this->id);

            return false;
        }

        try {
            $invoice = $financeiro->get("customer/{$customer->data->id}/invoice-in-progress");
            $hasFaturaAberta = false;
            $planKey = 'PLANO - ' . strtoupper($this->pet->nome_pet);
            $adicional = 0;

            foreach($invoice->data->itens as $item) {
                if($item->name == $planKey) {
                    $hasFaturaAberta = true;
                    $adicional = $this->valor - floatval($item->price);
                }
            }

            if(!$hasFaturaAberta) {
                $mensagemLog = "[RENOVAÇÃO]: Não foi possível encontrar um ITEM DE COBRANÇA na fatura aberta para o cliente {$cliente->nome_cliente}. Não foi possível lançar o adicional de cobrança da renovação do plano.";

                Logger::log(
                    LogEvent::WARNING,
                    'clientes',
                    LogPriority::MEDIUM,
                    $mensagemLog,
                    null,
                    'renovacoes',
                    $this->id);
                return false;
            }
        } catch (\Exception $e) {
            $mensagemLog = "[RENOVAÇÃO]: Não foi possível encontrar uma fatura aberta para o cliente {$cliente->nome_cliente}. Não foi possível lançar o adicional de cobrança da renovação do plano.";

            Logger::log(
                LogEvent::WARNING,
                'clientes',
                LogPriority::MEDIUM,
                $mensagemLog,
                null,
                'renovacoes',
                $this->id);

            return false;
        }

        //Adicionar diferença na fatura
        try {
            $form = [
                "item" => [
                    [
                        'type' => 'D',
                        'name' => 'DIFERENÇA DE RENOVAÇÃO - ' . $planKey,
                        'quantity' => 1,
                        'price' => number_format($adicional, 2)
                    ]
                ]
            ];

            $financeiro->post('invoice/' . $invoice->data->id, $form);
        } catch (\Exception $e) {
            $mensagemLog = "[RENOVAÇÃO]: Não foi possível lançar um novo item na fatura aberta do cliente {$cliente->nome_cliente}.";

            Logger::log(
                LogEvent::WARNING,
                'clientes',
                LogPriority::MEDIUM,
                $mensagemLog,
                null,
                'renovacoes',
                $this->id);

            return false;
        }

        $mensagemLog = "[RENOVAÇÃO]: O adicional de renovação do cliente {$this->cliente->nome_cliente} foi lançado com sucesso na fatura aberta.";

        Logger::log(
            LogEvent::WARNING,
            'clientes',
            LogPriority::MEDIUM,
            $mensagemLog,
            null,
            'renovacoes',
            $this->id);

        $adicional = Utils::money($adicional);
        $this->cliente->addNota("A diferença de renovação no valor de {$adicional} do PET {$this->pet->nome_pet} foi lançada na fatura aberta do cliente.");

        return true;
    }

    public function notificarProposta()
    {
        $valorBruto = Utils::money($this->valor_bruto);
        $desconto = $this->desconto . "%";

        $valorRenovacao = Utils::money($this->valor);
        $corpoNota = "A proposta de renovação do PET {$this->pet->nome_pet} foi enviada para o TUTOR {$this->pet->cliente->nome_cliente} no valor de {$valorRenovacao} e está aguardando pagamento. Valor sem desconto: {$valorBruto}. Valor do desconto: {$desconto}";
        $this->cliente->addNota($corpoNota);
    }

    public function toLog($append = [], $json = true)
    {
        $attributes = [
            'id'                => $this->id,
            'cliente'           => $this->cliente->nome_cliente,
            'pet'               => $this->pet->nome_pet,
            'plano'             => $this->plano->nome_plano,
            'status'            => $this->status,
            'id_link_pagamento' => $this->link->id,
            'paid_at'           => $this->paid_at,
            'regime'            => $this->regime,
            'valor'             => $this->valor,
            'desconto'          => $this->desconto,
            'valor_bruto'       => $this->valor_bruto,
            'reajuste'          => $this->reajuste,
            'competencia'       => "{$this->competencia_ano}-{$this->competencia_mes}",
        ];

        $data = array_merge($attributes, $append);
        if($json) {
            return json_encode($data);
        }

        return $data;
    }
}
