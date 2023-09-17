<?php

namespace App\Models;

use App\Helpers\Utils;
use Carbon\Carbon;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Vendas extends \Illuminate\Database\Eloquent\Model
{
    public $table = 'vendas';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'id_cliente',
        'id_vendedor',
        'id_pet',
        'id_plano',
        'adesao',
        'valor',
        'comissao',
        'data_inicio_contrato'
    ];

    public function create(array $attributes = [])
    {
        $comissao = 0;
        $taxa = 0.05;
        $vendas = self::where('id_vendedor', $attributes['id_vendedor'])
            ->whereBetween('created_at',
                [
                    Carbon::now()->startOfMonth()->startOfDay(),
                    Carbon::now()->endOfMonth()->endOfDay()
                ]
            )->count();

        if($vendas > 10) {
            $taxa = 0.25;
        }

        $comissao = ($attributes['adesao'] + $attributes['valor']) * $taxa;

        $attributes = array_merge($attributes,[
            'comissao' => $comissao
        ]);

        $venda = parent::create($attributes);
        $venda->pontuar();

        return $venda;
    }

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_vendedor' => 'integer',
        'id_pet' => 'integer',
        'id_plano' => 'integer',
        'adesao' => 'float',
        'valor' => 'float',
        'comissao' => 'float'
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function pet()
    {
        return $this->belongsTo(\App\Models\Pets::class, 'id_pet');
    }

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
    public function vendedor()
    {
        return $this->belongsTo(\App\Models\Vendedores::class, 'id_vendedor');
    }


    public function pontuar()
    {
        $pontos = 0;
        $pontos += $this->plano->pontos;
        if($this->pet->participativo) {
            $pontos = $pontos/2;
        }

        $regime = $this->pet->regime;
        if($regime === Pets::REGIME_MENSAL) {
            $pontos = $pontos * (self::multiplicadores()['MENSAL']);
        } else if ($this->pet->isAnual()) {
            if($this->pet->isParcelado()) {
                $pontos = $pontos * (self::multiplicadores()['ANUAL_PARCELADO']);
            } else {
                $pontos = $pontos * (self::multiplicadores()['ANUAL_A_VISTA']);
            }
        }

        VendedoresPontuacao::create([
            'id_venda' => $this->id,
            'id_vendedor' => $this->id_vendedor,
            'pontuacao' => $pontos
        ]);
    }

    public static function multiplicadores()
    {
        return [
            'MENSAL' => Parametros::get('vendedores_gamification_estrelas_multiplicador', 'MENSAL'),
            'ANUAL_A_VISTA' => Parametros::get('vendedores_gamification_estrelas_multiplicador', 'ANUAL_A_VISTA'),
            'DEBITO_EM_CREDITO' => Parametros::get('vendedores_gamification_estrelas_multiplicador', 'DEBITO_EM_CREDITO'),
            'ANUAL_PARCELADO' => Parametros::get('vendedores_gamification_estrelas_multiplicador', 'ANUAL_PARCELADO'),
        ];
    }
}