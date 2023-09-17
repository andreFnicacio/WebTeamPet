<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promocao extends Model
{
    use SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    protected $table = 'promocoes';

    protected $fillable = [
        'nome',
        'dt_inicio',
        'dt_termino',
        'cumulativo',
        'ativo',
        'tipo_desconto',
        'desconto'
    ];

    protected $casts = [
        'id' => 'integer',
        'nome' => 'string',
        'ativo' => 'boolean',
        'cumulativo' => 'boolean',
        'dt_inicio' => 'date',
        'dt_termino' => 'date'
    ];
}
