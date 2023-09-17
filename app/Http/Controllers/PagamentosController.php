<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePagamentosRequest;
use App\Http\Requests\UpdatePagamentosRequest;
use App\Models\Pagamentos;
use App\Repositories\PagamentosRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Entrust;

class PagamentosController extends AppBaseController
{
    /** @var  PagamentosRepository */
    private $pagamentosRepository;

    public function __construct(PagamentosRepository $pagamentosRepo)
    {
        $this->pagamentosRepository = $pagamentosRepo;
    }

    /**
     * Display a listing of the Pagamentos.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if(!Entrust::can('list_pagamentos')) {
            return self::notAllowed();
        }
        $limit = 10;
        $this->pagamentosRepository->pushCriteria(new RequestCriteria($request));
        $searchTotal = $this->pagamentosRepository->count();
        $pagamentos = $this->pagamentosRepository->paginate(10);

        $pagination = $this->pagination($request, count($pagamentos), $searchTotal, $limit);
        $data = [
            'pagamentos' => $pagamentos,
            'pagination' => $pagination,
        ];
        return view('pagamentos.index')
            ->with($data);
    }

    /**
     * Show the form for creating a new Pagamentos.
     *
     * @return Response
     */
    public function create()
    {
        if(!Entrust::can('create_pagamentos')) {
            return self::notAllowed();
        }
        return view('pagamentos.create')->with('pagamentos', new Pagamentos());
    }

    /**
     * Store a newly created Pagamentos in storage.
     *
     * @param CreatePagamentosRequest $request
     *
     * @return Response
     */
    public function store(CreatePagamentosRequest $request)
    {
        if(!Entrust::can('create_pagamentos')) {
            return self::notAllowed();
        }
        $input = $request->all();

        $pagamentos = $this->pagamentosRepository->create($input);

        Flash::success('Pagamentos saved successfully.');

        return redirect(route('pagamentos.index'));
    }

    /**
     * Display the specified Pagamentos.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        if(!Entrust::can('list_pagamentos')) {
            return self::notAllowed();
        }
        $pagamentos = $this->pagamentosRepository->findWithoutFail($id);

        if (empty($pagamentos)) {
            Flash::error('Pagamentos not found');

            return redirect(route('pagamentos.index'));
        }

        return view('pagamentos.show')->with('pagamentos', $pagamentos);
    }

    /**
     * Show the form for editing the specified Pagamentos.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        if(!Entrust::can('edit_pagamentos')) {
            return self::notAllowed();
        }
        $pagamentos = $this->pagamentosRepository->findWithoutFail($id);

        if (empty($pagamentos)) {
            Flash::error('Pagamentos not found');

            return redirect(route('pagamentos.index'));
        }

        return view('pagamentos.edit')->with('pagamentos', $pagamentos);
    }

    /**
     * Update the specified Pagamentos in storage.
     *
     * @param  int              $id
     * @param UpdatePagamentosRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePagamentosRequest $request)
    {
        if(!Entrust::can('edit_pagamentos')) {
            return self::notAllowed();
        }
        $pagamentos = $this->pagamentosRepository->findWithoutFail($id);

        if (empty($pagamentos)) {
            Flash::error('Pagamentos not found');

            return redirect(route('pagamentos.index'));
        }

        $pagamentos = $this->pagamentosRepository->update($request->all(), $id);

        Flash::success('Pagamentos updated successfully.');

        return redirect(route('pagamentos.index'));
    }

    /**
     * Remove the specified Pagamentos from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        if(!Entrust::can('delete_pagamentos')) {
            return self::notAllowed();
        }
        $pagamentos = $this->pagamentosRepository->findWithoutFail($id);

        if (empty($pagamentos)) {
            Flash::error('Pagamentos not found');

            return redirect(route('pagamentos.index'));
        }

        $this->pagamentosRepository->delete($id);

        Flash::success('Pagamentos deleted successfully.');

        return redirect(route('pagamentos.index'));
    }
}