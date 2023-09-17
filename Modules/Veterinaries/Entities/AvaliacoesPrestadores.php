<?php

namespace Modules\Veterinaries\Entities;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class AvaliacoesPrestadores extends Model
{
    use SoftDeletes;

    public $table = 'avaliacoes_prestadores';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'id_cliente',
        'id_prestador',
        'numero_guia',
        'id_clinica',
        'nota',
        'comentario',
        'publico'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_prestador' => 'integer',
        'numero_guia' => 'integer',
        'id_clinica' => 'integer',
        'nota' => 'string',
        'comentario' => 'string',
        'publico' => 'boolean'
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
        return $this->belongsTo(\App\Models\Clientes::class, 'id_cliente');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function clinica()
    {
        return $this->belongsTo(\Modules\Clinics\Entities\Clinicas::class, 'id_clinica');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function prestador()
    {
        return $this->belongsTo(\Modules\Veterinaries\Entities\Prestadores::class, 'id_prestador');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function pet()
    {
        return $this->belongsTo(\App\Models\Pets::class, 'id_pet');
    }
}
