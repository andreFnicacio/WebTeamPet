<?php

namespace Modules\Guides\Services;

use App\Exceptions\ClienteInadimplenteException;
use App\Exceptions\ClienteInativoException;
use App\Helpers\API\Financeiro\Financeiro;
use App\Helpers\API\Zenvia\Message;
use App\Helpers\Permissions;
use App\Helpers\Utils;
use App\Http\Controllers\AppBaseController;
use App\Http\Util\LogEvent;
use App\Http\Util\Logger;
use App\Http\Util\LogPriority;
use App\Models\Clientes;
use App\Models\Cobrancas;
use App\Models\Especialidades;
use App\Models\FaixasPlanos;
use App\Models\Pets;
use App\Models\PetsPlanos;
use App\Models\Planos;
use App\Models\PlanosCredenciados;
use App\Models\PlanosProcedimentos;
use App\Models\Procedimentos;
use App\Models\TabelasReferencia;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Clinics\Entities\Clinicas;
use Modules\Guides\Entities\AnexosGuia;
use Modules\Guides\Entities\HistoricoUso;
use Modules\Veterinaries\Entities\Prestadores;

class GuideService
{
    const ERRO_DEVERIA_EMITIR_RETORNO = 'Sua solicitação foi recusada. É necessária uma guia de retorno. Poderá ser feita emitindo uma guia com apenas o procedimento RETORNO DE CONSULTA.';
    const CREDENCIADO_NAO_HABILITADO  = 'O credenciado não está habilitado para o plano. A solicitação irá para a auditoria.';
    const GUIA_ADMINISTRATIVA = 'GUIA ADMINISTRATIVA';
    const VERIFIQUE_OS_PROBLEMAS_NA_GUIA = "Sua solicitação foi recusada.\nVerifique os problemas na guia e tente novamente:\n";
    const ERRO_PERIODO_PRE_EXISTENCIA = "A guia foi recusada devido a pré-existência de doenças relacionadas e o não cumprimento do período de 1 ano.";
    const GUIA_ISENTA_NAO_PAGA = "A guia foi recusada pois o credenciado declarou não receber o valor diretamente do cliente.";
    //const GUIA_PARTICIPATIVO_CARTAO_NAO_PAGA = "A guia foi recusada pois o pagamento não foi confirmado. Solicite ao cliente para verificar se o cartão está corretamente cadastrado ou se possui limite. No app da Lifepet, é possível cadastrar num novo cartão.";
    const GUIA_PARTICIPATIVO_CARTAO_NAO_PAGA = "A guia foi recusada. Favor orientar o cliente a entrar em contato com o suporte através do seu aplicativo e informar o erro FI-51.";
    //const GUIA_PARTICIPATIVO_CARTAO_PAGAMENTO_NAO_CONFIRMADO = "A guia foi recusada pois a captura do pagamento não foi confirmada. No app da Lifepet, é possível cadastrar num novo cartão.";
    const GUIA_PARTICIPATIVO_CARTAO_PAGAMENTO_NAO_CONFIRMADO = "A guia foi recusada. Favor orientar o cliente a entrar em contato com o suporte através do seu aplicativo e informar o erro FI-52.";
    const DADOS_CLIENTE_NAO_ENCONTRADOS_SF = 'Não foi possível obter os dados de pagamento do cliente.';
    const VALOR_COBRANCA_ZERADO = 'O valor total dos procedimentos da guia não configura cobrança e totaliza R$ 0,00.';
    const PARTICIPACAO_GUIA_RETORNO = 'A emissão da guia não possui cobrança. Guia de retorno com valor total de R$ 0,00';

    const SESSION_KEY__PAGAMENTO_ALTERNATIVO = 'guiasPagamentoAlternativoHabilitado';

    private $request = null;

    /**
     * @var Pets $pet
     */
    public $pet = null;

    /**
     * @var Clinicas|Clinicas[]|\Illuminate\Database\Eloquent\Collection|null
     */
    public $clinica = null;

    /**
     * @var Prestadores
     */
    public $prestador = null;
    public $id_prestador_solicitante = null;

    /**
     * @var Clinicas|\Illuminate\Database\Eloquent\Model|null
     */
    public $solicitador = null;

    /**
     * @var Clientes
     */
    public $cliente = null;

    /**
     * @var PetsPlanos $petsPlanos
     */
    public $petsPlanos = null;

    /**
     * @var Planos
     */
    public $planoAtual = null;

    public $numeroGuia = null;

    public $tabelaValores = null;
    public $especialidade = null;
    public $preExistencia = null;
    public $preCirurgico = null;
    public $procedimentoPreCirurgico = null;
    /**
     * @var Procedimentos[]|array|null|string
     */
    public $procedimentos = null;
    public $tipoAtendimento = null;
    public $laudo = null;
    public $observacao = null;
    public $justificativa = "";

    public $erros = null;
    public $alertas = null;

    public $autorizacao = null;
    public $id_autorizador = null;
    public $status = null;
    public $liberacaoAutomatica = false;
    public $administrativa = false;

    public $isento = null;
    public $pago = null;

    public $created_at = null;

    public $possuiAnestesia = false;
    public $possuiCirurgia = false;
    public $possuiInternacao = false;

    /**
     * @var Logger|null
     */
    public $logger = null;

    private $pagamentoAlternativoHabilitado = false;
    public $tentarNovoPagamento = false;

    public $cobrar = true;

    public function __construct(Request $request)
    {
        $this->request = $request;

        //$this->numeroGuia = self::getNumeroGuia();

        $this->anexos = $request->file('file') ?? null;

        $this->pet = (new Pets())->find($request->input('id_pet'));
        $this->clinica = (new Clinicas())->find($request->input('id_clinica'));
        $this->prestador = (new Prestadores())->find($request->input('id_prestador'));
        $this->solicitador = $this->getSolicitador();
        $this->especialidade = (new Especialidades())->find($request->input('id_especialidade'));

        $this->cliente = $this->pet->cliente;

        $this->petsPlanos = $this->pet->petsPlanos()->orderBy('created_at', 'DESC')->first();
        $this->planoAtual = $this->petsPlanos->plano()->first();

        $this->tabelaValores = $this->clinica->tabelaReferencia;

        $this->preExistencia = $request->input('pre_existencia');
        $this->preCirurgico  = $request->input('pre_cirurgico');
        $this->procedimentoPreCirurgico  = $request->input('procedimento_pre_cirurgico');
        $this->procedimentos = $request->input('procedimentos', []);
        $this->tipoAtendimento = $request->input('tipo_atendimento');
        $this->laudo = $request->input('laudo');

        $this->observacao = $request->input('observacao');

        $this->administrativa = $request->input('administrativa', $this->administrativa);

        $this->autorizacao = $request->input('autorizacao', 'AUDITORIA');
        $this->status = $this->autorizacao === "FORCADO" ? "LIBERADO" : 'AVALIANDO';
        if ($this->pet->contem_doenca_pre_existente){
            foreach ($this->procedimentos as $p){
                $procedimento = (new Procedimentos())->find($p);
                if ($procedimento->id_grupo == 10101048 || $procedimento->id_grupo == 10200){
                    $this->autorizacao = 'AUDITORIA';
                    $this->status = 'AVALIANDO';
                    break;
                }
            }
        }
        $this->erros = [];
        $this->alertas = [];

        $this->created_at = new Carbon();

        $this->logger = new Logger('emissao-guias', 'historico_uso');


        $this->isIsento($request);
        $this->isPago($request);

        $this->habilitarPagamentoAlternativo();

        if (Permissions::podeEmitirGuiaRetroativa()) {
            if ($request->filled('created_at')) {
                $this->created_at = Carbon::createFromFormat('d/m/Y', $request->get('created_at'));
            }
        }

       if (!$this->clienteHabilitadoProcedimentos()) {
           throw new ClienteInativoException();
       }

        if ($this->administrativa) {
            if (Permissions::podeEmitirGuiaAdministrativa()) {
                $this->justificativa = 'GUIA ADMINISTRATIVA';
                $this->habilitarLiberacaoAutomatica();
            }
            $this->verificarEmissaoCobranca($request);
        } else {
            if (!$this->pet->isAnual() && $this->pet->statusPagamento() !== "Em dia") {
                throw new ClienteInadimplenteException();
            }
        }

        if ($this->tipoAtendimento === HistoricoUso::TIPO_ENCAMINHAMENTO) {
            $this->id_prestador_solicitante = $this->prestador->id;
        } elseif ($this->tipoAtendimento === HistoricoUso::TIPO_EMERGENCIA) {
            $this->habilitarLiberacaoAutomatica();
        }

        $this->definirProcedimentos();

        $this->id_autorizador = 2;
        if ($this->autorizacao === HistoricoUso::AUTORIZACAO_FORCADO) {
            $this->id_autorizador = Auth::user()->id;
        }
    }

    /**
     * Retorna o próximo número de guia possível
     * @return int
     */
    private static function getNumeroGuia()
    {
        //$ultimaGuia = (new HistoricoUso())->max('numero_guia');
        //$numeroGuia = (int) $ultimaGuia + 1;
        $name = "LPT";
        if(auth()->user()) {
            $name = auth()->user()->name;
        }
        $name = Utils::remove_accents($name);
        $name = strtoupper($name);
        $vowels = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U", " ");
        $name = str_replace($vowels, "", $name);

        $name = preg_replace("/[^a-zA-Z]+/", "", $name);
        $creatorTreeIdentifier = substr($name, 0, 3);
        if(!$creatorTreeIdentifier) {
            $creatorTreeIdentifier = 'LPT';
        }

        //$key = $creatorTreeIdentifier . '_' . bin2hex(random_bytes(6));
        //hexdec(bin2hex(random_bytes(4)));
        $randNumber = rand(1, 9999);
        $key = $creatorTreeIdentifier . date('Ymd') . str_pad($randNumber, 4, "0", \STR_PAD_LEFT);

        return $key;
    }

    /**
     * Indica se o cliente e o pet estão ativos e aptos para realizar procedimentos
     * @return bool
     */
    public function clienteHabilitadoProcedimentos()
    {
        return $this->pet->ativo && $this->cliente->ativo;
    }

    private function adicionarProcedimentosInternacao()
    {
        if ($this->request->input('internacao')) {
            $dias = (int) $this->request->input('dias_internacao');
            for($i = 0; $i < $dias; $i++) {
                if ($this->request->input('tipo_internacao')) {
                    $this->procedimentos[] = $this->request->input('tipo_internacao');
                }
            }
        }
    }

    private function adicionarProcedimentosPreCirurgicos()
    {
        if ($this->preCirurgico && !in_array($this->procedimentoPreCirurgico, $this->procedimentos)) {
            $this->procedimentos[] = $this->procedimentoPreCirurgico;
        }
    }

    public function definirProcedimentos()
    {
        if (!empty($this->procedimentos) && !is_array($this->procedimentos)) {
            $this->procedimentos[] = $this->procedimentos;
        }

        /**
         * Lançando procedimentos pré-cirúrgicos
         */
        // $this->adicionarProcedimentosPreCirurgicos();

        /**
         * Lançando procedimentos de internação
         */
        $this->adicionarProcedimentosInternacao();
    }

    public function getProcedimentos()
    {
        $procedimentos = (new Procedimentos)->whereIn('id', $this->procedimentos)->get();
        $guideService = $this;
        $procedimentos = $procedimentos->map(function(Procedimentos $procedimento) use ($guideService, $procedimentos) {
            if ($this->temErros()) {
                return false;
            }

            if($this->possuiCirurgia === false) {
                $this->possuiCirurgia = $procedimento->isCirurgia();
            }

            if($this->possuiAnestesia === false) {
                $this->possuiAnestesia = $procedimento->isAnestesia();
            }

            if ($this->possuiInternacao === false) {
                $this->possuiInternacao = $procedimento->isInternacao();
            }
            
            $valorMomento = $guideService->getValorProcedimento($procedimento);
            $valorMomento = $valorMomento * (1 + Procedimentos::adicionalExclusividade($procedimento, $guideService->clinica));
            $procedimento->valor_momento = $valorMomento;

            if ($procedimento->liberacao_automatica) {
                $this->habilitarLiberacaoAutomatica();
            }

            if ($procedimento->nome_procedimento !== 'RETORNO DE CONSULTA') {
                if ($procedimento->shouldBeRetorno($guideService->pet, $guideService->clinica) &&
                    $guideService->tipoAtendimento != HistoricoUso::TIPO_ENCAMINHAMENTO &&
                    $guideService->tipoAtendimento != HistoricoUso::TIPO_EMERGENCIA) {

                    $this->adicionarErro(self::ERRO_DEVERIA_EMITIR_RETORNO);
                    return $procedimento;
                }
            }

            if (!$this->isProcedimentosConsulta()) {
                if ($this->preExistencia && !$this->pet->realizarPreExistencias()) {
                    $this->adicionarErro(self::ERRO_PERIODO_PRE_EXISTENCIA);
                    return $procedimento;
                }
            }

            $quantidades = $procedimentos->where('id_grupo', $procedimento->id_grupo)->count();
            $valido = $this->pet->validarProcedimento($procedimento, $this->tipoAtendimento, $quantidades);

            //TODO: Verificar utilização do 'pré-cirúrgico' nas regras de negócio do sistema. Está sendo checado porém 'código_erro' não existe.
            if (!$valido['status']) {
                $liberadoPreCirurgico = false;
                if ($this->preCirurgico && $valido['codigo_erro'] == Pets::CODIGO_ERRO_CARENCIA) {
                    foreach ($this->pet->getPlacarCarenciasPorGrupo() as $g) {
                        if (in_array($g['grupo']->id, ["99911", "99909"]) && $g['carencia_status'] == $this->pet->STATUS_CARENCIA_COMPLETO) {
                            $liberadoPreCirurgico = true;
                        }
                    }
                }
                if (!$liberadoPreCirurgico) {
                    $this->adicionarAlerta($valido['mensagem']);
                    $this->desabilitarLiberacaoAutomatica();
                }
            }

            return $procedimento;
        });

        return $procedimentos;
    }

    private function getValorProcedimento(Procedimentos $procedimento)
    {
        //Caso o plano seja isento de cobrança, é a prioridade zerar o valor.
        if($this->planoAtual->isento) {
            return 0;
        }

        $valorFaixa = 1;
        if ($this->clinica->aceite_urh) {
            $gruposExcecao = FaixasPlanos::$gruposExcecao;
            if ($this->planoAtual->faixa) {
                if (!in_array($procedimento->id_grupo, $gruposExcecao)) {
                    $valorFaixa = $this->planoAtual->faixa->valor;
                }
            }
        }

        $valorUrh = 1;
        if ($this->clinica->urh) {
            $valorUrh = $this->clinica->urh->valor_urh;
        }

        if (!$this->clinica->tabelaReferencia->tabela_base) {
            $valorTabelaEspecifica = $this->clinica->getValorProcedimento($procedimento);
            if ($valorTabelaEspecifica) {
                return $valorTabelaEspecifica * $valorFaixa;
            }
        }

        $valorProcedimentoPlano = PlanosProcedimentos::getValorProcedimento($procedimento,$this->planoAtual);
        if ($valorProcedimentoPlano) {
            return $valorProcedimentoPlano;
        }

        $valorTabelaBase = TabelasReferencia::getValorBaseProcedimento($procedimento);
        return $valorTabelaBase * $valorFaixa * $valorUrh;
    }

    public function isProcedimentosConsulta() {
        foreach($this->procedimentos as $procedimento) {
            $p = (new Procedimentos)->find($procedimento);
            if (!$p->isConsulta()) {
                return false;
            }
        }

        return true;
    }

    private function planoAtendido()
    {
        return (new PlanosCredenciados())->where('id_plano', $this->planoAtual->id)
            ->where('id_clinica', $this->clinica->id)
            ->where('habilitado', 1)
            ->exists();
    }

    public function validarGuia()
    {
        $planoAtendido = $this->planoAtendido();
        if (!$planoAtendido) {
            $this->adicionarAlerta(self::CREDENCIADO_NAO_HABILITADO);
        }
    }

    private function adicionarErro($erro)
    {
        $this->erros[] = $erro;
    }

    public function temErros()
    {
        return count($this->erros) > 0;
    }

    private function adicionarAlerta($alerta)
    {
        $this->alertas[] = $alerta;
        $this->mudarStatus(HistoricoUso::STATUS_RECUSADO);
        $this->justificativa .= "\n" . $alerta;
    }

    private function mudarStatus($status)
    {
        if ($this->status === HistoricoUso::STATUS_RECUSADO) {
            return;
        }

        $this->status = $status;
    }

    private function habilitarLiberacaoAutomatica($forcar = false)
    {
//        if (!$this->liberacaoAutomatica && !$forcar) {
//            return;
//        }

        $this->autorizacao = "AUTOMATICA";
        $this->id_autorizador = 2;
        $this->status = HistoricoUso::STATUS_LIBERADO;
        $this->liberacaoAutomatica = true;
    }
    private function desabilitarLiberacaoAutomatica()
    {
        $this->liberacaoAutomatica = false;
    }

    public function smsLiberado()
    {
        $agora = (new Carbon())->format('d/m/Y \à\s H:i');
        return "A guia #$this->numeroGuia foi liberada em $agora e precisamos da sua assinatura. Confira e assine todas as suas guias pelo app!";
    }

    public function smsRecusado()
    {
        $agora = (new Carbon())->format('d/m/Y \à\s H:i');
        return "Sua guia #$this->numeroGuia foi recusada em $agora. Confira suas guias no App Lifepet.";
    }

    public function emitirGuia()
    {
        $procedimentos = $this->getProcedimentos();
        $historicos = [];

        //Evita colisão de guia existente.
        do {
            $this->numeroGuia = self::getNumeroGuia();
            $exists = HistoricoUso::where('numero_guia', $this->numeroGuia)->exists();
        } while ($exists);

        $this->verificarPlanosIsentos();
        $this->verificarParticipativos();
        $this->verificarAnestesiaSemCirugia();
        $this->shouldDisableAutomaticHospitalization();

        if ($this->temErros()) {
            $this->notificarErro("A tentativa de emissão foi interrompida pois encontramos o seguinte erro:\n " . $this->getMensagensErros());
            throw new \Exception("A tentativa de emissão foi interrompida pois encontramos o seguinte erro:\n " . $this->getMensagensErros());
        } else {
            if ($this->alertas) {
                $mensagens = $this->getMensagensAlertas();
                $this->notificarRecusa(self::VERIFIQUE_OS_PROBLEMAS_NA_GUIA . $mensagens);
                $this->mudarStatus(HistoricoUso::STATUS_RECUSADO);
            }
            foreach ($procedimentos as $procedimento){
                if ($this->pet->contem_doenca_pre_existente){
                    if ($procedimento->id_grupo == 10101048 ||
                            $procedimento->id_grupo == 10200 ||
                            $procedimento->id_grupo == 10101050 ) {
                        $this->autorizacao = 'AUDITORIA';
                        $this->status = 'AVALIANDO';
                    }
                } elseif ($procedimento->id_grupo == 10101050){
                    $this->autorizacao = 'AUDITORIA';
                    $this->status = 'AVALIANDO';
                }
            }

            foreach ($procedimentos as $procedimento) {

                $data = [
                    'id_pet' => $this->pet->id,
                    'id_procedimento' => $procedimento->id,
                    'id_plano' => $this->planoAtual->id,
                    'id_prestador' => $this->prestador->id,
                    'id_prestador_solicitante' => $this->id_prestador_solicitante,
                    'id_clinica' => $this->clinica->id,
                    'id_especialidade' => $this->especialidade->id,
                    'numero_guia' => $this->numeroGuia,
                    'valor_momento' => $procedimento->valor_momento,
                    'justificativa' => $this->justificativa,
                    'laudo' => $this->laudo,
                    'observacao' => $this->observacao,
                    'autorizacao' => $this->autorizacao,
                    'tipo_atendimento' => $this->tipoAtendimento,
                    'status' => $this->status,
                    'id_autorizador' => $this->id_autorizador,
                    'id_solicitador' => $this->solicitador ? $this->solicitador->id : null,
                    'created_at' => $this->created_at,
                    'data_liberacao' => $this->getDataLiberacao(),
                ];

                $historicos[] = (new HistoricoUso())->create($data);
            }

            if($this->anexos){
                AnexosGuia::adicionarAnexos($this->anexos, $this->numeroGuia);
            }

            
        }
        if($this->status !== HistoricoUso::STATUS_RECUSADO) {
            $this->notificarSucesso($historicos);
        }

        return $historicos;
    }

    public function getDataLiberacao()
    {
        if(!$this->alertas) {
            return Carbon::now();
        }

        return null;
    }

    /**
     * @return string
     */
    protected function getMensagensErros()
    {
        $mensagens = join("\n\n", $this->erros);
        return $mensagens;
    }

    /**
     * @return string
     */
    protected function getMensagensAlertas()
    {
        $mensagens = join("\n\n", $this->alertas);
        return $mensagens;
    }

    private function notificarLiberacao($msg) {
        AppBaseController::setMessage($msg, 'success', 'Parabéns');
    }

    private function notificarRecusa($msg) {
        AppBaseController::setMessage($msg, 'warning', 'Guia Recusada');
    }

    private function notificarErro($msg) {
        AppBaseController::setMessage($msg, 'error', 'Emissão Recusada');
    }

    private function notificarAtencao($msg, $title = 'Atenção') {
        AppBaseController::setMessage($msg, 'info', $title);
    }

    /**
     * @param HistoricoUso[] $historicos
     */
    private function notificarSucesso($historicos) {
        if (!$this->liberacaoAutomatica) {
            if ($this->autorizacao != "FORCADO") {
                $this->notificarLiberacao('Sua solicitação foi enviada para análise. A resposta será dada em alguns instantes.');
            } else {
                $this->notificarLiberacao('A guia foi liberada de maneira forçada. Está sujeita à glosa.');
                if ($this->tipoAtendimento !== "ENCAMINHAMENTO") {
                    self::sms($this->cliente->celular, $this->smsLiberado());
                }
            }
        } else {
            $this->notificarLiberacao('Sua guia foi liberada automaticamente.');
            if ($this->tipoAtendimento !== "ENCAMINHAMENTO") {
                self::sms($this->cliente->celular, $this->smsLiberado());
            }
            
        }

        if ($this->tipoAtendimento === "ENCAMINHAMENTO") {
            $mensagem = "Informe ao cliente que o exame foi autorizado, porém, ele deverá acessar o aplicativo e agendar nos próximos 15 dias a realização em algum credenciado. Basta apertar em “+ Mais” > Rede Credenciada. Informe que a guia estará visível assim que ele acessar o app.";
            $titulo = "Prezado credenciado";

            $this->notificarAtencao($mensagem, $titulo);
        }

        /**
         * Caso plano seja Novo, não deixa ir para a cobrança dos antigos.
         */
        if ($this->planoAtual->participativo) return;

        /**
         * Cobranca de participativos antigos (< 2020)
         */
        if ($this->pet->participativo) {
            foreach($historicos as $h) {
                $h->participar();
            }
        }
    }

    public static function sms($celular, $mensagem, $id = null)
    {
        $message = new Message($celular, $mensagem);
        return $message->send($id, null, true);
    }

    /**
     * @return Clinicas|\Illuminate\Database\Eloquent\Model|null
     */
    protected function getSolicitador()
    {
        return (new Clinicas())->where('id_usuario', auth()->user()->id)->first();
    }

    protected function verificarPlanosIsentos() {
        if(!$this->cobrar) {
            return;
        }

        if($this->isento && !$this->pago) {
            $this->adicionarAlerta(self::GUIA_ISENTA_NAO_PAGA);
        }
    }

    /**
     * @doc Verificando se o pet é participativo depois de OUT/2020
     */
    protected function verificarParticipativos() {
        if(!$this->cobrar) {
            return;
        }

        if($this->planoAtual->participativo) {
            $this->pagamentoAlternativoHabilitado = true;
            $this->participar();
        }
    }

    /**
     * @return float|mixed
     */
    private function valorTotal() {
        $total = 0;
        //Verifica se o plano atual do pet eh um participativo de 2021
        if($this->planoAtual->participativo) {
            //Percorre todos os procedimentos
            foreach ($this->getProcedimentos() as $p) {
                //Soma o valor
                $total += PlanosProcedimentos::getValorBeneficio($p, $this->planoAtual);
            }
        } else {
            foreach ($this->getProcedimentos() as $p) {
                $total += PlanosProcedimentos::getValorCliente($p, $this->planoAtual);
            }
        }
        return $total;
    }

    private function detalheCobranca() {
        $message = "Referente à guia #{$this->numeroGuia}:\n";
        foreach ($this->getProcedimentos() as $procedimento) {
            $message .= "- {$procedimento->nome_procedimento}\n";
        }

        return $message;
    }

    private function isProcedimentosRetornoConsulta() {
        foreach($this->procedimentos as $procedimento) {
            $p = (new Procedimentos)->find($procedimento);
            if (!$p->isRetornoConsulta()) {
                return false;
            }
        }

        return true;
    }

    private function participar()
    {
        if(!$this->cobrar) {
            return;
        }

        //Interrompe o processo de pagamento evitando cobranças indevidas.
        if($this->temErros() || $this->alertas) {
            return;
        }


        $dadosJson = json_encode([
            'pet' => $this->pet->id,
            'cliente' => $this->cliente->id,
            'plano' => $this->planoAtual->nome_plano,
            'numero_guia' => $this->numeroGuia
        ]);

        $financeiro = new Financeiro();
        $customer = $this->cliente->getFinanceCustomer();
        if(!$customer) {
            self::adicionarErro(self::DADOS_CLIENTE_NAO_ENCONTRADOS_SF);
            return;
        }

        if($this->isProcedimentosRetornoConsulta()) {
            $this->logger->register(LogEvent::NOTICE, LogPriority::MEDIUM, self::PARTICIPACAO_GUIA_RETORNO . $dadosJson, $this->pet->id, 'pets');
            return;
        }

        $valor = $this->valorTotal();
        if($valor == 0) {
            $this->logger->register(LogEvent::NOTICE, LogPriority::HIGH, self::VALOR_COBRANCA_ZERADO . $dadosJson, $this->pet->id, 'pets');
            return;
        }
        

        $session = $financeiro->fingerprint();

        try {
            $payload = [
                'amount' => number_format($valor, 2),
                'customer_id' => $customer->id,
                'due_date' => Carbon::now()->format('Y-m-d'),
                'type' => 'creditcard',
                'fingerprint_ip' => $this->request->ip(),
                'fingerprint_session' => $session,
                'tags' => join(';', ['atendimento', 'participativo', 'venda', 'guia:' . $this->numeroGuia])
            ];

            $payment = $financeiro->pay($payload);
            
            $dados = json_encode([
                'enviado' => $payload,
                'recebido' => $payment
            ]);
            $this->logger->register(LogEvent::NOTICE, LogPriority::HIGH,
                "Houve um novo pagamento de participação.\nIdentificação: {$this->cliente->cpf}.\nGuia: #{$this->numeroGuia}\nDados: {$dados}"
            );
        } catch (\Exception $e) {
            $exception = "{$e->getMessage()}\n{$e->getFile()}:{$e->getLine()}";

            $dados = json_encode([
                'enviado' => $payload,
                'recebido' => null
            ]);

            $this->logger->register(LogEvent::ERROR, LogPriority::HIGH,
                "Houve um erro ao tentar processar o pagamento do cliente no SF.\nIdentificação: {$this->cliente->cpf}.\nGuia: #{$this->numeroGuia}\nExceção: {$exception} \nDados: {$dados}"
            );

            if($this->pagamentoAlternativoHabilitado) {
                self::adicionarAlerta('Não foi possível realizar o pagamento diretamente no cartão de crédito. Clique em "OK" para receber com Pix ou Picpay');
                $this->habilitarNovaTentativaPagamento();
                return;
            } else {
                self::adicionarAlerta(self::GUIA_PARTICIPATIVO_CARTAO_NAO_PAGA);
            }

            return;
        }
        if(!$payment || $payment->status != 'AVAILABLE') {
            
            $this->logger->register(LogEvent::ERROR, LogPriority::HIGH,
                "Houve um erro ao tentar processar o pagamento do cliente no SF.\nIdentificação: {$this->cliente->cpf}.\nGuia: #{$this->numeroGuia}\n\nDados: {$dados}"
            );
            self::adicionarAlerta(self::GUIA_PARTICIPATIVO_CARTAO_PAGAMENTO_NAO_CONFIRMADO);
            if($this->pagamentoAlternativoHabilitado) {
                $this->habilitarNovaTentativaPagamento();
                return;
            }

        } else {
            //Registra a cobrança no ERP
            Cobrancas::cobrancaAutomatica($this->cliente, $valor, $this->detalheCobranca(), null, null, $payment->id, true, $payment->id);
        }
    }

    /**
     * @param Request $request
     */
    private function isIsento(Request $request)
    {
        $this->isento = $request->input('isento', false);
        if ($this->isento == 'false') {
            $this->isento = false;
        } else if ($this->isento == 'true') {
            $this->isento = true;
        }
    }

    /**
     * @param Request $request
     */
    private function isPago(Request $request)
    {
        if ($this->isento) {
            $this->pago = $request->input('pago', false);
            if ($this->pago == 'false') {
                $this->pago = false;
            } else if ($this->pago == 'true') {
                $this->pago = true;
            }
        }
    }

    private function verificarEmissaoCobranca(Request $request) {
        if(!$request->has('emitir_cobranca')) {
            $this->cobrar = true;
            return;
        }

        $this->cobrar = (bool) $request->get('emitir_cobranca', true);

        return;
    }

    private function habilitarPagamentoAlternativo()
    {
        if($this->solicitador && in_array($this->solicitador->id, [237])) {
            $this->pagamentoAlternativoHabilitado = true;
            return;
        }

        if(in_array($this->clinica->id, [237, 68])) {
            $this->pagamentoAlternativoHabilitado = true;
        }
    }

    private function habilitarNovaTentativaPagamento()
    {
        $this->tentarNovoPagamento = true;

        $sessionKey = self::SESSION_KEY__PAGAMENTO_ALTERNATIVO;

        if(!session()->has($sessionKey)) {
            session()->put($sessionKey, []);
        }

        if(in_array($this->numeroGuia, session()->get($sessionKey))) {
            return;
        } else {
            session()->push($sessionKey, $this->numeroGuia);
        }
    }

    public static function removerTentativaPagamento($numeroGuia)
    {
        $sessionKey = self::SESSION_KEY__PAGAMENTO_ALTERNATIVO;

        if(!session()->has($sessionKey)) {
            return;
        }

        $guias = session()->get($sessionKey);
        $has = array_search($numeroGuia, $guias);

        if($has !== false) {
            unset($guias[$has]);
            session()->put($sessionKey, array_values($guias));
        }
    }

    public static function pagamentoAlternativoDisponivel($numeroGuia)
    {
        $sessionKey = self::SESSION_KEY__PAGAMENTO_ALTERNATIVO;

        if(!session()->has($sessionKey)) {
            return false;
        }

        $guias = session()->get($sessionKey);
        $has = in_array($numeroGuia, $guias);

        return $has;
    }

    public static function adicionarPagamentoAlternativo($numeroGuia)
    {
        $sessionKey = self::SESSION_KEY__PAGAMENTO_ALTERNATIVO;


        if(!session()->has($sessionKey)) {
            session()->put($sessionKey, []);
        }

        if(in_array($numeroGuia, session()->get($sessionKey))) {
            return;
        } else {
            session()->push($sessionKey, $numeroGuia);
        }
    }

    private function verificarAnestesiaSemCirugia()
    {
        if($this->possuiAnestesia && !$this->possuiCirurgia){
            $this->desabilitarLiberacaoAutomatica();
        }
    }

    private function shouldDisableAutomaticHospitalization()
    {
        if ($this->possuiCirurgia || $this->possuiInternacao) {
            $this->desabilitarLiberacaoAutomatica();
        }
    }
}
