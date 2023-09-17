<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInformacoesAdicionaisRequest;
use App\Http\Requests\UpdateInformacoesAdicionaisRequest;
use App\Models\InformacoesAdicionais;
use App\Models\InformacoesAdicionaisVinculos;
use App\Repositories\InformacoesAdicionaisRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Illuminate\Support\Facades\Validator;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Entrust;

class InformacoesAdicionaisController extends AppBaseController
{
    /** @var  InformacoesAdicionaisRepository */
    private $informacoesAdicionaisRepository;

    public function __construct(InformacoesAdicionaisRepository $informacoesAdicionaisRepo)
    {
        $this->informacoesAdicionaisRepository = $informacoesAdicionaisRepo;
    }

    /**
     * Display a listing of the InformacoesAdicionais.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if(!Entrust::can('list_informacoes_adicionais')) {
            return self::notAllowed();
        }

        $limit = 10;
        $this->informacoesAdicionaisRepository->pushCriteria(new RequestCriteria($request));
        $searchTotal = $this->informacoesAdicionaisRepository->count();
        $informacoesAdicionais = $this->informacoesAdicionaisRepository->paginate(10);

        $pagination = $this->pagination($request, count($informacoesAdicionais), $searchTotal, $limit);
        $data = [
            'informacoesAdicionais' => $informacoesAdicionais,
            'pagination' => $pagination,
        ];

        return view('informacoes_adicionais.index')
            ->with($data);


    }

    /**
     * Show the form for creating a new InformacoesAdicionais.
     *
     * @return Response
     */
    public function create()
    {
        if(!Entrust::can('create_informacoes_adicionais')) {
            return self::notAllowed();
        }

        return view('informacoes_adicionais.create');
    }


    public function store(Request $request)
    {

        if(!Entrust::can('create_informacoes_adicionais')) {
            return self::notAllowed();
        }
        $infoAdd = InformacoesAdicionais::create($request->all());
        if(!$infoAdd) {
            return self::setError('Houve um problema no cadastro da informação adicional.');
        }
        self::setSuccess('Informações adicionais cadastradas com sucesso.');

        return redirect(route('informacoesAdicionais.index'));
    }

    /**
     * Display the specified InformacoesAdicionais.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
//        $informacoesAdicionais = $this->informacoesAdicionaisRepository->findWithoutFail($id);
//
//        if (empty($informacoesAdicionais)) {
//            Flash::error('Informacoes Adicionais not found');
//
//            return redirect(route('informacoesAdicionais.index'));
//        }
//
//        return view('informacoes_adicionais.show')->with('informacoesAdicionais', $informacoesAdicionais);
    }

    /**
     * Show the form for editing the specified InformacoesAdicionais.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        if(!Entrust::can('edit_informacoes_adicionais')) {
            return self::notAllowed();
        }

        $informacoesAdicionais = $this->informacoesAdicionaisRepository->findWithoutFail($id);

        if (empty($informacoesAdicionais)) {
            Flash::error('Informacoes Adicionais not found');

            return redirect(route('informacoesAdicionais.index'));
        }

        return view('informacoes_adicionais.edit')->with('informacoesAdicionais', $informacoesAdicionais);
    }

    /**
     * Update the specified InformacoesAdicionais in storage.
     *
     * @param  int              $id
     * @param UpdateInformacoesAdicionaisRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateInformacoesAdicionaisRequest $request)
    {
        if(!Entrust::can('edit_informacoes_adicionais')) {
            return self::notAllowed();
        }
        $informacoesAdicionais = $this->informacoesAdicionaisRepository->findWithoutFail($id);

        if (empty($informacoesAdicionais)) {
            Flash::error('Informacoes Adicionais not found');

            return redirect(route('informacoesAdicionais.index'));
        }

        $informacoesAdicionais = $this->informacoesAdicionaisRepository->update($request->all(), $id);

        Flash::success('Informacoes Adicionais updated successfully.');

        return redirect(route('informacoesAdicionais.index'));
    }

    /**
     * Remove the specified InformacoesAdicionais from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
//        $informacoesAdicionais = $this->informacoesAdicionaisRepository->findWithoutFail($id);
//
//        if (empty($informacoesAdicionais)) {
//            Flash::error('Informacoes Adicionais not found');
//
//            return redirect(route('informacoesAdicionais.index'));
//        }
//
//        $this->informacoesAdicionaisRepository->delete($id);
//
//        Flash::success('Informacoes Adicionais deleted successfully.');
//
//        return redirect(route('informacoesAdicionais.index'));
    }

    /**
     * Vincula uma informação adicional a alguma tabela com um ID específico
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function vincular(Request $request)
    {
        $v = Validator::make($request->all(), InformacoesAdicionaisVinculos::$rules);

        if(!$v->fails()) {
            if(InformacoesAdicionaisVinculos::create($request->all())) {
                self::setSuccess('Informação vinculada.');
                return back();
            }
        }

        self::setError('Um ou mais parâmetros obrigatórios não atendem a especificação. Tente novamente.');
        return back();
    }
}
