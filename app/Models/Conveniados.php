<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Conveniados extends Model
{
    use SoftDeletes;

    public $table = 'conveniados';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'nome_conveniado',
        'contato_principal',
        'email_contato',
        'cep',
        'rua',
        'numero_endereco',
        'complemento_endereco',
        'bairro',
        'cidade',
        'estado',
        'desconto_porcentagem',
        'telefone'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'nome_conveniado' => 'string',
        'contato_principal' => 'string',
        'email_contato' => 'string',
        'cep' => 'string',
        'rua' => 'string',
        'numero_endereco' => 'string',
        'complemento_endereco' => 'string',
        'bairro' => 'string',
        'cidade' => 'string',
        'estado' => 'string',
        'desconto_porcentagem' => 'float',
        'telefone' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
