<?php

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Http\Requests\CreateUrhRequest;
use App\Http\Requests\UpdateUrhRequest;
use App\Models\Urh;
use App\Models\UrhHistorico;
use App\Repositories\UrhRepository;
use App\Http\Controllers\AppBaseController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class UrhController extends AppBaseController
{
    /** @var  UrhRepository */
    private $urhRepository;

    public function __construct(UrhRepository $urhRepo)
    {
        $this->urhRepository = $urhRepo;
    }

    /**
     * Display a listing of the Urh.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        return redirect(route('urh.create'));
    }

    /**
     * Show the form for creating a new Urh.
     *
     * @return Response
     */
    public function create()
    {
        $urhs = $this->urhRepository->orderBy('ativo', 'DESC')->orderBy('nome_urh', 'ASC')->get();
        $urh = new Urh();

        return view('urh.create')
            ->with('urh', $urh)
            ->with('urhs', $urhs);
    }

    /**
     * Store a newly created Urh in storage.
     *
     * @param CreateUrhRequest $request
     *
     * @return Response
     */
    public function store(CreateUrhRequest $request)
    {
        $input = $request->all();
        $input['valor_urh'] = Utils::moneyReverse($input['valor_urh']);

        $urh = $this->urhRepository->create($input);
        $urh->data_validade = $urh->created_at;
        $urh->save();

        $urhHistorico = new UrhHistorico();
        $urhHistorico->valor_urh = $urh->valor_urh;
        $urhHistorico->id_urh = $urh->id;
        $urhHistorico->save();

        Flash::success('URH cadastrado com sucesso');

        return redirect(route('urh.create'));
    }

    /**
     * Display the specified Urh.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $urh = $this->urhRepository->findWithoutFail($id);

        if (empty($urh)) {
            Flash::error('Urh not found');

            return redirect(route('urh.index'));
        }

        return view('urh.show')->with('urh', $urh);
    }

    /**
     * Show the form for editing the specified Urh.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $urh = $this->urhRepository->findWithoutFail($id);

        if (empty($urh)) {
            Flash::error('Urh not found');

            return redirect(route('urh.index'));
        }

        return view('urh.edit')->with('urh', $urh);
    }

    /**
     * Update the specified Urh in storage.
     *
     * @param  int              $id
     * @param UpdateUrhRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUrhRequest $request)
    {
        $urh = $this->urhRepository->findWithoutFail($id);

        if (empty($urh)) {
            Flash::error('Urh not found');

            return redirect(route('urh.index'));
        }

        $input = $request->all();
        $input['valor_urh'] = Utils::moneyReverse($input['valor_urh']);

        if ($urh->valor_urh != $input['valor_urh']) {
            $urhHistorico = new UrhHistorico();
            $urhHistorico->valor_urh =$input['valor_urh'];
            $urhHistorico->id_urh = $urh->id;
            $urhHistorico->save();
        }

        $input['data_validade'] = Carbon::now();
        $urh = $this->urhRepository->update($input, $id);

        Flash::success('URH atualizado com sucesso.');

        return redirect(route('urh.create'));
    }

    /**
     * Remove the specified Urh from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $urh = $this->urhRepository->findWithoutFail($id);

        if (empty($urh)) {
            Flash::error('Urh not found');

            return redirect(route('urh.index'));
        }

        $this->urhRepository->delete($id);

        Flash::success('Urh deleted successfully.');

        return redirect(route('urh.index'));
    }
}
