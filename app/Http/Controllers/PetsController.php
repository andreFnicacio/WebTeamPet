<?php

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Http\Requests\CreatePetsRequest;
use App\Http\Requests\UpdatePetsRequest;
use App\Models\Cancelamento;
use App\Models\ExtensaoProcedimento;
use App\Models\Notas;
use App\Models\Pets;
use App\Models\PetsGrupos;
use App\Models\PetsPlanos;
use App\Models\Planos;
use App\Models\Procedimentos;
use App\Models\Vendas;
use App\Repositories\PetsRepository;
use App\Repositories\ClinicasRepository;
use App\User;
use Carbon\Carbon;
use Entrust;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Image;
use Response;
use Validator;

class PetsController extends AppBaseController
{
    /** @var  PetsRepository */
    private $petsRepository;

    const UPLOAD_TO = 'pets/';

    public function __construct(PetsRepository $petsRepo, ClinicasRepository  $clinicasRepository)
    {
        $this->petsRepository = $petsRepo;
        $this->clinicasRepository = $clinicasRepository;
    }

    /**
     * Display a listing of the Pets.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if(!Entrust::can('list_pets')) {
            return self::notAllowed();
        }

        $limit = 10;

        $query = Pets::select(
            'pets.id', 
            'pets.numero_microchip',
            'pets.nome_pet', 
            'pets.tipo',
            'pets.familiar',
            'pets.ativo',  
            'r.nome as nome_raca',
            'c.nome_cliente', 
            'c.id as id_cliente',
            'p.nome_plano' 
        )
        ->join('pets_planos as pp', function($query) {
            $query->on('pets.id','=','pp.id_pet')
            ->whereRaw('pp.id IN (select MAX(pp2.id) from pets_planos as pp2 join pets as p2 on p2.id = pp2.id_pet group by p2.id)');
        })
        ->join('planos as p', 'p.id', '=', 'pp.id_plano')
        ->join('racas as r', 'r.id', '=', 'pets.id_raca')
        ->join('clientes as c', 'c.id', 'pets.id_cliente')
        ->orderBy('pets.id', 'ASC');
            
        //Search Criteria
        $searchCriteria = $request->get('search', null);
        if($searchCriteria !== null) {
            $query->where(function ($query) use ($searchCriteria) {
                return $query->where('pets.nome_pet', 'LIKE', '%'.$searchCriteria.'%')
                    ->orWhere('pets.numero_microchip', 'LIKE', '%'.$searchCriteria.'%')
                    ->orWhere('pets.id', '=', '%'.$searchCriteria.'%')
                    ->orWhere('c.nome_cliente', 'LIKE', '%'.$searchCriteria.'%')
                    ->orWhere('pets.observacoes', 'LIKE', '%'.$searchCriteria.'%');   
            });
        }
            
        //Status
        $paramStatus = $request->get('status', null);
        if($paramStatus !== null) {
            $query->where('pets.ativo', $paramStatus);
        }

        //Tipo
        $paramTipo = $request->get('tipo', null);
        if($paramTipo) {
            $query->where('pets.tipo', $paramTipo);
        }

        //Plano
        $paramPlanos = $request->get('planos', null);
        if($paramPlanos) {
            $query->whereIn('p.id', $paramPlanos);
        }
        
        $planos = Planos::select('nome_plano', 'id')->get();
        
        $searchTotal = $query->count();
        $pets = $query->paginate($limit);
        $pagination = $this->pagination($request, count($pets), $searchTotal, $limit);

        return view('pets.index', [
            'pets' => $pets,
            'pagination' => $pagination,
            'planos' => $planos,
            'params' => [
                'status' => $paramStatus,
                'tipo'   => $paramTipo,
                'planos' => $paramPlanos ?? [],
                'search' => $request->get('search'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new Pets.
     *
     * @return Response
     */
    public function create()
    {
        if(!Entrust::can('create_pets')) {
            return self::notAllowed();
        }
        return view('pets.create')->with('pets', new Pets());
    }

    /**
     * Store a newly created Pets in storage.
     *
     * @param CreatePetsRequest $request
     *
     * @return Response
     */
    public function store(CreatePetsRequest $request)
    {
        if(!Entrust::can('create_pets')) {
            return self::notAllowed();
        }
        $input = $request->all();

        $pet = Pets::create($input);

        self::setSuccess('Pet criado com sucesso.');

        return redirect(route('pets.edit', $pet->id));
    }

    /**
     * Display the specified Pets.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        if(!Entrust::can('list_pets')) {
            return self::notAllowed();
        }
        $pets = $this->petsRepository->findWithoutFail($id);

        if (empty($pets)) {
            self::setError('Pet não encontrado');

            return redirect(route('pets.index'));
        }

        return view('pets.show')->with('pets', $pets);
    }

    /**
     * Show the form for editing the specified Pets.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        if(!Entrust::can('edit_pets')) {
            return self::notAllowed();
        }
        $pets = $this->petsRepository->findWithoutFail($id);
        $categorias = \Modules\Clinics\Entities\Clinicas::query()
            ->join('clinicas_categorias', 'clinicas.id', '=', 'clinicas_categorias.id_clinica')
            ->join('categorias', 'clinicas_categorias.id_categoria', '=', 'categorias.id')
            ->where('clinicas.id_usuario','=', Auth::user()->id)->get('categorias.nome');
        $microchip = false;
        if($categorias){
            foreach ($categorias as $categoria) {
                if ($categoria->nome == 'Ponto de Microchipagem') {
                    $microchip = true;
                }
            }
        }
        if (empty($pets)) {
            self::setError('Pet não encontrado');

            return redirect(route('pets.index'));
        }

        $procedimentoMicrochipagem = (new \App\Models\Procedimentos())->find(10101010);

        $data = [
            'pets' => $pets,
            'coberturaMicrochipagem' => $pets->plano()->procedimentoCoberto($procedimentoMicrochipagem),
            'microchip' => $microchip
        ];
    
        if(Entrust::hasRole(['MEDICO_LIFEPET'])) {
            return view('pets.medicos_lifepet')->with($data);
        } else {
            $data['participacoes'] = \App\Models\Participacao::where('id_pet', $pets->id)
                                                             ->orderBy('id', 'DESC')->get();
            return view('pets.perfil')->with($data);
        }

    }

    /**
     * Update the specified Pets in storage.
     *
     * @param  int              $id
     * @param UpdatePetsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePetsRequest $request)
    {
        if(!Entrust::can('edit_pets') && !Entrust::can('editar_informacoes_medicas_pet')) {
            return self::notAllowed();
        }
        $pet = $this->petsRepository->findWithoutFail($id);

        if (empty($pet)) {
            self::setError('Pet não encontrado');

            return redirect(route('pets.index'));
        }

        if(Entrust::hasRole(['MEDICO_LIFEPET'])) {
            $pet = $this->medicalUpdate($id, $request);
        } else {
            $input = $request->all();
            if(isset($input['numero_microchip'])) {
                $input['numero_microchip'] = trim($input['numero_microchip']);
            }

            if(isset($input['valor'])) {
                $input['valor'] = Utils::moneyReverse($input['valor']);
            }

            if(isset($input['contem_doenca_pre_existente'])) {
                $input['contem_doenca_pre_existente'] = trim($input['contem_doenca_pre_existente']) == 'on' ? 1:0;
            }

            $pet = $pet->fill($input)->update();
        }

        self::setSuccess('Pet atualizado com sucesso.');

        return back();
    }

    private function medicalUpdate($id, Request $request)
    {
        $pet = $this->petsRepository->findWithoutFail($id);
        return $pet->fill([
            'numero_microchip' => trim($request->get('numero_microchip')),
            'contem_doenca_pre_existente' => $request->get('contem_doenca_pre_existente'),
            'doencas_pre_existentes' => $request->get('doencas_pre_existentes'),
            'observacoes' => $request->get('observacoes'),
        ])->update();
    }

    /**
     * Remove the specified Pets from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        if(!Entrust::can('delete_pets')) {
            return self::notAllowed();
        }
        $pets = $this->petsRepository->findWithoutFail($id);

        if (empty($pets)) {
            self::setError('Pet não encontrado');

            return redirect(route('pets.index'));
        }

        $this->petsRepository->delete($id);

        self::setSuccess('Pet excluído com sucesso.');

        return redirect(route('pets.index'));
    }

    public static function realizarCancelamentosAgendados() {
        /**
         * @var Cancelamento[] $cancelamentos
         */
        $cancelamentos = Cancelamento::whereDate('data_cancelamento', "<=", Carbon::today())
                         ->whereNull('cancelado_em')
                         ->get();

        $controller = new self(new PetsRepository(app()));

        foreach ($cancelamentos as $c) {
            if($c->pet->ativo) {
                $controller->cancelarPet($c->id_pet, null, $c->cancelar_externo);
                $c->cancelado_em = Carbon::now();
                $c->update();
            } else {
                $c->cancelado_em = Carbon::now();
                $c->update();
            }
        }
    }

    public function deletePetsPlanos(Request $request)
    {
        $data = $request->all();
        PetsPlanos::find($data['id_pets_planos'])->delete();
        return back();
    }

    public function createPetsPlanos(Request $request)
    {

        $v = Validator::make($request->all(), [
            'id_pet' => 'required',
            'data_inicio_contrato' => 'required|date_format:"d/m/Y"',
            'id_vendedor' => 'required'
        ]);
        if ($v->fails()) {
            $messages = join("\n", $v->getMessageBag()->all());
            self::setError($messages, 'Oops.');

            return back()
                ->withErrors($v)
                ->withInput();
        }

        $idPlano = $request->input('id_plano');
        $idPet = $request->input('id_pet');
        $idPetsPlanos = $request->input('id_pets_planos');
        $novoPlano = \App\Models\Planos::find($idPlano);
        $idVendedor = $request->input('id_vendedor');
        $adesao = $request->input('adesao', 0);
        $descontoFolha = $request->input('desconto_folha', 0);

        $idConveniada = NULL;

        if(!empty($request->input('id_conveniada'))){
            $idConveniada = $request->input('id_conveniada');
        }

        $pet = \App\Models\Pets::find($idPet);
        $familiar = $request->input('familiar');
        $pet->familiar = $familiar;
        $mes_reajuste = $request->input('mes_reajuste');
        $pet->mes_reajuste = $mes_reajuste;

        $valorMomento = $request->input('familiar') ? $novoPlano->preco_plano_familiar : $novoPlano->preco_plano_individual;
        if($request->input('valor_plano')) {
            $valorMomento = $request->input('valor_plano');
        }

        $dataInicioContrato = $request->input('data_inicio_contrato');

        $transicao = $request->input('transicao');

        if($request->filled('id_pets_planos')) {
            $petsPlanosAnterior = PetsPlanos::find($idPetsPlanos);
            if (empty($petsPlanos->data_encerramento_contrato)) {
                $petsPlanosAnterior->fill([
                    'data_encerramento_contrato' => $dataInicioContrato
                ]);
                $petsPlanosAnterior->update();
            }
        }

        if (empty($adesao)) {
            $adesao = 0;
        }

        try {
            $petsPlanos = PetsPlanos::create([
                'id_plano' => $novoPlano->id,
                'id_pet'   => $idPet,
                'valor_momento' => $valorMomento,
                'data_inicio_contrato' => $dataInicioContrato,
                'data_encerramento_contrato' => null,
                'id_vendedor' => $idVendedor,
                'status' => $request->input('status'),
                'adesao' => $adesao,
                'desconto_folha' => $descontoFolha,
                'id_conveniada' => $idConveniada,
                'transicao' => $transicao,
                'id_contrato_superlogica' => isset($petsPlanosAnterior) ? $petsPlanosAnterior->id_contrato_superlogica : null
            ]);

            $pet->id_pets_planos = $petsPlanos->id;
            $pet->update();

            (new Vendas())->create([
                'id_cliente' => $pet->cliente->id,
                'id_vendedor' => $idVendedor,
                'id_pet' => $idPet,
                'id_plano' => $novoPlano->id,
                'adesao' => $adesao,
                'valor' => $valorMomento,
                'data_inicio_contrato' => Carbon::createFromFormat('d/m/Y', $dataInicioContrato)
            ]);

            PetsGrupos::where('id_pet', $idPet)->delete();
            ExtensaoProcedimento::where('id_pet', $idPet)->delete();

            AppBaseController::toast('Plano atualizado');
            return redirect(route('pets.edit', $request->input('id_pet')));

        } catch (\Exception $e) {
            AppBaseController::toast('Erro ao cadastrar assinatura na Vindi');
            return redirect(route('pets.edit', $request->input('id_pet')));
        }
    }

    public function cancelarPet($id, $data = null, $cancelarExterno = false)
    {

        $pet = Pets::find($id);
        $pet->ativo = 0;
        $pet->update();

        $petsPlanos = $pet->petsPlanos()->orderBy('id', 'DESC')->first();

        if($petsPlanos) {
            if(!$petsPlanos->data_encerramento_contrato) {
                PetsPlanos::where('id', $petsPlanos->id)->update(array(
                    'data_encerramento_contrato' => ($data ? $data : Carbon::today())
                ));
            }
        }

        Notas::registrar("Cancelamento automático agendado realizado.", $pet->cliente, User::find(1));

    }

    public function reativarPet($id, UpdatePetsRequest $request)
    {
        $pet = Pets::find($id);
        $pet->ativo = 1;
        $pet->update();

        self::setSuccess('O Pet foi reativado com sucesso!');
        return redirect()->route('pets.edit', ['id' => $id]);
    }

    public function cancelamento($id, UpdatePetsRequest $request)
    {
        if(!Entrust::can('edit_pets')) {
            return self::notAllowed();
        }
        $pet = $this->petsRepository->findWithoutFail($id);
        $inputs = $request->all();

        $data_cancelamento = Carbon::createFromFormat('d/m/Y', $inputs['data_cancelamento']);
        $today = Carbon::today();

        if (empty($pet)) {
            self::setError('Pet não encontrado');

            return redirect(route('pets.index'));
        }

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

        if($request->file->isValid()) {
            $extension = $request->file->extension();
            $size = $request->file->getClientSize();
            $mime = $request->file->getClientMimeType();
            $originalName = $request->file->getClientOriginalName();
            $path = $request->file->store('uploads');
            $upload = \App\Models\Uploads::create([
                'original_name' => $originalName,
                'mime'          => $mime,
                'description'   => 'Cancelamento de Contrato. Pet: ' . $pet->id,
                'extension'     => $extension,
                'size'          => $size,
                'public'        => 1,
                'path'          => $path,
                'bind_with'     => 'clientes',
                'binded_id'     => $pet->id_cliente,
                'user_id'       => auth()->user()->id
            ]);
            if($upload) {

                $inputs['data_cancelamento'] = $data_cancelamento->format('Y-m-d');
                Cancelamento::create($inputs);

                if ($data_cancelamento->startOfDay()->gt($today->startOfDay())){
                    self::setSuccess('Cancelamento agendado com sucesso.');
                } else {
                    self::cancelarPet($pet->id, $data_cancelamento, isset($inputs['cancelar_externo']) ? $inputs['cancelar_externo'] : false);
                    self::setSuccess('Cancelamento efetuado com sucesso.');
                }
            }
        } else {
            self::setMessage("Erro no upload.\n\n" + $request->file->getError(), 'error', 'Falha');
        }

        return back();
    }

    public function revogarCancelamento($id, UpdatePetsRequest $request)
    {
        $cancelamento = Cancelamento::find($request['id_cancelamento']);
        if ($cancelamento) {
            self::setSuccess('O cancelamento agendado foi Revogado com sucesso!');
            $cancelamento->delete();

            $pet = $this->petsRepository->findWithoutFail($id);

        } else {
            self::setError('O cancelamento agendado não pôde ser revogado. Tente novamente.', 'Oops.');
        }

        return redirect()->route('pets.edit', ['id' => $id]);
    }

    public function criarExtensaoProcedimento(Request $request)
    {
        if(!\Entrust::can('create_excecao_grupo')) {
            return self::notAllowed();
        }

        $v = Validator::make($request->all(), [
            'id_pet' => 'required',
            'id_procedimento' => 'required',
        ]);
        if ($v->fails()) {
            $messages = join("\n", $v->getMessageBag()->all());
            self::setError($messages, 'Oops.');

            return back()
                ->withErrors($v)
                ->withInput();
        }

        try {
            /**
             * @var Pets $pet
             */
            $pet = Pets::findOrFail($request->get('id_pet'));
            /**
             * @var Procedimentos $procedimento
             */
            $procedimento = Procedimentos::findOrFail($request->get('id_procedimento'));
            $pet->extender($procedimento);

            self::setSuccess('A extensão de procedimento foi criada.');
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062){
                self::setError('Ocorreu um erro na tentativa de criar a exceção: Já existe uma exceção para esse grupo');
            } else {
                self::setError('Ocorreu um erro na tentativa de criar a exceção: ' . $e->getMessage());
            }
        }


        return back();
    }

    public function criarExcecaoGrupo(Request $request)
    {
        if(!\Entrust::can('create_excecao_grupo')) {
            return self::notAllowed();
        }

        $v = Validator::make($request->all(), [
            'id_pet' => 'required',
            'excecao_grupo' => 'required',
            'excecao_dias_carencia' => 'required|numeric',
            'excecao_quantidade_usos' => 'required|numeric',
        ]);
        if ($v->fails()) {
            $messages = join("\n", $v->getMessageBag()->all());
            self::setError($messages, 'Oops.');

            return back()
                ->withErrors($v)
                ->withInput();
        }

        try {
            $excecao = \App\Models\PetsGrupos::create([
                'id_pet' => $request->get('id_pet'),
                'id_grupo' => $request->get('excecao_grupo'),
                'dias_carencia' => $request->get('excecao_dias_carencia'),
                'quantidade_usos' => $request->get('excecao_quantidade_usos'),
                'liberacao_automatica' => $request->get('excecao_liberacao_automatica', 0)
            ]);

            self::setSuccess('A exceção foi criada.');

        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062){
                self::setError('Ocorreu um erro na tentativa de criar a exceção: Já existe uma exceção para esse grupo');
            } else {
                self::setError('Ocorreu um erro na tentativa de criar a exceção: ' . $e->getMessage());
            }
        }


        return back();
    }

    public static function setFoto($pets, $foto)
    {
        if($foto) {
            $extension = 'png';
            $path = static::UPLOAD_TO . $pets->id . '/' . 'foto-' . $pets->id . '-' . Carbon::now()->timestamp . '.' . $extension;
            $image = Image::make($foto);

            \Storage::put($path, (string) $image->encode());

            $pets->foto = $path;
            $pets->update();
        }
    }

    public function foto($id) {
        $pet = Pets::findOrFail($id);
        $path = storage_path('app/' . $pet->foto);
        if (!\File::exists($path)) {
            abort(404);
        }

        $file = \File::get($path);
        $type = \File::mimeType($path);

        $response = \Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    public function fichaAvaliacaoBuscar(Request $request)
    {
        $clinica = \Modules\Clinics\Entities\Clinicas::where('id_usuario', Auth::user()->id)->first();
        if(!$clinica) {
            return self::notAllowed();
        }

        $nome_cpf = $request->get('nome_cpf');
        $cliente = null;
        if ($nome_cpf) {
            $cliente = (new \App\Models\Clientes())
                            ->where('nome_cliente', $request->get('nome_cpf'))
                            ->orWhere('cpf', $request->get('nome_cpf'))
                            ->orWhere('cpf', preg_replace('/\D+/','', $request->get('nome_cpf')))
                            ->first();

            if (!$cliente) {
                self::setWarning('Cliente não encontrado');
            }
        }

        $data = [
            'cliente' => $cliente
        ];

        return view('pets.ficha_avaliacao.buscar')->with($data);
    }

    public function fichaAvaliacao(Request $request, $id)
    {
        $pet = $this->petsRepository->findWithoutFail($id);
        if (!$pet) {
            dd('asfdgfb');
        }

        if (!$pet) {
            self::setError('Pet não encontrado');
            if(Entrust::can('list_pets')) {
                return redirect('pets.index');
            } else {
                return redirect('/');
            }
        }

        $ficha = (new \Modules\Veterinaries\Entities\FichasAvaliacoes)->where('id_pet', $pet->id);
        if ($ficha->exists()) {
            $ficha = $ficha->first();
            foreach ($ficha->respostas as $resposta) {
                // $categorias[] = $resposta->pergunta->categoria;
                $respostas[$resposta->pergunta->categoria][] = $resposta;
            }
            $categorias = collect($respostas)->keys();
            // dd($categorias);
            return view('pets.ficha_avaliacao.show_ficha_avaliacao')->with([
                'ficha' => $ficha,
                'categorias' => $categorias,
                'respostas' => $respostas
            ]);
        } else {
            $clinica = \Modules\Clinics\Entities\Clinicas::where('id_usuario', Auth::user()->id)->first();
            if(!$clinica) {
                self::setMessage('Este pet ainda não possui uma ficha de avaliação eletrônica.', 'warning', 'Oops.');
                if(Entrust::can('edit_pets')) {
                    return redirect(route('pets.edit', $pet->id));
                } else {
                    return redirect('/');
                }
            }
        }

        $p = [];
        $fichaPerguntas = \App\Models\FichasPerguntas::where('ativo', 1)->orderBy('categoria', 'ASC')->get();
        foreach ($fichaPerguntas as $pergunta) {
            $perguntas[$pergunta->categoria][] = $pergunta;
        }

        $data = [
            'pet' => $pet,
            'is_free' => ($pet->plano()->id == 43 ? true : false),
            'clinica' => $clinica,
            'perguntas' => $perguntas,
        ];

        return view('pets.ficha_avaliacao.create_ficha_avaliacao')->with($data);
    }

    public function fichaAvaliacaoStore(Request $request)
    {
        $data = $request->all();

        $ficha = (new \Modules\Veterinaries\Entities\FichasAvaliacoes())::create([
            'id_pet' => $data['id_pet'],
            'id_clinica' => $data['id_clinica'],
            'porte' => $data['porte'],
            'pelagem' => $data['pelagem'],
            'numero_microchip' => $data['numero_microchip'],
        ]);

        foreach ($data['respostas'] as $resposta) {
            $respostas = (new \App\Models\FichasRespostas())::create([
                'id_pergunta' => $resposta['id_pergunta'],
                'id_ficha' => $ficha->id,
                'resposta' => $resposta['resposta'],
                'descricao' => $resposta['descricao']
            ]);
        }

        return redirect()->route('pets.ficha_avaliacao', ['idPet' => $ficha->id_pet]);
    }

    public function fichaAvaliacaoAssinarCliente(Request $request)
    {
        // $user = Auth::user();
        // if ($user) {
        //     $cliente = (new Clientes())->where('id_usuario', $user->id)->first();
        // }

        // if (!$request->get('numero_guia')) {
        //     return response()->json(["msg" => "O número da guia ou a senha não foi reconhecido!"], 401);
        // } else if (!$request->get('senha_plano')) {
        //     return response()->json(["msg" => "A senha não confere"], 401);
        // }

        // $res = $cliente->assinarGuia($request->get('numero_guia'), $request->get('senha_plano'), 2);

        // return response()->json(["msg" => $res['msg']], $res['http']);
    }

    public function fichaAvaliacaoAssinarPrestador(Request $request)
    {
        // $user = Auth::user();
        // if ($user) {
        //     $cliente = (new Clientes())->where('id_usuario', $user->id)->first();
        // }

        // if (!$request->get('numero_guia')) {
        //     return response()->json(["msg" => "O número da guia ou a senha não foi reconhecido!"], 401);
        // } else if (!$request->get('senha_plano')) {
        //     return response()->json(["msg" => "A senha não confere"], 401);
        // }

        // $res = $cliente->assinarGuia($request->get('numero_guia'), $request->get('senha_plano'), 2);

        // return response()->json(["msg" => $res['msg']], $res['http']);
    }

    public function avatarCropUpload(Request $request)
    {
        $image = $request->image;
        $pet = (new Pets())->find($request->id_pet);

        list($type, $image) = explode(';', $image);
        list(, $image)      = explode(',', $image);
        $image = base64_decode($image);
        $image_name= time().'.png';

        $path = static::UPLOAD_TO . $pet->id . '/' . 'foto-' . $pet->id . '-' . $image_name;

        $image = Image::make($request->image);
        \Storage::put($path, (string) $image->encode());
        $pet->foto = $path;
        $pet->update();

        self::setSuccess('Foto do pet atualizada com sucesso.');

        return response()->json(['status'=>true]);
    }
}
