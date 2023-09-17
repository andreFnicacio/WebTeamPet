<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Pagamentos extends Model
{
    use SoftDeletes;

    public $table = 'pagamentos';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'id_cobranca',
        'data_pagamento',
        'complemento',
        'forma_pagamento',
        'valor_pago',
        'id_financeiro'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_cobranca' => 'integer',
        'data_pagamento' => 'date',
        'complemento' => 'string',
        'forma_pagamento' => 'integer',
        'valor_pago' => 'float'
    ];

    public static $formasPagamento = [
        'BOLETO',
        'DÉBITO',
        'CRÉDITO'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function cobranca()
    {
        return $this->belongsTo(\App\Models\Cobrancas::class, 'id_cobranca');
    }

    public function setDataPagamentoAttribute($value)
    {
        $this->attributes['data_pagamento'] = date('Y-m-d H:i:s', strtotime($value) );
    }

    public function getFormaPagamentoAttribute($value)
    {
        if($value) {
            return self::$formasPagamento[$value];
        }
    }
}