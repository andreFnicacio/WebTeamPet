<?php

namespace App\Models;

use App\Curl;
use App\Http\Controllers\DashboardController;
use App\Http\Util\Superlogica\Request;
use Carbon\Carbon;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class DadosTemporais extends Model
{
    //use SoftDeletes;

    const TIPO_NUMERO = 'numero';
    const TIPO_TEXTO = 'texto';


    public $table = 'dados_temporais';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at', 'data_referencia'];


    public $fillable = [
        'indicador',
        'tipo_valor',
        'valor_numerico',
        'valor_textual',
        'data_referencia'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'indicador' => 'string',
        'tipo_valor' => 'string',
        'valor_numerico' => 'float',
        'valor_textual' => 'string',
        'data_referencia' => 'date'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function format() {
        $formatted = new \stdClass();
        $formatted->valor = $this->tipo_valor == self::TIPO_NUMERO ? $this->valor_numerico : $this->valor_textual;
        $formatted->nome = $this->data_referencia->format('d/m/Y');
        return $formatted;
    }

    public static function registrarCancelamentos($dataReferencia = null)
    {
        $r = new \Illuminate\Http\Request();
        $c = new DashboardController();
        $cancelamentos = $c->cancelamentos($r, false)['value'];

        return self::create([
            'indicador' => 'cancelamentos',
            'tipo_valor' => 'numero',
            'data_referencia' => $dataReferencia ? $dataReferencia : (new Carbon()),
            'valor_numerico' => $cancelamentos,
        ]);
    }

    public static function registrarVidasAtivas($dataReferencia = null)
    {
        $r = new \Illuminate\Http\Request();
        $c = new DashboardController();
        $vidasAtivas = $c->vidasAtivas($r, false)['value'];

        return self::create([
            'indicador' => 'vidas_ativas',
            'tipo_valor' => 'numero',
            'data_referencia' => $dataReferencia ? $dataReferencia : (new Carbon()),
            'valor_numerico' => $vidasAtivas,
        ]);
    }

    public static function registrarVidasInativas($dataReferencia = null)
    {
        $r = new \Illuminate\Http\Request();
        $c = new DashboardController();
        $vidasInativas = $c->vidasInativas($r, false)['value'];

        return self::create([
            'indicador' => 'vidas_inativas',
            'tipo_valor' => 'numero',
            'data_referencia' => $dataReferencia ? $dataReferencia : (new Carbon()),
            'valor_numerico' => $vidasInativas,
        ]);
    }

    public static function registrarVidasMensais($dataReferencia = null)
    {
        $r = new \Illuminate\Http\Request();
        $c = new DashboardController();
        $vidasAtivas = $c->vidasAtivasMensais($r, false)['value'];

        return self::create([
            'indicador' => 'vidas_ativas_mensais',
            'tipo_valor' => 'numero',
            'data_referencia' => $dataReferencia ? $dataReferencia : (new Carbon()),
            'valor_numerico' => $vidasAtivas,
        ]);
    }

    public static function registrarVidasAnuais($dataReferencia = null)
    {
        $r = new \Illuminate\Http\Request();
        $c = new DashboardController();
        $vidasAtivas = $c->vidasAtivasAnuais($r, false)['value'];

        return self::create([
            'indicador' => 'vidas_ativas_anuais',
            'tipo_valor' => 'numero',
            'data_referencia' => $dataReferencia ? $dataReferencia : (new Carbon()),
            'valor_numerico' => $vidasAtivas,
        ]);
    }

    public static function registrarNPS()
    {
        $r = new \Illuminate\Http\Request();
        $c = new DashboardController();
        $nps = $c->npsSurveyMonkey($r)['value'];

        return self::create([
            'indicador' => 'nps',
            'tipo_valor' => 'numero',
            'data_referencia' => (new Carbon()),
            'valor_numerico' => $nps,
        ]);

    }

    public static function registrarSinistralidadeMensal()
    {
        $r = new \Illuminate\Http\Request();
        $c = new DashboardController();
        $value = $c->sinistralidadeMensal($r);

        return self::create([
            'indicador' => 'sinistralidade',
            'tipo_valor' => 'numero',
            'data_referencia' => (new Carbon()),
            'valor_numerico' => $value['rawValue'],
            'valor_textual' => $value['value'],
        ]);
    }

    public static function registrarVendas()
    {
        $r = new \Illuminate\Http\Request();
        $c = new DashboardController();
        $value = $c->vendas($r)['value'];

        return self::create([
            'indicador' => 'vendas',
            'tipo_valor' => 'numero',
            'data_referencia' => (new Carbon()),
            'valor_numerico' => $value,
        ]);
    }

    public static function registrarAtrasoMensal()
    {
        $r = new \Illuminate\Http\Request();
        $c = new DashboardController();
        $value = $c->atrasoMensal($r);

        return self::create([
            'indicador' => 'inadimplencia',
            'tipo_valor' => 'numero',
            'data_referencia' => (new Carbon()),
            'valor_numerico' => $value['rawValue'],
            'valor_textual' => $value['value'],
        ]);
    }

    public static function registrarFaturamentoMensal()
    {
        $r = new \Illuminate\Http\Request();
        $c = new DashboardController();
        $value = $c->faturamentoMensal($r);

        return self::create([
            'indicador' => 'faturamento_mensal',
            'tipo_valor' => 'numero',
            'data_referencia' => (new Carbon()),
            'valor_numerico' => $value['rawValue'],
            'valor_textual' => $value['value'],
        ]);
    }

    public static function registrarMRM()
    {
        $r = new \Illuminate\Http\Request();
        $c = new DashboardController();
        $value = $c->mediaRecorrenteMensal($r);

        return self::create([
            'indicador' => 'media_recorrente_mensal',
            'tipo_valor' => 'numero',
            'data_referencia' => (new Carbon()),
            'valor_numerico' => number_format($value['rawValue'], 2, '.', ''),
            'valor_textual' => $value['value'],
        ]);
    }

    public static function registrarFaturamentoMensalPrevisto()
    {
        $r = new \Illuminate\Http\Request();
        $c = new DashboardController();
        $value = $c->faturamentoMensalPrevisto($r);

        return self::create([
            'indicador' => 'media_recorrente_mensal',
            'tipo_valor' => 'numero',
            'data_referencia' => (new Carbon()),
            'valor_numerico' => number_format($value['rawValue'], 2, '.', ''),
            'valor_textual' => $value['value'],
        ]);
    }

    public static function getVidasAtivas(Carbon $dataReferencia)
    {
        $result = self::where('indicador', 'vidas_ativas')
            ->where('data_referencia', $dataReferencia->format('Y-m-d'))->first();
        if($result) {
            return $result->format();
        }
    }

    public static function getNps(Carbon $dataReferencia)
    {
        $result = self::where('indicador', 'nps')
            ->where('data_referencia', $dataReferencia->format('Y-m-d'))->first();
        if($result && $result['valor_numerico'] > 0) {
            return $result['valor_numerico'];
        }
        return "Indefinido";
    }

    public static function registrarRentabilididadeMensalDosPlanos(Carbon $dataReferencia = null)
    {
        if(!Carbon::today()->isLastOfMonth()) {
            return;
        }

        $r = new \Illuminate\Http\Request();
        $today = $dataReferencia ?: Carbon::today();
        $r->merge([
            "start" => $today->startOfMonth()->format('d/m/Y'),
            "end"   => $today->endOfMonth()->format('d/m/Y'),
        ]);
        $c = new DashboardController();
        $value = $c->rentabilidadeDePlano($r, false);

        return self::create([
            'indicador' => 'rentabilidade_mensal_planos',
            'tipo_valor' => self::TIPO_TEXTO,
            'data_referencia' => $today,
            'valor_numerico' => 0,
            'valor_textual' =>  serialize($value)
        ]);
    }

    public static function registrarSuperlogicaCobrancas($indicador, $dados = [], $campoSoma = 'vl_emitido_recb') {
                
        $curl = new \App\Helpers\API\Superlogica\Curl();
        $url = 'https://api.superlogica.net/v2/financeiro/cobranca?' . http_build_query($dados);

        $curl->getDefaults($url);
        $response = $curl->execute();
        $curl->close();

        $valor = collect($response)->sum($campoSoma);

        return self::create([
            'indicador' => $indicador,
            'tipo_valor' => self::TIPO_NUMERO,
            'data_referencia' => Carbon::today(),
            'valor_numerico' => round($valor, 2),
            'valor_textual' =>  null
        ]);

    }

    public static function registrarSuperlogicaFinanceiro() {
        self::registrarSuperlogicaCobrancas('superlogica_total_faturado', [
            'apenasColunasPrincipais' => '1',
            'dtInicio' => (new \Carbon\Carbon())->today()->startOfMonth()->format('m/d/Y'),
            'dtFim' => (new \Carbon\Carbon())->today()->endOfMonth()->format('m/d/Y'),
        ], 'vl_emitido_recb');
        self::registrarSuperlogicaCobrancas('superlogica_total_liquidado', [
            'apenasColunasPrincipais' => '1',
            'filtrarpor' => 'liquidacao',
        ], 'vl_total_recb');
        self::registrarSuperlogicaCobrancas('superlogica_total_creditado', [
            'apenasColunasPrincipais' => '1',
            'filtrarpor' => 'credito',
        ], 'vl_valorcreditado_calc');
        self::registrarSuperlogicaCobrancas('superlogica_total_vencer', [
            'apenasColunasPrincipais' => '1',
            'status' => 'pendentes',
            'filtrarPor' => 'competencia',
            'dtInicio' => (new \Carbon\Carbon())->today()->format('m/d/Y'),
            'dtFim' => (new \Carbon\Carbon())->today()->endOfMonth()->format('m/d/Y'),
        ], 'vl_emitido_recb');
        self::registrarSuperlogicaCobrancas('superlogica_total_atrasado', [
            'apenasColunasPrincipais' => '1',
            'status' => 'pendentes',
            'filtrarPor' => 'competencia',
            'dtInicio' => (new \Carbon\Carbon())->today()->startOfMonth()->format('m/d/Y'),
            'dtFim' => (new \Carbon\Carbon())->today()->subDay()->format('m/d/Y'),
        ], 'vl_total_recb');
        self::registrarSuperlogicaCobrancas('superlogica_total_atrasado_acumulado', [
            'apenasColunasPrincipais' => '1',
            'status' => 'pendentes',
            'dtInicio' => (new \Carbon\Carbon())->today()->startOfDecade()->format('m/d/Y'),
            'dtFim' => (new \Carbon\Carbon())->today()->subDay()->format('m/d/Y'),
        ], 'vl_total_recb');
        return ['status' => 'ok'];
    }

    public static function registrarLeadsLPT($leads)
    {
        return self::create([
            'indicador' => 'leadsLPT',
            'tipo_valor' => 'numero',
            'data_referencia' => (new Carbon()),
            'valor_numerico' => $leads,
        ]);
    }

    public function scopeLeads($query)
    {
        return $query->where('indicador', 'leadsLPT')
                     ->groupBy(DB::raw('DATE_FORMAT(`data_referencia`,\'%Y-%m\')'))
                     ->select(DB::raw('SUM(valor_numerico) as valor, DATE_FORMAT(`data_referencia`,\'%Y-%m\') as competencia'));
    }

    public function scopeMidia($query)
    {
        return $query->where('indicador', 'midiaLPT')
            ->groupBy(DB::raw('DATE_FORMAT(`data_referencia`,\'%Y-%m\')'))
            ->select(DB::raw('SUM(valor_numerico) as valor, DATE_FORMAT(`data_referencia`,\'%Y-%m\') as competencia'));
    }

    public function scopeNps($query)
    {
        return $query->where('indicador', 'npsLPT')->orderBy('data_referencia', 'DESC');
    }

    public function scopeMeta($query, $dataReferencia = null)
    {
        if(!$dataReferencia) {
            $dataReferencia = Carbon::now();
        }
        return $query->where('indicador', 'metaLPT')
                     ->whereMonth('data_referencia', $dataReferencia->month)
                     ->orderBy('data_referencia', 'DESC');
    }
}