<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class LeadsDadosAdicionais extends Model
{
    public $table = 'leads_dados_adicionais';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'logradouro',
        'numero',
        'bairro',
        'cidade',
        'uf',
        'cep',
        'pets',
        'id_lead'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'logradouro' => 'string',
        'numero' => 'string',
        'bairro' => 'string',
        'cidade' => 'string',
        'uf' => 'string',
        'cep' => 'string',
        'pets' => 'string',
        'id_lead' => 'integer'
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
    public function lead()
    {
        return $this->belongsTo(\App\Models\Lead::class);
    }
}