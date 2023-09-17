<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTabelasReferenciaRequest;
use App\Http\Requests\UpdateTabelasReferenciaRequest;
use App\Models\Procedimentos;
use App\Models\TabelasReferencia;
use App\Repositories\TabelasReferenciaRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Entrust;

class TabelasReferenciaController extends AppBaseController
{
    /** @var  TabelasReferenciaRepository */
    private $tabelasReferenciaRepository;

    public function __construct(TabelasReferenciaRepository $tabelasReferenciaRepo)
    {
        $this->tabelasReferenciaRepository = $tabelasReferenciaRepo;
    }

    /**
     * Display a listing of the TabelasReferencia.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if(!Entrust::can('list_tabelas_referencia')) {
            return self::notAllowed();
        }
        $this->tabelasReferenciaRepository->pushCriteria(new RequestCriteria($request));
        $tabelasReferencias = $this->tabelasReferenciaRepository->all();
// dd($tabelasReferencias);
        return view('tabelas_referencias.index')
            ->with('tabelasReferencias', $tabelasReferencias);
    }

    /**
     * Show the form for creating a new TabelasReferencia.
     *
     * @return Response
     */
    public function create()
    {
        if(!Entrust::can('create_tabelas_referencia')) {
            return self::notAllowed();
        }
        return view('tabelas_referencias.create')->with('tabelasReferencia', new TabelasReferencia());
    }

    /**
     * Store a newly created TabelasReferencia in storage.
     *
     * @param CreateTabelasReferenciaRequest $request
     *
     * @return Response
     */
    public function store(CreateTabelasReferenciaRequest $request)
    {
        if(!Entrust::can('create_tabelas_referencia')) {
            return self::notAllowed();
        }
        $input = $request->all();

        $tabelasReferencia = $this->tabelasReferenciaRepository->create($input);

        Flash::success('Tabelas Referencia saved successfully.');

        return redirect(route('tabelasReferencias.edit', $tabelasReferencia->id));
    }

    /**
     * Display the specified TabelasReferencia.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        if(!Entrust::can('list_tabelas_referencia')) {
            return self::notAllowed();
        }
        $tabelasReferencia = $this->tabelasReferenciaRepository->findWithoutFail($id);

        if (empty($tabelasReferencia)) {
            Flash::error('Tabelas Referencia not found');

            return redirect(route('tabelasReferencias.index'));
        }

        return view('tabelas_referencias.show')->with('tabelasReferencia', $tabelasReferencia);
    }

    /**
     * Show the form for editing the specified TabelasReferencia.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        if(!Entrust::can('list_tabelas_referencia')) {
            return self::notAllowed();
        }
        $tabelasReferencia = $this->tabelasReferenciaRepository->findWithoutFail($id);

        if (empty($tabelasReferencia)) {
            Flash::error('Tabelas Referencia not found');

            return redirect(route('tabelasReferencias.index'));
        }

        return view('tabelas_referencias.edit')->with('tabelasReferencia', $tabelasReferencia);
    }

    /**
     * Update the specified TabelasReferencia in storage.
     *
     * @param  int              $id
     * @param UpdateTabelasReferenciaRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTabelasReferenciaRequest $request)
    {
        if(!Entrust::can('edit_tabelas_referencia')) {
            return self::notAllowed();
        }
        $tabelasReferencia = $this->tabelasReferenciaRepository->findWithoutFail($id);

        if (empty($tabelasReferencia)) {
            Flash::error('Tabelas Referencia not found');

            return redirect(route('tabelasReferencias.index'));
        }

        $tabelasReferencia = $this->tabelasReferenciaRepository->update($request->all(), $id);

        Flash::success('Tabelas Referencia updated successfully.');

        return redirect(route('tabelasReferencias.index'));
    }

    /**
     * Remove the specified TabelasReferencia from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        if(!Entrust::can('delete_tabelas_referencia')) {
            return self::notAllowed();
        }
        $tabelasReferencia = $this->tabelasReferenciaRepository->findWithoutFail($id);

        if (empty($tabelasReferencia)) {
            Flash::error('Tabelas Referencia not found');

            return redirect(route('tabelasReferencias.index'));
        }

        $this->tabelasReferenciaRepository->delete($id);

        Flash::success('Tabelas Referencia deleted successfully.');

        return redirect(route('tabelasReferencias.index'));
    }

    public function procedimentos($idTabela)
    {
        $query = DB::table('procedimentos')
            ->select(
                'procedimentos.id',
                'procedimentos.nome_procedimento',
                'procedimentos.valor_base',
                'procedimentos.ativo',
                'grupos_carencias.nome_grupo'
            )
            ->join('grupos_carencias', 'procedimentos.id_grupo', '=', 'grupos_carencias.id')
            ->orderBy('grupos_carencias.nome_grupo', 'ASC')
            ->orderBy('procedimentos.nome_procedimento', 'ASC');
        return $query->get();
    }

    public static function getTabelaBase()
    {
        return TabelasReferencia::where('tabela_base', 1)->first();
    }

}
