<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEspecialidadesRequest;
use App\Http\Requests\UpdateEspecialidadesRequest;
use App\Repositories\EspecialidadesRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Entrust;

class EspecialidadesController extends AppBaseController
{
    /** @var  EspecialidadesRepository */
    private $especialidadesRepository;

    public function __construct(EspecialidadesRepository $especialidadesRepo)
    {
        $this->especialidadesRepository = $especialidadesRepo;
    }

    /**
     * Display a listing of the Especialidades.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if(!Entrust::can('list_especialidades')) {
            return self::notAllowed();
        }
        $this->especialidadesRepository->pushCriteria(new RequestCriteria($request));
        $especialidades = $this->especialidadesRepository->all();

        return view('especialidades.index')
            ->with('especialidades', $especialidades);
    }

    /**
     * Show the form for creating a new Especialidades.
     *
     * @return Response
     */
    public function create()
    {
        if(!Entrust::can('create_especialidades')) {
            return self::notAllowed();
        }
        return view('especialidades.create')->with('especialidades', new \App\Models\Especialidades);
    }

    /**
     * Store a newly created Especialidades in storage.
     *
     * @param CreateEspecialidadesRequest $request
     *
     * @return Response
     */
    public function store(CreateEspecialidadesRequest $request)
    {
        if(!Entrust::can('create_especialidades')) {
            return self::notAllowed();
        }
        $input = $request->all();

        $especialidades = $this->especialidadesRepository->create($input);

        Flash::success('Especialidades saved successfully.');

        return redirect(route('especialidades.index'));
    }

    /**
     * Display the specified Especialidades.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        if(!Entrust::can('list_especialidades')) {
            return self::notAllowed();
        }
        $especialidades = $this->especialidadesRepository->findWithoutFail($id);

        if (empty($especialidades)) {
            Flash::error('Especialidades not found');

            return redirect(route('especialidades.index'));
        }

        return view('especialidades.show')->with('especialidades', $especialidades);
    }

    /**
     * Show the form for editing the specified Especialidades.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        if(!Entrust::can('list_especialidades')) {
            return self::notAllowed();
        }
        $especialidades = $this->especialidadesRepository->findWithoutFail($id);

        if (empty($especialidades)) {
            Flash::error('Especialidades not found');

            return redirect(route('especialidades.index'));
        }

        return view('especialidades.edit')->with('especialidades', $especialidades);
    }

    /**
     * Update the specified Especialidades in storage.
     *
     * @param  int              $id
     * @param UpdateEspecialidadesRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateEspecialidadesRequest $request)
    {
        if(!Entrust::can('edit_especialidades')) {
            return self::notAllowed();
        }
        $especialidades = $this->especialidadesRepository->findWithoutFail($id);

        if (empty($especialidades)) {
            Flash::error('Especialidades not found');

            return redirect(route('especialidades.index'));
        }

        $especialidades = $this->especialidadesRepository->update($request->all(), $id);

        Flash::success('Especialidades updated successfully.');

        return redirect(route('especialidades.index'));
    }

    /**
     * Remove the specified Especialidades from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        if(!Entrust::can('delete_especialidades')) {
            return self::notAllowed();
        }
        $especialidades = $this->especialidadesRepository->findWithoutFail($id);

        if (empty($especialidades)) {
            Flash::error('Especialidades not found');

            return redirect(route('especialidades.index'));
        }

        $this->especialidadesRepository->delete($id);

        Flash::success('Especialidades deleted successfully.');

        return redirect(route('especialidades.index'));
    }
}