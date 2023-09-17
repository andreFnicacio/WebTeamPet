<?php

namespace App\Models;

use Illuminate\Database\Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Guides\Entities\HistoricoUso;


class PlanosProcedimentos extends Model\Model
{
    use SoftDeletes;

    public $table = 'planos_procedimentos';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'id_procedimento',
        'id_plano',
        'observacao',
        'valor_cliente',
        'valor_credenciado',
        'beneficio_tipo',
        'beneficio_valor'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_procedimento' => 'integer',
        'id_plano' => 'integer',
        'observacao' => 'string'
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
    public function plano()
    {
        return $this->belongsTo(\App\Models\Planos::class, 'id_plano');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function procedimento()
    {
        return $this->belongsTo(\App\Models\Procedimentos::class, 'id_procedimento');
    }

    /**
     * Obtém o valor do procedimento específico para um plano
     * @param Procedimentos $procedimento
     * @param Planos $plano
     * @return int
     */
    public static function getValorProcedimento(Procedimentos $procedimento, Planos $plano)
    {
        $valorMomento = 0;
        $pp = self::where('id_procedimento', $procedimento->id)
            ->where('id_plano', $plano->id)->first();
        if($pp) {
            $valorMomento = $pp->valor_credenciado;
        }

        return $valorMomento;
    }

    public static function getValorCliente(Procedimentos $procedimento, Planos $plano) {
        $participacaoOriginal = 0;
        $pp = self::where('id_procedimento', $procedimento->id)
            ->where('id_plano', $plano->id)->first();

        $participacaoOriginal = $procedimento->valor_base * HistoricoUso::TAXA_PARTICIPACAO;
        if($pp && $pp->valor_cliente) {
            $participacaoOriginal = $pp->valor_cliente;
        }

        return $participacaoOriginal;
    }

    public static function getValorBeneficio(Procedimentos $procedimento, Planos $plano) {
        $valor = 0;

        $pp = self::where('id_procedimento', $procedimento->id)
            ->where('id_plano', $plano->id)->first();
        if(!$pp) {
            return self::getValorCliente($procedimento, $plano);
        }

        $tipo = $pp->beneficio_tipo;
        $valor = $pp->beneficio_valor;

        if($tipo === null || $valor === null) {
            return self::getValorCliente($procedimento, $plano);
        }
        
        if($tipo === 'fixo') {
            return $valor;
        } else {
            return ($procedimento->valor_base * (1 - ((100-$valor)/100)));
        }
    }

    public static function getValorIsento(Procedimentos $procedimento, Planos $plano)
    {
        $participacaoOriginal = $procedimento->valor_base;
        $pp = self::where('id_procedimento', $procedimento->id)
            ->where('id_plano', $plano->id)->first();

        if($pp) {
            $beneficioTipo = $pp->beneficio_tipo ? $pp->beneficio_tipo : 'fixo';

            $participacaoOriginal = $pp->beneficio_valor;
            if($beneficioTipo != 'fixo') {
                $participacaoOriginal = $procedimento->valor_base * $pp->beneficio_valor;
            }
        }

        return $participacaoOriginal;
    }
}