<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class InformacoesAdicionaisVinculos extends Model
{
    //use SoftDeletes;

    public $table = 'informacoes_adicionais_vinculos';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'id_informacoes_adicionais',
        'tabela_vinculada',
        'id_vinculado'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_informacoes_adicionais' => 'integer',
        'tabela_vinculada' => 'string',
        'id_vinculado' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'id_informacoes_adicionais' => 'required|exists:informacoes_adicionais,id',
        'tabela_vinculada' => 'required',
        'id_vinculado' => 'required'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function informacoesAdicionais()
    {
        return $this->belongsTo(\App\Models\InformacoesAdicionais::class, 'id_informacoes_adicionais', 'id');
    }
}
