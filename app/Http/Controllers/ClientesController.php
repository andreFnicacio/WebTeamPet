<?php

namespace App\Http\Controllers;

use App\Helpers\API\Financeiro\DirectAccess\Models\Payment;
use App\Helpers\API\Financeiro\DirectAccess\Models\Sale;
use App\Helpers\API\Financeiro\DirectAccess\Services\CustomerService;
use App\Helpers\API\Superlogica\Invoice;
use App\Helpers\API\Superlogica\V2\Charge;
use App\Helpers\API\Superlogica\V2\Domain\Models\CreditCard;
use App\Helpers\API\Superlogica\V2\PaymentMethod;
use App\Helpers\Utils;
use App\Http\Requests\CreateClientesRequest;
use App\Http\Requests\UpdateClientesRequest;
use App\Jobs\SuperlogicaSyncSignature;
use App\Jobs\SuperlogicaUpdateSignatureInfo;
use App\Jobs\SyncSuperlogicaClientInfo;
use App\Models\{Clientes, Cobrancas, Uploads, PreCadastros, Raca, DocumentosClientes, DocumentosPets};
use App\Models\Pets;
use App\Repositories\ClientesRepository;
use App\Http\Controllers\AppBaseController;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use \Illuminate\Support\Facades\Validator;
use Prettus\Repository\Criteria\RequestCriteria;
use Carbon\Carbon;
use App\Helpers\API\Financeiro\Financeiro;
use Illuminate\Database\Eloquent as Model;
use Flash;
use Response;
use Entrust;
use Auth;
use Image;
use Mail;

use App\Helpers\API\RDStation\Services\RDSendBoletoAvulsoService;

/**
 * Controller de Clientes
 */
class ClientesController extends AppBaseController
{
    /** @var  ClientesRepository */
    private $clientesRepository;

    const UPLOAD_TO = 'clientes/';

    public function __construct(ClientesRepository $clientesRepo)
    {
        $this->clientesRepository = $clientesRepo;
    }

    protected static function log($message, $data = [])
    {
        $data['area'] = 'clientes';
        parent::log($message, $data);
    }

    /**
     * @param $request
     * @param $clientes
     */
    protected static function getChanges($request, Model\Model $model)
    {
        $originalAtrributes = $model->getAttributes();
        $changedAttributes = $model->fill($request->all())->getAttributes();
        $changes = [];
        $changes['old'] = array_diff($originalAtrributes, $changedAttributes);
        $changes['new'] = array_diff($changedAttributes, $originalAtrributes);

        $changelog = self::formatModelChanges($changes);

        return $changelog;
    }

    /**
     * Display a listing of the Clientes.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if (!Entrust::can('list_clientes')) {
            return self::notAllowed();
        }

        $query = Clientes::query();
        $term = $request->get('search');
        //Busca textual
        $query->where(function($query) use ($term) {
            $term = '%' . $term . '%';
            $query->where('nome_cliente', 'LIKE', $term)
                  ->orWhere('id', 'LIKE', $term)
                  ->orWhere('cpf', 'LIKE', $term)
                  ->orWhere('email', 'LIKE', $term)
                  ->orWhere('celular', 'LIKE', $term)
                  ->orWhere('telefone_fixo', 'LIKE', $term);

            return $query;
        });

        //Estado
        $estados = $request->get('estados', null);
        if($estados) {
            $query->whereIn('estado', $estados);
        }

        //Ano de adesão
        $requestAnos = $request->get('anos', []);
        //QueryBuilder
        if(!empty($requestAnos)) {
            $query->where(function($query) use ($requestAnos) { //Closure
                foreach($requestAnos as $ano) {
                    $query->orWhereYear('created_at', $ano);
                }
            });
        }

        //Status
        $status = $request->get('status', null);
        if($status != '') {
            $query->where('ativo', $status);
        }

        //Order
        switch ($request->get('ordem')) {
            case "nome":
                $query->orderBy('nome_cliente', 'ASC');
                break;
            case "maisAntigos":
                $query->orderBy('id', 'ASC');
                break;
            default:
                $query->orderBy('id', 'DESC');
                break;
        }

        $limit = 10;
        $searchTotal = $query->count();
        $clientes = $query->paginate($limit);

        $estados = Clientes::groupBy('estado')->get()->pluck('estado');
        
        $anos = Clientes::selectRaw('YEAR(created_at) as ano')->groupBy('ano')->get()->pluck('ano');
        
        $pagination = $this->pagination($request, count($clientes), $searchTotal, $limit);
    
        return view('clientes.index', [
            'clientes' => $clientes,
            'pagination' => $pagination,
            'estados' => $estados,
            'anos' => $anos,
            'params' => [
                'estados' => $request->get('estados') ?? [],
                'anos' => $request->get('anos') ?? [],
                'ordem' => $request->get('ordem'),
                'status' => $status,
            ]
        ]);
    }

    public function notApproved(Request $request)
    {
        if (!Entrust::can('list_clientes')) {
            return self::notAllowed();
        }
        $limit = 10;
        $this->clientesRepository->pushCriteria(new RequestCriteria($request));
        $searchTotal = $this->clientesRepository->count();
        $clientes = $this->clientesRepository->findWhere(['aprovado' => 0]);

        $data = [
            'clientes' => $clientes,
        ];
        return view('clientes.aprovacao')
            ->with($data);
    }

    public function approve($idCliente)
    {
        $cliente = Clientes::find($idCliente);
        $cliente->aprovado = 1;
        $cliente->update();

        return back();
    }

    /**
     * Show the form for creating a new Clientes.
     *
     * @return Response
     */
    public function create()
    {
        if (!Entrust::can('create_clientes')) {
            return self::notAllowed();
        }
        return view('clientes.create', [
            'cliente' => (new \App\Models\Clientes),
            'ufs' => self::$ufs
        ]);
    }

    /**
     * Store a newly created Clientes in storage.
     *
     * @param CreateClientesRequest $request
     *
     * @return Response
     */
    public function store(CreateClientesRequest $request)
    {
        if (!Entrust::can('create_clientes')) {
            return self::notAllowed();
        }
        $input = $request->all();

        IndicacoesController::confirmar($request->email);

        $clientes = $this->clientesRepository->create($input);
        if (empty($input['numero_contrato'])) {
            $clientes->numero_contrato = $clientes->id;
            $clientes->update();
        }

        if (empty($input['dia_vencimento'])) {
            $clientes->dia_vencimento = Carbon::now()->day;
            $clientes->update();
        }

        self::creation("Novo cliente cadastrado no sistema.", 'clientes', 'clientes', $clientes->id, auth()->user()->id);

        self::setSuccess('Cliente criado com sucesso.');

        return redirect(route('clientes.edit', $clientes->id));
    }

    /**
     * Display the specified Clientes.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        if (!Entrust::can('list_clientes')) {
            return self::notAllowed();
        }
        $clientes = $this->clientesRepository->findWithoutFail($id);

        if (empty($clientes)) {
            Flash::error('Clientes not found');

            return redirect(route('clientes.index'));
        }

        return view('clientes.show')->with('clientes', $clientes);
    }

    /**
     * Show the form for editing the specified Clientes.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        if (!Entrust::can('list_clientes')) {
            return self::notAllowed();
        }

        $clientes = $this->clientesRepository->findWithoutFail($id);
        // $lista = $clientes->getListaCompletaDocumentos();
        // dd($lista);

        if (empty($clientes)) {
            self::setError('Cliente não encontrado');

            return redirect(route('clientes.index'));
        }

        /**
         * VERIFICA SE O CLIENTE ESTÁ SINCRONIZADO COM O FINANCEIRO
         */
        try {
            if(!empty($clientes->id_externo)) {
                $financeiro = new Financeiro();
                $info = $financeiro->get('/customer/refcode/'.$clientes->id_externo);
            }

        }catch (\Exception $e){
            try {
            $customer = $financeiro->createCustomer($clientes);

            $financeiro->createSubscription([
                'customer_id' => $customer->id,
                'dia_vencimento' => $customer->due_day,
                'forma_pagamento' => $customer->payment_type,
                'pets' => $clientes->pets
            ]);
            }
            catch (\Exception $e){}
        }

        return view('clientes.edit')->with([
            'cliente' => $clientes,
            'ufs' => self::$ufs
        ]);
    }

    /**
     * Update the specified Clientes in storage.
     *
     * @param  int              $id
     * @param UpdateClientesRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateClientesRequest $request)
    {
        if (!Entrust::can('edit_clientes')) {
            return self::notAllowed();
        }

        $clientes = $this->clientesRepository->findWithoutFail($id);
        if (empty($clientes)) {
            self::setError('Cliente não encontrado');
            return back();
        }

        $changes = self::getChanges($request, $clientes);


        $input = $request->all();
        $clientes->update($input);

        if(!empty($changes)) {
            self::alter("Os dados do cliente foram modificados.\n{$changes}", 'clientes', 'clientes', $clientes->id, auth()->user()->id);
        }

        self::setSuccess('Cliente alterado com sucesso.');

        return redirect(route('clientes.edit', $id));
    }



    /**
     * Remove the specified Clientes from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        if (!Entrust::can('delete_clientes')) {
            return self::notAllowed();
        }
        $clientes = $this->clientesRepository->findWithoutFail($id);

        if (empty($clientes)) {
            Flash::error('Clientes not found');

            return redirect(route('clientes.index'));
        }

        $client_data = json_encode($clientes->getAttributes());
        $this->clientesRepository->delete($id);

        self::exclusion("O cliente foi excluído.\n{$client_data}", 'clientes', 'clientes', $clientes->id, auth()->user()->id);

        Flash::success('Clientes deleted successfully.');

        return redirect(route('clientes.index'));
    }

    public function forcarDebito(Request $request)
    {
        if (!Entrust::can('edit_clientes')) {
            return self::notAllowed();
        }

        $id_cobranca = $request->get('id_cobranca');
        $id_cartao = $request->get('card_id');

        $v = Validator::make($request->all(), [
            'id_cobranca' => 'required|numeric',
            'card_id' => 'required|numeric'
        ]);
        if ($v->fails()) {
            $messages = join("\n", $v->getMessageBag()->all());

            return response()->json([
                'status_code' => 400,
                'message' => $messages
            ], 400);
        }

        if(empty($id_cobranca)) {
            return response()->json([
                'status_code' => 400,
                'message' => "É preciso informar o ID da cobrança do sistema financeiro para continuar."
            ], 400);
        }

        $cobranca = \App\Models\Cobrancas::where('id_financeiro', '=', $id_cobranca)->first();

        if($cobranca === null) {
            return response()->json([
                'status_code' => 400,
                'message' => "Não foi possível localizar a cobrança {$id_cobranca} no ERP."
            ], 400);
        }

        $financeiro = new Financeiro();

        try {
            self::warning("O débito da cobrança $id_cobranca foi forçado.", 'clientes', 'cobrancas', $cobranca->id, auth()->user()->id);

            $url = "/payment/process/$id_cobranca";
            if($id_cartao) {
                $url = "/$id_cartao";
            }

            $info = $financeiro->get($url);

            return response()->json([
                'status_code' => 200,
                'message' => 'Pagamento processado com sucesso!'
            ], 200);
        }
        catch(\Exception $e) {

            return response()->json([
                'status_code' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function abrirFatura($id, $id_cobranca)
    {
        if (!Entrust::can('edit_clientes')) {
            return self::notAllowed();
        }

        if(empty($id)) {
            return response()->json([
                'status_code' => 400,
                'message' => "É preciso informar o ID do cliente para continuar."
            ], 400);
        }

        $cliente = \App\Models\Clientes::where('id', '=', $id)->first();

        if($cliente === null) {
            return view('error', [
                'status_code' => 400,
                'message' => "Não foi possível localizar o cliente {$id} no ERP."
            ]);
        }
        $cobranca = \App\Models\Cobrancas::where('id', '=', $id_cobranca)->first();

        if($cobranca === null) {
            return view('error', [
                'status_code' => 400,
                'message' => "Não foi possível localizar a cobrança {$id} no ERP."
            ]);

        }
        if (trim($cobranca->id_cliente) !== trim($id))
        {
            return view('error', [
                'status_code' => 400,
                'message' => "Não é possível visualizar essa fatura."
            ]);
        }

        if($cobranca->hash_boleto && $cobranca->id_financeiro) {
            return $this->visualizarFaturaSistemaFinanceiro($cobranca);
        }

        if($cobranca->id_superlogica) {
            $chargeService = new Charge();
            $charge = $chargeService->getCharge($cobranca->id_superlogica);
            if(!$charge) {
                return view('error', [
                    'status_code' => 400,
                    'message' => "Não é possível visualizar essa fatura. Ela não pôde ser encontrada no Superlógica"
                ]);
            }

            return redirect()->away($charge->link_2via);
        }

        return view('error', [
            'status_code' => 400,
            'message' => "Não é possível visualizar essa fatura. Não foi encontrada em nenhum sistema financeiro."
        ]);

        //return Redirect::intended('');

    }

    public function boletoAvulso($id, Request $request)
    {
        if (!Entrust::can('edit_clientes')) {
            return self::notAllowed();
        }

        $cliente = $this->clientesRepository->findWithoutFail($id);

        if (empty($cliente)) {
            return response()->json(['error' => 'Cliente não encontrado'], 404);
        }

        $input = $request->all();

        $valor = str_replace(['.',','],'',$input['valor']) / 100;
        $valor = number_format($valor, 2, '.', '');

        $dtVencimento = Carbon::createFromFormat('d/m/Y', $input['vencimento'])->format('Y-m-d');

        $financeiro = new Financeiro();
        try {
            $response = $financeiro->post('/boleto',[
                'amount' => $valor,
                'status' => 'PENDING',
                'status_code' => 1,
                'reference' => date('m/Y'),
                'due_date' => $dtVencimento,
                'customer_id' => $input['customer_id'],
                'obs' => $input['obs'],
                'multa' => $input['multa'],
                'juros' => $input['juros']
                // 'instrucao1' => ' ',
                // 'instrucao2' => ' ',
                // 'instrucao3' => ' ',
                // 'instrucao4' => ' ',
                // 'instrucao5' => ' ',
            ]);

            $cobranca = [
                'id_cliente' => $cliente->id,
                'complemento' => $input['obs'],
                'valor_original' => $valor,
                'data_vencimento' => $dtVencimento,
                'status' => 1,
                'competencia' => date('Y-m'),
                'id_financeiro' => $response->payment_id,
                'hash_boleto' => $response->hash
            ];

            $cobranca = \App\Models\Cobrancas::create($cobranca);

            $rdSendBoletoAvulso = new RDSendBoletoAvulsoService($response, $cliente);
            $rdSendBoletoAvulso->process();

            return response()->json(['message' => 'Boleto gerado com sucesso!'], 201);

        }
        catch(\Exception $e) {
            return response()->json(json_decode($e->getMessage()), 400);
        }

        
    }

    /**
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function definirCartaoPrincipal(Request $request)
    {
        if (!Entrust::can('cliente_cartao_credito_principal')) {
            return self::notAllowed();
        }


        $financeiro = new Financeiro();
        $customer_id = $request->get('customer_id');
        $card_id = $request->get('card_id');

        self::notice("SF: O cartão principal do cliente $customer_id foi alterado para o cartão $card_id", 'sistema_financeiro', null, null, auth()->user()->id);

        try {
            $financeiro->get("/customer/card/default/$customer_id/$card_id");
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(json_decode($e->getMessage()), 400);
        }
    }

    /**
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function excluirCartao(Request $request)
    {
        if (!Entrust::can('cliente_cartao_credito_excluir')) {
            return self::notAllowed();
        }


        $financeiro = new Financeiro();
        //$customer_id = $request->get('customer_id');
        $card_id = $request->get('card_id');

        self::notice("SF: O cartão $card_id foi excluído.", 'sistema_financeiro', null, null, auth()->user()->id);

        try {
            $financeiro->delete("/credit-card/$card_id");
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(json_decode($e->getMessage()), 400);
        }
    }

    public function upload($id, Request $request)
    {
        /**
         * @var $v \Illuminate\Validation\Validator
         */
        $v = Validator::make($request->all(), [
            'file' => 'file|required|mimes:pdf,tiff,bmp,jpg,png,jpeg,webp'
        ]);
        if ($v->fails()) {
            $messages = join("\n", $v->getMessageBag()->all());
            $messages = str_replace('file', 'O arquivo', $messages);
            self::setError($messages, 'Oops.');

            return back()
                ->withErrors($v)
                ->withInput();
        }
        if ($request->file->isValid()) {
            $extension = $request->file->extension();
            $size = $request->file->getClientSize();
            $public = $request->get('publico');
            $mime = $request->file->getClientMimeType();
            $originalName = $request->file->getClientOriginalName();
            $description = "";
            if ($request->filled('description')) {
                $description = $request->get('description');
            }
            $path = $request->file->store('uploads');
            $upload = \App\Models\Uploads::create([
                'original_name' => $originalName,
                'mime'          => $mime,
                'description'   => $description,
                'extension'     => $extension,
                'size'          => $size,
                'public'        => $public,
                'path'          => $path,
                'bind_with'     => 'clientes',
                'binded_id'     => $id,
                'user_id'       => auth()->user()->id
            ]);
            if ($upload) {
                self::setSuccess('Arquivo carregado com sucesso.');
                return back();
            }
        } else {
            self::setMessage("Erro no upload.\n\n" + $request->file->getError(), 'error', 'Falha');
            back();
        }
    }

    public function resetPassword(Request $request)
    {
        $user = auth()->user();

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = false;
        if ($request->get('password') == $request->get('password_confirmation')) {
            $response = $this->doResetPassword($user, $request->get('password'));
        }

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $response
            ? $this->sendResetResponse($user)
            : $this->sendResetFailedResponse();
    }

    private function doResetPassword($user, $password)
    {
        $user->forceFill([
            'password' => bcrypt($password),
            'remember_token' => Str::random(60),
        ])->save();

        $this->guard()->login($user);
        return true;
    }

    private function sendResetResponse($user)
    {
        $cliente = \App\Models\Clientes::where('id_usuario', $user->id)->first();
        if ($cliente) {
            $cliente->primeiro_acesso = 0;
            $cliente->update();
        }

        self::toast('Senha modificada com ', 'SUCESSO', 'font-green-meadow');
        return redirect('/home');
    }

    private function sendResetFailedResponse()
    {
        self::setError('Ocorreu um erro ao tentar modificar a senha. Tente novamente');
        return back();
    }

    /**
     * Get the guard to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    public function boletosBichos(Request $request)
    {
        $clientsCsv = storage_path('csv/id_clientes_bichos.csv');
        $clientsId = \App\Helpers\Utils::csvToArray($clientsCsv, ",");


        if (empty($request->get('id_cliente'))) {
            return view('extra.boletos_bichos', [
                'clientes' => $clientsId,
                'todasCobrancas' => []
            ]);
        } else {
            $collected = collect($clientsId);
            $contain = $collected->contains('id', '=', $request->get('id_cliente'));
            if (!$contain) {
                return view('extra.boletos_bichos', [
                    'clientes' => $clientsId,
                    'todasCobrancas' => []
                ]);
            }

            $invoiceManager = new Invoice();

            $todasCobrancas = [];
            $clientId = $collected->where('id', '=', $request->get('id_cliente'))->first();

            $idClienteBichos = $clientId['id'];
            $cobrancas = $invoiceManager->getByClientId($idClienteBichos, Invoice::OPEN);
            $todasCobrancas[$idClienteBichos] = [
                'nome' => $clientId['nome'],
                'cobrancas' => $cobrancas
            ];

            return view('extra.boletos_bichos', [
                'clientes' => $clientsId,
                'todasCobrancas' => $todasCobrancas
            ]);
        }
    }

    public function saveSegundoResponsavel(Request $request)
    {
        /**
         * @var $v \Illuminate\Validation\Validator
         */
        $v = Validator::make($request->all(), [
            'segundo_responsavel_nome' => 'required|max:255',
            'segundo_responsavel_email' => 'required|email|max:255',
            'segundo_responsavel_telefone' => 'required|max:255'
        ]);
        if ($v->fails()) {
            $messages = join("\n", $v->getMessageBag()->all());

            self::setError($messages, 'Oops.');

            return back()
                ->withErrors($v)
                ->withInput();
        }

        if (Entrust::hasRole(['CLIENTE'])) {
            $cliente = self::loggedClient();
        } else {
            if (!Entrust::can('clientes_definir_segundo_responsavel')) {
                return self::notAllowed();
            }

            $cliente = Clientes::find($request->get('id_cliente'))->first();
        }

        if (!$cliente) {
            return abort(404, 'Cliente não encontrado');
        }

        $cliente->segundo_responsavel_nome = $request->get('segundo_responsavel_nome');
        $cliente->segundo_responsavel_email = $request->get('segundo_responsavel_email');
        $cliente->segundo_responsavel_telefone = $request->get('segundo_responsavel_telefone');

        $cliente->update();

        return back();
    }

    public static function setAssinatura($clientes, Request $request)
    {
        if ($request->filled('assinatura')) {
            $assinatura = $request->get('assinatura');
            $extension = 'png';
            $path = static::UPLOAD_TO . $clientes->id . '/' . 'assinatura.' . $extension;
            $image = Image::make($assinatura);

            \Storage::put($path, (string) $image->encode());

            $clientes->assinatura = $path;
            $clientes->update();
        }
    }

    public function assinatura($id)
    {
        $cliente = Clientes::findOrFail($id);
        $path = storage_path('app/' . $cliente->assinatura);
        if (!\File::exists($path)) {
            abort(404);
        }

        $file = \File::get($path);
        $type = \File::mimeType($path);

        $response = \Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    public function checkClienteCpf($cpf)
    {
        $cpf = str_replace('.', '', str_replace('-', '', $cpf));
        $cliente = Clientes::where('cpf', $cpf);
        $data = [
            'exists' => $cliente->exists(),
            'dados' => $cliente->first()
        ];
        return $data;
    }

    public function deleteUpload(Request $request)
    {
        $input = $request->all();
        $checkPass = Hash::check($input['senha'], \Illuminate\Support\Facades\Auth::user()->password);
        $data = [
            'input' => $input,
        ];

        if ($checkPass) {
            $upload = Uploads::find($input['id_upload']);
            $upload->id_usuario_delete = \Illuminate\Support\Facades\Auth::user()->id;
            $upload->justificativa_delete = $input['justificativa'];
            $upload->save();
            $upload->delete();
            $data['msg']['title'] = "Sucesso!";
            $data['msg']['text'] = "O arquivo foi deletado!";
            $data['msg']['type'] = "success";
        } else {
            $data['msg']['title'] = "Erro!";
            $data['msg']['text'] = "A senha não está correta!";
            $data['msg']['type'] = "error";
        }
        return $data;
    }

    public function getDadosProposta($idCliente, $numProposta)
    {
        $cliente = (new \App\Models\Clientes)->find($idCliente);
        $iconCheckbox = asset('_app_cadastro_cliente/proposta/img/icon-checkbox.png');

        $dados_proposta = json_decode($cliente->dados_proposta);

        if (!empty($dados_proposta[$numProposta])) {
            $data = [
                'iconCheckbox' => $iconCheckbox,
                'dados_proposta' => $dados_proposta[$numProposta],
                'idCliente' => $idCliente,
                'versao' => $dados_proposta[$numProposta]->versao,
                'aceite' => isset($dados_proposta[$numProposta]->aceite) && $dados_proposta[$numProposta]->aceite == true ? true : false,
                'numProposta' => $numProposta
            ];
            return $data;
        } else {
            return false;
        }
    }

    public function proposta($idCliente, $numProposta)
    {
        $data = self::getDadosProposta($idCliente, $numProposta);
        if ($data) {
            return view('clientes.propostas.' . $data['versao'] . '.proposta', $data)->with('successo_aceite', session('successo_aceite'));
        } else {
            self::setWarning('Proposta não encontrada!');
            return redirect(route('clientes.edit', $idCliente));
        }
    }

    public function aceiteProposta(Request $request, $idCliente, $numProposta)
    {
        $cliente = (new \App\Models\Clientes)->find($idCliente);
        $vendedor = $cliente->pets()->first()->petsPlanosAtual->first()->vendedor();

        $anexos = $request->anexos;

        // $this::setAssinatura($cliente, $request);

        $dados_proposta = json_decode($cliente->dados_proposta);
        $dados_proposta[$numProposta]->aceite = true;
        $cliente->dados_proposta = json_encode($dados_proposta);
        $cliente->update();


        Mail::send('mail.documentacao_cliente', [
            'cliente' => $cliente,
        ], function ($message) use ($vendedor, $cliente, $anexos) {
            $message->to($vendedor->email_contato)->subject('Envio de documentos de cliente: ' . $cliente->nome_cliente);
            foreach ($anexos as $file) {
                $message->attach(
                    $file->getRealPath(),
                    array(
                        'as' => $file->getClientOriginalName(),
                        'mime' => $file->getMimeType()
                    )
                );
            }
        });

        session(['successo_aceite' => 'Sua proposta foi validada com sucesso.']);
        return redirect(route('clientes.proposta', ['id' => $idCliente, 'numProposta' => $numProposta]));
    }

    public function downloadProposta($idCliente, $numProposta)
    {
        $data = self::getDadosProposta($idCliente, $numProposta);
        if ($data) {
            $view = view('clientes.propostas.' . $data['versao'] . '.proposta', $data);
            $renderedHtml = $view->render();

            return view('clientes.proposta_download')
                ->with('html', $renderedHtml)
                ->with('download', true);
        } else {
            self::setWarning('Proposta não encontrada!');
            return redirect(route('clientes.edit', $idCliente));
        }
    }

    public function atualizarDocumentos(Request $request, $idCliente)
    {
        $cliente = Clientes::find($idCliente);
        $data = $request->all();

        if (isset($data['documentos_clientes'])) {
            foreach ($data['documentos_clientes'] as $id => $doc) {
                $documento = DocumentosClientes::find($id);
                if ($doc['status']) {
                    $documento->status = DocumentosClientes::STATUS_APROVADO;
                    $documento->data_aprovacao = Carbon::now();
                    $documento->id_usuario_aprovacao = auth()->user()->id;
                } else {
                    $documento->status = DocumentosClientes::STATUS_REPROVADO;
                    $documento->data_reprovacao = Carbon::now();
                    $documento->motivo_reprovacao = $doc['motivo_reprovacao'];
                    $documento->id_usuario_reprovacao = auth()->user()->id;
                    foreach ($documento->uploads()->get() as $upload) {
                        Storage::delete($upload->path);
                        $upload->delete();
                    }
                }
                $documento->save();
            }
        }

        if (isset($data['documentos_pets'])) {
            foreach ($data['documentos_pets'] as $id => $doc) {
                $documento = DocumentosPets::find($id);
                if ($doc['status']) {
                    $documento->status = DocumentosPets::STATUS_APROVADO;
                    $documento->data_aprovacao = Carbon::now();
                    $documento->id_usuario_aprovacao = auth()->user()->id;
                } else {
                    $documento->status = DocumentosPets::STATUS_REPROVADO;
                    $documento->data_reprovacao = Carbon::now();
                    $documento->motivo_reprovacao = $doc['motivo_reprovacao'];
                    $documento->id_usuario_reprovacao = auth()->user()->id;
                    foreach ($documento->uploads()->get() as $upload) {
                        Storage::delete($upload->path);
                        $upload->delete();
                    }
                }
                $documento->save();
            }
        }

        self::setSuccess('Documentos avaliados com sucesso!');
        return back();
    }

    public function enviarDocumentos($id, Request $request)
    {
        $v = Validator::make($request->all(), [
            'documentos_novos_uploads.*' => 'file|required|mimes:pdf,tiff,bmp,jpg,png,jpeg,webp'
        ]);
        if ($v->fails()) {
            $messages = join("\n", $v->getMessageBag()->all());
            $messages = str_replace('file', 'O arquivo', $messages);
            self::setError($messages, 'Oops.');

            return back()
                ->withErrors($v)
                ->withInput();
        }

        if ($request->filled('tipo_documento')) {
            if ($request->get('tipo_documento') == 'clientes') {
                $documento = DocumentosClientes::find($request->get('id_documento'));
            } else {
                $documento = DocumentosPets::find($request->get('id_documento'));
            }
            $files = $request->file('documentos_novos_uploads');
            foreach ($files as $file) {
                \App\Models\Uploads::makeUpload($file, 'documentos_clientes', $documento->id, $documento->nome);
            }
            $documento->update([
                'status' => $documento::STATUS_APROVADO,
                'id_usuario_aprovacao' => auth()->user()->id,
                'data_aprovacao' => Carbon::now()
            ]);
            self::setSuccess('Documentos enviados com sucesso!');
        } else {
            self::setError('Erro no envio de documentos!');
        }

        return back();
    }

    public function pets($id)
    {
        $cliente = (new \App\Models\Clientes)->findOrFail($id);

        $pets = $cliente->pets;
        return $pets->map(function(Pets $p) {
            $pet = new \stdClass();
            $pet->nome_pet = $p->nome_pet;
            $pet->id = $p->id;
            $pet->ativo = $p->ativo;
            $pet->regime = $p->regime;
            $pet->plano = $p->plano()->id;

            return $pet;
        });
    }

    public function syncWithFinance($id)
    {
        $cliente = Clientes::find($id);

        if(!$cliente) {
            abort(404, 'Não foi encontrado cliente com o id informado');
        }

        $cliente->syncWithFinance();

        self::setSuccess('O cadastro do cliente foi sincronizado com sucesso.');

        return redirect()->back();
    }

    public function syncSuperlogica($id)
    {
        $cliente = Clientes::find($id);

        if(!$cliente) {
            abort(404, 'Não foi encontrado cliente com o id informado');
        }

        $job = new SyncSuperlogicaClientInfo($cliente);
        dispatch($job);

        $job = new SuperlogicaSyncSignature($cliente);
        dispatch($job);

        self::setSuccess('O cadastro do cliente foi sincronizado com sucesso. Atualizando informações de assinaturas e dados pessoais.');

        return redirect()->back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function registrarCobrancaManualmente(Request $request, $id)
    {
        $cliente = Clientes::find($id);
        if(!$cliente) {
            self::setError('Cliente não encontrado');
            return redirect()->back();
        }

        $v = Validator::make($request->all(), [
            'competencia_ano' => 'required|numeric|gte:2017',
            'competencia_mes' => 'required|numeric|gte:1|lte:12',
            'vencimento'      => 'required',
            'valor'           => 'required',
        ]);

        $v->sometimes(['descricao_pagamento'],'required', function($input) {
            return $input->incluir_pagamento;
        });

        if($v->fails()) {
            $errors = $v->getMessageBag()->all();
            $errors = join("\n", $errors);
            self::setError('Erros de validação: ' . "\n $errors", 'Erro ao validar');
            return redirect()->back();
        }



        $pago = (bool) $request->get('incluir_pagamento', false);
        $valor = $request->get('valor');
        $valor = Utils::brazilianFloat($valor);
        $complemento = $request->get('descricao_pagamento', null);
        $competencia = sprintf("%02d", $request->get('competencia_mes')) . '/'  . $request->get('competencia_ano');
        $vencimento = $request->get('vencimento');
        $vencimento = Carbon::createFromFormat(Utils::BRAZILIAN_DATE, $vencimento);
        $pagamento = $request->get('pagamento');
        if(!$pagamento) {
            $pagamento = $vencimento;
        } else {
            $pagamento = Carbon::createFromFormat(Utils::BRAZILIAN_DATE, $pagamento);
        }
        $tags = $request->get('tags', []);
        //Criar cobrança manual
        $cobranca = Cobrancas::cobrancaAutomatica($cliente, $valor, $complemento, $vencimento, $competencia, null, $pago, null, $pagamento);
        //Registrar no SF
        if($request->get('incluir_sf', false)) {
            $sale = new Sale();
            $customer = CustomerService::getByRefcode($cliente->id_externo);
            $sale->manual($customer, $valor, $competencia, $vencimento, $pagamento, $complemento, $tags);

            $payment = $sale->save();

            foreach($cobranca->pagamentos() as $p) {
                if(!$p->id_financeiro) {
                    $p->id_financeiro = 'PAYMENT-' . $payment->id;
                    $p->update();
                }
            }
        }

        self::setSuccess('Cobrança manual lançada com sucesso.');

        return redirect()->back();
    }

    public function addCreditCardForm(Request $request, $id) {
        if(!$id) {
            abort('Cliente não encontrado');
        }
        $cliente = Clientes::find($id);
        if(!$cliente) {
            abort('Cliente não encontrado');
        }
        if(!$cliente->ativo) {
            abort('Cliente inativo');
        }

        return view('clientes.credit-card.add', ['cliente' => $cliente]);
    }

    public function addCreditCard(Request $request, $id)
    {
        if(!$id) {
            abort('Cliente não encontrado');
        }
        $cliente = Clientes::find($id);

        if(!$cliente) {
            abort('Cliente não encontrado');
        }
        if(!$cliente->ativo) {
            abort('Cliente inativo');
        }
        if(!$cliente->id_superlogica) {
            abort('Cliente não vinculado ao sistema financeiro.');
        }

        $v = Validator::make($request->all(), [
            'cardNumber' => 'required',
            'validDate'  => 'required',
            'cvv'        => 'required|numeric',
            'brand'      => 'required',
            'holder'     => 'required',
        ]);


        if($v->fails()) {
            $errors = $v->getMessageBag()->all();
            $errors = join("\n", $errors);
            self::setError('Erros de validação: ' . "\n $errors", 'Erro ao validar');
            return redirect()->back();
        }

        $cardNumber = $request->get('cardNumber');
        $validDate = explode('/', $request->get('validDate'));
        $validMonth = $validDate[0];
        $validYear = $validDate[1];;
        $cvv = $request->get('cvv');
        $holder = $request->get('holder');
        $brand = $request->get('brand');

        $creditCard = new CreditCard(
            $cardNumber,
            $validMonth,
            $validYear,
            $cvv,
            $holder,
            $brand
        );

        $paymentMethod = new PaymentMethod();
        $paymentMethod->addCard($cliente->id_superlogica, $creditCard);

        $cliente->credit_card = $creditCard->getHashedCardNumber();
        $cliente->credit_card_added_at = now();

        return redirect()->to(route('clientes.credit-card.add.success'));
    }

    public function creditCardAddSuccessPage()
    {
        return view('clientes.credit-card.success');
    }

    /**
     * @param $cobranca
     * @return false|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function visualizarFaturaSistemaFinanceiro($cobranca)
    {
        if (empty($cobranca->id_financeiro)) {
            return view('error', [
                'status_code' => 400,
                'message' => "Não possui vínculo com sistema financeiro"
            ]);
        }
        $financeiro = new Financeiro();

        $payment_financeiro = $financeiro->get('/payment/' . $cobranca->id_financeiro);
        $invoice_financeiro = $financeiro->get('/invoice/' . $payment_financeiro->invoice_id);

        $client = new Client();
        try {

            $res = $client->request('GET', config('financeiro.api.url') . '/invoice/' . $invoice_financeiro->hash . '/pdf', [
                'headers' => [
                    'appid' => config('financeiro.api.app_id'),
                    'secretid' => config('financeiro.api.secret_id'),
                ],
                'http_errors' => false,
            ]);

            $statusCode = $res->getStatusCode();
            $body = $res->getBody();

            if ($statusCode != 200) {
                echo $body['error']['description'];
                return false;
            }
            $content = base64_decode($body, true);
            header('Content-Type: application/pdf');
            header('Content-Length: ' . strlen($content));
            header('Content-Disposition: inline; filename="fatura.pdf"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            ini_set('zlib.output_compression', '0');

            die($content);
        } catch (HTTPException $e) {
            $this->error = 'Há um problema de comunicação com o servidor. Tente novamente mais tarde.';
            return false;
        }
    }
}
