<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Sugestoes extends Model
{
    use SoftDeletes;

    public $table = 'sugestoes';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'titulo',
        'corpo',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_usuario' => 'integer',
        'titulo' => 'string',
        'corpo' => 'string',
        'lido' => 'boolean',
        'visto_por' => 'integer',
        'realizado' => 'boolean',
        'realizador' => 'integer',
        'prioridade' => 'integer',
        'arquivada' => 'boolean'
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
    public function user()
    {
        return $this->belongsTo(\App\User::class, 'id_usuario');
    }
}
