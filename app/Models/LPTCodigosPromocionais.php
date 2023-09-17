<?php

namespace App\Models;

use App\Helpers\Utils;
use App\LifepetCompraRapida;
use Carbon\Carbon;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use File;
use Response;


/**
 * @property mixed aplicabilidade
 */
class LPTCodigosPromocionais extends \Illuminate\Database\Eloquent\Model
{
    public $table = 'lpt__codigos_promocionais';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at', 'expira_em'];


    public $fillable = [
        'codigo',
        'expira_em',
        'desconto',
        'id_plano',
        'tipo_desconto',
        'permanente',
        'aplicabilidade'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'codigo' => 'string',
        'desconto' => 'float',
        'id_plano' => 'integer',
        'tipo_desconto' => 'string',
        'permanente' => 'boolean',
        'aplicabilidade' => 'string'
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
    public function plano()
    {
        return $this->belongsTo(\App\Models\Planos::class, 'id_plano');
    }

    public function setExpiraEmAttribute($value)
    {
        $this->attributes['expira_em'] = Carbon::createFromFormat(Utils::BRAZILIAN_DATE, $value);
    }

    public function valido()
    {
        return $this->expira_em->gte(Carbon::now());
    }

    public function aplicar($valor)
    {
        if(!$this->valido()) {
            return $valor;
        }

        if($this->tipo_desconto == 'percentual') {
            $desconto = $valor * $this->desconto/100;
            if($desconto > $valor) {
                return 0;
            }

            return ($valor - $desconto);
        } else {
            if($this->desconto > $valor) {
                return 0;
            }

            return $valor - $this->desconto;
        }
    }

    /**
     * Verifica se o cupom é válido para o regime da contratação.
     * @param $regime
     * @return bool
     */
    public function regimeAplicavel($regime): bool
    {
        $regime = strtoupper(substr($regime, 0, 1));
        if($this->aplicabilidade === 'T' || $this->aplicabilidade === $regime) {
            return true;
        }

        return false;
    }

    public function compras()
    {
        return $this->hasMany(LifepetCompraRapida::class, 'id_cupom');
    }

    public function getAplicabilidadeForHumansAttribute(): string
    {
        switch ($this->aplicabilidade) {
            case 'T':
                return 'Todos';
            case 'A':
                return 'Anual';
            case 'M':
                return 'Mensal';
            default:
                return 'T';
        }
    }

    public function documentos()
    {
        $documentos = DocumentosInternos::where('id_cupom', $this->id)->get();

        $uploads = Uploads::bindTable('documentos')->whereIn('binded_id', $documentos->pluck('id'))->orderBy('id', 'DESC')->get();
        $uploads->map(function($u) use ($documentos) {
            $u->documento = $documentos->where('id', '=', $u->binded_id)->first();
        });
        return $uploads;
    }

    public function getRegulamentoAttribute()
    {
        $documentos = $this->documentos();
        if(!$documentos) {
            return null;
        }

        return $documentos->first();
    }

    public function regulamento()
    {
        return $this->responseAsFile($this->regulamento, self::getRegulamentoFilename($this, $this->regulamento));
    }

    public function aditivo()
    {
        return $this->responseAsFile($this->aditivo, self::getAditivoFilename($this, $this->aditivo));
    }

    public static function getRegulamentoFilename(LPTCodigosPromocionais $cupom, Uploads $regulamento): string
    {
        return  join('', [
            'LIFEPET-REGULAMENTO-',
            strtoupper($cupom->codigo) . '-' . $cupom->id,
            '.',
            $regulamento->extension
        ]);
    }

    public static function getAditivoFilename(LPTCodigosPromocionais $cupom, Uploads $aditivo): string
    {
        return  join('', [
            'LIFEPET-ADITIVO-',
            strtoupper($cupom->codigo) . '-' . $cupom->id,
            '.',
            $aditivo->extension
        ]);
    }

    /**
     * @return mixed
     */
    public function responseAsFile(Uploads $upload, $downloadFilename)
    {
        if (!$upload) {
            abort(404);
        }

        $path = storage_path('app/' . $upload->path);
        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        $filename = $downloadFilename;

        $response->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }
}
