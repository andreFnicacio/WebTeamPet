<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePlanosRequest;
use App\Http\Requests\UpdatePlanosRequest;
use App\Models\PLanos;
use App\Models\PlanosGrupos;
use App\Models\PlanosProcedimentos;
use App\Models\Procedimentos;
use App\Repositories\PlanosRepository;
use App\Services\PlanoInconsistenciasService;
use Entrust;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Response;

class PlanosController extends AppBaseController
{
    /** @var  PlanosRepository */
    private $planosRepository;

    public function __construct(PlanosRepository $planosRepo)
    {
        $this->planosRepository = $planosRepo;
    }

    /**
     * Display a listing of the Planos.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if(!Entrust::can('list_planos')) {
            return self::notAllowed();
        }

        $query = Planos::query();

        //Status
        $paramStatus = $request->get('status', null);
        if($paramStatus !== null) {
            $query->where('ativo', $paramStatus);
        }

        //Modalidade
        $paramModalidade = $request->get('modalidade', null);
        if($paramModalidade !== null) {
            $query->where('participativo', $paramModalidade);
        }

        //Termo
        if($request->get('termo') != '') {
            $query->where('nome_plano', 'LIKE', '%' . trim($request->get('termo')) . '%');
        }

        $limit = 20;
        $searchTotal = $query->count();
        $planos = $query->orderBy('data_vigencia', 'DESC')->paginate($limit);

        $pagination = $this->pagination($request, count($planos), $searchTotal, $limit);

        return view('planos.index', [
            'planos' => $planos,
            'pagination' => $pagination,
            'params' => [
                'termo' => $request->get('termo'),
                'status' => $paramStatus,
                'modalidade' => $paramModalidade,
            ]
        ]);
    }

    /**
     * Show the form for creating a new Planos.
     *
     * @return Response
     */
    public function create()
    {
        if(!Entrust::can('create_planos')) {
            return self::notAllowed();
        }

        return view('planos.create')->with('planos', new \App\Models\Planos());
    }

    /**
     * Store a newly created Planos in storage.
     *
     * @param CreatePlanosRequest $request
     *
     * @return Response
     */
    public function store(CreatePlanosRequest $request)
    {
        if(!Entrust::can('create_planos')) {
            return self::notAllowed();
        }
        $input = $request->all();

        $planos = $this->planosRepository->create($input);

        self::setSuccess('Plano criado com sucesso.');

        return redirect(route('planos.edit', $planos->id));
    }

    /**
     * Display the specified Planos.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        if(!Entrust::can('list_planos')) {
            return self::notAllowed();
        }
        $planos = $this->planosRepository->findWithoutFail($id);

        if (empty($planos)) {
            self::setError('Plano não encontrado');

            return redirect(route('planos.index'));
        }

        return view('planos.show')->with('planos', $planos);
    }

    /**
     * Show the form for editing the specified Planos.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        if(!Entrust::can('list_planos')) {
            return self::notAllowed();
        }
        $planos = $this->planosRepository->findWithoutFail($id);
        $clinicas = \Modules\Clinics\Entities\Clinicas::where('ativo', 1)->orderBy('nome_clinica', 'ASC')->get();

        $gruposVinculados = [];
        $procedimentosPorGrupo = [];
        foreach($planos->planosGrupos()->get() as $planoGrupo) {
            $grupo = $planoGrupo->grupo()->first();
            $gruposVinculados[] = $grupo->id;
            foreach($planoGrupo->plano()->first()->procedimentosPorGrupo($grupo) as $procedimento) {
                $procedimentosPorGrupo[$grupo->id][] = $procedimento->id_procedimento;
            }
        }

        if($gruposVinculados) {
            $gruposNaoVinculados = \App\Models\Grupos::whereNotIn('id', $gruposVinculados)->get();
        } else {
            $gruposNaoVinculados = \App\Models\Grupos::all();
        }

        $data = [
            'planos' => $planos,
            'clinicas' => $clinicas,
            'gruposNaoVinculados' => $gruposNaoVinculados,
            'gruposVinculados' => $gruposVinculados,
            'procedimentosPorGrupo' => $procedimentosPorGrupo,
        ];

        if (empty($planos)) {
            self::setError('Plano não encontrado');

            return redirect(route('planos.index'));
        }

        return view('planos.edit', $data);
    }

    /**
     * Update the specified Planos in storage.
     *
     * @param  int              $id
     * @param UpdatePlanosRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePlanosRequest $request)
    {
        if(!Entrust::can('edit_planos')) {
            return self::notAllowed();
        }
        $planos = $this->planosRepository->findWithoutFail($id);

        if (empty($planos)) {
            self::setError('Plano não encontrado');

            return redirect(route('planos.index'));
        }

        $planos = $this->planosRepository->update($request->all(), $id);

        self::setSuccess('Plano atualizado com sucesso.');

        return redirect(route('planos.index'));
    }

    /**
     * Remove the specified Planos from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        if(!Entrust::can('delete_planos')) {
            return self::notAllowed();
        }
        $planos = $this->planosRepository->findWithoutFail($id);

        if (empty($planos)) {
            self::setError('Plano não encontrado');

            return redirect(route('planos.index'));
        }

        $this->planosRepository->delete($id);

        self::setSuccess('Plano excluído com sucesso.');

        return redirect(route('planos.index'));
    }

    public function addNovoGrupo(Request $request)
    {
        $data = $request->all();
        $data['uso_unico'] = $data['uso_unico'] === "true";
        $planoGrupo = new PlanosGrupos();
        $planoGrupo->fill($data);
        $planoGrupo->save();

        $grupo = $planoGrupo->grupo()->first();
        $retorno = [
            'grupo' => $grupo,
            'listaProcedimentos' => \App\Models\Procedimentos::where('id_grupo', $grupo->id)->where('ativo', true)->orderBy('nome_procedimento')->get(),
        ];
        return $retorno;
    }

    public function editGrupo(Request $request)
    {
        $data = $request->all();

        $planoGrupo = (new PlanosGrupos)->find($data['planosgrupos_id']);
        $planoGrupo->quantidade_usos = $data['quantidade_usos'];
        $planoGrupo->dias_carencia = $data['dias_carencia'];
        $planoGrupo->uso_unico = $data['uso_unico'] === "true";
        $planoGrupo->save();

        return $planoGrupo;
    }

    public function deleteGrupo(Request $request)
    {
        $data = $request->all();

        $planoGrupo = PlanosGrupos::find($data['id']);
        $plano = $planoGrupo->plano()->first();
        $grupo = $planoGrupo->grupo()->first();
        $planosProcedimentos = $plano->procedimentosPorGrupo($grupo);
        foreach ($planosProcedimentos as $proc) {
            $procedimento = PlanosProcedimentos::find($proc->id)->delete();
        }
        $planoGrupo->delete();

        return 'Sucesso!';
    }

    public function addProcedimentosGrupo(Request $request)
    {
        $data = $request->all();

        $procedimentos = Procedimentos::where('id_grupo', $data['id_grupo'])->where('ativo', true)->get();
        $procedimentosIds = $procedimentos->pluck('id')->all();

        $planosProcedimentos = PlanosProcedimentos::where('id_plano', $data['id_plano'])->whereIn('id_procedimento', $procedimentosIds)->get();
        $vinculadosIds = $planosProcedimentos->pluck('id_procedimento')->all();

        if(!isset($data['multi_procedimentos'])) {
            $removidos = $vinculadosIds;
            $adicionados = [];
        }else{
            $removidos = array_diff($vinculadosIds, $data['multi_procedimentos']);
            $adicionados = array_diff($data['multi_procedimentos'], $vinculadosIds);    
        }
       
        foreach($removidos as $r) {
            PlanosProcedimentos::where('id_plano', $data['id_plano'])->where('id_procedimento', $r)->first()->delete();
        }
        foreach($adicionados as $a) {
            $newPlanoProc = new PlanosProcedimentos();
            $newPlanoProc->id_procedimento = $a;
            $newPlanoProc->id_plano = $data['id_plano'];
            $newPlanoProc->save();
        }
        return back();
    }

    public function checarProcedimentosPlano(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'file|required|mimes:csv,txt',
            'plano_id' => 'required'
        ]);

        if ($validator->fails()) {
            $messages = join("\n", $validator->getMessageBag()->all());
            $messages = str_replace('file', 'O arquivo', $messages);
            self::setError($messages, 'Oops.');

            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $plano = $this->planosRepository->findWithoutFail($request->plano_id);
        $procedimentosArquivo = \App\Helpers\Utils::csvToArray($request->file, ",");

        $planoInconsistenciasService = new PlanoInconsistenciasService($plano);
        $procedimentosInconsistentes = $planoInconsistenciasService->checarInconsistencias($procedimentosArquivo);

        if(empty($procedimentosInconsistentes)) {
            self::setSuccess('Não existem inconsistências.');
            return redirect(route('planos.edit', $plano->id));
        }

        session(['procedimentosInconsistentes' => $procedimentosInconsistentes]);

        return view('planos.check_procedimentos', [
            'procedimentosInconsistentes' => $procedimentosInconsistentes,
            'id_plano' => $plano->id,
        ]);
    }

    public function corrigirInconsistencias($id)
    {
        $plano = $this->planosRepository->findWithoutFail($id);
        $procedimentosInconsistentes = session('procedimentosInconsistentes');

        $planoInconsistenciasService = new PlanoInconsistenciasService($plano);
        $planoInconsistenciasService->corrigirInconsistencias($procedimentosInconsistentes);

        self::setSuccess('Inconsistências resolvidas com sucesso.');

        return redirect(route('planos.edit', $id));
    }
}