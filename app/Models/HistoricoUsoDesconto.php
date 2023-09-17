<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class HistoricoUsoDesconto extends Model
{
    use SoftDeletes;

    public $table = 'historico_uso_descontos';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'valor_desconto',
        'descricao_desconto',
        'tipo_desconto',
        'id_guia',
        'id_guia_secundaria'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'descricao_desconto' => 'string',
        'tipo_desconto' => 'string',
        'id_guia' => 'integer',
        'id_guia_secundaria' => 'integer'
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
    public function historicoUso()
    {
        return $this->belongsTo(\Modules\Guides\Entities\HistoricoUso::class);
    }
}
