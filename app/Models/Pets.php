<?php

namespace App\Models;

use App\Helpers\API\Financeiro\Financeiro;
use App\Helpers\API\RDStation\Services\RDRenovacaoConfirmadaService;
use App\Helpers\API\Superlogica\V2\Domain\Models\CreditCard;
use App\Helpers\API\Superlogica\V2\Signature;
use App\Http\Util\LogEvent;
use App\Http\Util\Logger;
use App\Http\Util\LogPriority;
use Carbon\Carbon;
use Entrust;
use Illuminate\Database\Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Guides\Entities\HistoricoUso;

/**
 * Class Pets
 * @package App\Models
 * @property Clientes $cliente
 * @property $nome_pet
 * @property $participativo
 * @property $regime
 * @property $mes_reajuste
 */
class Pets extends Model\Model
{
    use SoftDeletes;

    public $table = 'pets';

    protected $dates = ['deleted_at', 'data_nascimento'];

    public static $placeholders = [
        'CACHORRO' => '/assets/layouts/layout2/img/placeholder_cao.jpg',
        'GATO' => '/assets/layouts/layout2/img/placeholder_gato.jpg'
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const REGIME_MENSAL = 'MENSAL';
    const REGIME_ANUAL = 'ANUAL';
    const REGIMES_ANUAIS = ['ANUAL', 'ANUAL EM 2X', 'ANUAL EM 3X', 'ANUAL EM 4X', 'ANUAL EM 5X',
        'ANUAL EM 6X', 'ANUAL EM 7X', 'ANUAL EM 8X', 'ANUAL EM 9X', 'ANUAL EM 10X', 'ANUAL EM 11X', 'ANUAL EM 12X'];

    const STATUS_CARENCIA_COMPLETO = 1;
    const STATUS_CARENCIA_PARCIAL = 2;
    const STATUS_CARENCIA_INCOMPLETO = 3;

    const PADRAO_ANGEL_REGIME = "MENSAL";
    const PADRAO_ANGEL_VALOR = 4.9;
    const PADRAO_ANGEL_CARENCIA = 30;
    const PADRAO_ANGEL_MEIO = 1;

    public $fillable = [
        'nome_pet',
        'tipo',
        'id_raca',
        'sexo',
        'id_externo',
        'numero_microchip',
        'data_nascimento',
        'id_cliente',
        'id_plano',
        'contem_doenca_pre_existente',
        'doencas_pre_existentes',
        'familiar',
        'observacoes',
        'ativo',
        'regime',
        'valor',
        'id_conveniado',
        'participativo',
        'vencimento',
        'mes_reajuste',
        'reembolso',
        'valor_reembolso',
        'regime_reembolso',
        'total_gasto_reembolso',
        'total_contratado_reembolso',
        'limite_reembolso',
        'data_reembolso',
        'carencia_reembolso',
        'meio_reembolso',
        'porcentagem_reembolso',
        'exame_ultimos_12_meses',
        'data_primeiro_pag_reembolso',
        'status_primeiro_pag_reembolso',
        'data_ultima_verif_reembolso',
        'id_cobranca_externa_reembolso',
        'data_cobranca_externa_reembolso',
        'assinatura_superlogica_reembolso',
        'angel',
        'valor_angel',
        'carencia_angel',
        'data_angel',
        'regime_angel',
        'meio_angel',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'nome_pet' => 'string',
        'tipo' => 'string',
        'id_raca' => 'integer',
        'id_externo' => 'string',
        'numero_microchip' => 'string',
        'data_nascimento' => 'date',
        'id_plano' => 'integer',
        'contem_doenca_pre_existente' => 'boolean',
        'doencas_pre_existentes' => 'string',
        'familiar' => 'boolean',
        'observacoes' => 'string',
        'ativo' => 'boolean',
        'participativo' => 'boolean',
        'mes_reajuste' => 'integer',
        'data_reembolso' => 'datetime',
        'reembolso' => 'boolean',
        'valor_reembolso' => 'decimal',
        'regime_reembolso' => 'string',
        'total_gasto_reembolso' => 'decimal',
        'total_contratado_reembolso' => 'decimal',
        'limite_reembolso' => 'decimal',
        'data_reembolso' => 'datetime',
        'carencia_reembolso' => 'integer',
        'meio_reembolso' => 'integer',
        'angel' => 'boolean',
        'valor_angel' => 'decimal',
        'carencia_angel' => 'integer',
        'data_angel' => 'datetime',
        'regime_angel' => 'string',
        'meio_angel' => 'integer',
    ];

    protected $appends = [
        'primeiro_nome_pet'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
//        'numero_microchip' => 'unique:pets,numero_microchip',
//        'tipo' => 'in:CACHORRO,GATO',
//        'data_nascimento' => 'before_or_equal:today',
//        'data_contrato' => 'before_or_equal:today',
//        'data_encerramento' => 'after_or_equal:data_contrato'
    ];

    public static $regimes = [
        "ANUAL",
        "MENSAL",
        "ANUAL EM 2X",
        "ANUAL EM 3X",
        "ANUAL EM 4X",
        "ANUAL EM 5X",
        "ANUAL EM 6X",
        "ANUAL EM 7X",
        "ANUAL EM 8X",
        "ANUAL EM 9X",
        "ANUAL EM 10X",
        "ANUAL EM 11X",
        "ANUAL EM 12X",
    ];

    public function getPrimeiroNomePetAttribute() {
        return head(explode(' ', trim($this->nome_pet)));
    }

    public function setDataNascimentoAttribute($value) {
        $this->attributes['data_nascimento'] = \DateTime::createFromFormat('d/m/Y', $value);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function historicoUsos()
    {
        return $this->hasMany(\Modules\Guides\Entities\HistoricoUso::class, 'id_pet', 'id');
    }

    public function cliente()
    {
        return $this->belongsTo(\App\Models\Clientes::class, 'id_cliente', 'id');
    }

    public function raca()
    {
        return $this->belongsTo(\App\Models\Raca::class, 'id_raca', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function petsPlanos()
    {
        return $this->hasMany(\App\Models\PetsPlanos::class, 'id_pet', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function fichas()
    {
        return $this->hasMany(\Modules\Veterinaries\Entities\FichasAvaliacoes::class, 'id_pet', 'id');
    }

    public function documentos()
    {
        return $this->hasMany(\App\Models\DocumentosPets::class, 'id_pet', 'id');
    }

    /**
     * @return PetsPlanos|Model\Relations\HasMany
     */
    public function petsPlanosAtual()
    {
        $petsPlanos = $this->petsPlanos()
                           //->whereNull('data_encerramento_contrato')
                           ->orderBy('id', 'DESC')->limit(1);
        return $petsPlanos;
    }

    public function getMigrationLastValidSubscription()
    {
        return $this->petsPlanos()
            ->whereNull('data_encerramento_contrato')
            ->whereNull('financial_id')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()->last();
    }

    public function getLastValidSubscription()
    {
        return $this->petsPlanos()
            ->whereNull('data_encerramento_contrato')
            ->whereNotNull('financial_id')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()->last();
    }

    public function plano()
    {
        $petsPlanos = $this->petsPlanosAtual()->first();

        if(!$petsPlanos) {
            return new \App\Models\Planos;
        }

        $plano = $petsPlanos->plano()->first();
        return $plano;
    }

    public function vigencias($total = true) {
        if($total) {
            $petsPlanos = $this->primeiroContrato();
        } else {
            $petsPlanos = $this->petsPlanosAtual()->first();
        }

        $today = new \Carbon\Carbon();
        $dataContrato = $petsPlanos ? $petsPlanos->data_inicio_contrato : $today;
        $diffInDays = $today->diffInDays($dataContrato);

        if($diffInDays > 365) {
            $dataUltimoContrato = \Carbon\Carbon::createFromFormat('d/m/Y', $dataContrato->format('d/m') . '/' . $today->format('Y'));
            if($dataUltimoContrato->gt($today)) {
                $dataUltimoContrato = \Carbon\Carbon::createFromFormat('d/m/Y', $dataContrato->format('d/m') . '/' . $today->copy()->subYear()->format('Y'));
            }
            $start = $dataUltimoContrato;
        } else {
            $start = $dataContrato;
        }
        $end = \Carbon\Carbon::createFromFormat('d/m/Y', $start->format('d/m/') . $start->copy()->addYear()->format('Y'));

        return [
            $start,
            $end
        ];
    }

    /**
     * Verifica a utilização de qualquer procedimento do mesmo grupo do procedimento solicitado
     * @param \App\Models\Procedimentos $procedimento
     * @return integer
     */
    public function utilizacoesProcedimento(\App\Models\Procedimentos $procedimento, $agrupar = true, $considerarVigencia = true)
    {
        $procedimentos = [];
        if($agrupar) {
            $procedimentos = $procedimento->grupo()->first()->procedimentos()->where('contavel',1)->get(['id'])->pluck('id');
        } else {
            $procedimentos[] = $procedimento->id;
        }

        $utilizacoes = 0;

        $queryUtilizacoesProcedimentos = \Modules\Guides\Entities\HistoricoUso::query()
                                                        ->where('id_pet', $this->id)
                                                        ->whereIn('id_procedimento', $procedimentos)
                                                        ->where('status', 'LIBERADO');

       

        $queryNaoEncaminhados = clone $queryUtilizacoesProcedimentos;
        $queryNaoEncaminhados->where('tipo_atendimento', '!=', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO);
        if($considerarVigencia) {
            $queryNaoEncaminhados->whereBetween('created_at', $this->vigencias());
        }
        $utilizacoesEmGuiasNaoEncaminhadas = $queryNaoEncaminhados->count();
                                                        

        $utilizacoes += $utilizacoesEmGuiasNaoEncaminhadas;

        $queryEncaminhados = clone $queryUtilizacoesProcedimentos;
        $queryEncaminhados->where('tipo_atendimento', '=', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO);
        if($considerarVigencia) {
            $queryEncaminhados->whereBetween('realizado_em', $this->vigencias());
        }
        $utilizacoesEmGuiasEncaminhadas = $queryEncaminhados->count();

        $utilizacoes += $utilizacoesEmGuiasEncaminhadas;
        

        return $utilizacoes;
    }

    /**
     * @param Procedimentos $procedimento
     * @param $diasDePlano
     * @return array
     */
    public function validarProcedimentoBichos(\App\Models\Procedimentos $procedimento, $diasDePlano)
    {
        $planosProcedimento = $procedimento->planosProcedimentos()
            ->where('id_plano', $this->plano()->id)
            ->first();

        $carencia = $planosProcedimento
            ->dias_carencia;

        if($diasDePlano < $carencia) {
            return [
                'status' => false,
                'mensagem' => "A carência para a realização do procedimento (" . $procedimento->id . "-" . $procedimento->nome_procedimento . ") ainda não foi cumprida. Carência cumprida: " . $diasDePlano
            ];
        }

        $utilizacoes = $this->utilizacoesProcedimento($procedimento, false);
        if($utilizacoes >= $planosProcedimento->bichos_quantidade_usos) {
            return [
                'status' => false,
                'mensagem' => "O procedimento (" . $procedimento->id . "-" . $procedimento->nome_procedimento . ") excedeu o limite de " . $planosProcedimento->quantidade_usos . " utilizações anuais. \n "
            ];
        }

        $ultimaUtilizacao = $this->ultimaUtilizacao($procedimento);
        if($ultimaUtilizacao < $procedimento->intervalo_usos) {
            return [
                'status' => false,
                'mensagem' => "O procedimento (" . $procedimento->id . "-" . $procedimento->nome_procedimento . ") possui um intervalo de utilizações de " . $procedimento->intervalo_usos . " dias. \n "
                // . "Limite: " . $planoGrupo->quantidade_usos . "\n"
                // . "Utilizações: " . $utilizacoes
            ];
        }

        return [
            'status' => true
        ];
    }

    /**
     * @param PlanosGrupos $grupoProcedimentos
     * @param int $diasDePlano
     * @return bool
     */
    public function validarCarenciaGrupoProcedimento(Grupos $grupoProcedimentos, $diasDePlano = null): bool
    {
        if(!$diasDePlano) {
            $diasDePlano = $this->totalDiasPlano();
        }

       $planoGrupo = $this
            ->plano()
            ->planosGrupos()
            ->where('grupo_id', $grupoProcedimentos->id)
            ->first();
            
        if(!$planoGrupo) {
            return false;
        }

        $excecao = \App\Models\PetsGrupos::where('id_pet', $this->id)->where('id_grupo', $planoGrupo->grupo_id)->first();
        
        $carencia = $excecao ? $excecao->dias_carencia : $planoGrupo->dias_carencia;

        if($diasDePlano < $carencia) {
            return false;
        }

        return true;
    }

    /**
     * @param Procedimentos $procedimento
     * @param int $diasDePlano
     * @return bool
     */
    public function validarCarenciaProcedimento(Procedimentos $procedimento, $diasDePlano = null): bool
    {
        return $this->validarCarenciaGrupoProcedimento($procedimento->grupo, $diasDePlano);
    }
    
    public function validarProcedimento(\App\Models\Procedimentos $procedimento, $tipoAtendimento, $qtdProcedimentosGuia = 0)
    {
        /**
         * Já irá verificar se o procedimento pode ser feito por um Pet com o plano Bichos
         * ou não.
         * Basta não relacionar o procedimento na tabela planos_procedimentos com o plano
         * da Cia dos Bichos
         */
        $coberto = $this->plano()->procedimentoCoberto($procedimento);

        $diasDePlano = $this->totalDiasPlano();

        if(!$coberto) {
            /**
             * Verificar extensões de cobertura
             */
            $extensao = $this->extensao($procedimento);

            if(!$extensao) {
                return [
                    'status' => false,
                    'mensagem' => "O plano não possui cobertura para o procedimento: " . $procedimento->id . "-" . $procedimento->nome_procedimento
                ];
            }
        }

        /**
         * Estratégia alternativa de validação apenas para os planos da C. dos Bichos
         */
        if($this->isBichos()) {
            return $this->validarProcedimentoBichos($procedimento, $diasDePlano);
        }

        $planoGrupo = $this
            ->plano()
            ->planosGrupos()
            ->where('grupo_id', $procedimento->id_grupo)
            ->first();
        if(!$planoGrupo) {
            return [
                'status' => false,
                'mensagem' => "As definições de Grupo não foram discriminadas para o plano: " . $this->plano()->nome_plano
            ];
        }

        $excecao = \App\Models\PetsGrupos::where('id_pet', $this->id)->where('id_grupo', $planoGrupo->grupo_id)->first();
        $carencia = $excecao ? $excecao->dias_carencia : $planoGrupo->dias_carencia;

        if($diasDePlano < $carencia) {
            return [
                'status' => false,
                'mensagem' => "A carência para a realização do procedimento (" . $procedimento->id . "-" . $procedimento->nome_procedimento . ") ainda não foi cumprida. Carência cumprida: " . $diasDePlano
            ];
        }

        $utilizacoes = $this->utilizacoesProcedimento($procedimento);
        $quantidadesUsos = $excecao ? $excecao->quantidade_usos : $planoGrupo->quantidade_usos;

        if($utilizacoes >= $quantidadesUsos) {
            return [
                'status' => false,
                'mensagem' => "O procedimento (" . $procedimento->id . "-" . $procedimento->nome_procedimento . ") excedeu o limite de " . $quantidadesUsos . " utilizações anuais. \n "
                // . "Limite: " . $planoGrupo->quantidade_usos . "\n"
                // . "Utilizações: " . $utilizacoes
            ];
        }

        if ($qtdProcedimentosGuia > 0) {
            if($utilizacoes + $qtdProcedimentosGuia > $quantidadesUsos) {
                return [
                    'status' => false,
                    'mensagem' => "Este pet já realizou " . $utilizacoes . " do(s) " . $quantidadesUsos . " procedimento(s) permitido(s) anualmente do grupo " . $procedimento->grupo->nome_grupo . ". Os " . $qtdProcedimentosGuia . " procedimentos selecionados excedem este limite. \n "
                ];
            }
        }

        if($planoGrupo->uso_unico) {
            if($utilizacoes > 0) {
                return [
                    'status' => false,
                    'mensagem' => "O grupo (" . $procedimento->grupo->id . "-" . $procedimento->grupo->nome_grupo . ") possui apenas uma utilização. Um procedimento já foi utilizado anteriormente. \n "
                    // . "Limite: " . $planoGrupo->quantidade_usos . "\n"
                    // . "Utilizações: " . $utilizacoes
                ];
            }
            
        }

        if($this->plano()->aplicarIntervaloDeUsos()) {
            $ultimaUtilizacao = $this->ultimaUtilizacao($procedimento);
            if($ultimaUtilizacao < $procedimento->intervalo_usos) {
                return [
                    'status' => false,
                    'mensagem' => "O procedimento (" . $procedimento->id . "-" . $procedimento->nome_procedimento . ") possui um intervalo de utilizações de " . $procedimento->intervalo_usos . " dias. \n "
                    // . "Limite: " . $planoGrupo->quantidade_usos . "\n"
                    // . "Utilizações: " . $utilizacoes
                ];
            }
        }


        return [
            'status' => true
        ];
    }

    /**
     * Retorna a quantidade de dias desde a última utilização de um procedimento
     */
    public function ultimaUtilizacao(\App\Models\Procedimentos $procedimento) {
        $utilizacoes = \Modules\Guides\Entities\HistoricoUso::where('id_pet', $this->id)
            //->where('id_plano', $this->plano()->id)
            ->where('id_procedimento', $procedimento->id)
            ->whereBetween('created_at', $this->vigencias())
            ->where('status', 'LIBERADO')
            ->orderBy('id', 'DESC')
            ->first(['created_at']);
        if(empty($utilizacoes)) {
            return PHP_INT_MAX;
        }

        return $utilizacoes->created_at->diffInDays(new Carbon());
    }

    public function primeiroContrato()
    {
        $petsPlanos = $this->petsPlanos()
            ->where('status', PetsPlanos::STATUS_PRIMEIRO_PLANO)
            //->whereNull('data_encerramento_contrato')
            ->orderBy('id', 'DESC')
            ->first();
        if(!$petsPlanos) {
            $petsPlanos = $this->petsPlanos()
                ->whereNull('data_encerramento_contrato')
                ->orderBy('id', 'DESC')
                ->first();
        }

        return $petsPlanos;
    }

    public function totalDiasPlano()
    {
        $petsPlanos = $this->primeiroContrato();

        if(!$petsPlanos) {
            return 0;
        }

        return $petsPlanos->data_inicio_contrato->diffInDays(new Carbon());
    }

    public function isBichos() {
        return $this->plano()->bichos;
    }

    public function statusPagamento()
    {
        if($this->regime === self::REGIME_ANUAL) {
            return Clientes::PAGAMENTO_EM_DIA;
        }

        if($this->isBichos()) {
            return $this->cliente->statusPagamento(30);
        }

        return $this->cliente()->first()->status_pagamento;
    }

    public function getPrimeiroNomeAttribute()
    {
        return explode(' ',$this->nome_pet)[0];
    }

    public function getInicialAttribute()
    {
        return substr($this->getPrimeiroNomeAttribute(), 0,1);
    }

    public static function petsFromUser()
    {
        $userId = auth()->user()->id;
        if(!Entrust::hasRole(['CLIENTE'])) {
            return [];
        }

        $cliente = \App\Models\Clientes::where('id_usuario', $userId)->first();
        return $cliente->pets()->get();
    }

    /**
     * Retorna a quantidade de participação lançada no período da vigência
     * @return double
     */
    public function participado()
    {
        return Participacao::participacado($this->id);
    }

    public function cancelamentoAgendado()
    {
        $cancelamentoAgendado = Cancelamento::where('id_pet', $this->id)
            ->where('data_cancelamento', '>', Carbon::today()->format('Y-m-d'))
            ->get()
            ->first();
        if ($cancelamentoAgendado) {
            return $cancelamentoAgendado;
        }
        return false;
    }

    public function getProcedimentosPorGrupo($pg)
    {
        $grupo = $pg->grupo()->first();
        $queryProcedimentos = DB::table('planos_procedimentos')
            ->select("procedimentos.*")
            ->join("procedimentos", "procedimentos.id", "=", "planos_procedimentos.id_procedimento")
            ->join("planos_grupos", "planos_grupos.grupo_id", "=", "procedimentos.id_grupo")
            ->join("grupos_carencias", "grupos_carencias.id", "=", "planos_grupos.grupo_id")
            ->where('procedimentos.ativo', 1)
            ->where('grupos_carencias.id', $grupo->id)
            ->where('planos_procedimentos.id_plano', $this->plano()->id)
            ->where('planos_procedimentos.deleted_at', null)
            ->groupBy('procedimentos.id')
            ->orderBy("planos_procedimentos.id_plano")
            ->orderBy("grupos_carencias.nome_grupo");
        return $queryProcedimentos->get();
    }

    public function calculoPlacarCarenciasPorProcedimento($p)
    {
        $diff = $this->totalDiasPlano();

        $vigencias = $this->vigencias();
        $start = $vigencias[0];
        $end = $vigencias[1];

        $utilizacoes = \Modules\Guides\Entities\HistoricoUso::where('id_pet', $this->id)
            ->where('id_procedimento', $p->id_procedimento)
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'LIBERADO')
            ->count();

        $quantidadesUso = $p->bichos_quantidade_usos;
        if($quantidadesUso === 99999) {
            $quantidadesUso = "Ilimitado";
        }

        $utilizacoesRestantes = is_numeric($quantidadesUso) ? $quantidadesUso - $utilizacoes : $quantidadesUso;
        $badgeColor = "badge-warning";
        if(is_numeric($utilizacoesRestantes)) {
            if($utilizacoesRestantes == $quantidadesUso) {
                $badgeColor = "bg-green-meadow";
            } else if ($utilizacoesRestantes > $quantidadesUso/2) {
                $badgeColor = "bg-green-sharp";
            } else if ($utilizacoesRestantes > 1) {
                $badgeColor = "bg-yellow-casablanca";
            } else if ($utilizacoesRestantes == 0) {
                $badgeColor = "bg-grey-mint";
            } else {
                $badgeColor = 'bg-red-mint';
            }
        }

        $carencia_dias = (($diff - $p->bichos_carencia  >= 0) ? 0 : abs($p->bichos_carencia - $diff));

        return [
            'id' => $p->procedimento()->first()->id,
            'nome' => $p->procedimento()->first()->nome_procedimento,
            'carencia_dias' => $carencia_dias,
            'carencia' => '<span class="badge badge-success">' . (($diff - $p->bichos_carencia  >= 0) ? '<i class="fa fa-check"></i>' : abs($p->bichos_carencia - $diff)) . '</span>',
            'qtd_permitida' => '<span class="badge badge-success"> ' . $quantidadesUso . '</span>',
            'qtd_utilizada' => '<span class="badge badge-success">' . $utilizacoes . '</span>',
            'qtd_restante' => '<span class="badge ' . $badgeColor . '">' . $utilizacoesRestantes . '</span>',
            'item' => $p
        ];
    }

    public function getPlacarCarenciasPorProcedimento()
    {
        $placar = [];

        $plano = $this->plano();
        $procedimentos = \App\Models\PlanosProcedimentos::where('id_plano', $plano->id)->get();

        foreach ($procedimentos as $p) {
            $placar[] = $this->calculoPlacarCarenciasPorProcedimento($p);
        }

        return $placar;
    }

    /**
     * Retorna um array com todos os procedimentos do plano atual e seus respectivos dias de atividade dentro do período de vigência
     * @return array
     */
    public function getCarenciasProcedimentos()
    {
        $planos = [];
        $planoAtual = $this->plano();
        $primeiroPlano = $this->petsPlanos()->where('status', PetsPlanos::STATUS_PRIMEIRO_PLANO)
                                            //->whereNull('data_encerramento_contrato')
                                            ->orderBy('id', 'DESC')
                                            ->first();
        if(!$primeiroPlano) {
            $primeiroPlano = $this->petsPlanos()->whereNull('data_encerramento_contrato')
                ->orderBy('id', 'DESC')
                ->first();
        }
        $plano = $primeiroPlano->plano()->get()->first();
        $periodoDias = \Carbon\Carbon::today()->diffInDays($primeiroPlano->data_inicio_contrato);

        $planos[$plano->id] = [
            'periodo' => $periodoDias,
            'procedimentos' => []
        ];
        $planos[$planoAtual->id] = [
            'periodo' => $periodoDias,
            'procedimentos' => []
        ];

        if ($planoAtual->id != $plano->id) {
            foreach ($plano->planosProcedimentos()->get()->toArray() as $planoProcedimento) {
                $planos[$plano->id]['procedimentos'][] = $planoProcedimento['id_procedimento'];
            }
        }

        $procedimentos = $planoAtual->planosProcedimentos()->get()->mapWithKeys(function($p) use ($planos, $planoAtual) {
            return [$p->id_procedimento => $planos[$planoAtual->id]['periodo']];
        });

        foreach ($planos as $plano) {
            foreach ($plano['procedimentos'] as $procId) {
                if (isset($procedimentos[$procId])) {
                    $procedimentos[$procId] += $plano['periodo'];
                }
            }
        }
        return $procedimentos;
    }

    /**
     * Retorna um array com todos os grupos do plano atual e seus respectivos procedimentos
     * @return array
     */
    public function getGruposProcedimentosPlanoAtual()
    {
        $planoAtual = $this->plano();
        $grupos = $planoAtual->planosGrupos()->get()->map(function($pg) {
            $procedimentos = $this->getProcedimentosPorGrupo($pg);
            $grupo = [
                'grupo' => $pg->grupo()->get()->first(),
                'planoGrupo' => $pg,
                'procedimentos' => $procedimentos
            ];
            return $grupo;
        });
        return $grupos;
    }

    public function getGruposProcedimentosPrimeiroPlano()
    {
        $petsPlanos = $this->petsPlanos()
                           ->orderBy('id', 'DESC')
                           ->where('status', '!=', PetsPlanos::STATUS_RENOVACAO)
                            ->first();
        $planoAtual = $petsPlanos->plano;


        $grupos = $planoAtual->planosGrupos()->get()->map(function($pg) {
            $procedimentos = $this->getProcedimentosPorGrupo($pg);
            $grupo = [
                'grupo' => $pg->grupo()->get()->first(),
                'planoGrupo' => $pg,
                'procedimentos' => $procedimentos
            ];
            return $grupo;
        });
        return $grupos;
    }



    public function calculoPlacarCarenciasHistoricoUso(\App\Models\PlanosGrupos $pg)
    {
        $utilizacaoConsultasEmergenciais = 0;

        $vigencias = $this->vigencias();
        $start = $vigencias[0];
        $end = $vigencias[1];

        $grupo = $pg->grupo()->first();

        $excecao = \App\Models\PetsGrupos::where('id_pet', $this->id)->where('id_grupo', $grupo->id)->first();

        $procedimentos = $grupo->procedimentos()->where('contavel',1)->get(['id'])->map(function($item) {
            return $item->id;
        });

        $utilizacoes = \Modules\Guides\Entities\HistoricoUso::where('id_pet', $this->id)
            ->whereIn('id_procedimento', $procedimentos)
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'LIBERADO')
            ->where('tipo_atendimento', '!=', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
            ->count();
        $utilizacoes += \Modules\Guides\Entities\HistoricoUso::where('id_pet', $this->id)
            ->whereIn('id_procedimento', $procedimentos)
            ->whereBetween('realizado_em', [$start, $end])
            ->where('status', 'LIBERADO')
            ->where('tipo_atendimento', '=', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
            ->count();

        $quantidadesUso = $excecao ? $excecao->quantidade_usos : $pg->quantidade_usos;
        if($grupo->id == '10101016'){
            $quantidadesUso = "Conforme a necessidade";
        } else if ($quantidadesUso >= 90000 ) {
            $quantidadesUso = "Ilimitado";
        } else if ($quantidadesUso >= 80000 ) {
            $quantidadesUso = "Conforme consultas comuns";

            $utilizacaoConsultasEmergenciais = \Modules\Guides\Entities\HistoricoUso::where('id_pet', $this->id)
                ->whereIn('id_procedimento', $procedimentos)
                ->whereBetween('created_at', [$start, $end])
                ->count();
        }
        $utilizacoesRestantes = is_numeric($quantidadesUso) ? $quantidadesUso - $utilizacoes : $quantidadesUso;
        $badgeColor = "badge-warning";
        if(is_numeric($utilizacoesRestantes)) {
            if($utilizacoesRestantes == $quantidadesUso) {
                $badgeColor = "bg-green-meadow";
            } else if ($utilizacoesRestantes > $quantidadesUso/2) {
                $badgeColor = "bg-green-sharp";
            } else if ($utilizacoesRestantes > 1) {
                $badgeColor = "bg-yellow-casablanca";
            } else if ($utilizacoesRestantes == 0) {
                $badgeColor = "bg-grey-mint";
            } else {
                $badgeColor = 'bg-red-mint';
            }
        }

        $qtdRestante = ($grupo->id == '10100' ? (is_numeric($quantidadesUso) ? ($quantidadesUso - ($utilizacoes + $utilizacaoConsultasEmergenciais)) : $quantidadesUso) : $utilizacoesRestantes);

        return [
            'qtd_permitida' => $quantidadesUso,
            'qtd_permitida_label' => '<span class="badge badge-success"> ' . $quantidadesUso . '</span>',
            'qtd_utilizada' => $utilizacoes,
            'qtd_utilizada_label' => '<span class="badge badge-success">' . $utilizacoes . '</span>',
            'qtd_restante' => $qtdRestante,
            'qtd_restante_label' => '<span class="badge ' . $badgeColor . '">' . ($grupo->id == '10100' ? (is_numeric($quantidadesUso) ? ($quantidadesUso - ($utilizacoes + $utilizacaoConsultasEmergenciais)) : $quantidadesUso) : $utilizacoesRestantes) . '</span>',
        ];
    }


    /**
     * Retorna um array com todos os grupos do plano atual,seus respectivos procedimentos
     * e todos os dados necessários para a exibição completa do placar das carências
     * @return array
     */
    public function getPlacarCarenciasPorGrupo()
    {
        $grupos = $this->getGruposProcedimentosPrimeiroPlano();
        $carencias = $this->getCarenciasProcedimentos();
        $placar = [];
        foreach ($grupos as $g) {

            $statusCarencia = 0; // 1=COMPLETO, 2=PARCIAL, 3=INCOMPLETO
            $procedimentos = [];

            foreach ($g['procedimentos'] as $p) {

                $excecao = \App\Models\PetsGrupos::where('id_pet', $this->id)->where('id_grupo', $p->id_grupo)->first();
                $carencia = $excecao ? $excecao->dias_carencia : $g['planoGrupo']->dias_carencia;

                $carenciaRestante = $carencia - $carencias[$p->id];
                $procedimentos[] = [
                    'id_procedimento' => $p->id,
                    'procedimento' => $p->nome_procedimento,
                    'carencia' => $carenciaRestante,
                ];

                if ($statusCarencia === 0) {
                    if ($carenciaRestante <= 0){
                        $statusCarencia = $this::STATUS_CARENCIA_COMPLETO;
                    } else {
                        $statusCarencia = $this::STATUS_CARENCIA_INCOMPLETO;
                    }
                } else {
                    if($statusCarencia == $this::STATUS_CARENCIA_COMPLETO && $carenciaRestante > 0)
                        $statusCarencia = $this::STATUS_CARENCIA_PARCIAL;

                    if($statusCarencia == $this::STATUS_CARENCIA_INCOMPLETO && $carenciaRestante <= 0)
                        $statusCarencia = $this::STATUS_CARENCIA_PARCIAL;
                }
            }

            if ($procedimentos) {
                $helperCarencia = [
                    1 => [
                        'tooltip' => 'Todos os procedimentos deste grupo passaram do período de carência.',
                        'conteudo' => '<i class="fa fa-check"></i>',
                        'cor' => 'bg-green-meadow',
                    ],
                    2 => [
                        'tooltip' => 'Existem procedimentos neste grupo que ainda estão em período de carência, consulte a listagem.',
                        'conteudo' => '<i class="fa fa-warning"></i>',
                        'cor' => 'bg-yellow-gold',
                    ],
                    3 => [
                        'tooltip' => 'Todos os procedimentos deste grupo estão em período de carência.',
                        'conteudo' => '<i class="fa fa-times"></i>',
                        'cor' => 'bg-red',
                    ],
                ];

                $placar[] = [
                    'grupo' => $g['grupo'],
                    'procedimentos' => $procedimentos,
                    'carencia_status' => $statusCarencia,
                    'carencia_dias' => $g['planoGrupo']->dias_carencia,
                    'carencia_helper' => $helperCarencia[$statusCarencia],
                    'historicoUso' => $this->calculoPlacarCarenciasHistoricoUso($g['planoGrupo']),
                ];
            }

        };
        $placar = collect($placar)->sortBy('grupo.nome_grupo')->sortBy('carencia_status');
        return $placar;
    }

    public function opcoes()
    {
        return $this->hasMany(PetsOpcoes::class, 'id_pet');
    }

    public function opcao($key)
    {
        return $this->opcoes()->where('chave', $key)->first()->valor;
    }

    public function saveOpcao($key, $value)
    {
        if(PetsOpcoes::where('chave', $key)->exists()) {
            $petsOpcoes = PetsOpcoes::where('chave', $key)->first();
            $petsOpcoes->valor = $value;
            return $petsOpcoes->update();
        }

        return PetsOpcoes::create([
            'id_pet' => $this->id,
            'chave'  => $key,
            'valor'  => $value
        ]);
    }

    public static function byOption($key, $value)
    {
        return Pets::with('opcoes')->whereHas('opcoes', function($q) use ($key, $value) {
            $q->where('chave', $key);
            $q->where('valor', $value);
        })->get();
    }

    public static function allByPlano(Planos $plano)
    {
        $query = Pets::query()
            ->join('pets_planos', 'pets.id_pets_planos', '=', 'pets_planos.id')
            ->join('planos', 'pets_planos.id_plano', '=', 'planos.id')
            ->where('pets.ativo', 1)
            ->where('planos.id', '=', $plano->id)
            ->orderBy('pets.nome_pet', 'ASC');

        return $query->get();
    }

    public function isAnual()
    {
        return in_array($this->regime, self::regimesAnuais());
    }

    public function isParcelado()
    {
        return strpos($this->regime, 'ANUAL EM');
    }

    public static function regimesAnuais()
    {
        return [
            'ANUAL',
            'ANUAL EM 1X',
            'ANUAL EM 2X',
            'ANUAL EM 3X',
            'ANUAL EM 4X',
            'ANUAL EM 5X',
            'ANUAL EM 6X',
            'ANUAL EM 7X',
            'ANUAL EM 8X',
            'ANUAL EM 9X',
            'ANUAL EM 10X',
            'ANUAL EM 11X',
            'ANUAL EM 12X',
        ];
    }

    public function isRegimeAnual()
    {
        if(strpos($this->regime, 'ANUAL') > -1) {
            return true;
        }

        return false;
    }

    public function realizarPreExistencias()
    {
        $hoje = Carbon::now();
        $petsPlanos = $this->petsPlanos()->orderBy('id', 'DESC')->first();
        if(!$petsPlanos) {
            return false;
        }

        return $hoje->diffInDays($petsPlanos->data_inicio_contrato) > 365;
    }

    public function getValorUtilizado($start = null, $end = null)
    {
        $hu = $this->historicoUsos()->where('status', HistoricoUso::STATUS_LIBERADO);

        if($start && $end){
            $hu->where(function($query) use ($start, $end) {
                $query->where(function($query) use ($start, $end) {
                    $query->where('tipo_atendimento', "!=", HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->whereBetween('created_at', [$start, $end]);
                });
                $query->orWhere(function($query) use ($start, $end) {
                    $query->where('tipo_atendimento', HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->whereBetween('realizado_em', [$start, $end]);
                });
            });
        }

        return $hu->sum('valor_momento');
    }

    public function getLTV() {
        $planos = $this->petsPlanos()
            ->whereNotNull('data_encerramento_contrato')
            ->whereNotIn('id_plano', [22,23,24,25,26]) // Planos Bichos
            ->where('data_encerramento_contrato', '!=', '0000-00-00')
            ->whereDate('data_inicio_contrato', '>', '2000-01-01')
            ->whereDate('data_encerramento_contrato', '>', '2000-01-01')
            ->whereDate('data_encerramento_contrato', '<', '2038-01-01');

        $ltv = 0;
        foreach ($planos->get() as $plano) {
            $ltv += Carbon::parse($plano->data_inicio_contrato)->diffInDays(Carbon::parse($plano->data_encerramento_contrato));
        }
        return $ltv;
    }

    public function avatar(){
        $exists = Storage::disk('local')->exists($this->foto);
        if ($this->foto && $exists) {
            $foto = '/' . $this->foto;
        } else {
            $foto = (isset(self::$placeholders[$this->tipo])) ?: '';
        }
        return url('/') . $foto;
    }

    public function getImagemCarteirinha()
    {
        if (!$this->petsPlanosAtual()->exists()) {
            if ($this->angel) {
                return url('/') . "/assets/images/carteirinhas/angel.png";
            }
        }
        return $this->plano()->getImagemCarteirinha();
    }

    public function carenciaAngelRestante() {
        $carencia_angel_restante = null;
        if ($this->data_angel) {
            $carencia_angel_restante = (new Carbon)->startOfDay()->diffInDays($this->data_angel->addDays($this->carencia_angel)->startOfDay(), false);
            if ($carencia_angel_restante <= 0) {
                $carencia_angel_restante = 0;
            }
        }
        return $carencia_angel_restante;
    }

    public function extensao(Procedimentos $procedimento)
    {
        return ExtensaoProcedimento::where('id_pet', $this->id)->where('id_procedimento', $procedimento->id)->exists();
    }

    public function extensoes()
    {
        return $this->hasMany(ExtensaoProcedimento::class, 'id_pet');
    }

    public function extender(Procedimentos $procedimento)
    {
        if(!$this->extensao($procedimento)) {
            $extensao = new ExtensaoProcedimento();
            $extensao->fill([
                'id_pet' => $this->id,
                'id_procedimento' => $procedimento->id
            ]);
            $extensao->save();
        }
    }

    public function assinarAngel($dados) {

        $regime        = $dados['regime'] ?? self::PADRAO_ANGEL_REGIME;
        $carencia      = $dados['carencia'] ?? self::PADRAO_ANGEL_CARENCIA;
        $meio          = $dados['meio'] ?? self::PADRAO_ANGEL_MEIO;

        if (isset($dados['valor'])) {
            $valor = $dados['valor'];
        } else {
            $idade = $this->data_nascimento->age;
            $valor_angel =  DB::table('plano_angel_valores')
                ->where('idade_min', '<=', $idade)
                ->where('idade_max', '>=', $idade)
                ->first();
            $valor = $dados['regime'] == 'ANUAL' ? $valor_angel->valor_anual : $valor_angel->valor_mensal;
        }

        $this->update([
            'angel' => 1,
            'valor_angel' => $valor,
            'carencia_angel' => $carencia,
            'data_angel' => new Carbon(),
            'regime_angel' => $regime,
            'meio_angel' => $meio
        ]);
    }

    public function renovacoes()
    {
        return $this->hasMany(Renovacao::class, 'id_pet');
    }

    public function renovar(Renovacao $renovacao)
    {
        /**
         * Encerramento do contrato atual
         * @var $petsPlanos PetsPlanos
         */
        $petsPlanos = $this->petsPlanosAtual()->first();

        //Criação do contrato de renovação
        $data = [
            'id_pet'                     => $this->id,
            'id_plano'                   => $renovacao->plano->id,
            'valor_momento'              => number_format($renovacao->valor_bruto, 2, '.', ''),
            'data_inicio_contrato'       => $petsPlanos->data_inicio_contrato->year(Carbon::now()->year)->format('d/m/Y'),
            'data_encerramento_contrato' => null,
            'id_vendedor'                => 1,
            'status'                     => PetsPlanos::STATUS_RENOVACAO,
            'adesao'                     => 0,
            'desconto_folha'             => $petsPlanos->desconto_folha,
            'id_conveniada'              => $petsPlanos->id_conveniada
        ];

        $pp                   = PetsPlanos::create($data);
        $this->id_pets_planos = $pp->id;
        $this->regime         = $renovacao->regime;
        $this->update();

        Notas::registrar("O plano do pet {$this->nome_pet} foi renovado para R$ {$renovacao->valor}", $this->cliente);

        $dadosJson = json_encode([
            'novoPlano' => $data,
            'planoAnterior' => $petsPlanos,
            'renovacao' => $renovacao->toLog([], false)
        ]);

        $logger = new Logger(Renovacao::LOG_AREA, Renovacao::LOG_TABLE, 1);
        $logger->register(LogEvent::UPDATE, LogPriority::HIGH, "As informações de pagamento do pet {$this->nome_pet} foram atualizadas. O plano acabou de ser renovado com sucesso\nDados:\n\n$dadosJson", $renovacao->id);

        (new RDRenovacaoConfirmadaService())->process($renovacao);

        return $pp;
    }

    public function atualizarFaturaAberta($chave, $valor, $competencia)
    {
        $financeiro = new Financeiro();
        $cliente = $this->cliente;
        $chave = strtoupper($chave);


        try {
            $customer = $financeiro->get('/customer/refcode/' . $cliente->id_externo);
        } catch (\Exception $e) {
            $mensagemLog = "[PETS]: O cliente {$cliente->nome_cliente} não pôde ser encontrado no SF com o 'refcode' informado. Não foi possível atualizar a fatura.";

            Logger::log(
                LogEvent::WARNING,
                'clientes',
                LogPriority::MEDIUM,
                $mensagemLog,
                null,
                'pets',
                $this->id);

            return false;
        }

        try {
            $invoice = $financeiro->get("/customer/{$customer->data->id}/invoice-in-progress");
            $hasFaturaAberta = true;
            $reference = $invoice->data->reference;
            $reference = explode('/', $reference);
            $reference = $reference[1] . '-' . $reference[0];
//
//            if($reference !== $competencia) {
//                $mensagemLog = "[PETS]: O item de cobrança especificado não será lançado fatura do cliente {$cliente->nome_cliente}. A competência não é a mesma da fatura aberta ({$competencia}). ";
//
//                Logger::log(
//                    LogEvent::WARNING,
//                    'clientes',
//                    LogPriority::MEDIUM,
//                    $mensagemLog,
//                    null,
//                    'pets',
//                    $this->id);
//                return false;
//            }

            foreach($invoice->data->itens as $item) {
                if($item->name == $chave) {
                    $hasFaturaAberta = false;
                }
            }

            if(!$hasFaturaAberta) {
                $mensagemLog = "[PETS]: O item de cobrança especificado já foi lançado na fatura do cliente {$cliente->nome_cliente}.";

                Logger::log(
                    LogEvent::WARNING,
                    'clientes',
                    LogPriority::MEDIUM,
                    $mensagemLog,
                    null,
                    'pets',
                    $this->id);
                return false;
            }
        } catch (\Exception $e) {
            $mensagemLog = "[PETS]: Não foi possível encontrar uma fatura aberta para o cliente {$cliente->nome_cliente}.";

            Logger::log(
                LogEvent::WARNING,
                'clientes',
                LogPriority::MEDIUM,
                $mensagemLog,
                null,
                'pets',
                $this->id);

            return false;
        }

        //Adicionar diferença na fatura
        try {
            $form = [
                "item" => [
                    [
                        'type' => 'D',
                        'name' => $chave,
                        'quantity' => 1,
                        'price' => number_format($valor, 2, ',', ''),
                        'total' => number_format($valor, 2, ',', '')
                    ]
                ]
            ];

            $return = $financeiro->post('/invoice/' . $invoice->data->id, $form);
        } catch (\Exception $e) {

            $mensagemLog = "[PETS]: Não foi possível lançar um novo item na fatura aberta do cliente {$cliente->nome_cliente}.";

            Logger::log(
                LogEvent::WARNING,
                'clientes',
                LogPriority::MEDIUM,
                $mensagemLog,
                null,
                'pets',
                $this->id);

            return false;
        }


        if($return->id) {

            $mensagemLog = "[PETS]: O item $chave foi lançado com sucesso na fatura do cliente {$cliente->nome_cliente}.";

            Logger::log(
                LogEvent::WARNING,
                'clientes',
                LogPriority::MEDIUM,
                $mensagemLog,
                null,
                'pets',
                $this->id);

            return true;
        } else {
            $mensagemLog = "[PETS]: O item $chave não foi lançado com sucesso na fatura do cliente {$cliente->nome_cliente}. Ocorreu um erro inesperado. Não foi possível identificar a inclusão do item.";

            Logger::log(
                LogEvent::WARNING,
                'clientes',
                LogPriority::MEDIUM,
                $mensagemLog,
                null,
                'pets',
                $this->id);

            return false;
        }
    }

    public function scopeLPT($query)
    {
        return $query->select('pets.*')
                     ->join('pets_planos','pets_planos.id', '=', 'pets.id_pets_planos')
                     ->join('planos','planos.id', '=', 'pets_planos.id_plano')
                     ->whereIn('planos.id', [61, 59, 58, 56, 55, 52]);
    }

    public function scopeAtivo($query)
    {
        return $query->where('ativo', 1);
    }

    public function getIdentificadorPlano()
    {
        $pieces = ['PLANO'];
        $pieces[] = $this->plano()->id;
        $pieces[] = $this->primeiroNome;
        $pieces[] = $this->id;
        if($this->plano()->lpt) {
            $pieces[] = 'LPT';
        }

        return join('_', $pieces);
    }

    public function getValorPlano()
    {
        $petsPlanos = $this->petsPlanosAtual()->first();
        return $petsPlanos->valor_momento;
    }

    public function assinarSuperlogica(CreditCard $creditCard = null, $forcarInsercaoCartao = false)
    {
        if(!$this->ativo) {
            return;
        }

        if(!$this->petsPlanosAtual()->first()->id_contrato_superlogica) {
            $signatureService = new Signature();

            try {
                $response = $signatureService->sign($this, false, $creditCard, $forcarInsercaoCartao);
            } catch (\Exception $e) {
                if(strpos($e->getMessage(), 'já foi contratado anteriormente') !== false) {
                    return null;
                }

                throw $e;
            }

        }

        return $this->petsPlanosAtual()->first()->id_contrato_superlogica;
    }

    public function loggable()
    {
        return [
            'id' => $this->id,
            'nome_pet' => $this->nome_pet,
            'tipo' => $this->tipo,
            'ativo' => $this->ativo,
            'regime' => $this->regime,
            'plano' => $this->getIdentificadorPlano()
        ];
    }

    /**
     * Inactivate Pet
     * @return self
     */
    public function inactivate(): self
    {
        if ($this->ativo) {
            $this->ativo = false;
            $this->update();
        }

        return $this;
    }

    /**
     * Activate Pet
     * @return $this
     */
    public function activate(): self
    {
        if (!$this->ativo) {
            $this->ativo = true;
            $this->update();
        }

        return $this;
    }
}
