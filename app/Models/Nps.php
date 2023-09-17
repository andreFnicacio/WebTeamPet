<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Nps extends Model
{
    use SoftDeletes;

    public $table = 'nps';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'nota',
        'comentario',
        'origem',
        'id_cliente'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'nota' => 'integer',
        'comentario' => 'string',
        'origem' => 'string'
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
        return $this->belongsTo(\App\Models\Cliente::class, 'id_cliente');
    }

    public static function getNpsGlobal()
    {
        $promotores = Nps::where('nota', '>=', 9)->count();
        $detratores = Nps::where('nota', '<=', 6)->count();
        $total = Nps::all()->count();

        if ($total <= 10) {
            return [
                "nps" => 0,
                "status" => "Poucas avaliações, resultado inconclusivo."
            ];
        }

        $nps = ($promotores/$total*100) - ($detratores/$total*100);

        if ($nps >= 80 && $nps <= 100) {
            $status = "Excelente";
        } elseif ($nps >= 70 && $nps <= 79) {
            $status = "Muito Bom";
        } elseif ($nps >= 50 && $nps <= 69) {
            $status = "Bom";
        } elseif ($nps >= 0 && $nps <= 49) {
            $status = "Ruim";
        } else {
            $status = "Péssimo";
        }

        return [
            "nps" => $nps,
            "status" => $status
        ];
    }

    public static function listarAcumuladoPorDia() {
        return DB::select('SELECT 
                date(created_at) as data,
                (
                    (
                        (SELECT COUNT(1) FROM nps WHERE nota > 8 AND date(created_at) <= date(nps_base.created_at) AND deleted_at IS NULL)
                        -
                        (SELECT COUNT(1) FROM nps WHERE nota < 6 AND date(created_at) <= date(nps_base.created_at) AND deleted_at IS NULL)
                    ) 
                    /
                    (SELECT COUNT(1) FROM nps WHERE deleted_at IS NULL AND date(created_at) <= date(nps_base.created_at))
                ) 
                * 100 as total
                FROM 
                    nps nps_base
                WHERE
                    deleted_at IS NULL
                GROUP BY 
                    date(created_at) 
                ORDER BY 
                    date(created_at) DESC');
    }

    public static function listarAcumuladoPorMes() {
        return DB::select('SELECT
                LAST_DAY(created_at) as data,
                (
                    (
                        (SELECT COUNT(1) FROM nps WHERE nota > 8 AND date(created_at) <= LAST_DAY(nps_base.created_at) AND deleted_at IS NULL)
                        - 
                        (SELECT COUNT(1) FROM nps WHERE nota < 6 AND date(created_at) <= LAST_DAY(nps_base.created_at) AND deleted_at IS NULL)
                    )
                    / 
                    (SELECT COUNT(1) FROM nps WHERE deleted_at IS NULL AND date(created_at) <= LAST_DAY(nps_base.created_at))
                )
                * 100 as total
                FROM 
                    nps nps_base 
                WHERE
                    deleted_at IS NULL
                GROUP BY
                    YEAR(created_at), MONTH(created_at)
                ORDER BY
                    date(created_at) DESC');
    }

    public static function listarPorMes() {
        return DB::select("SELECT
                LAST_DAY(created_at) as data,
                (
                    (
                        (SELECT COUNT(1) FROM nps WHERE nota > 8 AND date(created_at) BETWEEN DATE_FORMAT(nps_base.created_at, '%Y-%m-01') AND LAST_DAY(nps_base.created_at) AND deleted_at IS NULL)
                        - 
                        (SELECT COUNT(1) FROM nps WHERE nota < 6 AND date(created_at) BETWEEN DATE_FORMAT(nps_base.created_at, '%Y-%m-01') AND LAST_DAY(nps_base.created_at) AND deleted_at IS NULL)
                    )
                    / 
                    (SELECT COUNT(1) FROM nps WHERE deleted_at IS NULL AND date(created_at) BETWEEN DATE_FORMAT(nps_base.created_at, '%Y-%m-01') AND LAST_DAY(nps_base.created_at))
                )
                * 100 as total
                FROM 
                    nps nps_base 
                WHERE
                    deleted_at IS NULL
                GROUP BY
                    YEAR(created_at), MONTH(created_at)
                ORDER BY
                    date(created_at) DESC");
    }
}
