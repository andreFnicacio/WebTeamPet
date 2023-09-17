<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProcedimentosRequest;
use App\Http\Requests\UpdateProcedimentosRequest;
use App\Models\Planos;
use App\Models\PlanosProcedimentos;
use App\Models\Procedimentos;
use App\Repositories\ProcedimentosRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\Grupos;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Entrust;

class ProcedimentosController extends AppBaseController
{
    /** @var  ProcedimentosRepository */
    private $procedimentosRepository;

    public function __construct(ProcedimentosRepository $procedimentosRepo)
    {
        $this->procedimentosRepository = $procedimentosRepo;
    }

    /**
     * Display a listing of the Procedimentos.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if(!Entrust::can('list_procedimentos')) {
            return self::notAllowed();
        }

        $grupos = Grupos::all();
        
        $query = Procedimentos::query();

        if($request->get('codigo') != '') {
            $query->where('cod_procedimento', 'LIKE', '%' . trim($request->get('codigo')) . '%');
        }

        if($request->get('nome') != '') {
            $query->where('nome_procedimento', 'LIKE', '%' . trim($request->get('nome')) . '%');
        }
        
        if($request->get('grupos') != '') {
            $query->whereIn('id_grupo', $request->get('grupos'));
        }

        $limit = 20;
        $searchTotal = $query->count();
        $procedimentos = $query->orderBy('updated_at', 'DESC')->paginate($limit);

        $pagination = $this->pagination($request, count($procedimentos), $searchTotal, $limit);

        return view('procedimentos.index', [
            'procedimentos' => $procedimentos,
            'pagination' => $pagination,
            'grupos' => $grupos,
            'params' => [
                'codigo' => $request->get('codigo'),
                'nome' => $request->get('nome'),
                'grupos' => $request->get('grupos') ?? []
            ]
        ]);
    }

    /**
     * Show the form for creating a new Procedimentos.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        if(!Entrust::can('create_procedimentos')) {
            return self::notAllowed();
        }
        $id_grupo = $request->get('id_grupo');
        return view('procedimentos.create')->with([
            'procedimentos' => new Procedimentos(),
            'id_grupo' => $id_grupo
        ]);
    }

    /**
     * Store a newly created Procedimentos in storage.
     *
     * @param CreateProcedimentosRequest $request
     *
     * @return Response
     */
    public function store(CreateProcedimentosRequest $request)
    {
        if(!Entrust::can('create_procedimentos')) {
            return self::notAllowed();
        }
        $input = $request->all();

        $procedimentos = $this->procedimentosRepository->create($input);

        Flash::success('Procedimentos saved successfully.');

        return redirect(route('procedimentos.index'));
    }

    /**
     * Display the specified Procedimentos.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        if(!Entrust::can('list_procedimentos')) {
            return self::notAllowed();
        }
        $procedimentos = $this->procedimentosRepository->findWithoutFail($id);

        if (empty($procedimentos)) {
            Flash::error('Procedimentos not found');

            return redirect(route('procedimentos.index'));
        }


        return view('procedimentos.show')->with('procedimentos',$procedimentos);
    }

    /**
     * Show the form for editing the specified Procedimentos.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        if(!Entrust::can('list_procedimentos')) {
            return self::notAllowed();
        }
        $procedimentos = $this->procedimentosRepository->findWithoutFail($id);

        if (empty($procedimentos)) {
            Flash::error('Procedimentos not found');

            return redirect(route('procedimentos.index'));
        }
        $planos = Planos::all();

        $planosProcedimentos = $planos->map(function($p) use ($procedimentos) {
            $mapped = new \stdClass();
            $mapped->plano = $p;
            $pp = PlanosProcedimentos::where('id_plano', $p->id)
                ->where('id_procedimento', $procedimentos->id)->first();
            $mapped->vinculado = $mapped->valor_credenciado = $mapped->valor_cliente = null;
            if($pp){
                $mapped->id_vinculo = $pp->id;
                $mapped->vinculado = !is_null($pp);
                $mapped->valor_credenciado = $pp->valor_credenciado;
                $mapped->valor_cliente = $pp->valor_cliente;
            }

            return $mapped;
        });



        return view('procedimentos.edit')->with([
            'procedimentos' => $procedimentos,
            'planosProcedimentos' => $planosProcedimentos
        ]);
    }

    /**
     * Update the specified Procedimentos in storage.
     *
     * @param  int              $id
     * @param UpdateProcedimentosRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateProcedimentosRequest $request)
    {
        if(!Entrust::can('edit_procedimentos')) {
            return self::notAllowed();
        }
        $procedimentos = $this->procedimentosRepository->findWithoutFail($id);

        if (empty($procedimentos)) {
            Flash::error('Procedimentos not found');

            return redirect(route('procedimentos.index'));
        }

        $procedimentos = $this->procedimentosRepository->update($request->all(), $id);

        Flash::success('Procedimentos updated successfully.');

        return redirect(route('procedimentos.index'));
    }

    /**
     * Remove the specified Procedimentos from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        if(!Entrust::can('list_procedimentos')) {
            return self::notAllowed();
        }
        $procedimentos = $this->procedimentosRepository->findWithoutFail($id);

        if (empty($procedimentos)) {
            Flash::error('Procedimentos not found');

            return redirect(route('procedimentos.index'));
        }

        $this->procedimentosRepository->delete($id);

        Flash::success('Procedimentos deleted successfully.');

        return redirect(route('procedimentos.index'));
    }
}