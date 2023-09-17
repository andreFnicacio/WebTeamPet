<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Log extends Model
{
    public $table = 'log';
    
    const CREATED_AT = 'created_at';

    public $timestamps = false;

    public $fillable = [
        'evento',
        'area',
        'executor',
        'importancia',
        'mensagem',
        'id_relacional',
        'tabela_relacionada',
        'created_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'evento' => 'string',
        'area' => 'string',
        'executor' => 'integer',
        'importancia' => 'string',
        'mensagem' => 'string',
        'id_relacional' => 'integer',
        'tabela_relacionada' => 'string',
        'created_at' => 'timestamp'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];
}
