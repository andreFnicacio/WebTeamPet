<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class DocumentosInternos extends Model
{
   //use SoftDeletes;

    public $table = 'documentos_internos';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DOCUMENTO_TABELA = 'TABELA';
    const DOCUMENTO_CONTRATO = 'CONTRATO';
    const DOCUMENTO_REGULAMENTO = 'REGULAMENTO';
    const DOCUMENTO_ADITIVO = 'ADITIVO';
    const DOCUMENTO_OUTROS = 'OUTROS';
    const TIPOS = [
        self::DOCUMENTO_TABELA, self::DOCUMENTO_CONTRATO, self::DOCUMENTO_REGULAMENTO, self::DOCUMENTO_ADITIVO, self::DOCUMENTO_OUTROS
    ];


    //protected $dates = ['deleted_at'];


    public $fillable = [
        'tipo',
        'id_plano',
        'id_cupom'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function plano()
    {
        return $this->belongsTo(\App\Models\Planos::class, 'id_plano', 'id');
    }

    public function cupom()
    {
        return $this->belongsTo(\App\Models\LPTCodigosPromocionais::class, 'id_cupom', 'id');
    }

    public function getRegulamentoAttribute()
    {
        return Uploads::bindTable('lpt__codigos_promocionais')->bindId($this->id)->orderBy('id','DESC')->first();
    }

    public function getFileAttribute()
    {
        return Uploads::bindTable('documentos')->bindId($this->id)->first();
    }
}
