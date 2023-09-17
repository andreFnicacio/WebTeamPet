<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class VendedoresPontuacao extends Model
{
    public $table = 'vendedores_pontuacao';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'id_vendedor',
        'id_venda',
        'pontuacao'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_vendedor' => 'integer',
        'id_venda' => 'integer',
        'pontuacao' => 'integer'
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
    public function venda()
    {
        return $this->belongsTo(\App\Models\Vendas::class, 'id_venda');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function vendedor()
    {
        return $this->belongsTo(\App\Models\Vendedores::class, 'id_vendedor');
    }

    public static function bonus($pontos) {
        if($pontos >= 50 && $pontos < 75) {
            return Parametros::get('vendedores_gamification_estrelas', 50);
        } else if ($pontos >= 75 && $pontos < 100) {
            return Parametros::get('vendedores_gamification_estrelas', 75);
        } else if ($pontos >= 100 && $pontos < 150) {
            return Parametros::get('vendedores_gamification_estrelas', 100);
        } else if ($pontos >= 150 && $pontos < 200) {
            return Parametros::get('vendedores_gamification_estrelas', 150);
        } else if ($pontos >= 200 && $pontos < 250) {
            return Parametros::get('vendedores_gamification_estrelas', 200);
        } else if ($pontos >= 250 && $pontos < 350) {
            return Parametros::get('vendedores_gamification_estrelas', 250);
        } else if ($pontos >= 350 && $pontos < 500) {
            return Parametros::get('vendedores_gamification_estrelas', 350);
        } else if ($pontos > 500) {
            return Parametros::get('vendedores_gamification_estrelas', 500);
        }

        return 0;
    }
}
