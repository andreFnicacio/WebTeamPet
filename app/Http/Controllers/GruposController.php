<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGruposRequest;
use App\Http\Requests\UpdateGruposRequest;
use App\Models\Grupos;
use App\Repositories\GruposRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Entrust;

class GruposController extends AppBaseController
{
    /** @var  GruposRepository */
    private $gruposRepository;

    public function __construct(GruposRepository $gruposRepo)
    {
        $this->gruposRepository = $gruposRepo;
    }

    /**
     * Display a listing of the Grupos.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if(!Entrust::can('list_grupos')) {
            return self::notAllowed();
        }
        $this->gruposRepository->pushCriteria(new RequestCriteria($request));
        $grupos = $this->gruposRepository->orderBy('nome_grupo')->all();

        return view('grupos.index')
            ->with('grupos', $grupos);
    }

    /**
     * Show the form for creating a new Grupos.
     *
     * @return Response
     */
    public function create()
    {
        if(!Entrust::can('create_grupos')) {
            return self::notAllowed();
        }
        return view('grupos.create')->with('grupos', new Grupos());
    }

    /**
     * Store a newly created Grupos in storage.
     *
     * @param CreateGruposRequest $request
     *
     * @return Response
     */
    public function store(CreateGruposRequest $request)
    {
        if(!Entrust::can('create_grupos')) {
            return self::notAllowed();
        }
        $input = $request->all();

        $grupos = $this->gruposRepository->create($input);

        Flash::success('Grupos saved successfully.');

        return redirect(route('grupos.index'));
    }

    /**
     * Display the specified Grupos.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        if(!Entrust::can('list_grupos')) {
            return self::notAllowed();
        }
        $grupos = $this->gruposRepository->findWithoutFail($id);

        if (empty($grupos)) {
            Flash::error('Grupos not found');

            return redirect(route('grupos.index'));
        }

        return view('grupos.show')->with('grupos', $grupos);
    }

    /**
     * Show the form for editing the specified Grupos.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        if(!Entrust::can('list_grupos')) {
            return self::notAllowed();
        }
        $grupos = $this->gruposRepository->findWithoutFail($id);

        if (empty($grupos)) {
            Flash::error('Grupos not found');

            return redirect(route('grupos.index'));
        }

        return view('grupos.edit')->with('grupos', $grupos);
    }

    /**
     * Update the specified Grupos in storage.
     *
     * @param  int              $id
     * @param UpdateGruposRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateGruposRequest $request)
    {
        if(!Entrust::can('edit_grupos')) {
            return self::notAllowed();
        }
        $grupos = $this->gruposRepository->findWithoutFail($id);

        if (empty($grupos)) {
            Flash::error('Grupos not found');

            return redirect(route('grupos.index'));
        }

        $grupos = $this->gruposRepository->update($request->all(), $id);

        Flash::success('Grupos updated successfully.');

        return redirect(route('grupos.index'));
    }

    /**
     * Remove the specified Grupos from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        if(!Entrust::can('delete_grupos')) {
            return self::notAllowed();
        }
        $grupos = $this->gruposRepository->findWithoutFail($id);

        if (empty($grupos)) {
            Flash::error('Grupos not found');

            return redirect(route('grupos.index'));
        }

        $this->gruposRepository->delete($id);

        Flash::success('Grupos deleted successfully.');

        return redirect(route('grupos.index'));
    }
}