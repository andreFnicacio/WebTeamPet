<?php

namespace App\Models;

use Illuminate\Database\Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Clinics\Entities\Clinicas;
use Modules\Guides\Entities\HistoricoUso;


class Procedimentos extends Model\Model
{
    const CONSULTA_RETORNO = '10101012';
    const PERCENTUAL_EXCLUSIVIDADE = 0.1;
    use SoftDeletes;

    public $table = 'procedimentos';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];

    public static $gruposConsulta = [
        '10100',
        '99900'
    ];

    public static $gruposEspecialistas = [
        '10102'
    ];

    public static $gruposCirurgicos = [
        '10200',
        '99907',
        '99909',
        '99910',
        '10101027',
        '10101028',
        '10101029',
        '10101032',
        '10101033',
        '10101039',
        '10101030',
        '99911',
        '1010103',
        '10101050'
    ];

    public $fillable = [
        'cod_procedimento',
        'nome_procedimento',
        'especialista',
        'intervalo_usos',
        'valor_base',
        'id_grupo',
        'liberacao_automatica',
        'dados_adicionais',
        'ativo',
        'pre_cirurgico',
        'emergencial',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'cod_procedimento' => 'string',
        'nome_procedimento' => 'string',
        'especialista' => 'boolean',
        'intervalo_usos' => 'integer',
        'valor_base' => 'float',
        'id_grupo' => 'integer',
        'liberacao_automatica' => 'boolean',
        'ativo' => 'boolean',
        'pre_cirurgico' => 'boolean',
        'emergencial' => 'boolean',
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
    public function grupo()
    {
        return $this->belongsTo(\App\Models\Grupos::class, 'id_grupo');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function historicoUsos()
    {
        return $this->hasMany(\Modules\Guides\Entities\HistoricoUso::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function planosProcedimentos()
    {
        return $this->hasMany(\App\Models\PlanosProcedimentos::class, 'id_procedimento');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function tabelasProcedimentos()
    {
        return $this->hasMany(\App\Models\TabelasProcedimentos::class, 'id_procedimento');
    }

    public function salvo($id_tabela_referencia)
    {
        return \App\Models\TabelasProcedimentos::where([
            'id_procedimento' => $this->id,
            'id_tabela_referencia' => $id_tabela_referencia
        ])->first();
    }

    /**
     * Verifica se um procedimento deveria ser um retorno mas não é.
     */
    public function shouldBeRetorno(\App\Models\Pets $pet, \Modules\Clinics\Entities\Clinicas $clinica) {
        $dias = 30;
        if(in_array($this->id_grupo, static::$gruposConsulta)) {
            $dias = 30;
            $grupos = static::$gruposConsulta;
        } else if (in_array($this->id_grupo, static::$gruposEspecialistas)) {
            $dias = 90;
            $grupos = static::$gruposEspecialistas;
        } else {
            return false;
        }


        /**
         * @var $procedimentosConsulta array de ids dos procedimentos dos grupos
         */
        $procedimentos = \App\Models\Procedimentos::whereIn('id_grupo', $grupos)
                                                          ->get()
                                                          ->pluck('id');

        $query = \Modules\Guides\Entities\HistoricoUso::where('id_pet', $pet->id)
                                ->where('id_clinica', $clinica->id)
                                ->where('status', 'LIBERADO')
                                ->where('tipo_atendimento', HistoricoUso::TIPO_NORMAL)
                                ->orderBy('id', 'DESC');


        $queryUltimaConsulta = clone $query;
        $ultimaConsulta = $queryUltimaConsulta->whereIn('id_procedimento', $procedimentos)
                                              ->first();

        $queryUltimoRetorno = clone $query;
        $ultimoRetorno = $queryUltimoRetorno->where('id_procedimento', self::CONSULTA_RETORNO)
                                            ->first();

        if(!$ultimaConsulta) {
            return false;
        }

        if($ultimoRetorno) {
            /*
             * A última consulta já teve um retorno subsequente
             */
            if($ultimoRetorno->created_at->gte($ultimaConsulta->created_at)) {
                return false;
            }
        }
        
        if(!empty($ultimaConsulta)) {
            return $ultimaConsulta->created_at->diffInDays(new \Carbon\Carbon()) < $dias;
        } 

        return false;
    }

    public static function byHistoricoUso(HistoricoUso $historicoUso)
    {
        return self::whereIn('id', HistoricoUso::where('numero_guia', $historicoUso->numero_guia)
                                           ->groupBy('id_procedimento')->pluck('id_procedimento'))->get();

    }

    public function isConsulta()
    {
        return in_array($this->id_grupo, static::$gruposConsulta);
    }

    public function isRetornoConsulta() {
        return $this->id == self::CONSULTA_RETORNO;
    }

    public static function adicionalExclusividade(Procedimentos $procedimento, Clinicas $clinica)
    {
        if(!$clinica->percentual_exclusividade) {
            return 0;
        }

        if($procedimento->isConsulta()) {
            return self::PERCENTUAL_EXCLUSIVIDADE;
        }

        return 0;
    }

    public function planos()
    {
        return $this->belongsToMany(Planos::class, 'planos_procedimentos', 'id_procedimento', 'id_plano')->withPivot();
    }

    public function valorParticipacao(Planos $plano) {
      
        if($plano->participativo) {
            return PlanosProcedimentos::getValorBeneficio($this, $plano);
        } else {
            return PlanosProcedimentos::getValorCliente($this, $plano);
        }
        
        return 0;
    }

    public function isCirurgia()
    {
        return in_array($this->id_grupo, self::$gruposCirurgicos);
    }

    public function isAnestesia()
    {
        return in_array($this->id_grupo, [11300, 10101026]);
    }

    public function isInternacao()
    {
        return in_array($this->id_grupo, [10101048, 10101037, 99920, 99917, 99914, 20100]);
    }
}