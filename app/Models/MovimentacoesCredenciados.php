<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class MovimentacoesCredenciados extends Model
{
    use SoftDeletes;

    public $table = 'movimentacoes_credenciados';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const TIPO_GUIA = 1; // Guia;
    const TIPO_GAMIFICATION_CONSULTA = 2; // Gamification Consulta;
    const TIPO_GAMIFICATION_TEMPO_FORMACAO = 3; // Tempo de Formação;
    const TIPO_GAMIFICATION_AVALIACAO_ATENDIMENTO = 4; // Avaliação de Atendimento;


    protected $dates = ['deleted_at'];


    public $fillable = [
        'tipo',
        'descricao',
        'valor',
        'pago',
        'id_clinica',
        'id_guia_consulta',
        'id_guia_origem'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'tipo' => 'string',
        'valor' => 'integer',
        'descricao' => 'string',
        'pago' => 'boolean',
        'id_clinica' => 'integer',
        'id_guia_consulta' => 'integer',
        'id_guia_origem' => 'integer'
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
    public function clinica()
    {
        return $this->belongsTo(\App\Models\Clinica::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function guiaConsulta()
    {
        return $this->belongsTo(\Modules\Guides\Entities\HistoricoUso::class, 'id_guia_consulta', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function guiaOrigem()
    {
        return $this->belongsTo(\Modules\Guides\Entities\HistoricoUso::class, 'id_guia_origem', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function prestador()
    {
        return $this->guiaConsulta->prestador;
    }
}
