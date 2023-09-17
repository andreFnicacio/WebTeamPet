<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCobrancasRequest;
use App\Http\Requests\UpdateCobrancasRequest;
use App\Http\Util\Logger;
use App\Http\Util\LogMessages;
use App\Models\Cobrancas;
use App\Repositories\CobrancasRepository;
use App\Http\Controllers\AppBaseController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Flash;
use Illuminate\Support\Facades\Validator;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Entrust;

class CobrancasController extends AppBaseController
{
    /** @var  CobrancasRepository */
    private $cobrancasRepository;

    public function __construct(CobrancasRepository $cobrancasRepo)
    {
        $this->cobrancasRepository = $cobrancasRepo;
    }

    /**
     * Display a listing of the Cobrancas.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if(!Entrust::can('list_cobrancas')) {
            return self::notAllowed();
        }

        $limit = 10;
        $this->cobrancasRepository->pushCriteria(new RequestCriteria($request));
        $searchTotal = $this->cobrancasRepository->count();
        $cobrancas = $this->cobrancasRepository->paginate(10);

        $pagination = $this->pagination($request, count($cobrancas), $searchTotal, $limit);
        $data = [
            'cobrancas' => $cobrancas,
            'pagination' => $pagination,
        ];
        return view('cobrancas.index')
            ->with($data);
    }

    /**
     * Show the form for creating a new Cobrancas.
     *
     * @return Response
     */
    public function create()
    {
        if(!Entrust::can('create_cobrancas')) {
            return self::notAllowed();
        }
        return view('cobrancas.create')->with('cobrancas', new \App\Models\Cobrancas());;
    }

    /**
     * Store a newly created Cobrancas in storage.
     *
     * @param CreateCobrancasRequest $request
     *
     * @return Response
     */
    public function store(CreateCobrancasRequest $request)
    {
        if(!Entrust::can('edit_cobrancas')) {
            return self::notAllowed();
        }
        $input = $request->all();

        $cobrancas = $this->cobrancasRepository->create($input);

        Flash::success('Cobrancas saved successfully.');

        return redirect(route('cobrancas.index'));
    }

    /**
     * Display the specified Cobrancas.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        // if(!Entrust::can('edit_cobrancas')) {
        //     return self::notAllowed();
        // }
        // $cobrancas = $this->cobrancasRepository->findWithoutFail($id);

        // if (empty($cobrancas)) {
        //     Flash::error('Cobrancas not found');

        //     return redirect(route('cobrancas.index'));
        // }

        // return view('cobrancas.show')->with('cobrancas', $cobrancas);
    }

    /**
     * Show the form for editing the specified Cobrancas.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        if(!Entrust::can('edit_cobrancas')) {
            return self::notAllowed();
        }
        $cobrancas = $this->cobrancasRepository->findWithoutFail($id);

        if (empty($cobrancas)) {
            Flash::error('Cobrancas not found');

            return redirect(route('cobrancas.index'));
        }

        return view('cobrancas.edit')->with('cobrancas', $cobrancas);
    }

    /**
     * Update the specified Cobrancas in storage.
     *
     * @param  int              $id
     * @param UpdateCobrancasRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCobrancasRequest $request)
    {
        if(!Entrust::can('edit_cobrancas')) {
            return self::notAllowed();
        }
        $cobrancas = $this->cobrancasRepository->findWithoutFail($id);

        if (empty($cobrancas)) {
            Flash::error('Cobrancas not found');

            return redirect(route('cobrancas.index'));
        }

        $cobrancas = $this->cobrancasRepository->update($request->all(), $id);

        Flash::success('Cobrancas updated successfully.');

        return redirect(route('cobrancas.index'));
    }

    /**
     * Remove the specified Cobrancas from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        if(!Entrust::can('delete_cobrancas')) {
            return self::notAllowed();
        }
        $cobrancas = $this->cobrancasRepository->findWithoutFail($id);

        if (empty($cobrancas)) {
            Flash::error('Cobrancas not found');

            return redirect(route('cobrancas.index'));
        }

        $this->cobrancasRepository->delete($id);

        Flash::success('Cobrancas deleted successfully.');

        return redirect(route('cobrancas.index'));
    }

    /**
     * Cancela uma cobrança permitindo informar que há um acordo.
     * @param Request $request
     * @return $this|array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cancelar(Request $request)
    {
        if(!Entrust::can('edit_cobrancas')) {
            return self::notAllowed();
        }
        $v = Validator::make($request->all(), [
            'cobrancas' => 'array',
            'justificativa' => 'required|string',
            'acordo' => 'integer|nullable'
        ]);
        if ($v->fails()) {
            $messages = join("\n", $v->getMessageBag()->all());
            self::setError($messages, 'Oops.');

            return back()
                ->withErrors($v)
                ->withInput();
        }
        $cobrancas = $request->get('cobrancas');
        foreach($cobrancas as $c) {
            $c = Cobrancas::find($c);
            if($c) {
                $c->cancelada_em = new Carbon();
                $c->justificativa = $request->get('justificativa');
                $c->acordo = $request->get('acordo');
                $c->update();

                $mensagem = 'A cobrança #' . $c->id . " foi cancelada.";
                Logger::log(LogMessages::EVENTO['ALTERACAO'], 'Cobranças',
                    'ALTA', $mensagem,
                    auth()->user()->id, 'cobrancas', $c->id);

                self::toast('A cobrança #' . $c->id . " foi ", 'CANCELADA.', 'font-red');
            }
        }

        return back();
    }

    public function cobrancasSuperlogicaCSV()  {
        ini_set('max_execution_time', 60*10);
        /**
         * @var \App\Models\Cobrancas[] $cobrancas
         */
        $cobrancas = \App\Models\Cobrancas::whereNotNull('id_superlogica')->
        whereDate('created_at', '>=', '2020-10-23 00:00:00')->get();
        $invoiceService = new \App\Helpers\API\Superlogica\Invoice();
        $csv = "";

        if(!$cobrancas) {
            return [
                'msg' => 'Nenhuma cobrança encontrada'
            ];
        }

        foreach($cobrancas as $c) {

            $invoice = $invoiceService->get($c->id_superlogica);

            if($invoice) {
                $cliente = $c->cliente;
                $link = $invoice[0]->link_2via;
                $line = "{$cliente->nome_cliente};";
                $line .= "{$cliente->celular};";
                $line .= "{$cliente->email};";
                $line .= "{$c->valor_original};";
                $line .= "$link;\n";
                $csv .= $line;
            }
        }
        file_put_contents(storage_path('csv/cobrancas-superlogica-inadimplentes-20201023.csv'), $csv);
    }
}
