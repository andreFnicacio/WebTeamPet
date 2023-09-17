<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTabelasReferenciaAPIRequest;
use App\Http\Requests\API\UpdateTabelasReferenciaAPIRequest;
use App\Models\TabelasReferencia;
use App\Repositories\TabelasReferenciaRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TabelasReferenciaController
 * @package App\Http\Controllers\API
 */

class TabelasReferenciaAPIController extends AppBaseController
{
    /** @var  TabelasReferenciaRepository */
    private $tabelasReferenciaRepository;

    public function __construct(TabelasReferenciaRepository $tabelasReferenciaRepo)
    {
        $this->tabelasReferenciaRepository = $tabelasReferenciaRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tabelasReferencias",
     *      summary="Get a listing of the TabelasReferencias.",
     *      tags={"TabelasReferencia"},
     *      description="Get all TabelasReferencias",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/TabelasReferencia")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->tabelasReferenciaRepository->pushCriteria(new RequestCriteria($request));
        $this->tabelasReferenciaRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tabelasReferencias = $this->tabelasReferenciaRepository->all();

        return $this->sendResponse($tabelasReferencias->toArray(), 'Tabelas Referencias retrieved successfully');
    }

    /**
     * @param CreateTabelasReferenciaAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tabelasReferencias",
     *      summary="Store a newly created TabelasReferencia in storage",
     *      tags={"TabelasReferencia"},
     *      description="Store TabelasReferencia",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TabelasReferencia that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TabelasReferencia")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/TabelasReferencia"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTabelasReferenciaAPIRequest $request)
    {
        $input = $request->all();

        $tabelasReferencias = $this->tabelasReferenciaRepository->create($input);

        return $this->sendResponse($tabelasReferencias->toArray(), 'Tabelas Referencia saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tabelasReferencias/{id}",
     *      summary="Display the specified TabelasReferencia",
     *      tags={"TabelasReferencia"},
     *      description="Get TabelasReferencia",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TabelasReferencia",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/TabelasReferencia"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var TabelasReferencia $tabelasReferencia */
        $tabelasReferencia = $this->tabelasReferenciaRepository->findWithoutFail($id);

        if (empty($tabelasReferencia)) {
            return $this->sendError('Tabelas Referencia not found');
        }

        return $this->sendResponse($tabelasReferencia->toArray(), 'Tabelas Referencia retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTabelasReferenciaAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tabelasReferencias/{id}",
     *      summary="Update the specified TabelasReferencia in storage",
     *      tags={"TabelasReferencia"},
     *      description="Update TabelasReferencia",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TabelasReferencia",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TabelasReferencia that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TabelasReferencia")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/TabelasReferencia"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTabelasReferenciaAPIRequest $request)
    {
        $input = $request->all();

        /** @var TabelasReferencia $tabelasReferencia */
        $tabelasReferencia = $this->tabelasReferenciaRepository->findWithoutFail($id);

        if (empty($tabelasReferencia)) {
            return $this->sendError('Tabelas Referencia not found');
        }

        $tabelasReferencia = $this->tabelasReferenciaRepository->update($input, $id);

        return $this->sendResponse($tabelasReferencia->toArray(), 'TabelasReferencia updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tabelasReferencias/{id}",
     *      summary="Remove the specified TabelasReferencia from storage",
     *      tags={"TabelasReferencia"},
     *      description="Delete TabelasReferencia",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TabelasReferencia",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var TabelasReferencia $tabelasReferencia */
        $tabelasReferencia = $this->tabelasReferenciaRepository->findWithoutFail($id);

        if (empty($tabelasReferencia)) {
            return $this->sendError('Tabelas Referencia not found');
        }

        $tabelasReferencia->delete();

        return $this->sendResponse($id, 'Tabelas Referencia deleted successfully');
    }
}
