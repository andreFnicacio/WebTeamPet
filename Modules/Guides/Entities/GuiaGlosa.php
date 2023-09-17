<?php

namespace Modules\Guides\Entities;

use App\Models\Uploads;
use Eloquent as Model;


class GuiaGlosa extends Model
{
//    use SoftDeletes;

    public $table = 'guias_glosas';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'justificativa',
        'defesa',
        'data_defesa',
        'justificativa_confirmacao',
        'data_confirmacao',
        'id_historico_uso',
        'id_usuario'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'justificativa' => 'string',
        'justificativa_confirmacao' => 'string',
        'defesa' => 'string',
        'id_historico_uso' => 'integer',
        'id_usuario' => 'integer'
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
    public function historicoUso()
    {
        return $this->belongsTo(\Modules\Guides\Entities\HistoricoUso::class, 'id_historico_uso')->get()->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_usuario');
    }

    public function getArquivo()
    {
        return Uploads::where('bind_with', 'glosas')->where('binded_id', $this->id)->first();
    }
}
