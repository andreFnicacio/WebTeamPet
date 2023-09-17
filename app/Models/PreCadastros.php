<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PreCadastros extends Model
{
    use SoftDeletes;

    public $table = 'pre_cadastros';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'cpf',
        'nome',
        'email',
        'celular',
        'data_nascimento',
        'data_adesao',
        'id_cliente',
        'cidade',
        'estado'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'cpf' => 'string',
        'nome' => 'string',
        'email' => 'string',
        'celular' => 'string',
        'data_nascimento' => 'date',
        'cidade' => 'string',
        'estado' => 'string'
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
        return $this->belongsTo(\App\Models\Cliente::class);
    }
}
