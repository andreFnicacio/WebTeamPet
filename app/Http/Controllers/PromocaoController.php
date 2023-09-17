<?php

namespace App\Http\Controllers;

use App\Models\Promocao;
use App\Helpers\Utils;
use App\Repositories\PromocaoRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Http\Util\Logger;
use Entrust;
use App\Http\Util\LogMessages;
use Carbon\Carbon;

class PromocaoController extends AppBaseController
{
    private $repository;

    public function __construct(PromocaoRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!Entrust::hasRole(['ADMINISTRADOR'])) {
            return self::notAllowed();
        }

        $this->repository->pushCriteria(new RequestCriteria($request));
        $registros = $this->repository->orderBy('ativo', 'DESC')->orderBy('nome')->all();

        return view('promocoes.index', [
            'registros' => $registros
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Entrust::hasRole(['ADMINISTRADOR'])) {
            return self::notAllowed();
        }
        return view('promocoes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Entrust::hasRole(['ADMINISTRADOR'])) {
            return self::notAllowed();
        }
        $input = $request->all();

        $input['dt_inicio'] = (new Carbon())->createFromFormat('d/m/Y', request('dt_inicio'))->format('Y-m-d');

        if(!empty($input['dt_termino'])){
            $input['dt_termino'] = (new Carbon())->createFromFormat('d/m/Y', request('dt_termino'))->format('Y-m-d');
        }

        $promocao = $this->repository->create($input);

        $mensagem = "A promoção {$promocao->id} foi cadastrada.";
        Logger::log(LogMessages::EVENTO['CRIACAO'], 'Promocaos',
            'ALTA', $mensagem,
            auth()->user()->id, 'promocoes', $promocao->id);

        self::setSuccess('Promocao salva com sucesso.');

        return redirect(route('promocoes.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Promocao  $promocao
     * @return \Illuminate\Http\Response
     */
    public function show(Promocao $promocao)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Promocao  $promocao
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!Entrust::hasRole(['ADMINISTRADOR'])) {
            return self::notAllowed();
        }
        $promocao = $this->repository->findWithoutFail($id);

        if (empty($promocao)) {
            self::setError('Promoção não encontrada.');

            return redirect(route('promocoes.index'));
        }

        return view('promocoes.edit')->with([
            'promocao' => $promocao
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Promocao  $promocao
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        if(!Entrust::hasRole(['ADMINISTRADOR'])) {
            return self::notAllowed();
        }
        $promocao = $this->repository->findWithoutFail($id);

        if (empty($promocao)) {
            self::setError('Promoção não encontrada.');

            return redirect(route('promocoes.index'));
        }

        $input = $request->all();

        $input['dt_inicio'] = (new Carbon())->createFromFormat('d/m/Y', request('dt_inicio'))->format('Y-m-d');

        if(!empty($input['dt_termino'])){
            $input['dt_termino'] = (new Carbon())->createFromFormat('d/m/Y', request('dt_termino'))->format('Y-m-d');
        }

        $promocao = $this->repository->update($input, $id);

        $mensagem = "A promoção $id foi alterada.";
        Logger::log(LogMessages::EVENTO['ALTERACAO'], 'Promocaos',
            'ALTA', $mensagem,
            auth()->user()->id, 'promocoes', $id);

        self::setSuccess('Dados alterados com sucesso.');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Promocao  $promocao
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!Entrust::hasRole(['ADMINISTRADOR'])) {
            return self::notAllowed();
        }
        $promocao = $this->repository->findWithoutFail($id);

        if (empty($promocao)) {
            self::setError('Promoção não encontrada.');

            return redirect(route('promocoes.index'));
        }

        $this->repository->delete($id);

        self::setWarning('Promoção excluída com sucesso.');

        $mensagem = "A promoção $id foi excluída.";
        Logger::log(LogMessages::EVENTO['EXCLUSAO'], 'Promocaos',
            'ALTA', $mensagem,
            auth()->user()->id, 'promocoes', $id);

        return redirect(route('promocoes.index'));
    }
}
