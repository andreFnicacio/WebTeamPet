<?php

namespace App\Models;

use App\Helpers\Utils;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Guides\Entities\HistoricoUso;


class Participacao extends Model
{

    public $table = 'participacao';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const FORMATO_COMPETENCIA = 'Y-m';

    protected $dates = ['deleted_at', 'agendado', 'executado'];


    public $fillable = [
        'id_historico_uso',
        'id_cliente',
        'id_pet',
        'vigencia_inicio',
        'vigencia_fim',
        'competencia',
        'valor_participacao',
        'id_guia',
        'agendado',
        'executado'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_historico_uso' => 'integer',
        'id_cliente' => 'integer',
        'id_pet' => 'integer',
        'id_externo' => 'integer',
        'valor_participacao' => 'float',
        'vigencia_inicio' => 'datetime',
        'vigencia_fim' => 'datetime',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];


    public function cobrar($forcar = false) {

        if(!$forcar) {
            if($this->agendado && $this->agendado->gt(Carbon::now())) {
                return false;
            }
        }

        /**
         * @var Clientes $cliente
         */
        $cliente = $this->cliente()->first();

        /**
         * @var Pets $pet
         */
        $pet = $this->pet()->first();

        $historicoUso = $this->historicoUso;
        $now = Carbon::now()->format(Utils::BRAZILIAN_DATE);

        $complemento = $this->historicoUso()->first()->procedimento()->first()->nome_procedimento;
        $chave = 'PARTICIPAÇÃO - ' . $pet->nome_pet . ' - ' . $complemento .  " - GUIA #{$historicoUso->numero_guia} - $now";

        $atualizado = $pet->atualizarFaturaAberta($chave, $this->valor_participacao, $this->competencia);

        if($atualizado) {
            $this->update([
                'executado' => Carbon::now()
            ]);

            return true;
        }
    }

    /**
     * @return BelongsTo
     */
    public function cliente() {
        return $this->belongsTo(\App\Models\Clientes::class, 'id_cliente');
    }

    /**
     * @return BelongsTo
     */
    public function pet() {
        return $this->belongsTo(\App\Models\Pets::class, 'id_pet');
    }

    /**
     * @return BelongsTo
     */
    public function historicoUso() {
        return $this->belongsTo(\Modules\Guides\Entities\HistoricoUso::class, 'id_historico_uso');
    }

    public static function competencia()
    {
        return (new Carbon())->format('Y-m');
    }

    public static function participado($id_pet)
    {
        $pet = Pets::findOrFail($id_pet);
        $vigencias = $pet->vigencias(false);
        $vigenciaInicio = $vigencias[0]->format('Y-m-d');
        $vigenciaFim = $vigencias[1]->format('Y-m-d');
        return self::where('id_pet', $pet->id)
            ->where('vigencia_inicio', $vigenciaInicio)
            ->where('vigencia_fim', $vigenciaFim)
            ->groupBy('competencia')
            ->orderBy("competencia")
            ->sum('valor_participacao');
    }

    public static function participadoTotal($id_pet, $outrasVigencias = [])
    {
        $pet = Pets::findOrFail($id_pet);
        $vigencias = $pet->vigencias(false);
        $vigenciaInicio = isset($outrasVigencias[0]) ? $outrasVigencias[0] : $vigencias[0]->format('Y-m-d');
        $vigenciaFim =  isset($outrasVigencias[1]) ? $outrasVigencias[1] : $vigencias[1]->format('Y-m-d');
        
        return self::where('id_pet', $pet->id)
            ->where('vigencia_inicio', '>=', $vigenciaInicio)
            ->where('vigencia_fim', '<=', $vigenciaFim)
            ->groupBy('id_pet')
            ->sum('valor_participacao');
    }

    public static function participacaoMes($id_pet, $competencia = null) {
        $pet = Pets::findOrFail($id_pet);
        $vigencias = $pet->vigencias(false);

        if(!$competencia) {
            $competencia = self::competencia();
        }

        $vigenciaInicio = $vigencias[0]->format('Y-m-d');
        $vigenciaFim = $vigencias[1]->format('Y-m-d');

        return self::where('id_pet', $pet->id)
            ->where('vigencia_inicio', $vigenciaInicio)
            ->where('vigencia_fim', $vigenciaFim)
            ->where('competencia', $competencia)
            ->groupBy('competencia')
            ->orderBy("competencia")
            ->sum('valor_participacao');
    }

    public static function competenciaLivre($id_pet)
    {
        $pet = Pets::findOrFail($id_pet);
        $vigencias = $pet->vigencias(false);
        $formatoCompetencia = self::FORMATO_COMPETENCIA;

        $vigenciaInicio = $vigencias[0]->format('Y-m-d');
        $vigenciaFim = $vigencias[1]->format('Y-m-d');

        $competencias = self::where('id_pet', $id_pet)
                            ->where('vigencia_inicio', $vigenciaInicio)
                            ->where('vigencia_fim', $vigenciaFim)
                            ->groupBy('competencia')
                            ->orderBy("competencia")
                            ->selectRaw("SUM(valor_participacao) as participacao, competencia")
                            ->get();
        $competenciaAtual = (new Carbon())->format($formatoCompetencia);
        $ultimaParticipacao = $competencias->last();
        if(!$ultimaParticipacao) {
            $ultimaParticipacao = new self();
            $ultimaParticipacao->competencia = (new Carbon())->format($formatoCompetencia);
        }

        $dataUltimaCompetencia = Carbon::createFromFormat($formatoCompetencia, $ultimaParticipacao->competencia);
        $tetoMensal = $pet->plano()->teto_participativo / 10;

        if((!$competencias || $dataUltimaCompetencia->lt(new Carbon())) && $ultimaParticipacao->participacao < $tetoMensal) {
            return [
                "competencia" => $competenciaAtual,
                "participacao" => self::participacaoMes($id_pet)
            ];
        }

        if($ultimaParticipacao->participacao == $tetoMensal) {
            return [
                "competencia" => Carbon::createFromFormat($formatoCompetencia, $ultimaParticipacao->competencia)->addMonth()->format($formatoCompetencia),
                "participacao" => 0
            ];
        } else {
            return [
                "competencia" => $ultimaParticipacao->competencia,
                "participacao" => $ultimaParticipacao->participacao
            ];
        }
    }

    public static function participacaoDisponivelMes($id_pet, $competencia = null) {
        $pet = Pets::findOrFail($id_pet);
        $plano = $pet->plano();
        $teto = $plano->teto_participativo / 10;
        $participado = self::participacaoMes($id_pet, $competencia);
        $remanescente = $teto - $participado;
        return $remanescente <= 0 ? 0 : $remanescente;
    }

    public static function participacaoPossivel($id_pet) {
        $pet = Pets::findOrFail($id_pet);
        $vigencias = $pet->vigencias();
        $plano = $pet->plano();
        $vigenciaInicio = $vigencias[0]->format('Y-m-d');
        $vigenciaFim = $vigencias[1]->format('Y-m-d');

        $participacao = self::where('id_pet', $id_pet)
            ->where('vigencia_inicio', $vigenciaInicio)
            ->where('vigencia_fim', $vigenciaFim)
            ->orderBy("competencia")
            ->sum("valor_participacao");
        $restante = $plano->teto_participativo - $participacao;
        return  $restante <= 0 ? 0 : $restante;
    }

    public static function participadoGuia(HistoricoUso $guia) {
        return self::where('id_historico_uso', $guia->id)->sum('valor_participacao');
    }

    public static function participadoPlano($idPlano, $start, $end)
    {
        $plano = Planos::find($idPlano);
        if(!$plano) {
            return 0;
        }

        return Participacao::query()->join('historico_uso', 'id_historico_uso', '=', 'historico_uso.id')
            ->where('historico_uso.id_plano', '=', $plano->id)
            ->whereBetween('historico_uso.created_at', [$start, $end])
            ->sum('participacao.valor_participacao');
    }
}
