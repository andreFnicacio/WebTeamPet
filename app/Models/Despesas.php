<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Despesas extends Model
{
    use SoftDeletes;

    public $table = 'despesas';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'id_superlogica',
        'valor_total',
        'forma_pagamento',
        'id_centrocusto_superlogica',
        'nome_centrocusto',
        'porcentagem_participacao',
        'valor_participacao',
        'observacoes',
        'historico',
        'label',
        'data_ordenacao',
        'data_previsaocredito',
        'data_emissao'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_superlogica' => 'integer',
        'valor_total' => 'float',
        'forma_pagamento' => 'string',
        'id_centrocusto_superlogica' => 'integer',
        'nome_centrocusto' => 'string',
        'porcentagem_participacao' => 'float',
        'valor_participacao' => 'float',
        'observacoes' => 'string',
        'historico' => 'string',
        'label' => 'string',
        'data_ordenacao' => 'date',
        'data_previsaocredito' => 'date',
        'data_emissao' => 'date'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
