<?php

namespace App\Models;

use Illuminate\Database\Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class TabelasProcedimentos extends Model\Model
{
    use SoftDeletes;

    public $table = 'tabelas_procedimentos';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'id_tabela_referencia',
        'id_procedimento',
        'valor'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_tabela_referencia' => 'integer',
        'id_procedimento' => 'integer',
        'valor' => 'float'
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
    public function procedimento()
    {
        return $this->belongsTo(\App\Models\Procedimentos::class, 'id_procedimento', 'id')->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function tabelasReferencia()
    {
        return $this->belongsTo(\App\Models\TabelasReferencia::class, 'id_tabela_referencia', 'id')->first();
    }
}
