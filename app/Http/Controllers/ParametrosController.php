<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateParametrosRequest;
use App\Http\Requests\UpdateParametrosRequest;
use App\Repositories\ParametrosRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ParametrosController extends AppBaseController
{
    /** @var  ParametrosRepository */
    private $parametrosRepository;

    public function __construct(ParametrosRepository $parametrosRepo)
    {
        $this->parametrosRepository = $parametrosRepo;
    }

    /**
     * Display a listing of the Parametros.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->parametrosRepository->pushCriteria(new RequestCriteria($request));
        $parametros = $this->parametrosRepository->all();

        return view('parametros.index')
            ->with('parametros', $parametros);
    }

    /**
     * Show the form for creating a new Parametros.
     *
     * @return Response
     */
    public function create()
    {
        return view('parametros.create');
    }

    /**
     * Store a newly created Parametros in storage.
     *
     * @param CreateParametrosRequest $request
     *
     * @return Response
     */
    public function store(CreateParametrosRequest $request)
    {
        $input = $request->all();

        $parametros = $this->parametrosRepository->create($input);

        Flash::success('Parametros saved successfully.');

        return redirect(route('parametros.index'));
    }

    /**
     * Display the specified Parametros.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $parametros = $this->parametrosRepository->findWithoutFail($id);

        if (empty($parametros)) {
            Flash::error('Parametros not found');

            return redirect(route('parametros.index'));
        }

        return view('parametros.show')->with('parametros', $parametros);
    }

    /**
     * Show the form for editing the specified Parametros.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $parametros = $this->parametrosRepository->findWithoutFail($id);

        if (empty($parametros)) {
            Flash::error('Parametros not found');

            return redirect(route('parametros.index'));
        }

        return view('parametros.edit')->with('parametros', $parametros);
    }

    /**
     * Update the specified Parametros in storage.
     *
     * @param  int              $id
     * @param UpdateParametrosRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateParametrosRequest $request)
    {
        $parametros = $this->parametrosRepository->findWithoutFail($id);

        if (empty($parametros)) {
            Flash::error('Parametros not found');

            return redirect(route('parametros.index'));
        }

        $parametros = $this->parametrosRepository->update($request->all(), $id);

        Flash::success('Parametros updated successfully.');

        return redirect(route('parametros.index'));
    }

    /**
     * Remove the specified Parametros from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $parametros = $this->parametrosRepository->findWithoutFail($id);

        if (empty($parametros)) {
            Flash::error('Parametros not found');

            return redirect(route('parametros.index'));
        }

        $this->parametrosRepository->delete($id);

        Flash::success('Parametros deleted successfully.');

        return redirect(route('parametros.index'));
    }
}
