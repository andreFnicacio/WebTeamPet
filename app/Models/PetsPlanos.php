<?php

namespace App\Models;

use App\Helpers\API\Superlogica\V2\Signature;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;


/**
 * Class PetsPlanos
 * @package App\Models
 *
 * @property Carbon $data_inicio_contrato
 * @property $data_encerramento_contrato
 * @property $id_pet
 * @property $id_plano
 * @property $valor_momento
 * @property Planos $plano
 * @property mixed id_primeira_cobranca_superlogica
 * @property int $id_contrato_superlogica
 * @property Carbon $synced_at;
 * @property int $financial_id
 */
class PetsPlanos extends Model
{
    const STATUS_PRIMEIRO_PLANO = "P";
    const STATUS_UPGRADE = "U";
    const STATUS_DOWNGRADE = "D";
    const STATUS_RENOVACAO = "R";
    const STATUS_ALTERACAO = "A";
    const STATUS_MIGRACAO = 'M';

    const STATUS = [
        self::STATUS_PRIMEIRO_PLANO => 'Primeiro Plano',
        self::STATUS_UPGRADE => 'Upgrade',
        self::STATUS_DOWNGRADE => 'Downgrade',
        self::STATUS_RENOVACAO => 'Renovação',
        self::STATUS_ALTERACAO => 'Alteração',
        self::STATUS_MIGRACAO => 'Migração'
    ];

    const STATUS_CORES = [
        self::STATUS_PRIMEIRO_PLANO => 'bg-yellow-saffron',
        self::STATUS_UPGRADE => 'bg-green-jungle',
        self::STATUS_DOWNGRADE => 'bg-red-pink',
        self::STATUS_RENOVACAO => 'bg-blue-sharp',
        self::STATUS_ALTERACAO => 'bg-purple-wisteria',
        self::STATUS_MIGRACAO => 'bg-grey-gallery'
    ];

    const TRANSICAO__NOVA_COMPRA = 'Nova compra do e-commerce';
    const TRANSICAO__RETORNO = 'Era cliente antigo que cancelou e retornou';
    const TRANSICAO__MIGRACAO = 'Cliente ativo que migrou de plano fora do mês de compra';
    const TRANSICAO__RENOVACAO = 'Cliente ativo que trocou de plano no ato da renovação';
    const TRANSICAO__MESMO_MES = 'Cliente novo que trocou de plano no mesmo mês que contratou outro plano (Menos de 30 dias)';
    const TRANSICAO__TRIAL = 'Cliente comum que ganhou algum benefício gratuito';
    const TRANSICAO__B2B_TRIAL = 'Cliente B2B que ganhou algum benefício gratuito';
    const TRANSICAO__B2B_DESCONTO = 'Cliente B2B que ganhou desconto fixo';
    const TRANSICAO__NOVA_COMPRA_MANUAL = 'Nova compra feita diretamente com o CX ou Inside Sales';

    const TRANSICOES = [
        self::TRANSICAO__NOVA_COMPRA_MANUAL,
        self::TRANSICAO__RETORNO,
        self::TRANSICAO__MIGRACAO,
        self::TRANSICAO__RENOVACAO,
        self::TRANSICAO__MESMO_MES,
        self::TRANSICAO__TRIAL,
        self::TRANSICAO__B2B_TRIAL,
        self::TRANSICAO__B2B_DESCONTO
    ];

    use SoftDeletes;

    public $table = 'pets_planos';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at','data_inicio_contrato', 'data_encerramento_contrato'];


    public $fillable = [
        'id_pet',
        'id_plano',
        'participativo',
        'valor_momento',
        'data_inicio_contrato',
        'data_encerramento_contrato',
        'id_vendedor',
        'status',
        'adesao',
        'desconto_folha',
        'id_conveniada',
        'transicao',
        'id_contrato_superlogica',
        'financial_id',
        'synced_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_pet' => 'integer',
        'id_plano' => 'integer',
        'valor_momento' => 'float',
        'data_inicio_contrato' => 'date',
        'data_encerramento_contrato' => 'date',
        'status' => 'string',
        'synced_at' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function setDataInicioContratoAttribute($value) {
        $this->attributes['data_inicio_contrato'] = \DateTime::createFromFormat('d/m/Y', $value);
    }

    public function setDataEncerramentoContratoAttribute($value) {
        if(empty($value)) {
            return $this->attributes['data_encerramento_contrato'] = null;
        }
        $this->attributes['data_encerramento_contrato'] = \DateTime::createFromFormat('d/m/Y', $value);
    }

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
    public function plano()
    {
        return $this->belongsTo(\App\Models\Planos::class, 'id_plano');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function vendedor()
    {
        return $this->belongsTo(\App\Models\Vendedores::class, 'id_vendedor')->first();
    }

    public static function totalPorDataInicioContrato() {
        return self::select('data_inicio_contrato', DB::raw('COUNT(1) as total'), DB::raw("SUM(CASE WHEN p.regime = 'MENSAL' then valor_momento else valor_momento/12 end) as valor"))
            ->from('pets_planos')
            ->join(DB::raw("(SELECT id_pet, MIN(id) id FROM pets_planos WHERE pets_planos.status = 'P' AND pets_planos.deleted_at IS NULL GROUP BY id_pet) t2"), function($join) {
                $join->on('pets_planos.id', '=', 't2.id');
                $join->on('pets_planos.id_pet', '=', 't2.id_pet');
            })
            ->leftJoin('pets as p', 't2.id_pet', '=', 'p.id');
    }

    public static function totalPorDataEncerramentoContrato() {
        return self::select('data_encerramento_contrato', DB::raw('COUNT(1) as total'), DB::raw("SUM(CASE WHEN p.regime = 'MENSAL' then valor_momento else valor_momento/12 end) as valor"))
            ->from('pets_planos')
            ->join(DB::raw('(SELECT id_pet, MAX(id) id FROM pets_planos WHERE pets_planos.deleted_at IS NULL GROUP BY id_pet) t2'), function($join) {
                $join->on('pets_planos.id', '=', 't2.id');
                $join->on('pets_planos.id_pet', '=', 't2.id_pet');
            })
            ->leftJoin('pets as p', 't2.id_pet', '=', 'p.id');
    }

    public static function totalCanceladosQuery() {
        return self::select(DB::raw('COUNT(1) as total'))
            ->from('pets_planos')
            ->join(DB::raw('(SELECT id_pet, MAX(id) id FROM pets_planos WHERE pets_planos.deleted_at IS NULL GROUP BY id_pet) t2'), function($join) {
                $join->on('pets_planos.id', '=', 't2.id');
                $join->on('pets_planos.id_pet', '=', 't2.id_pet');
            })
            ->join('cancelamentos as c', function($join) {
                $join->on('c.id_pet', '=', 'pets_planos.id_pet');
                $join->on('c.data_cancelamento', '=', 'pets_planos.data_encerramento_contrato');
            });
    }

    public static function totalAtivosQuery() {
        return self::select(DB::raw('COUNT(1) as total'))
            ->from('pets_planos')
            ->join(DB::raw("(SELECT id_pet, MAX(id) id FROM pets_planos WHERE pets_planos.deleted_at IS NULL GROUP BY id_pet) t2"), function($join) {
                $join->on('pets_planos.id', '=', 't2.id');
                $join->on('pets_planos.id_pet', '=', 't2.id_pet');
            })
            ->leftJoin('pets as p', 't2.id_pet', '=', 'p.id')
            ->where('p.ativo', '=', 1);
    }

    public static function totalPrimeiroPlanoQuery() {
        return self::select(DB::raw('COUNT(1) as total'))
        ->from('pets_planos')
        ->join(DB::raw("(SELECT id_pet, MIN(id) id FROM pets_planos WHERE pets_planos.deleted_at IS NULL GROUP BY id_pet) t2"), function($join) {
            $join->on('pets_planos.id', '=', 't2.id');
            $join->on('pets_planos.id_pet', '=', 't2.id_pet');
        })
        ->where('pets_planos.status', '=', 'P');
    }

    public static function totalUpgradesQuery() {
        return self::select(DB::raw('COUNT(1) as total'))
            ->from('pets_planos')
            ->where('pets_planos.status', '=', 'U');
    }

    public static function aniversarioPorPeriodoRawQuery($dtInicio = null, $dtFim = null) {

        if(!isset($dtInicio) && !isset($dtFim)) {
            throw new \Exception('aniversarioPorPeriodoRawQuery: É necessário passar como atributo uma data de início ou fim');
        }
            
        if(isset($dtInicio, $dtFim)) {
            return "
                DATE_FORMAT(data_inicio_contrato, '%m-%d')
                    BETWEEN DATE_FORMAT('{$dtInicio}', '%m-%d') AND DATE_FORMAT('{$dtFim}', '%m-%d')
                AND DATE_FORMAT(data_inicio_contrato, '%Y') < DATE_FORMAT('{$dtInicio}', '%Y')
            ";
        }

        if(isset($dtInicio)) {
            return "DATE_FORMAT(data_inicio_contrato, '%m-%d') <= DATE_FORMAT('{$dtInicio}', '%m-%d')
                        AND DATE_FORMAT(data_inicio_contrato, '%Y') < DATE_FORMAT('{$dtInicio}', '%Y')
            ";
        } 
            
        return "DATE_FORMAT(data_inicio_contrato, '%m-%d') <= DATE_FORMAT('{$dtFim}', '%m-%d')
                        AND DATE_FORMAT(data_inicio_contrato, '%Y') < DATE_FORMAT('{$dtFim}', '%Y')
                ";
        
    }

    public function atualizarSuperlogica()
    {
        $service = new Signature();
        $service->sync($this);
    }

    /**
     * Check if pet has a plan associated and if the plan is monthly
     *
     * @param int $petId
     * @return bool
     */
    public static function petHasPlan(int $petId)
    {
        return DB::table('pets_planos')
            ->join('pets', 'pets.id', '=','pets_planos.id_pet')
            ->where('id_pet', $petId)
            ->where('pets.regime', Pets::REGIME_MENSAL)
            ->count() > 0;
    }

    public static function getCurrentSubscription(int $petId): self
    {
        return self::where('id_pet', $petId)->orderBy('created_at', 'desc')->limit(1)->first();
    }
}