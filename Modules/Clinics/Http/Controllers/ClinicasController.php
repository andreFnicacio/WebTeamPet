<?php

namespace Modules\Clinics\Http\Controllers;

use App\Helpers\API\RDStation\Services\RDCredenciadoCadastradoService;
use App\Http\Controllers\AppBaseController;
use App\Http\Util\Logger;
use App\Http\Util\LogMessages;
use App\Models\Grupos;
use App\Models\Role;
use App\Repositories\ClinicasRepository;
use App\Services\ClinicaAtendimentoTagService;
use App\User;
use Entrust;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Image;
use Mail;
use Modules\Clinics\Entities\Clinicas;
use Modules\Clinics\Http\Requests\CreateClinicasRequest;
use Modules\Clinics\Http\Requests\UpdateClinicasRequest;
use Response;

class ClinicasController extends AppBaseController
{
    /** @var  ClinicasRepository */
    private $clinicasRepository;

    const UPLOAD_TO = 'clinicas/';

    public function __construct(ClinicasRepository $clinicasRepo)
    {
        $this->clinicasRepository = $clinicasRepo;
    }

    /**
     * Display a listing of the Clinicas.
     *
     * @param Request $request
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function index(Request $request)
    {
        if(!Entrust::can('list_clinicas')) {
            return self::notAllowed();
        }

        $searchTerm = $request->get('termo');
        $active = $request->get('ativo');

        $query = Clinicas::getClinicsByTerms($searchTerm, $active);

        $searchTotal = $query->count();
        $clinics = $query->paginate(Clinicas::PAGINATION_SIZE);

        $pagination = $this->pagination($request, count($clinics), $searchTotal, Clinicas::PAGINATION_SIZE);

        return view('clinics::index', [
            'clinicas' => $clinics,
            'pagination' => $pagination,
            'params' => [
                'termo' => $searchTerm,
                'ativo' => $active,
            ]
        ]);
    }

    /**
     * Show the form for creating a new Clinicas.
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function create()
    {
        if(!Entrust::can('create_clinicas')) {
            return self::notAllowed();
        }
        return view('clinics::create', [
            'clinicas' => new Clinicas(),
            'ufs' => self::$ufs
        ]);
    }

    /**
     * Store a newly created Clinicas in storage.
     *
     * @param CreateClinicasRequest $request
     *
     * @return Response
     */
    public function store(CreateClinicasRequest $request)
    {
        if(!Entrust::can('create_clinicas')) {
            return self::notAllowed();
        }
        $input = $request->all();

        $clinicas = $this->clinicasRepository->create($input);

        $mensagem = "A credenciada {$clinicas->id} foi cadastrada.";
        Logger::log(LogMessages::EVENTO['CRIACAO'], 'Clínicas',
            'ALTA', $mensagem,
            auth()->user()->id, 'clinicas', $clinicas->id);

        try {
            (new RDCredenciadoCadastradoService())->process($clinicas);
        } catch (\Exception $e) {
            $mensagem = "Não foi possível notificar a RD do cadastro da credenciada {$clinicas->id}";
            Logger::log(LogMessages::EVENTO['NOTICIA'], 'Clínicas',
                'ALTA', $mensagem,
                auth()->user()->id, 'clinicas', $clinicas->id);
        }

        self::setSuccess('Credenciada salva com sucesso.');

        return redirect(route('clinicas.index'));
    }

    /**
     * Display the specified Clinicas.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        if(!Entrust::can('list_clinicas')) {
            return self::notAllowed();
        }
        $clinicas = $this->clinicasRepository->findWithoutFail($id);

        if (empty($clinicas)) {
            self::setError('Clínica não encontrada.');

            return redirect(route('clinicas.index'));
        }

        return view('clinics::show')->with('clinicas', $clinicas);
    }

    /**
     * Show the form for editing the specified Clinicas.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        if(!Entrust::can('list_clinicas')) {
            return self::notAllowed();
        }
        $clinicas = $this->clinicasRepository->findWithoutFail($id);
        
        if (empty($clinicas)) {
            self::setError('Clínica não encontrada.');

            return redirect(route('clinicas.index'));
        }

        return view('clinics::edit')->with([
            'clinicas' => $clinicas,
            'ufs'      => self::$ufs
        ]);
    }

    public function perfil($id)
    {
        if(!Entrust::can('list_clinicas')) {
            return self::notAllowed();
        }
        $clinica = $this->clinicasRepository->findWithoutFail($id);

        if (empty($clinica)) {
            Flash::error('Credenciado não encontrado');

            return redirect(route('clinicas.index'));
        }

        return view('clinics::perfil')->with([
            'clinica' => $clinica,
            'ufs' => self::$ufs
        ]);
    }

    /**
     * Update the specified Clinicas in storage.
     *
     * @param  int              $id
     * @param UpdateClinicasRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateClinicasRequest $request)
    {
        if(!Entrust::can('edit_clinicas')) {
            return self::notAllowed();
        }
        $clinicas = $this->clinicasRepository->findWithoutFail($id);

        if (empty($clinicas)) {
            self::setError('Clínica não encontrada.');

            return redirect(route('clinicas.index'));
        }

        
        if(Entrust::can('editar_tags_atendimentos_clinicas')) {
            try {

                // Atente-se que o objeto abaixo é obrigatoriamente instanciado com o ID da clínica
                $clinicaAtendimentoTagService = new ClinicaAtendimentoTagService($id);
    
                if($request->filled('atendimento_tags')) {
                    
                    /**
                     * Dentro do método abaixo é feito todo o processo de salvar a tag,
                     * seja cadastrando as tags no banco e vinculando à clinica
                     * ou apenas vinculando as selecionadas que já existem à clínica
                    */
                    $clinicaAtendimentoTagService->save($request->input('atendimento_tags'));
                } else {

                    // No caso do usuário remover todas as tags
                    $clinicaAtendimentoTagService->deleteAll();
                }
    
            } catch (\Exception $e) {
                self::setError($e->getMessage());
                return redirect()->back();
            }
        }

        $clinicas = $this->clinicasRepository->update($request->all(), $id);

        $mensagem = "A credenciada $id foi alterada.";
        Logger::log(LogMessages::EVENTO['ALTERACAO'], 'Clínicas',
            'ALTA', $mensagem,
            auth()->user()->id, 'clinicas', $id);

        self::setSuccess('Dados alterados com sucesso.');
        return redirect()->back();
    }

    /**
     * Remove the specified Clinicas from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        if(!Entrust::can('delete_clinicas')) {
            return self::notAllowed();
        }
        $clinicas = $this->clinicasRepository->findWithoutFail($id);

        if (empty($clinicas)) {
            self::setError('Clínica não encontrada.');

            return redirect(route('clinicas.index'));
        }

        $this->clinicasRepository->delete($id);

        self::setWarning('Credenciada excluída com sucesso.');

        $mensagem = "A credenciada $id foi excluída.";
        Logger::log(LogMessages::EVENTO['EXCLUSAO'], 'Clínicas',
            'ALTA', $mensagem,
            auth()->user()->id, 'clinicas', $id);

        return redirect(route('clinicas.index'));
    }

    public function manualCredenciado() {
        return view('clinicas.manual_credenciado');
    }

    public function prestadores(Request $request) {

        $clinica = (new \Modules\Clinics\Entities\Clinicas)->where('id_usuario', Auth::user()->id)->first();

        if(!$clinica->aceite_urh) {
            return self::notAllowed();
        }

        $prestadores = $clinica->prestadores;

        $data = [
            'clinica' => $clinica,
            'prestadores' => $prestadores,
        ];

        return view('clinicas.prestadores')->with($data);
    }

    public function atualizarUsuario(Request $request){
        $clinica = (new \Modules\Clinics\Entities\Clinicas)->find($request->get('id'));
        if ($clinica->id_usuario) {
            $user = (new \App\User)->find($clinica->id_usuario);
            $user->email = $request->get('email');
            $user->password = Hash::make($request->get('password'));
            $user->save();
        } else {
            $user = (new \App\User)->create([
                'name'      => $clinica->nome_clinica,
                'email'     => $request->get('email'),
                'password'  => Hash::make($request->get('password'))
            ]);
            $clinica->id_usuario = $user->id;
            $clinica->save();
            $user->attachRole((new \App\Models\Role)->where('name', 'CLINICAS')->first());
        }

        self::setSuccess('Usuário atualizado com sucesso.');

        return view('clinics::edit')->with([
            'clinicas' => $clinica,
            'ufs'      => self::$ufs
        ]);
    }

    public function vincularPrestador(Request $request){
        $clinica = (new \Modules\Clinics\Entities\Clinicas)->find($request->get('id'));
        $prestador = (new \Modules\Veterinaries\Entities\Prestadores())->find($request->get('id_prestador'));
        if ($prestador) {
            if (!$clinica->prestadores->contains($prestador->id)) {
                $clinica->prestadores()->attach($prestador->id);
            }
            $msg = 'Prestador adicionado com sucesso.';
            self::setSuccess($msg);
            return ['msg' => $msg];
        } else {
            $msg = 'Prestador não encontrado!';
            self::setError($msg);
            return ['msg' => $msg];
        }
    }

    public function desvincularPrestador(Request $request){
        $clinica = (new \Modules\Clinics\Entities\Clinicas)->find($request->get('id'));
        $prestador = (new \Modules\Veterinaries\Entities\Prestadores())->find($request->get('id_prestador'));
        $clinica->prestadores()->detach($prestador->id);
        $msg = 'Prestador desvinculado com sucesso.';
        self::setSuccess($msg);
        return ['msg' => $msg];
    }

    public function solicitarPrestador(Request $request){
        $data = $request->all();
        $titulo = 'Nova solicitação de Prestador';
        $documentos = $request->file('documentos');
        $data['clinica'] = (new \Modules\Clinics\Entities\Clinicas)->where('id_usuario', Auth::user()->id)->first()->nome_clinica;
        unset($data['_token']);
        unset($data['documentos']);
        Mail::send('mail.credenciados.solicitar_prestador', [
            'titulo' => $titulo,
            'dadosPrestador' => $data
        ], function($message) use ($titulo, $documentos) {

            $message->to('credenciados@lifepet.com.br');
            $message->cc('ramon.penna@lifepet.com.br');
            $message->subject($titulo);
            foreach($documentos as $documento) {
                $message->attach($documento->getRealPath(), array(
                    'as' => $documento->getClientOriginalName(),
                    'mime' => $documento->getMimeType())
                );
            }
        });

        self::setSuccess('Solicitação efetuada com sucesso!');
        return back();
    }

    public function avatar($id) {
        
        $clinica = Clinicas::findOrFail($id);
        $path = storage_path('app/' . $clinica->foto);
        if (!\File::exists($path)) {
            abort(404);
        }

        $file = \File::get($path);
        $type = \File::mimeType($path);

        $response = \Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    public function avatarCropUpload(Request $request)
    {
        $image = $request->image;
        $clinica = (new Clinicas())->find($request->id_clinica);

        list($type, $image) = explode(';', $image);
        list(, $image)      = explode(',', $image);
        $image = base64_decode($image);
        $image_name= time().'.png';

        $path = static::UPLOAD_TO . $clinica->id . '/' . 'foto-' . $clinica->id . '-' . $image_name;

        $image = Image::make($request->image);
        \Storage::put($path, (string) $image->encode());
        $clinica->foto = $path;
        $clinica->update();

        self::setSuccess('Foto do credenciado atualizada com sucesso.');

        return response()->json(['status'=>true]);
    }

    public function atualizaAcessoUser(Request $request){
        $clinica = (new Clinicas())->find($request->id_clinica);

        $newUser = false;
        if ($clinica->user) {
            $user = $clinica->user;
        } else {
            $newUser = true;
            $user = new User();
        }
        $user->name = $clinica->nome_clinica;
        $user->email = $request->get('email');
        $user->password = bcrypt($request->get('password'));
        $user->save();

        if ($newUser) {
            $role = Role::where('name','=','CLINICAS')->first();
            $user->attachRole($role);

            $clinica->id_usuario = $user->id;
            $clinica->update();
        }
        
        self::setSuccess('Dados de acesso atualizados com sucesso.');

        return back();
    }

    public function atualizaPlanos(Request $request){
        $clinica = (new Clinicas())->find($request->id_clinica);

        foreach ($request->get('planoCredenciado') as $id_plano => $habilitado) {
            \App\Models\PlanosCredenciados::updateOrCreate(
                ['id_clinica' => $clinica->id, 'id_plano' => $id_plano],
                ['habilitado' => $habilitado]
            );
        }

        self::setSuccess('Planos atualizados com sucesso.');

        return back();
    }

    public function atualizaPrestadores(Request $request){
        $clinica = (new Clinicas())->find($request->id_clinica);

        $prestadores_id = array_keys($request->get('prestadorCredenciado'), "1");
        $clinica->prestadores()->sync($prestadores_id);

        self::setSuccess('Prestadores atualizados com sucesso.');

        return back();
    }

    public function atualizaCategorias(Request $request){
        $clinica = (new Clinicas())->find($request->id_clinica);

        $categorias_id = array_keys($request->get('categoriaCredenciado'), "1");
        $clinica->categorias()->sync($categorias_id);

        self::setSuccess('Categorias atualizados com sucesso.');

        return back();
    }

    public function atualizaLimites(Request $request) {
        $v = Validator::make($request->all(), [
            'id_clinica' => 'required',
            'id_grupo' => 'required',
            //'limite' => 'required|numeric',
        ]);

        if ($v->fails()) {
            $messages = join("\n", $v->getMessageBag()->all());
            self::setError($messages, 'Oops.');

            return back()
                ->withErrors($v)
                ->withInput();
        }

        $clinica = Clinicas::find($request->id_clinica);
        $grupo = Grupos::find($request->id_grupo);
        $limite = $clinica->atualizarLimite($grupo, $request->limite);

        return [
            'limite' => $limite
        ];
    }

    public function consultaCnpj(Request $request)
    {
        if(!Entrust::can('create_clinicas')) {
            return self::notAllowed();
        }


        if (!$request->has('cpf_cnpj')) {
            return response()->json(['message'=>'Não enviou o campo CNPJ'], 400);
        }

        $clinica = Clinicas::where('cpf_cnpj', $request->cpf_cnpj)->first();

        if ($clinica === null)
        {
            return response()->json(['message'=>'Clinica não encontrada'], 404);
        }

        return response()->json(['clinicas'=>$clinica], 200);
    }

    public function consultaEmail(Request $request)
    {
        if(!Entrust::can('create_clinicas')) {
            return self::notAllowed();
        }


        if (!$request->has('email')) {
            return response()->json(['message'=>'Não possui campo de e-mail'], 400);
        }

        $clinica = Clinicas::where('email_contato', $request->email)->first();

        if ($clinica === null)
        {
            return response()->json(['message'=>'Clinica não encontrada'], 404);
        }

        return response()->json(['clinicas'=>$clinica], 200);
    }
}
