<?php

namespace Modules\Guides\Http\Controllers;

use App\Exceptions\ClienteInadimplenteException;
use App\Exceptions\ClienteInativoException;
use App\Helpers\API\Financeiro\DirectAccess\Models\Sale;
use App\Helpers\API\Financeiro\Financeiro;
use App\Helpers\Permissions;
use App\Helpers\Utils;
use App\Http\Controllers\AppBaseController;
use App\Http\Util\LogEvent;
use App\Http\Util\Logger;
use App\Http\Util\LogMessages;
use App\Http\Util\LogPriority;
use App\Models\Cobrancas;
use App\Models\Procedimentos;
use App\User;
use Carbon\Carbon;
use Entrust;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mail;
use Modules\Clinics\Entities\Clinicas;
use Modules\Guides\Entities\AnexosGuia;
use Modules\Guides\Entities\GuiaGlosa;
use Modules\Guides\Entities\HistoricoUso;
use Modules\Guides\Services\GuideService;
use Modules\Veterinaries\Entities\Prestadores;
use Validator;

class AutorizadorController extends AppBaseController
{
    public $gruposExclusividadeLifepet = [
        '10100'
    ];

    const GLOSA_UPLOAD_TO = 'glosas/';

    /**
     * @return array
     */
    protected static function validationRules()
    {
        return [
            'id_pet' => 'required|numeric',
            'id_prestador' => 'required|numeric',
        ];
    }

    /**
     * @param $procedimentos
     * @return array
     */
    private static function definirProcedimentos(Request $request, $procedimentos)
    {
        $preCirurgico  = $request->input('pre_cirurgico');
        $procedimentoPreCirurgico  = $request->input('procedimento_pre_cirurgico');

        if (!empty($procedimentos) && !is_array($procedimentos)) {
            $procedimentos = [
                $procedimentos
            ];
        }

        // Define procedimentos pré-cirurgicos
        if ($preCirurgico && !in_array($procedimentoPreCirurgico, $procedimentos)) {
            $procedimentos[] = $procedimentoPreCirurgico;
        }

        /**
         * Lançando procedimentos de internação
         */
        self::definirProcedimentosInternacao($request, $procedimentos);

        return $procedimentos;
    }

    use AuthenticatesUsers;

    public function login() {
        return view('guides::login');
    }

    public function home(Request $request) {
        if(Auth::guest()) {
            return redirect(route('autorizador.login'));
        }

        // $tipo_atendimento = $this::isEmergencia() ? 'EMERGENCIA' : 'NORMAL';
        $tipo_atendimento = $request->get('tipo_atendimento', 'NORMAL');
        $microchip = $request->get('microchip', '');

        if(Entrust::hasRole(['AUTORIZADOR', 'ADMINISTRADOR', 'AUDITORIA', 'GRUPO_HOSPITALAR'])) {
            return view('guides::home', [
                'clinica' => new \Modules\Clinics\Entities\Clinicas,
                'tipo_atendimento' => $tipo_atendimento,
                'microchip' => $microchip
            ]);
        }

        if(!Entrust::hasRole(['CLINICAS'])) {
            self::setMessage('Você precisa ser uma clínica, grupo hospitalar ou autorizador para acessar esse conteúdo', 'error', 'Erro');
            return redirect(route('autorizador.login'));
        }

        $clinica = \Modules\Clinics\Entities\Clinicas::where('id_usuario', Auth::user()->id)->first();
        if(!$clinica) {
            self::setMessage('Você não está vinculado a nenhuma clínica no sistema. Entre em contato com a administração do sistema e tente novamente.', 'warning', 'Oops.');
            return redirect(route('autorizador.login'));
        }

        return view('guides::home', [
            'tipo_atendimento' => $tipo_atendimento,
            'clinica' => $clinica,
            'microchip' => $microchip
        ]);
    }

    public function tryLogin(Request $request) {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended(route('autorizador.verGuias'));
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [$this->username() => trans('auth.failed')];

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }

    public static function isProcedimentosConsulta(array $procedimentos) {
        foreach($procedimentos as $procedimento) {
            $p = Procedimentos::find($procedimento);
            if(!$p->isConsulta()) {
                return false;
            }
        }

        return true;
    }

    /**
     *
     * Realiza o registro da emissão da guia
     *
     * Obs.: Pode falar que isso aqui ficou uma obra prima ❤
     * @param Request $request
     */
    protected function emitirGuia(Request $request)
    {

        if(!Entrust::can('emitir_guia')) {
            return self::notAllowed();
        }

        $validator = Validator::make($request->all(), self::validationRules());

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }


        /**
         * Controle da construção da guia
         */
        try {
            /**
             * Inicia o DAO de HistoricoUso para criar a Guia
             */
            $guideService = new GuideService($request);
        } catch (ClienteInadimplenteException $e) {
            return $this->emissaoRecusada($e);
        } catch (ClienteInativoException $e) {
            return $this->emissaoRecusada($e);
        } catch (\Exception $e) {
            return $this->emissaoRecusada($e, $e->getTraceAsString());
        }

        /**
         * @var \Modules\Guides\Entities\HistoricoUso[]
         */

        $historicos = [];
        try {
            $historicos = $guideService->emitirGuia();
        } catch (\Exception $e) {
            return $this->emissaoRecusada($e);
        }

        if($guideService->tentarNovoPagamento) {
            return redirect(route('autorizador.pagamentoDireto', ['numeroGuia' => $guideService->numeroGuia]));
        }

        if ($guideService->status == HistoricoUso::STATUS_LIBERADO) {
            self::applyGamificationConsulta($guideService->numeroGuia);
        }

        if ($guideService->tipoAtendimento == HistoricoUso::TIPO_ENCAMINHAMENTO) {
            self::notifyClienteEmissaoEncaminhamento($guideService);
        }

        return redirect(route('autorizador.verGuias'));
    }

    public function agendar(Request $request) {
        if(!Entrust::hasRole(['ADMINISTRADOR', 'AUTORIZADOR', 'AUDITORIA'])) {
            return self::notAllowed();
        }

        $clinica = $request->get('clinica_encaminhamento', null);
        $prestador = $request->get('prestador_encaminhamento', null);
        $data = $request->get('data_liberacao');
        $numeroGuia = $request->get('numero_guia');

        $guias = HistoricoUso::where('numero_guia', $numeroGuia)->get();
        $clinica = Clinicas::find($clinica);
        $prestador = Prestadores::find($prestador);
        $data = Carbon::createFromFormat("Y-m-d\TH:i",$data);
        $formattedData = $data->format('d/m/Y H:i');
        $cliente = $guias[0]->pet()->first()->cliente()->first();

        $dataFill = [
            'data_liberacao' => $data,
            'status' => HistoricoUso::STATUS_LIBERADO,
            'autorizacao' => HistoricoUso::AUTORIZACAO_AUDITORIA,
            'id_autorizador' => auth()->user()->id,
        ];

        if ($clinica) {
            $dataFill['id_clinica'] = $clinica->id;
        }

        foreach($guias as $g) {
            $g->fill($dataFill);

            if($prestador) {
                $g->id_prestador = $prestador->id;
            }

            $g->update();
        }
        self::setMessage("As guias foram agendadas e liberadas para a data de {$formattedData}", 'info', 'Atenção');
        self::notifyClienteEncaminhamentoAgendamento($guias->first());
        //self::sms($cliente->celular, "Sua guia #{$numeroGuia} está liberada para agendamento a partir do dia {$formattedData}. Confira sua rede e agende direto com o credenciado.");
        return redirect(route('autorizador.guiasEncaminhamento'));
    }

    public function adicionarLaudo(Request $request)
    {
        $numeroGuia = $request->get('numero_guia');
        $guias = HistoricoUso::where('numero_guia', $numeroGuia)->get();
        $dataAlteracao = (new \Carbon\Carbon());
        $formattedData = $dataAlteracao->format('d/m/Y H:i');
        foreach ($guias as $guia){
            $laudoEditavel = (new \Carbon\Carbon())->lte($guia->created_at->addHours(HistoricoUso::HORAS_LAUDO_EDITAVEL));
            if($laudoEditavel){
                $laudoAtual = $guia->laudo;
                $guia->laudo = $laudoAtual."\n ".$formattedData." - ".$request->get('laudo_adicional');
                $guia->update();
                if($request->file('file')){
                    AnexosGuia::adicionarAnexos(
                        $request->file('file'), 
                        $request->get('numero_guia')
                    );
                }
                $mensagem = "O laudo foi alterado e encaminhado para a auditoria.";
                self::setMessage($mensagem, 'success', 'Sucesso!');
                Logger::log(LogMessages::EVENTO['ALTERACAO'], 'Histórico de uso',
                    'MEDIA', $mensagem,
                    auth()->user()->id, 'historico_uso', $guia->id);
            } else {

                self::setMessage("O tempo de alteração do laudo expirou.", 'warning', 'Oops!');

            }
        }

        return back();
    }

    public function adicionarAnexo(Request $request)
    {
        $v = Validator::make($request->all(), [
            'file' => 'required',
            'numero_guia' => 'required|string'
        ]);
        if ($v->fails()) {
            $messages = join("\n", $v->getMessageBag()->all());
            $messages = str_replace('file', 'O arquivo', $messages);
            self::setError($messages, 'Oops.');

            return back()
                ->withErrors($v)
                ->withInput();
        }

        if(!$request->hasFile('file')) {
            self::setError('Nenhum arquivo foi encontrado', 'Oops.');
            return back()->withErrors($v);
        }
        
        try{
            AnexosGuia::adicionarAnexos(
                $request->file('file'), 
                $request->get('numero_guia')
            );
            self::setSuccess('Anexos criados com sucesso');
            
        } catch(\Throwable $throwable) {
            self::setError($throwable->getMessage(), 'Oops.');
        }

        return back();
    }

    public static function definirProcedimentosInternacao($request, &$procedimentos)
    {
        if($request->input('internacao')) {
            $dias = (int) $request->input('dias_internacao');
            for($i = 0; $i < $dias; $i++) {
                if($request->input('tipo_internacao')) {
                    $procedimentos[] = $request->input('tipo_internacao');
                }
            }
        }
    }

    protected function emitirGuiaAdministrativa(GuideService $guideService)
    {
        try {
            $guideService->emitirGuia();
        } catch (\Exception $e) {
            $e->getMessage();
        }

        self::toast('Guia administrativa emitida com número', $guideService->numeroGuia, 'font-yellow-crusta');

        return redirect(route('autorizador.verGuias'));
    }

    public function guias(Request $request)
    {
        if(!Entrust::can('listar_guias')) {
            return self::notAllowed();
        }

        if($request->filled('start')) {
            $start = Carbon::createFromFormat('d/m/Y', $request->get('start'));
            if(!$request->get('end')) {
                $end = $start->copy()->lastOfMonth();
            } else {
                $end = Carbon::createFromFormat('d/m/Y', $request->get('end'));
            }
        } else {
            $start = new Carbon('first day of this month');
            $end = new Carbon('last day of this month');
        }
        $start->setTime(0,0,0);
        $end->setTime(23,59,59);
        /**
         * @var \Illuminate\Database\Eloquent\Builder $query
         */
        $query = \Modules\Guides\Entities\HistoricoUso::groupBy('numero_guia')
                ->orderBy('historico_uso.created_at','DESC')
                ->whereBetween('historico_uso.created_at', [$start, $end]);

        if($request->get('termo') != '') {
            $query->join('pets', 'pets.id', '=', 'historico_uso.id_pet');
            $query->where(function($query) use ($request) {
                $query->where('historico_uso.numero_guia', $request->get('termo'))
                    ->orWhere('pets.nome_pet', 'LIKE', '%' . $request->get('termo') . '%')
                    ->orWhere('pets.numero_microchip', 'LIKE', '%' . $request->get('termo') . '%');
            });
            $query->select('historico_uso.*');
        }

        $status = $request->get('status', null);
        if($status) {
            $query->where('status', $status);
        }

        $autorizacao = $request->get('autorizacao', null);
        if($autorizacao) {
            $query->where('autorizacao', $autorizacao);
        }

        if(Entrust::hasRole(['ADMINISTRADOR', 'AUTORIZADOR', 'AUDITORIA', 'ATENDIMENTO'])) {
            $query->where('tipo_atendimento', '<>', HistoricoUso::TIPO_ENCAMINHAMENTO);

        } else {
            if(Entrust::hasRole(['GRUPO_HOSPITALAR'])) {
                $grupo = \App\Models\GrupoHospitalar::where('id_usuario', Auth::user()->id)->first();
                $vinculados = $grupo->clinicas()->get()->pluck('id');
                $query->where(function($query) use ($vinculados) {
                    $query->whereIn('id_clinica', $vinculados)
                        ->orWhereIn('id_solicitador', $vinculados);
                });

            } else {
                $clinica = \Modules\Clinics\Entities\Clinicas::where('id_usuario', Auth::user()->id)->first();
                $query->where(function($query) use ($clinica) {
                    $query->where('id_clinica', $clinica->id)
                        ->orWhere('id_solicitador', $clinica->id);
                });
            }
        }

        $itemsPerPage = 15;
        $guias = $query->paginate($itemsPerPage);

        return view('guides::guias')->with([
            'guias' => $guias,
            'params' => [
                'start' => $start->format('d/m/Y'),
                'end'   => $end->format('d/m/Y'),
                'termo' => $request->get('termo'),
                'status' => $request->get('status'),
                'autorizacao' => $request->get('autorizacao')
            ]
        ]);
    }

    public function guiasEncaminhamento(Request $request)
    {
        if(!Entrust::can('listar_guias_encaminhamento')) {
            return self::notAllowed();
        }

        if($request->filled('start')) {
            $start = Carbon::createFromFormat('d/m/Y', $request->get('start'));
            if(!$request->get('end')) {
                $end = $start->copy()->lastOfMonth();
            } else {
                $end = Carbon::createFromFormat('d/m/Y', $request->get('end'));
            }
        } else {
            $start = new Carbon('first day of this month');
            $end = new Carbon('last day of this month');
        }
        $start->setTime(0,0,0);
        $end->setTime(23,59,59);
        /**
         * @var \Illuminate\Database\Eloquent\Builder $query
         */
        $query = \Modules\Guides\Entities\HistoricoUso::groupBy('numero_guia')
                ->orderBy('historico_uso.created_at','DESC')
                ->orderBy('historico_uso.data_liberacao', 'DESC')
                ->where(function($query) use ($start, $end) {
                    $query->whereBetween('historico_uso.created_at', [$start, $end]);
                    $query->orWhereBetween('historico_uso.data_liberacao', [$start, $end]);
                })
                ->where('historico_uso.tipo_atendimento', HistoricoUso::TIPO_ENCAMINHAMENTO);

        if($request->get('termo') != '') {
            // $query->whereHas('pet', function($query) use ($request) {
            //     $query->where('nome_pet', 'LIKE', '%' . $request->get('termo') . '%')
            //         ->orWhere('numero_microchip', 'LIKE', '%' . $request->get('termo') . '%');
            // });

            $query->join('pets', 'pets.id', '=', 'historico_uso.id_pet');
            $query->where(function($query) use ($request) {
                $query->where('historico_uso.numero_guia', $request->get('termo'))
                    ->orWhere('pets.nome_pet', 'LIKE', '%' . $request->get('termo') . '%')
                    ->orWhere('pets.numero_microchip', 'LIKE', '%' . $request->get('termo') . '%');
            });
            $query->select('historico_uso.*');
        }
        if(Entrust::hasRole(['ADMINISTRADOR', 'AUTORIZADOR', 'AUDITORIA'])) {
            $guias = $query->paginate(50);
        } else {
            $clinica = \Modules\Clinics\Entities\Clinicas::where('id_usuario', Auth::user()->id)->first();
            $query->where(function($query) use ($clinica) {
                $query->where('id_clinica', $clinica->id)
                    ->orWhere('id_solicitador', $clinica->id);
            });
            $guias = $query->paginate(50);
        }

        return view('guides::guias_encaminhamento')->with([
            'guias' => $guias,
            'params' => [
                'start' => $start->format('d/m/Y'),
                'end'   => $end->format('d/m/Y'),
                'termo' => $request->get('termo')
            ]
        ]);
    }

    public function buscarEncaminhamento(Request $request)
    {
        $clinica = \Modules\Clinics\Entities\Clinicas::where('id_usuario', Auth::user()->id)->first();
        if(!$clinica) {
            return self::notAllowed();
        }

        $guia = null;
        $procedimentos = null;
        $numero_guia = null;
        if($request->filled('numero_guia')) {
            $numero_guia = trim($request->get('numero_guia')); // 118294

            $historicos = (new HistoricoUso())->where('numero_guia', $numero_guia)
                ->where('tipo_atendimento', HistoricoUso::TIPO_ENCAMINHAMENTO)
                ->where('status', HistoricoUso::STATUS_LIBERADO)
                ->whereNull('realizado_em')
                ->whereNotNull('data_liberacao');

            $procedimentos = (new Procedimentos())->whereIn('id', $historicos->pluck('id_procedimento'))->get();
            $guia = $historicos->first();

            if (!$guia) {
                self::setError('Guia não encontrada!');
            }
        }

        return view('guides::buscar_encaminhamento')->with([
            'clinica' => $clinica,
            'numero_guia' => $numero_guia,
            'guia' => $guia,
            'procedimentos' => $procedimentos,
        ]);
    }

    public function realizarEncaminhamento(Request $request){

        HistoricoUso::where('numero_guia', $request->get('numero_guia'))
            ->update([
                'id_prestador' => $request->get('id_prestador'),
                'id_clinica' => $request->get('id_clinica'),
                'realizado_em' => (new \Carbon\Carbon())
            ]);

        self::setSuccess('Guia realizada com sucesso!');

        return redirect(route('autorizador.verGuias'));

    }

    public function guiasCancelar(Request $request)
    {
        if(!Entrust::can('listar_guias_cancelar') && Entrust::can('autorizar_guia')) {
            return self::notAllowed();
        }

        if($request->filled('start')) {
            $start = Carbon::createFromFormat('d/m/Y', $request->get('start'));
            if(!$request->get('end')) {
                $end = $start->copy()->lastOfMonth();
            } else {
                $end = Carbon::createFromFormat('d/m/Y', $request->get('end'));
            }
        } else {
            $start = new Carbon('first day of this month');
            $end = new Carbon('last day of this month');
        }
        $start->setTime(0,0,0);
        $end->setTime(23,59,59);
        /**
         * @var \Illuminate\Database\Eloquent\Builder $query
         */
        $query = \Modules\Guides\Entities\HistoricoUso::groupBy('numero_guia')
            ->orderBy('data_liberacao', 'DESC')
            ->orderBy('created_at','DESC')
            ->where(function($query) use ($start, $end) {
                $query->whereBetween('created_at', [$start, $end]);
                $query->orWhereBetween('data_liberacao', [$start, $end]);
            })
            ->whereNotNull('cancelamento');

        if($request->get('termo') != '') {
            $query->whereHas('pet', function($query) use ($request) {
                $query->where('nome_pet', 'LIKE', '%' . $request->get('termo') . '%')
                    ->orWhere('numero_microchip', 'LIKE', '%' . $request->get('termo') . '%');
            });
        }
        if(Entrust::hasRole(['ADMINISTRADOR', 'AUTORIZADOR', 'AUDITORIA'])) {
            $guias = $query->paginate(50);
        } else {
            $clinica = \Modules\Clinics\Entities\Clinicas::where('id_usuario', Auth::user()->id)->first();
            $query->where(function($query) use ($clinica) {
                $query->where('id_clinica', $clinica->id)
                    ->orWhere('id_solicitador', $clinica->id);
            });
            $guias = $query->paginate(50);
        }

        return view('guides::guias_canceladas')->with([
            'guias' => $guias,
            'params' => [
                'start' => $start->format('d/m/Y'),
                'end'   => $end->format('d/m/Y'),
                'termo' => $request->get('termo')
            ]
        ]);
    }

    public function autorizar(Request $request)
    {
        if(!Entrust::can('autorizar_guia')) {
            return self::notAllowed();
        }

        $validator = Validator::make($request->all(), [
            'numero_guia' => 'required'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }
        $numeroGuia = $request->input('numero_guia');

        $guia = HistoricoUso::where('numero_guia', $numeroGuia)->first();

        if ($guia->tipo_atendimento != HistoricoUso::TIPO_ENCAMINHAMENTO) {
            self::applyGamificationConsulta($numeroGuia);
        }

        $cliente = $guia->pet()->first()->cliente()->first();

        $agora = (new Carbon())->format('d/m/Y \à\s H:i');
        // $smsLiberado = "Sua guia #$numeroGuia foi liberada em $agora. Confira suas guias no App Lifepet.";
        // self::sms($cliente->celular, $smsLiberado);
        $status = $this->atualizarStatusGuia($numeroGuia, 'LIBERADO');
        GuideService::removerTentativaPagamento($numeroGuia);
        return [
            'status' => $status
        ];
    }

    public function recusar(Request $request) {
        if(!Entrust::can('autorizar_guia')) {
            return self::notAllowed();
        }

        $validator = Validator::make($request->all(), [
            'numero_guia' => 'required',
            'justificativa' => 'required'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $numeroGuia = $request->input('numero_guia');
        $status = $this->atualizarStatusGuia($request->input('numero_guia'), 'RECUSADO', $request->input('justificativa'));

        $guia = HistoricoUso::where('numero_guia', $numeroGuia)->first();
        $cliente = $guia->pet()->first()->cliente()->first();

        $agora = (new Carbon())->format('d/m/Y \à\s H:i');
//        $smsNegado = "Sua guia #$numeroGuia foi negada em $agora. Confira suas guias no App Lifepet.";
//        self::sms($cliente->celular, $smsNegado);
        GuideService::removerTentativaPagamento($numeroGuia);
        return [
            'status' => $status
        ];
    }

    private function atualizarStatusGuia($guia, $status, $justificativa = null)

    {
        $historico = \Modules\Guides\Entities\HistoricoUso::where('numero_guia', '=', $guia)->first();
        if(!$historico) {
            return false;
        }
        $data = [
            'status' => strtoupper($status),
            'id_autorizador' => Auth::user()->id
        ];
        if($justificativa) {
            $data = array_merge($data, [
                'justificativa' => $justificativa
            ]);
        }


        $saved = \Modules\Guides\Entities\HistoricoUso::where('numero_guia', '=', $guia)->update($data);
        $pet = $historico->pet()->first();
        if(strtoupper($status) === HistoricoUso::STATUS_LIBERADO && $pet->participativo) {
            /**
             * @var $guias HistoricoUso[]
             */
            $guias = \Modules\Guides\Entities\HistoricoUso::where('numero_guia', "=", $guia)->get();
            foreach($guias as $g) {
                $g->participar();
            }
        }

        $clinica = $historico->clinica()->first();
        if($clinica->email_contato == '') {
            return;
        }

        return $this->notifyCredenciado($clinica, [
            'status_guia' => $data['status'],
            'numero_guia' => $guia,
            'data'        => date('d/m/Y'),
            'hora'        => date('H:i:s')
        ]);
    }

    public function verGuia($numero_guia) {
        $query = \Modules\Guides\Entities\HistoricoUso::where('numero_guia', $numero_guia);
        if(Entrust::hasRole(['CLINICAS']) && !Entrust::hasRole(['AUTORIZADOR','ADMINISTRADOR', 'AUDITORIA'])) {
            $clinica = \Modules\Clinics\Entities\Clinicas::where('id_usuario', Auth::user()->id)->first();
            $query->where(function($query) use ($clinica) {
                $query->where('id_clinica', $clinica->id)
                    ->orWhere('id_solicitador', $clinica->id);
            });
        }

        $historicos = $query->get();

        return view('guides::guia')->with([
            'historicos' => $historicos,
            'guia' => $historicos->first(),
        ]);
    }

    public function notifyAtendimento(\Modules\Clinics\Entities\Clinicas $credenciado, array $dadosGuia)
    {
        $to      = "auditoria@lifepet.com.br";
        //$to      = "alexandre.moreira@lifepet.com.br";
        $subject = 'Emissão de Guia Nº ' . $dadosGuia['numero_guia'];
        $view  = view('mail.emissao_guia')->with($dadosGuia);
        $message = $view->render();
        $headers = 'From: Lifepet <auditoria@lifepet.com.br>' . "\r\n" .
                   'Reply-To: ' . $credenciado->email_contato . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);
    }

    public function notifyCredenciado(\Modules\Clinics\Entities\Clinicas $credenciado, array $dadosGuia)
    {

        $to      = $credenciado->email_contato;
        $subject = 'Atualização da Guia Nº ' . $dadosGuia['numero_guia'];
        $view  = view('mail.atualizacao_guia')->with($dadosGuia);
        $message = $view->render();
        $headers = 'From: Lifepet <auditoria@lifepet.com.br>' . "\r\n" .
                   'Reply-To: auditoria@lifepet.com.br' . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);
    }

    public function notifyClienteEmissaoEncaminhamento(GuideService $guideService)
    {
        $cliente = $guideService->cliente;
        $smsLiberado = "Uma nova guia de encaminhamento foi emitida. Confira suas guias no App Lifepet.";
        self::sms($cliente->celular, $smsLiberado);

        $data = [
            'nome_cliente' => ucwords(mb_strtolower($cliente->nome_cliente)),
            'nome_pet' => ucwords(mb_strtolower($guideService->pet->nome_pet)),
            'nome_clinica' => ucwords(mb_strtolower($guideService->clinica->nome_clinica)),
            'nome_prestador' => ucwords(mb_strtolower($guideService->prestador->nome)),
            'status_guia' => $guideService->status,
        ];

        Mail::send('mail.guia_emissao_encaminhamento', $data, function($message) use ($guideService, $cliente) {
            $message->to($cliente->email)
                ->subject("Guia de encaminhamento emitida");
        });

        return ['status' => true];
    }

    public function notifyClienteEncaminhamentoAgendamento(HistoricoUso $guia)
    {
        $numeroGuia = $guia->numero_guia;
        $cliente = $guia->pet->cliente;

        $smsLiberado = "Sua guia #$numeroGuia foi liberada para agendamento a partir de {$guia->data_liberacao->format('d/m/Y')}. Confira suas guias no App Lifepet.";
        self::sms($cliente->celular, $smsLiberado);

        $data = [
            'periodo_agendamento' => $guia->data_liberacao->format('d/m/Y') . " a " . $guia->data_liberacao->addDays(15)->format('d/m/Y'),
            'procedimentos' => \App\Models\Procedimentos::whereIn('id', (\Modules\Guides\Entities\HistoricoUso::where('numero_guia', $numeroGuia)->pluck('id_procedimento')))->get()->pluck('nome_procedimento'),
            'nome_cliente' => ucwords(mb_strtolower($cliente->nome_cliente)),
            'nome_pet' => ucwords(mb_strtolower($guia->pet->nome_pet)),
            'nome_clinica' => ucwords(mb_strtolower($guia->clinica->nome_clinica)),
            'nome_prestador' => ucwords(mb_strtolower($guia->prestador->nome)),
            'numero_guia' => $numeroGuia,
        ];

        Mail::send('mail.guia_encaminhamento_agendado', $data, function($message) use ($guia, $cliente, $numeroGuia) {
            $message->to($cliente->email)
                ->subject("Guia #{$numeroGuia} liberada para agendamento");
        });

        return ['status' => true];
    }

    public function formRealizar(Request $request, $numeroGuia)
    {
        $guia = HistoricoUso::where('numero_guia', $numeroGuia)->first();
        if($guia->realizado_em) {
            self::setWarning("A guia #{$numeroGuia} já foi realizada anteriormente.");
            return redirect(route('autorizador.verGuias'));
        }
        return view('guides::realizar', [
            'numero_guia' => $numeroGuia,
            'guia' => $guia
        ]);
    }

    /**
     * Função que define a realização de uma guia de encaminhamento.
     * @param Request $request
     */
    public function realizar(Request $request)
    {
        $today = new \Carbon\Carbon();
        $realizado_em = new Carbon();
        $validator = Validator::make($request->all(), [
            'numero_guia' => 'required',
            'id_prestador' => 'required|numeric',
            'laudo' => 'required'
        ]);


        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }
        /**
         * @var HistoricoUso[] $guias
         */
        $guias = HistoricoUso::where('numero_guia', $request->get('numero_guia'))->get();

        $primeiraGuia = $guias[0];
        $pet = $primeiraGuia->pet()->first();
        if(!$pet->isAnual() && $pet->statusPagamento() !== "Em dia") {
            self::setMessage(ClienteInadimplenteException::MESSAGE, 'error', 'Guia Recusada.');
            return back();
        }

        foreach($guias as $g) {

            $g->id_prestador = $request->get('id_prestador');
            $g->realizado_em = $realizado_em;

            $g->update();

            $g->appendLaudo($request->get('laudo'));
        }

        self::applyGamificationConsulta($request->get('numero_guia'));
        self::sms($pet->cliente->celular, "A guia #{$primeiraGuia->numero_guia} foi realizada e precisamos da sua assinatura. Confira e assine todas as suas guias pelo app!");

        return redirect(route('autorizador.verGuias'));
    }

    /*
     * Invalida automaticamente as guias que expiraram o prazo de execução de 15 dias
     */
    public static function invalidarEncaminhamentosExpirados()
    {
        $prazo = 15;
        $expirados = HistoricoUso::where('tipo_atendimento', HistoricoUso::TIPO_ENCAMINHAMENTO)
                                 ->where('data_liberacao', '<', (new Carbon())->subDays($prazo))
                                 ->whereNull('realizado_em')
                                 ->where('status', HistoricoUso::STATUS_LIBERADO)
                                 ->get();

        $userAutomacao = User::automacao();
        $userId = $userAutomacao ? $userAutomacao->id : 1;

        if(auth()->user()) {
           $userId = auth()->user()->id;
        }

        foreach($expirados as $e) {
            $e->status = HistoricoUso::STATUS_RECUSADO;
            $e->cancelamento = "Guia cancelada automaticamente por expirar o prazo de execução.";
            $e->update();
            $mensagem = "A guia #{$e->numero_guia} foi cancelada automaticamente por expirar o prazo de execução.";
            Logger::log(LogMessages::EVENTO['ALTERACAO'], 'Histórico de uso',
                'ALTA', $mensagem,
                $userId, 'historico_uso', $e->id);
        }
    }

    /**
     * Endpoint para a realização de cancelamentos
     * @param Request $request
     */
    public function solicitarCancelamento(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numero_guia' => 'required',
            'justificativa' => 'required',
        ]);
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $numeroGuia = $request->get('numero_guia');
        $guias = HistoricoUso::where('numero_guia', $numeroGuia)->get();

        foreach($guias as $g) {
            $g->cancelamento = $request->get('justificativa');
            $g->update();
        }

        $this->atualizarStatusGuia($request->input('numero_guia'), 'RECUSADO', $request->input('justificativa'));

        return ['status' => true];
    }

    public function glosas(Request $request)
    {
        if(!Entrust::can('listar_guias')) {
            return self::notAllowed();
        }

        if($request->filled('start')) {
            $start = Carbon::createFromFormat('d/m/Y', $request->get('start'));
            if(!$request->get('end')) {
                $end = $start->copy()->lastOfMonth();
            } else {
                $end = Carbon::createFromFormat('d/m/Y', $request->get('end'));
            }
        } else {
            $start = (new Carbon())->startOfYear();
            $end = (new Carbon())->endOfYear();
        }
        $start->setTime(0,0,0);
        $end->setTime(23,59,59);
        /**
         * @var \Illuminate\Database\Eloquent\Builder $query
         */
        $query = \Modules\Guides\Entities\HistoricoUso::groupBy('numero_guia')
            ->orderBy('historico_uso.created_at','DESC')
            ->whereIn('historico_uso.glosado', ['1', '3'])
            ->whereBetween('historico_uso.created_at', [$start, $end]);

        if($request->get('termo') != '') {
            $query->join('pets', 'pets.id', '=', 'historico_uso.id_pet');
            $query->where(function($query) use ($request) {
                $query->where('historico_uso.numero_guia', $request->get('termo'))
                    ->orWhere('pets.nome_pet', 'LIKE', '%' . $request->get('termo') . '%')
                    ->orWhere('pets.numero_microchip', 'LIKE', '%' . $request->get('termo') . '%');
            });
            $query->select('historico_uso.*');
        }
        if(Entrust::hasRole(['ADMINISTRADOR', 'AUTORIZADOR'])) {
            $query->where('tipo_atendimento', '<>', HistoricoUso::TIPO_ENCAMINHAMENTO);
            $guias = $query->paginate(50);
        } else {
            if(Entrust::hasRole(['GRUPO_HOSPITALAR'])) {
                $grupo = \App\Models\GrupoHospitalar::where('id_usuario', Auth::user()->id)->first();
                $vinculados = $grupo->clinicas()->get()->pluck('id');
                $query->where(function($query) use ($vinculados) {
                    $query->whereIn('id_clinica', $vinculados)
                        ->orWhereIn('id_solicitador', $vinculados);
                });
                $guias = $query->paginate(50);
            } else {
                $clinica = \Modules\Clinics\Entities\Clinicas::where('id_usuario', Auth::user()->id)->first();
                $query->where(function($query) use ($clinica) {
                    $query->where('id_clinica', $clinica->id)
                        ->orWhere('id_solicitador', $clinica->id);
                });
                $guias = $query->paginate(50);
            }
        }

        return view('guides::glosas')->with([
            'guias' => $guias,
            'params' => [
                'start' => $start->format('d/m/Y'),
                'end'   => $end->format('d/m/Y'),
                'termo' => $request->get('termo')
            ]
        ]);
    }

    /**
     * Função que glosa a guia
     * @param Request $request
     */
    public function glosar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numero_guia' => 'required',
            'justificativa' => 'required',
        ]);
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $numeroGuia = $request->get('numero_guia');
        $guia = HistoricoUso::where('numero_guia', $numeroGuia)->first();
        $guia->update(['glosado' => '1']);

        $guiaGlosa = new GuiaGlosa;
        $guiaGlosa->id_usuario = Auth::user()->id;
        $guiaGlosa->id_historico_uso = $guia->id;
        $guiaGlosa->justificativa = $request->get('justificativa');
        $guiaGlosa->save();

        // LOG
        $mensagem = "A guia #{$guia->numero_guia} foi glosada. Justificativa: {$guiaGlosa->justificativa}";
        Logger::log(LogMessages::EVENTO['ALTERACAO'], 'Histórico de uso',
            'MEDIA', $mensagem,
            auth()->user()->id, 'historico_uso', $guia->id);

        $clinica = Clinicas::find($guia->id_clinica);
        $this->mailGlosa(
            $guia,
            $clinica->email_contato,
            "A guia #{$guia->numero_guia} foi glosada",
            "A guia #{$guia->numero_guia} foi glosada. <br> <strong>Justificativa:</strong> {$guiaGlosa->justificativa}"
        );

        return ['status' => true];
    }

    /**
     * Função que envia a defesa da glosa
     * @param Request $request
     */
    public function defenderGlosa(Request $request)
    {
        $v = Validator::make($request->all(), [
            'id' => 'required',
            'defesa' => 'required',
            'arquivo_defesa' => 'file|mimes:pdf,jpg,png,jpeg'
        ]);
        if ($v->fails()) {
            $messages = join("\n", $v->getMessageBag()->all());
            $messages = str_replace('file', 'O arquivo', $messages);
            self::setError($messages, 'Oops.');

            return back()
                ->withErrors($v)
                ->withInput();
        }

        $inputs = $request->all();
        $inputs['data_defesa'] = new Carbon();
        $guiaGlosa = GuiaGlosa::find($inputs['id']);
        $guia = $guiaGlosa->historicoUso();

        if($request->arquivo_defesa && $request->arquivo_defesa->isValid()) {
            $extension = $request->arquivo_defesa->extension();
            $size = $request->arquivo_defesa->getClientSize();
            $mime = $request->arquivo_defesa->getClientMimeType();
            $originalName = $request->arquivo_defesa->getClientOriginalName();
            $path = $request->arquivo_defesa->store('uploads');
            $upload = \App\Models\Uploads::create([
                'original_name' => $originalName,
                'mime' => $mime,
                'description' => 'Arquivo de defesa da Guia Glosada: ' . $guiaGlosa->id,
                'extension' => $extension,
                'size' => $size,
                'public' => 1,
                'path' => $path,
                'bind_with' => 'glosas',
                'binded_id' => $guiaGlosa->id,
                'user_id' => auth()->user()->id
            ]);
        }

        $guiaGlosa->update($inputs);
        self::setSuccess('Sua defesa foi enviada. Aguarde até que ela seja enviada.');

        $this->mailGlosa(
            $guia,
            "auditoria@lifepet.com.br",
            "A glosa da guia #{$guia->numero_guia} foi defendida",
            "A glosa da guia #{$guia->numero_guia} foi defendida. <br> <strong>Defesa:</strong> {$guiaGlosa->defesa}"
        );

        // LOG
        $mensagem = "A glosa da guia #{$guia->numero_guia} foi defendida.";
        Logger::log(LogMessages::EVENTO['ALTERACAO'], 'Histórico de uso',
            'MEDIA', $mensagem,
            auth()->user()->id, 'historico_uso', $guia->id);

        return back();
    }

    /**
     * Função que reverte a glosa
     * @param Request $request
     */
    public function reverterGlosa(Request $request)
    {
        if(Entrust::hasRole(['ADMINISTRADOR', 'AUTORIZADOR'])) {
            $numero_guia = $request->get('numero_guia');
            $guia = HistoricoUso::where('numero_guia', $numero_guia)->first();
            $glosado = $guia->glosado == '2' ? '1' : '2';
            $guia->update(['glosado' => $glosado]);

            // LOG
            $mensagem = "A glosa da guia #{$guia->numero_guia} foi revertida.";
            Logger::log(LogMessages::EVENTO['ALTERACAO'], 'Histórico de uso',
                'MEDIA', $mensagem,
                auth()->user()->id, 'historico_uso', $guia->id);

            if($glosado == '2') {
                $mensagemMail = $mensagem;
            } else {
                $mensagemMail = "A guia #{$guia->numero_guia} foi glosada.";
            }
            $clinica = Clinicas::find($guia->id_clinica);
            $this->mailGlosa(
                $guia,
                $clinica->email_contato,
                "A guia #{$guia->numero_guia} foi atualizada",
                $mensagemMail
            );
        }

        return ['status' => true];
    }

    /**
     * Função que confirma a glosa
     * @param Request $request
     */
    public function confirmarGlosa(Request $request)
    {
        if(Entrust::hasRole(['ADMINISTRADOR', 'AUTORIZADOR'])) {
            $numero_guia = $request->get('numero_guia');
            $guia = HistoricoUso::where('numero_guia', $numero_guia)->first();
            $guia->update(['glosado' => '3']);

            $guiaGlosa = GuiaGlosa::find($request->get('id_glosa'));
            $guiaGlosa->justificativa_confirmacao = $request->get('justificativa_confirmacao');
            $guiaGlosa->data_confirmacao = new Carbon();
            $guiaGlosa->save();

            // LOG
            $mensagem = "A glosa da guia #{$guia->numero_guia} foi confirmada. Justificativa: {$guiaGlosa->justificativa_confirmacao}";
            Logger::log(LogMessages::EVENTO['ALTERACAO'], 'Histórico de uso',
                'MEDIA', $mensagem,
                auth()->user()->id, 'historico_uso', $guia->id);

            $clinica = Clinicas::find($guia->id_clinica);
            $this->mailGlosa(
                $guia,
                $clinica->email_contato,
                "A glosa da guia #{$guia->numero_guia} foi confirmada",
                "A glosa da guia #{$guia->numero_guia} foi confirmada. <br> <strong>Justificativa:</strong> {$guiaGlosa->justificativa_confirmacao}"
            );
        }

        return ['status' => true];
    }

    public function mailGlosa(\Modules\Guides\Entities\HistoricoUso $guia, $to, $subj, $mensagem)
    {
        $data = [
            'guia' => $guia,
            'data' => new Carbon(),
            'mensagem' => $mensagem,
        ];

        Mail::send('mail.guia_glosa', $data, function($message) use ($to, $subj) {
            $message->to($to)
                    ->subject($subj);
        });

        return ['status' => true];
    }

    public function checkRegrasPlanosAntigos(Request $request)
    {
        $input = $request->all();
        $data = [
            'status' => 'ok' // Status que liberará a emissão padrão da guia
        ];

        // Gold Tab1
        if ($input['id_plano'] == 2) {
            $gruposInternacao = ["10101011", "26100"];
            $procedimentosInternacao = [];
            foreach($input['procedimentos'] as $procId) {
                $procedimento = Procedimentos::find($procId);
                if (in_array($procedimento->grupo->id, $gruposInternacao) && $procId != "8877478") {
                    $procedimentosInternacao[] = $procedimento->nome_procedimento;
                    $data['status'] = 'warning';
                }
            }
            if($data['status'] != 'ok'){
                $procedimentosListaHtml = '';
                foreach ($procedimentosInternacao as $proc) {
                    $procedimentosListaHtml .= '<li class="margin-bottom-5"><i class="fa fa-arrow-right"></i> '.$proc.'</li>';
                }

                $data['msg']['title'] = 'Atenção!';
                $data['msg']['html'] = '<div>
                                            <strong>Alguns procedimentos só podem ser liberados caso o pet esteja internado (sujeito a glosa).</strong>
                                            <p>Você confirma que o pet está internado e necessita dos procedimentos abaixo?</p>
                                            <ul class="list-unstyled text-left font-sm">'.$procedimentosListaHtml.'</ul>
                                        </div>';
            }
        }


        return $data;
    }

    /**
     * @param $e
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function emissaoRecusada($e, $extra = "")
    {
        self::setMessage($e->getMessage() .  $extra, 'error', 'Emissão Recusada.');
        return back();
    }

    public static function applyGamificationConsulta($numeroGuia)
    {
        /**
         * Gamification de Consultas do Credenciado
         */
        $gamification = new \App\Helpers\GamificationCredenciados($numeroGuia);
        $gamification->applyGamificationConsulta();
    }

    public function assinarGuiaCliente(Request $request){
        $senha_plano = $request->get('senha_plano');
        $numero_guia = $request->get('numero_guia');
        $meio = $request->get('meio') ?: 1;
        $hu = HistoricoUso::where('numero_guia', $numero_guia)->first();
        $cliente = $hu->pet->cliente;

        $data = $cliente->assinarGuia($numero_guia, $senha_plano, $meio);
        self::setMessage($data['msg'], ($data['status'] ? 'success' : 'error'), ($data['status'] ? 'Sucesso!' : 'Erro!'));
        return back()->with($data);
    }

    public function assinarGuiaPrestador(Request $request){
        $senha_prestador = $request->get('senha_prestador');
        $numeros_guia = (array)$request->get('numero_guia');

        foreach ($numeros_guia as $numero_guia) {
            $hu = HistoricoUso::where('numero_guia', $numero_guia)->first();
            $prestador = $hu->prestador;
            $data = $prestador->assinarGuia($numero_guia, $senha_prestador);
        }
        self::setMessage($data['msg'], ($data['status'] ? 'success' : 'error'), ($data['status'] ? 'Sucesso!' : 'Erro!'));
        return back()->with($data);
    }

    public function assinaturasPendentes()
    {
        $clinica = \Modules\Clinics\Entities\Clinicas::where('id_usuario', Auth::user()->id)->first();
        if(!$clinica) {
            self::setMessage('Você não está vinculado a nenhuma clínica no sistema. Entre em contato com a administração do sistema e tente novamente.', 'warning', 'Oops.');
            return redirect(route('autorizador.login'));
        }

        $guias = $clinica->guiasPendentesAssinatura();
        $prestadores = (new \Modules\Veterinaries\Entities\Prestadores())::whereIn('id', $guias->pluck('id_prestador'))->get()->map(function($prestador) use ($guias){
            $prestador->guias = $guias->where('id_prestador', $prestador->id);
            return $prestador;
        });

        $data = [
            'prestadores' => $prestadores,
        ];
        return view('guides::assinaturas_pendentes')->with($data);
    }

    public function pagamentoDireto(Request $request, $numeroGuia) 
    {
        $administrador = Permissions::podeEmitirGuiaAdministrativa();
        $atendimento = Entrust::hasRole(['ATENDIMENTO']);
        $clinica = \Modules\Clinics\Entities\Clinicas::where('id_usuario', Auth::user()->id)->first();
        $historicos = HistoricoUso::where('numero_guia', $numeroGuia)->get();

        if(!count($historicos)) {
            self::setMessage('Não foi possível encontrar a guia referida no sistema.', 'warning', 'Oops.');
            return redirect(route('autorizador.verGuias'));
        }

        if($historicos->first()->status === HistoricoUso::STATUS_LIBERADO) {
            self::setMessage('A guia já está liberada no sistema.', 'info', 'Informativo');
            return redirect(route('autorizador.verGuias'));
        }

        if(!$administrador && !$atendimento) {
            if(!$clinica) {
                self::setMessage('Você não está vinculado a nenhuma clínica no sistema. Entre em contato com a administração do sistema e tente novamente.', 'warning', 'Oops.');
                return redirect(route('autorizador.login'));
            }
        } else {
            $clinica = $historicos->first()->clinica;
        }

        $description = "Guia: " . $historicos->first()->numero_guia . "\n";
        $participacaoTotal = 0;
        foreach($historicos as $historico) {
            $participacaoProcedimento = $historico->procedimento->valorParticipacao($historico->plano);
            $participacaoTotal += $participacaoProcedimento;
            $description .= $historico->procedimento->nome_procedimento . "\n";
        }

        //$disponivel = GuideService::pagamentoAlternativoDisponivel($numeroGuia);
        $disponivel = true;
        if(!$disponivel && (!$administrador && !$atendimento)) {
            self::setMessage('Guia indisponível para pagamento direto.', 'error', 'Oops.');
            return redirect(route('autorizador.verGuias'));
        }

        return view('guides::pagamento_direto', [
            'historicos' => $historicos, 
            'clinica' => $clinica, 
            'numeroGuia' => $numeroGuia,
            //'charge' => $charge
        ]);
    }

    public function confirmarRecebimentoPagamentoDireto(Request $request, $numeroGuia)
    {
        //Validações
        $rules = [
            'total_procedimentos' => 'required',
        ];

        $paymentMethod = $request->get('payment_method');
        if($paymentMethod === 'picpay') {
            $rules = array_merge($rules, [
                'usuario_picpay' => 'sometimes|required',
                'id_transacao_picpay' => 'sometimes|required',
            ]);
        } else if ($paymentMethod === 'pix') {
            $rules = array_merge($rules, [
                'acquirer_transaction_id' => 'required'
            ]);
        }

        $v = Validator::make($request->all(), $rules);

        if ($v->fails()) {
            $messages = join("\n", $v->getMessageBag()->all());
            self::setError($messages, 'Oops.');

            return back()
                ->withErrors($v)
                ->withInput();
        }



        //Atualizar status da guia para liberada.
        $historicos = HistoricoUso::where('numero_guia', $numeroGuia)->get();
        if(!$historicos) {
            self::setMessage('Guia não encontrada para confirmação.', 'error', 'Oops.');
            return redirect(route('autorizador.verGuias'));
        }

        $total_procedimentos = $request->get('total_procedimentos');
        $additionalDescription = "";

        if($paymentMethod === 'picpay') {
            $usuarioPicpay = $request->get('usuario_picpay');
            $idTransacao = $request->get('id_transacao_picpay');
            $observacao = "Pagamento de coparticipação da guia #{$numeroGuia} via PICPAY ({$usuarioPicpay}) no valor de " . Utils::money($total_procedimentos);
            $mensagemLog = "Um novo pagamento de participação de guia foi realizado via PicPay.";
            $additionalDescription = "Usuário: $usuarioPicpay";
        } else {
            $observacao = "Pagamento de coparticipação da guia #{$numeroGuia} via '{$paymentMethod}' no valor de " . Utils::money($total_procedimentos);
            $mensagemLog = "Um novo pagamento de participação de guia foi realizado via {$paymentMethod}.";
        }


        foreach($historicos as $historico) {
            $historico->status = HistoricoUso::STATUS_LIBERADO;
            $historico->update();
        }

        /**
         * @var HistoricoUso $historico
         */
        $historico = $historicos->first();

        //Inserir nota no cliente.
        \App\Models\Notas::create([
           'user_id' => auth()->user()->id,
           'cliente_id' => $historico->pet->cliente->id,
           'corpo' => $observacao
        ]);

        //Gravar informação no Log
        $dadosJson = json_encode([
            'cliente' => [
                'nome' => $historico->pet->cliente->nome_cliente,
                'cpf' => $historico->pet->cliente->cpf
            ],
            'pet' => [
                'nome' => $historico->pet->nome_pet,
                'microchip' => $historico->pet->numero_microchip,
                'id' => $historico->pet->id,
            ],
            'numero_guia' => $numeroGuia,
            'valor' => $total_procedimentos,
            'usuario_clinica' => [
                'nome' => auth()->user()->name,
                'id' => auth()->user()->id,
            ],
            'forma_pagamento' => $request->get('payment_method'),
            'usuario_picpay' => $request->get('usuario_picpay'),
            'acquirer_transaction_id' => $request->get('acquirer_transaction_id')
        ]);

        $mensagemLog .= " \n{$dadosJson}";
        Logger::log(
                LogEvent::NOTICE,
                'emissao-guias',
                LogPriority::HIGH,
                $mensagemLog,
                auth()->user()->id,
                'historico_uso',
                $historico->id);

        //Salvar venda no Financeiro
        $financeiro = new Financeiro();
        try {
            $customer = $financeiro->customerByRefcode($historico->pet->cliente->id_externo);
            if($customer) {
                $description = "Referente à guia {$numeroGuia}. " . $additionalDescription;


                $sale = new Sale();
                if($paymentMethod === 'picpay') {
                    $sale->picpay($customer->id, $total_procedimentos, Carbon::now()->format('m/Y'), $description, ['picpay', "guia:$numeroGuia"], $idTransacao);
                } else if ($paymentMethod === 'pix') {
                    $sale->pix($customer->id, $total_procedimentos, Carbon::now()->format('m/Y'), $request->get('acquirer_transaction_id'), $description, ['pix', "guia:$numeroGuia"]);
                } else {
                    $sale->picpay($customer->id, $total_procedimentos, Carbon::now()->format('m/Y'), $description, ['lifepet-wallet', "guia:$numeroGuia"]);
                }

                $sale->save();
                //Registrar pagamento na "linha de cobrança"
                Cobrancas::cobrancaAutomatica($historico->pet->cliente, $total_procedimentos, $observacao, null, Carbon::now()->format('m/Y'), $sale->id, true, $sale->id, null);
            } else {
                throw new \Exception('Cadastro de cliente não encontrado no SF com o refcode informado.');
            }
        } catch (\Exception $e) {
            $mensagemLog = "Não foi possível realizar o lançamento direto da venda no Sistema Financeiro. Erro:\n".$e->getMessage()."\n{$dadosJson}";

            Logger::log(
                LogEvent::NOTICE,
                'emissao-guias',
                LogPriority::HIGH,
                $mensagemLog,
                1,
                'historico_uso',
                $historico->id);
        }

        //Retornar sucesso.
        GuideService::removerTentativaPagamento($numeroGuia);
        self::setMessage('A guia já está disponível para atendimento.', 'success', 'Sucesso!');
        return redirect(route('autorizador.verGuias'));
    }

    public function cancelarRecebimentoPagamentoDireto(Request $request, $numeroGuia)
    {
        $historicos = HistoricoUso::where('numero_guia', $numeroGuia)->get();
        if(!$historicos) {
            self::setMessage('Guia não encontrada.', 'error', 'Oops.');
            return redirect(route('autorizador.verGuias'));
        }
        $historico = $historicos->first();
        GuideService::removerTentativaPagamento($numeroGuia);

        $dadosJson = json_encode([
            'cliente' => [
                'nome' => $historico->pet->cliente->nome_cliente,
                'cpf' => $historico->pet->cliente->cpf
            ],
            'pet' => [
                'nome' => $historico->pet->nome_pet,
                'microchip' => $historico->pet->numero_microchip,
                'id' => $historico->pet->id,
            ],
            'numero_guia' => $numeroGuia,
            'valor' => $request->get('total_procedimentos'),
            'usuario_clinica' => [
                'nome' => auth()->user()->name,
                'id' => auth()->user()->id,
            ]
        ]);

        $mensagemLog = "Um pagamento direto PicPay foi cancelado. \n{$dadosJson}";
        Logger::log(
                LogEvent::WARNING,
                'emissao-guias',
                LogPriority::MEDIUM,
                $mensagemLog,
                auth()->user()->id,
                'historico_uso',
                $historico->id);

        self::setMessage('A guia continua indisponível para atendimento.', 'info', 'Oops.');

        return redirect()->route('autorizador.verGuias');
    }
}
