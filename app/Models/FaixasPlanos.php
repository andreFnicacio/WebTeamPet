<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class FaixasPlanos extends Model
{
    //use SoftDeletes;

    public $table = 'faixas_planos';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    
    // Grupos em que a regra da faixa de planos não é aplicada
    public static $gruposExcecao = [
        '10100', // CONSULTAS COMUNS
        '10102', // CONSULTA ESPECIALIZADA
        '99900', // CONSULTA URGENCIA E EMERGENCIA
        '10101016', // MEDICAMENTOS
        '23100', // EXAMES LABORATORIAIS - ANTERIOR
        '10101011', // EXAMES LABORATORIAIS - ATUAL
        '26100', // EXAMES POR IMAGEM
        '22100', // VACINAS
    ];

    protected $dates = ['deleted_at'];


    public $fillable = [
        'descricao',
        'valor'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'descricao' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function planos()
    {
        return $this->hasMany(\App\Models\Planos::class);
    }
}
