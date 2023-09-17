<?php

namespace App\Models;

use App\Helpers\Permissions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Modules\Clinics\Entities\Clinicas;
use Modules\Guides\Entities\HistoricoUso;

/**
 * Class Planos
 * @package App\Models
 * @property string $nome_plano
 * @property string $display_name
 * @property float $preco_plano_familiar
 * @property float $preco_plano_individual
 * @property Carbon $data_vigencia
 * @property Carbon $data_inatividade
 * @property boolean $ativo
 * @property boolean $bichos
 * @property Carbon $created_at
 * @property Carbon $deleted_at
 * @property int $id_superlogica
 * @property int $id_superlogica_anual
 * @property string $imagem_carencias
 * @property float $teto_participativo
 * @property float $pontos
 * @property float $preco_participativo
 * @property int $id_faixa
 * @property FaixasPlanos $faixa
 * @property boolean $isento
 * @property boolean $participativo
 * @property boolean $aplicar_intervalo_usos
 * @property boolean $lpt
 */
class Planos extends Model\Model
{
    use SoftDeletes;

    const PLANOS_PARA_TODOS = [72, 71, 70, 64, 61, 59, 58, 56, 55, 52];

    public $table = 'planos';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at', 'data_vigencia', 'data_inatividade'];

    public $imagemCarteirinhas = [
        43 => 'red', //Plano FREE BRASIL
        42 => 'red', //Plano FREE
        41 => 'orange', //Essencial Teste
        40 => 'purple', //PLANO SÊNIOR
        39 => 'black', //PLANO BLACK
        38 => 'silver', //PLANO PLATINUM
        37 => 'orange', //PLANO ESSENCIAL
        36 => 'blue', //PLANO BÁSICO
        35 => 'orange', //FIDELIDADE TAB7
        33 => 'orange', //PLANO FÁCIL
        32 => 'orange', //FÁCIL PARTICIPATIVO
        31 => 'purple', //SENIOR TAB6
        30 => 'black', //BLACK TAB6
        29 => 'silver', //PLATINUM TAB6
        28 => 'orange', //FIDELIDADE TAB6
        27 => 'blue', //BASICO TAB6
        26 => 'blue', //PINSCHER BICHOS
        25 => 'blue', //COCKER BICHOS
        24 => 'silver', //SÃO BERNADO BICHOS
        23 => 'blue', //ESPECIAL BICHOS
        22 => 'blue', //BÁSICO BICHOS
        21 => 'purple', //SENIOR TAB5
        20 => 'black', //BLACK TAB5
        19 => 'silver', //PLATINUM TAB5
        18 => 'orange', //FIDELIDADE TAB5
        17 => 'blue', //BASICO TAB5
        16 => 'black', //PREMIUM TAB4
        15 => 'silver', //PLATINUM TAB4
        14 => 'gold', //GOLD TAB4
        13 => 'silver', //SILVER TAB4
        12 => 'black', //PREMIUM TAB3
        11 => 'silver', //PLATINUM TAB3
        10 => 'gold', //GOLD TAB3
        9 => 'silver', //SILVER TAB3
        8 => 'black', //PREMIUM TAB2
        7 => 'silver', //PLATINUM TAB2
        6 => 'gold', //GOLD TAB2
        5 => 'silver', //SILVER TAB2
        4 => 'black', //PREMIUM TAB1
        3 => 'silver', //PLATINUM TAB1
        2 => 'gold', //GOLD TAB1
        1 => 'silver', //SILVER TAB1
    ];

    public $fillable = [
        'nome_plano',
        'preco_plano_familiar',
        'preco_plano_individual',
        'data_vigencia',
        'data_inatividade',
        'ativo',
        'bichos',
        'teto_participativo',
        'pontos',
        'id_faixa',
        'isento',
        'participativo',
        'financial_plan_monthly_id',
        'financial_plan_annual_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'nome_plano' => 'string',
        'preco_plano_familiar' => 'float',
        'preco_plano_individual' => 'float',
        'data_vigencia' => 'date',
        'data_inatividade' => 'date',
        'ativo' => 'boolean',
        'bichos' => 'boolean',
        'teto_participativo' => 'double',
        'isento' => 'boolean',
        'participativo' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function setDataVigenciaAttribute($value) {
        $this->attributes['data_vigencia'] = \Carbon\Carbon::createFromFormat('d/m/Y', $value);
    }

    public function setDataInatividadeAttribute($value) {
        if ($value) {
            $this->attributes['data_inatividade'] = \Carbon\Carbon::createFromFormat('d/m/Y', $value);
        }
    }

    // public function getDataVigenciaAttribute() 
    // {
        
    // }

    public function getValorFaixaAttribute()
    {
        if(!$this->faixa) {
            return 0;
        }

        return $this->faixa->valor + 0;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function historicoUsos()
    {
        return $this->hasMany(\Modules\Guides\Entities\HistoricoUso::class, 'id_plano');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function petsPlanos()
    {
        return $this->hasMany(\App\Models\PetsPlanos::class, 'id_plano');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function planosGrupos()
    {
        return $this->hasMany(\App\Models\PlanosGrupos::class, 'plano_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function planosProcedimentos()
    {
        return $this->hasMany(\App\Models\PlanosProcedimentos::class, 'id_plano');
    }

    public function procedimentoCoberto(\App\Models\Procedimentos $procedimento)
    {
        return $this->planosProcedimentos()->where('id_procedimento', $procedimento->id)->exists();
    }

    public function procedimentos()
    {
        return $this->belongsToMany(Procedimentos::class, 'planos_procedimentos', 'id_plano', 'id_procedimento')
                ->whereNull('planos_procedimentos.deleted_at')
                ->withPivot([
                    'observacao',
                    'beneficio_tipo',
                    'beneficio_valor'
                ]);
    }

    public function procedimentosPorGrupo(\App\Models\Grupos $grupo)
    {
        return DB::table('planos_procedimentos')
                ->join('procedimentos', 'procedimentos.id', '=', 'planos_procedimentos.id_procedimento')
                ->join('grupos_carencias', 'grupos_carencias.id', '=', 'procedimentos.id_grupo')
                ->where('grupos_carencias.id', $grupo->id)
                ->where('planos_procedimentos.id_plano', $this->id)
                ->where('planos_procedimentos.deleted_at', null)
                ->select('planos_procedimentos.*')
                ->get();
    }

    public function documentosPublicos()
    {
        $documentos = \App\Models\DocumentosInternos::where('id_plano', $this->id)->get();
        $uploads = \App\Models\Uploads::whereIn('binded_id',$documentos->pluck('id'))
                                      ->where('bind_with', 'documentos')
                                      ->where('public', 1);

        return $uploads;
    }

    public function credenciados()
    {
        return $this->belongsToMany(\Modules\Clinics\Entities\Clinicas::class, 'planos_credenciados', 'id_plano', 'id_clinica')
                    ->wherePivot('habilitado', 1);
    }

    public function faixa()
    {
        return $this->belongsTo(\App\Models\FaixasPlanos::class, 'id_faixa', 'id');
    }

    public function configuracao()
    {
        return $this->hasOne(PlanoConfiguracao::class, 'id_plano');
    }

    /**
     * Busca todos os pagamentos no período de clientes com o plano
     * @param Carbon $start
     * @param Carbon $end
     */
    public function recebimentos()
    {
        $pets = Pets::allByPlano($this);

        return $pets->sum(function($pet) {
            if($pet->regime === Pets::REGIME_MENSAL) {
                return $pet->valor;
            }

            return $pet->valor/12;
        });
    }

    public function sinistralidade(Carbon $start, Carbon $end)
    {
        return HistoricoUso::where(function($query) use ($start, $end) {
            $query->where(function($query) use ($start, $end) {
                $query->where('tipo_atendimento', "!=", HistoricoUso::TIPO_ENCAMINHAMENTO)
                    ->whereBetween('created_at', [$start, $end]);
            });
            $query->orWhere(function($query) use ($start, $end) {
                $query->where('tipo_atendimento', HistoricoUso::TIPO_ENCAMINHAMENTO)
                    ->whereBetween('realizado_em', [$start, $end]);
            });
        })->where('id_plano', $this->id)
            ->where('status', '=', HistoricoUso::STATUS_LIBERADO)
            ->groupBy('id_plano')
            ->sum('valor_momento');
    }

    public function getImagemCarteirinha(){
        $carteirinha = isset($this->imagemCarteirinhas[$this->id]) ? $this->imagemCarteirinhas[$this->id] : 'blue';
        return url('/') . "/assets/images/carteirinhas/{$carteirinha}.png";
    }

    /**
     * @param Clinicas $clinica
     * @return bool
     */
    public function isHabilitadoParaClinica(Clinicas $clinica = null) {
        /**
         * Em caso de ser um usuário sem clínica porém administrador, habilita qualquer clínica
         */
        if(Permissions::podeEmitirGuiaAdministrativa()) {
            return true;
        }

        if(!$clinica) {
            return false;
        }

        return (new PlanosCredenciados())->where('id_plano', $this->id)
            ->where('id_clinica', $clinica->id)
            ->where('habilitado', 1)
            ->exists();
    }

    public function aplicarIntervaloDeUsos()
    {
        return $this->aplicar_intervalo_usos;
    }

    public function documentos() {
        $documentos = DocumentosInternos::where('id_plano', $this->id)->get();

        $uploads = Uploads::bindTable('documentos')->whereIn('binded_id', $documentos->pluck('id'))->orderBy('id', 'DESC')->get();
        $uploads->map(function($u) use ($documentos) {
           $u->documento = $documentos->where('id', '=', $u->binded_id)->first();
        });
        return $uploads;
    }

    public function getTabelaAttribute() {
        $documento = DocumentosInternos::where('id_plano', $this->id)->where('tipo', DocumentosInternos::DOCUMENTO_TABELA)->orderBy('id', 'DESC')->first();
        if(!$documento) {
            return null;
        }

        $upload = Uploads::bindTable('documentos')->where('binded_id', $documento->id)->orderBy('id', 'DESC')->first();
        return $upload;
    }

    public function getContratoAttribute()
    {
        $documento = DocumentosInternos::where('id_plano', $this->id)->where('tipo', DocumentosInternos::DOCUMENTO_CONTRATO)->orderBy('id', 'DESC')->first();
        if(!$documento) {
            return null;
        }

        $upload = Uploads::bindTable('documentos')->where('binded_id', $documento->id)->orderBy('id', 'DESC')->first();
        return $upload;
    }

    public function scopeLpt($query)
    {
        return $query->where('lpt', '=', 1);
    }
}