<?php

namespace Modules\Veterinaries\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Models\Especialidades;
use App\Repositories\PrestadoresRepository;
use Entrust;
use Flash;
use Illuminate\Http\Request;
use Modules\Veterinaries\Entities\Prestadores;
use Modules\Veterinaries\Http\Requests\CreatePrestadoresRequest;
use Modules\Veterinaries\Http\Requests\UpdatePrestadoresRequest;
use Response;

class PrestadoresController extends AppBaseController
{
    /** @var  PrestadoresRepository */
    private $prestadoresRepository;

    public function __construct(PrestadoresRepository $prestadoresRepo)
    {
        $this->prestadoresRepository = $prestadoresRepo;
    }

    /**
     * Display a listing of the Prestadores.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if(!Entrust::can('list_prestadores')) {
            return self::notAllowed();
        }

        $query = Prestadores::select(
            'prestadores.id',
            'prestadores.nome',
            'prestadores.email',
            'prestadores.telefone',
            'prestadores.crmv',
            'prestadores.crmv_uf',
            'prestadores.especialista',
            'e.nome as nome_especialidade'
        )
        ->leftJoin('especialidades as e', 'e.id', '=', 'prestadores.id_especialidade')
        ->orderBy('prestadores.nome', 'ASC');

        //Especialista
        $paramEspecialista = $request->get('especialista', null);
        if($paramEspecialista !== null) {
            $query->where('prestadores.especialista', $paramEspecialista);
        }

        //Especialidade
        $paramEspecialidades = $request->get('especialidades', null);
        if($paramEspecialidades) {
            $query->whereIn('e.id', $paramEspecialidades);
        }

        //CRMV
        $paramCRMV = $request->get('crmv', null);
        if($paramCRMV) {
            $query->where('prestadores.crmv', 'LIKE', '%' . trim($paramCRMV) . '%');
        }

        //Nome
        $paramNome = $request->get('nome', null);
        if($paramNome) {
            $query->where('prestadores.nome', 'LIKE', '%' . trim($paramNome) . '%');
        }

        $especialidades = Especialidades::select('nome', 'id')->get();

        $limit = 10;
        $searchTotal = $query->count();
        $prestadores = $query->paginate($limit);
        $pagination = $this->pagination($request, count($prestadores), $searchTotal, $limit);

        return view('veterinaries::index', [
            'prestadores' => $prestadores,
            'pagination' => $pagination,
            'especialidades' => $especialidades,
            'params' => [
                'especialista' => $paramEspecialista,
                'especialidades' => $paramEspecialidades ?? [],
                'crmv' => $paramCRMV,
                'nome' => $paramNome,
            ]
        ]);
    }

    /**
     * Show the form for creating a new Prestadores.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        if(!Entrust::can('create_prestadores')) {
            return self::notAllowed();
        }
        $id_clinica = $request->get('id_clinica');
        return view('veterinaries::create')->with([
            'prestadores' => new Prestadores(),
            'id_clinica' => $id_clinica
        ]);

    }

    /**
     * Store a newly created Prestadores in storage.
     *
     * @param CreatePrestadoresRequest $request
     *
     * @return Response
     */
    public function store(CreatePrestadoresRequest $request)
    {
        if(!Entrust::can('create_prestadores')) {
            return self::notAllowed();
        }

        $notValid = Prestadores::where('cpf', $request->get('cpf'))->exists();

        if ($notValid) {
            self::setError('O CPF digitado já está cadastrado!');
            return back();
        } else {
            $input = $request->all();
            $prestadores = $this->prestadoresRepository->create($input);

            Flash::success('Prestadores saved successfully.');
            return redirect(route('prestadores.index'));
        }
    }

    /**
     * Display the specified Prestadores.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        if(!Entrust::can('list_prestadores')) {
            return self::notAllowed();
        }
        $prestadores = $this->prestadoresRepository->findWithoutFail($id);

        if (empty($prestadores)) {
            Flash::error('Veterinário não encontrado');

            return redirect(route('prestadores.index'));
        }

        return view('veterinaries::show')->with('prestadores', $prestadores);
    }

    /**
     * Show the form for editing the specified Prestadores.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        if(!Entrust::can('list_prestadores')) {
            return self::notAllowed();
        }
        $prestadores = $this->prestadoresRepository->findWithoutFail($id);

        if (empty($prestadores)) {
            Flash::error('Veterinário não encontrado');

            return redirect(route('prestadores.index'));
        }

        return view('veterinaries::edit')->with('prestadores', $prestadores);
    }

    /**
     * Update the specified Prestadores in storage.
     *
     * @param $id
     * @param UpdatePrestadoresRequest $request
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View|void
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update($id, UpdatePrestadoresRequest $request)
    {
        if(!Entrust::can('edit_prestadores')) {
            return self::notAllowed();
        }

        $prestadores = $this->prestadoresRepository->findWithoutFail($id);

        if (empty($prestadores)) {
            Flash::error('Veterinário não encontrado');
            return redirect(route('prestadores.index'));
        }

        $this->prestadoresRepository->update($request->all(), $id);

        Flash::success('Veterinário atualizado com sucesso.');

        return redirect(route('prestadores.index'));
    }

    /**
     * Remove the specified Prestadores from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        if(!Entrust::can('delete_prestadores')) {
            return self::notAllowed();
        }
        $prestadores = $this->prestadoresRepository->findWithoutFail($id);

        if (empty($prestadores)) {
            Flash::error('Veterinário não encontrado');

            return redirect(route('prestadores.index'));
        }

        $this->prestadoresRepository->delete($id);

        Flash::success('Prestadores deleted successfully.');

        return redirect(route('prestadores.index'));
    }
}