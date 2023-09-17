<?php

namespace App\Models;

use Illuminate\Database\Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PlanosGrupos extends Model\Model
{
    use SoftDeletes;

    public $table = 'planos_grupos';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'liberacao_automatica',
        'dias_carencia',
        'quantidade_usos',
        'valor_desconto',
        'plano_id',
        'grupo_id',
        'uso_unico'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'liberacao_automatica' => 'boolean',
        'dias_carencia' => 'integer',
        'quantidade_usos' => 'integer',
        'valor_desconto' => 'float',
        'plano_id' => 'integer',
        'grupo_id' => 'integer',
        'uso_unico' => 'boolean'
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
    public function grupo()
    {
        return $this->belongsTo(\App\Models\Grupos::class, 'grupo_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function plano()
    {
        return $this->belongsTo(\App\Models\Planos::class, 'plano_id');
    }
}