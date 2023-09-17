<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class TabelasReferencia extends Model
{
    use SoftDeletes;

    public $table = 'tabelas_referencia';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'nome',
        'tabela_base'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'nome' => 'string',
        'tabela_base' => 'boolean'
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
    public function clinicas()
    {
        return $this->hasMany(\Modules\Clinics\Entities\Clinicas::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function tabelasProcedimentos()
    {
        return $this->hasMany(\App\Models\TabelasProcedimentos::class, 'id_tabela_referencia');
    }

    public function valorProcedimento(Procedimentos $procedimento)
    {
        $tabela = $this->tabelasProcedimentos()->where('id_procedimento', $procedimento->id)->first();
        return $tabela ? $tabela->valor : null;
    }

    public function isBase()
    {
        return $this->tabela_base;
    }

    /**
     * @return self
     */
    public static function getTabelaBase()
    {
        return self::where('tabela_base', 1)->first();
    }


    public static function getValorBaseProcedimento(Procedimentos $procedimento)
    {
        $tabelaBase = self::getTabelaBase();
        return $tabelaBase->valorProcedimento($procedimento);
    }
}
