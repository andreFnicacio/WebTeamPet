<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Parametros extends Model
{
    public $table = 'parametros';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'tipo',
        'chave',
        'valor',
        'descricao'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'chave' => 'string',
        'valor' => 'string',
        'descricao' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public static function set($tipo, $chave, $valor, $descricao = null)
    {
        $dados = [
            'tipo'  => $tipo,
            'chave' => $chave,
            'valor' => $valor,
            'descricao' => $descricao
        ];

        $parametro = self::where('tipo', $tipo)->where('chave', $chave)->first();

        if($parametro) {
            $parametro->fill($dados);
            return $parametro->update();
        }

        return self::create($dados);
    }

    public static function get($tipo, $chave = null)
    {
        $query = self::where('tipo', $tipo);
        if($chave) {
            $query->where('chave', $chave);
            $result = $query->first();
            return $result ? $result->valor : null;
        }

        return $query->get();
    }
}