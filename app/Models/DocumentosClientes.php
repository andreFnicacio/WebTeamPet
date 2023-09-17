<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class DocumentosClientes extends Model
{
    use SoftDeletes;

    public $table = 'documentos_clientes';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const STATUS_PENDENTE = 'PENDENTE';
    const STATUS_ENVIADO = 'ENVIADO';
    const STATUS_APROVADO = 'APROVADO';
    const STATUS_REPROVADO = 'REPROVADO';

    protected $dates = ['deleted_at'];


    public $fillable = [
        'tipo',
        'nome',
        'status',
        'avaliacao_obrigatoria',
        'data_envio',
        'data_recusa',
        'data_aprovacao',
        'motivo_reprovacao',
        'id_cliente',
        'id_usuario_aprovacao',
        'id_usuario_reprovacao'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'tipo' => 'string',
        'nome' => 'string',
        'status' => 'string',
        'avaliacao_obrigatoria' => 'boolean',
        'data_envio' => 'datetime',
        'data_recusa' => 'datetime',
        'data_reprovacao' => 'datetime',
        'data_aprovacao' => 'datetime',
        'motivo_reprovacao' => 'string',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
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

    public function usuarioAprovacao()
    {
        return $this->belongsTo(\App\User::class, 'id_usuario_aprovacao');
    }

    public function usuarioReprovacao()
    {
        return $this->belongsTo(\App\User::class, 'id_usuario_reprovacao');
    }

    public function uploads()
    {
        return \App\Models\Uploads::where('bind_with', '=', 'documentos_clientes')
                                    ->where('binded_id', '=', $this->id);
    }
}
