<?php

namespace Modules\Guides\Entities;

use App\Http\Controllers\AppBaseController;
use App\Http\Util\LogEvent;
use App\Http\Util\Logger;
use App\Http\Util\LogMessages;
use App\Http\Util\LogPriority;
use App\Models\Participacao;
use App\Models\Pets;
use App\Models\PlanosProcedimentos;
use App\Models\Procedimentos;
use Carbon\Carbon;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Mail;
use Modules\Clinics\Entities\Clinicas;
use Modules\Guides\Services\GuideService;


/**
 * @property Pets $pet
 */
class HistoricoUso extends Model
{
    const TAXA_PARTICIPACAO = 0.5;

    const STATUS_LIBERADO = "LIBERADO";
    const STATUS_RECUSADO = "RECUSADO";
    const STATUS_AGUARDANDO = "AVALIANDO";

    use SoftDeletes;

    public $table = 'historico_uso';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const TIPO_ENCAMINHAMENTO = 'ENCAMINHAMENTO';
    const TIPO_EMERGENCIA = 'EMERGENCIA';
    const TIPO_NORMAL = 'NORMAL';

    const AUTORIZACAO_AUTOMATICA = 'AUTOMATICA';
    const AUTORIZACAO_AUDITORIA = 'AUDITORIA';
    const AUTORIZACAO_FORCADO = 'FORCADO';

    const MEIO_ASSINATURA_SISTEMA = 1;
    const MEIO_ASSINATURA_APLICATIVO = 2;
    const MEIO_ASSINATURA_PRESENCIAL = 3;
    const HORAS_LAUDO_EDITAVEL = 36;

    protected $dates = ['deleted_at', 'data_liberacao', 'realizado_em'];


    public $fillable = [
        'id_pet',
        'id_procedimento',
        'id_plano',
        'id_prestador',
        'id_clinica',
        'id_especialidade',
        'numero_guia',
        'valor_momento',
        'justificativa',
        'laudo',
        'observacao',
        'autorizacao',
        'tipo_atendimento',
        'status',
        'id_autorizador',
        'id_solicitador',
        'created_at',
        'data_liberacao',
        'glosado',
        'id_prestador_solicitante',
        'assinatura_cliente',
        'data_assinatura_cliente',
        'meio_assinatura_cliente',
        'assinatura_prestador',
        'data_assinatura_prestador'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_pet' => 'integer',
        'id_procedimento' => 'integer',
        'id_plano' => 'integer',
        'id_prestador' => 'integer',
        'id_clinica' => 'integer',
        'id_especialidade' => 'integer',
        'numero_guia' => 'string',
        'valor_momento' => 'float',
        'justificativa' => 'string',
        'laudo' => 'string',
        'observacao' => 'string',
        'imagem_laudo' => 'string',
        'autorizacao' => 'string',
        'tipo_atendimento' => 'string',
        'status' => 'string',
        'glosado' => 'string',
        'id_prestador_solicitante' => 'integer',
        'assinatura_cliente' => 'string',
        'data_assinatura_cliente' => 'datetime',
        'meio_assinatura_cliente' => 'string',
        'assinatura_prestador' => 'string',
        'data_assinatura_prestador' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function getFormattedValorMomentoAttribute() {
        return number_format($this->attributes['valor_momento'], 2, ',');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function clinica()
    {
        return $this->belongsTo(\Modules\Clinics\Entities\Clinicas::class, 'id_clinica');
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
    public function especialidade()
    {
        return $this->belongsTo(\App\Models\Especialidades::class, 'id_especialidade');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function prestador()
    {
        return $this->belongsTo(\Modules\Veterinaries\Entities\Prestadores::class, 'id_prestador');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function prestador_solicitante()
    {
        return $this->belongsTo(\Modules\Veterinaries\Entities\Prestadores::class, 'id_prestador_solicitante');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function procedimento()
    {
        return $this->belongsTo(\App\Models\Procedimentos::class, 'id_procedimento')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function glosa()
    {
        return \Modules\Guides\Entities\GuiaGlosa::where('id_historico_uso', $this->id)->first();
    }

    public function isGlosado()
    {
        return ($this->glosado == 1 || $this->glosado == 3);
    }

    public function statusGlosa()
    {
        $status = 'Não';

        if ($this->glosado == 1) {
            $status = 'Glosado';
        } else if ($this->glosado == 2) {
            $status = 'Revertido';
        } else if ($this->glosado == 3) {
            $status = 'Confirmado';
        }

        return $status;
    }

    public function getGlosaLabel()
    {
        $class = '';
        if ($this->glosado == 1) {
            $class = 'warning';
        } else if ($this->glosado == 2) {
            $class = 'info';
        } else if ($this->glosado == 3) {
            $class = 'danger';
        }
        return '<span class="label label-sm label-'.$class.'">'.$this->statusGlosa().'</span>';
    }

    /**
     * pega valor do procedimento
     *
     */
    public function getValorProcedimento()
    {

        $total = 0;
        $planoAtual = $this->planoAtual();
        $procedimento = $this->procedimento()->first();


        if($planoAtual->participativo) {
            //Percorre todos os procedimentos
            $total += PlanosProcedimentos::getValorBeneficio($procedimento, $planoAtual);

        } else {
            $total += PlanosProcedimentos::getValorCliente($procedimento, $planoAtual);

        }

        return $total;

    }


    /**
     * petsPlanos
     */
    public function petsPlanos()
    {

        $petsPlanos = $this->pet->petsPlanos()->orderBy('created_at', 'DESC')->first();
        return $petsPlanos;
    }


    /**
     * petsPlanos
     */
    public function planoAtual()
    {
        $planoAtual = $this->petsPlanos()->plano()->first();
        return $planoAtual;
    }



    /**
     * Registra a participação no
     */
    public function participar()
    {
        if(!$this->id || $this->status !== self::STATUS_LIBERADO) {
            return;
        }

        $guiaCobrada = \App\Models\Participacao::where('id_guia', $this->id)->exists();

        if($guiaCobrada) {
            return;
        }

        /**
         * @var \App\Models\Pets $pet
         */
        $pet = $this->pet()->first();
        $plano = $this->plano()->first();
        $cliente = $pet->cliente()->first();
        $procedimento = $this->procedimento()->first();
        $planosProcedimentos = \App\Models\PlanosProcedimentos::where('id_procedimento', $procedimento->id)
                                                              ->where('id_plano', $plano->id)->first();
        $vigencias = $pet->vigencias(false);
        $vigenciaInicio = $vigencias[0]->format('Y-m-d');
        $vigenciaFim = $vigencias[1]->format('Y-m-d');

        //Prioriza o valor de mercado, porém, caso não haja, usa o valor base.
        $participacaoOriginal = $procedimento->valor_base * self::TAXA_PARTICIPACAO;
        if($planosProcedimentos->valor_cliente) {
            $participacaoOriginal = $planosProcedimentos->valor_cliente * self::TAXA_PARTICIPACAO;
        }


        if($pet->participativo) {
            $teto = $plano->teto_participativo;
            $participado = Participacao::participadoTotal($pet->id);
            $parcelaMaxima = ($teto/10);
            $participacao = $participacaoOriginal;

            if(($participado + $participacaoOriginal) > $teto) {
                $participacao = $teto - $participado;
            }

            $dados = [
                'pet' => $pet->nome_pet,
                'cliente' => $cliente->nome_cliente,
                'cpf' => $cliente->cpf,
                'vigencias' => $vigencias,
                'participacaoOriginal' => $participacaoOriginal,
                'teto' => $teto,
                'parcela_maxima' => $parcelaMaxima,
                'participado_total' => $participado,
                'participacao_calculada' => $participacao,
                'historico_uso' => $this->id,
                'procedimento' => $procedimento->nome_procedimento,
                'plano' => $plano->nome_plano
            ];

            $dadosJson = json_encode($dados);

            /**
             * Não existe participação para lançar e nesse caso será
             * gravado no log. O motivo é que o teto foi atingido.
             */
            if($participado >= $teto) {
                Logger::log(LogEvent::NOTICE, 'participativos-antigos', LogPriority::HIGH,
                "O cliente {$cliente->nome_cliente} já atingiu o teto de participação e será isento da cobrança de {$participacaoOriginal}\n\nDados: {$dadosJson}",
                 null,'clientes', $cliente->id);
                return true;
            }

            if($participacao == 0) {
                Logger::log(LogEvent::ERROR, 'participativos-antigos', LogPriority::HIGH,
                "A participação calculada atingiu o valor de R$ 0,00. Não haverá cobrança lançada.\n\nDados: {$dadosJson}",
                 null,'clientes', $cliente->id);
                return true;
            }

            $possivel = Participacao::competenciaLivre($pet->id);
            if($possivel['participacao'] !== 0) {
                $restanteMes = $parcelaMaxima - $possivel['participacao'];

                if($participacao > $restanteMes) {
                    $participacaoComplementar = $restanteMes;
                } else {
                    $participacaoComplementar = $participacao;
                }
                $participacao -= $participacaoComplementar;

                $competencia = $possivel['competencia'];

                Logger::log(LogEvent::NOTICE, 'participativos-antigos', LogPriority::HIGH,
                "Valor complementar de participação (R$ {$participacaoComplementar}) na competência livre {$competencia}.\n\nDados: {$dadosJson}",
                 null,'clientes', $cliente->id);
                

                $p = (new Participacao)->create([
                    'id_cliente' => $cliente->id,
                    'id_pet' => $pet->id,
                    'id_historico_uso' => $this->id,
                    'valor_participacao' => $participacaoComplementar,
                    'competencia' => $competencia,
                    'vigencia_inicio' => $vigenciaInicio,
                    'vigencia_fim' => $vigenciaFim,
                    'id_guia' => $this->id,
                    'agendado' => Carbon::createFromFormat(Participacao::FORMATO_COMPETENCIA, $possivel['competencia'])
                ]);

                $p->cobrar();
            }

            $razao = $participacao / $parcelaMaxima;
            $parcelas = floor($razao);
            $resto = $razao - $parcelas;

            if($parcelas > 0) {
                for($i = 0; $i < $parcelas; $i++) {
                    $possivel = Participacao::competenciaLivre($pet->id);

                    $parcela = $i+1;
                    $competencia = $possivel['competencia'];

                    Logger::log(LogEvent::NOTICE, 'participativos-antigos', LogPriority::HIGH,
                    "Parcela {$parcela}/{$parcelas} (R$ {$parcelaMaxima}) da participação total (R$ {$participacao}) na competência livre {$competencia}.\n\nDados: {$dadosJson}",
                     null,'clientes', $cliente->id);

                    $p = (new Participacao)->create([
                        'id_cliente' => $cliente->id,
                        'id_pet' => $pet->id,
                        'id_historico_uso' => $this->id,
                        'valor_participacao' => $parcelaMaxima,
                        'competencia' => $possivel["competencia"],
                        'vigencia_inicio' => $vigenciaInicio,
                        'vigencia_fim' => $vigenciaFim,
                        'id_guia' => $this->id,
                        'agendado' => Carbon::createFromFormat(Participacao::FORMATO_COMPETENCIA, $possivel['competencia'])
                    ]);

                    $p->cobrar();
                }
            }

            if($resto) {
                $possivel = Participacao::competenciaLivre($pet->id);
                $valorResto = round($parcelaMaxima * $resto, 4);

                $competencia = $possivel['competencia'];

                Logger::log(LogEvent::NOTICE, 'participativos-antigos', LogPriority::HIGH,
                    "Valor residual das parcelas (R$ {$valorResto}) da participacao total (R$ {$participacao}) na competência livre {$competencia}.\n\nDados: {$dadosJson}",
                     null,'clientes', $cliente->id);

                $p = (new Participacao)->create([
                    'id_cliente' => $cliente->id,
                    'id_pet' => $pet->id,
                    'id_historico_uso' => $this->id,
                    'valor_participacao' => $valorResto,
                    'competencia' => $possivel["competencia"],
                    'vigencia_inicio' => $vigenciaInicio,
                    'vigencia_fim' => $vigenciaFim,
                    'id_guia' => $this->id,
                    'agendado' => Carbon::createFromFormat(Participacao::FORMATO_COMPETENCIA, $possivel['competencia'])
                ]);

                $p->cobrar();
            }
        }
    }

    /**
     * Verifica se a data de liberação já foi alcançada
     * @return bool
     */
    public function dataLiberada()
    {
        if($this->tipo_atendimento != self::TIPO_ENCAMINHAMENTO) {
            return true;
        }

        if($this->data_liberacao) {
            return (new Carbon())->gte($this->data_liberacao);
        }

        return false;
    }

    /**
     * Adiciona um laudo na guia e registra no log
     * @param $laudo
     */
    public function appendLaudo($laudo)
    {
        $dataAlteracao = (new \Carbon\Carbon());
        $formattedData = $dataAlteracao->format('d/m/Y H:i');

        $laudoEditavel = (new \Carbon\Carbon())->lte($this->created_at->addHours(12));

        if($this->realizado_em && (new \Carbon\Carbon())->lte($this->realizado_em->addHours(12))) {
            $laudoEditavel = true;
        }

        if($laudoEditavel){
            $laudoAtual = $this->laudo;
            $this->laudo = $laudoAtual . "\n " . $formattedData . " - " . $laudo;
            $this->update();

            $mensagem = "O laudo foi alterado e encaminhado para a auditoria.";
            AppBaseController::setMessage($mensagem, 'success', 'Sucesso!');
            Logger::log(LogMessages::EVENTO['ALTERACAO'], 'Histórico de uso',
                'MEDIA', $mensagem,
                auth()->user()->id, 'historico_uso', $this->id);
        } else {

            self::setMessage("O tempo de alteração do laudo expirou.", 'warning', 'Oops!');

        }
    }

    public function solicitante()
    {
        return $this->belongsTo(Clinicas::class, 'id_solicitador', 'id');
    }

    public function semValor()
    {
        if($this->status !== self::STATUS_LIBERADO) {
            return true;
        }

        if($this->tipo_atendimento === self::TIPO_ENCAMINHAMENTO) {
            return is_null($this->realizado_em);
        }

        return false;
    }

    public static function usosPorCredenciada(Clinicas $credenciada,
                                              Procedimentos $procedimento,
                                              Carbon $start = null, Carbon $end = null,
                                              $status = self::STATUS_LIBERADO) {
        $query = HistoricoUso::where('id_clinica', $credenciada->id)
                             ->where('id_procedimento', $procedimento->id)
                             ->where('status', $status);
        if($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }
        return $query->count();
    }

    public function dataGuia()
    {
        return $this->tipo_atendimento === self::TIPO_ENCAMINHAMENTO ? $this->realizado_em : $this->created_at;
    }

    public function sendSmsAvaliacao() {
        $prestador = $this->prestador;
        $nomePrestador = explode(' ', $prestador->nome);
        //$message = new Message($this->pet->cliente->celular, "Avalie o atendimento do(a) prestador(a) " . $nomePrestador[0] . " respondendo esta mensagem com uma nota de 1 a 5");
        //return $message->send("avaliacao_credenciado", $this->numeroGuia);
    }

    public function sendMailAvaliacao()
    {
        $data = [
            'guia' => $this,
        ];

        Mail::send('mail.credenciados.avaliacao', $data, function($message) {
            $message->to($this->pet->cliente->email)
                ->subject('Lifepet - Avaliação de atendimento');
        });

        return ['status' => true];
    }

    public function gerarAssinaturaCliente($meio = 1){
        $assinatura = $this->pet->cliente->id . $this->pet->id . $this->numero_guia;
        $this->assinatura_cliente = md5($assinatura);
        $this->data_assinatura_cliente = new Carbon();
        $this->meio_assinatura_cliente = $meio;
        $this->save();
    }

    public function gerarAssinaturaPrestador(){
        $assinatura = $this->prestador->id . $this->pet->id . $this->numero_guia;
        $this->assinatura_prestador = md5($assinatura);
        $this->data_assinatura_prestador = new Carbon();
        $this->save();
    }

    public function canPagamentoAlternativo()
    {
        $petsPlanos = $this->pet->petsPlanos()->orderBy('created_at', 'DESC')->first();
        $planoAtual = $petsPlanos->plano()->first();

        if ($planoAtual->participativo && $this->status === self::STATUS_RECUSADO)
        {
            return true;
        }

        return false;

    }

    public function anexos()
    {
        return AnexosGuia::where('numero_guia', $this->numero_guia)->get();
    }
}
