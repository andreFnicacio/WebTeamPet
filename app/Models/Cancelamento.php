<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class Cancelamento extends Model
{
//    use SoftDeletes;

    public $table = 'cancelamentos';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const MOTIVO_INADIMPLENCIA = 'INADIMPLENCIA';
    const MOTIVO_INTERESSE = 'INTERESSE';
    const MOTIVO_FINANCEIRO = 'FINANCEIRO';
    const MOTIVO_DESCONTENTAMENTO = 'DESCONTENTAMENTO';
    const MOTIVO_JURIDICO = 'JURIDICO';
    const MOTIVO_OBITO = 'OBITO';
    const MOTIVO_OUTROS = 'OUTROS';

    const MOTIVOS = [
        self::MOTIVO_INADIMPLENCIA => 'Inadimplência',
        self::MOTIVO_INTERESSE => 'Interesse',
        self::MOTIVO_FINANCEIRO => 'Financeiro',
        self::MOTIVO_DESCONTENTAMENTO => 'Descontentamento',
        self::MOTIVO_JURIDICO => 'Jurídico',
        self::MOTIVO_OBITO => 'Óbito',
        self::MOTIVO_OUTROS => 'Outros'
    ];

    protected $dates = ['deleted_at'];


    public $fillable = [
        'motivo',
        'justificativa',
        'data_cancelamento',
        'id_usuario',
        'id_pet',
        'cancelar_externo'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'motivo' => 'string',
        'justificativa' => 'string',
        'data_cancelamento' => 'date',
        'id_usuario' => 'integer',
        'id_pet' => 'integer'
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
    public function pet()
    {
        return $this->belongsTo(\App\Models\Pets::class, 'id_pet');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * @return Builder
     */
    public static function ultimosPorPet() {
        return self::select('*')
                ->from('cancelamentos')
                ->join(DB::raw('(SELECT max(id) as id from cancelamentos group by id_pet) ultimo_cancelamento'), function($join) {
                    $join->on('cancelamentos.id', '=', 'ultimo_cancelamento.id');
                });
                    
    }
}
