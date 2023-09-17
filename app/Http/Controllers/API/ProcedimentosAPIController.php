<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateProcedimentosAPIRequest;
use App\Http\Requests\API\UpdateProcedimentosAPIRequest;
use App\Models\Procedimentos;
use App\Repositories\ProcedimentosRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ProcedimentosController
 * @package App\Http\Controllers\API
 */

class ProcedimentosAPIController extends AppBaseController
{
    /** @var  ProcedimentosRepository */
    private $procedimentosRepository;

    public function __construct(ProcedimentosRepository $procedimentosRepo)
    {
        $this->procedimentosRepository = $procedimentosRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/procedimentos",
     *      summary="Get a listing of the Procedimentos.",
     *      tags={"Procedimentos"},
     *      description="Get all Procedimentos",
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
     *                  @SWG\Items(ref="#/definitions/Procedimentos")
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
        $this->procedimentosRepository->pushCriteria(new RequestCriteria($request));
        $this->procedimentosRepository->pushCriteria(new LimitOffsetCriteria($request));
        $procedimentos = $this->procedimentosRepository->all();

        return $this->sendResponse($procedimentos->toArray(), 'Procedimentos retrieved successfully');
    }

    /**
     * @param CreateProcedimentosAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/procedimentos",
     *      summary="Store a newly created Procedimentos in storage",
     *      tags={"Procedimentos"},
     *      description="Store Procedimentos",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Procedimentos that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Procedimentos")
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
     *                  ref="#/definitions/Procedimentos"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateProcedimentosAPIRequest $request)
    {
        $input = $request->all();

        $procedimentos = $this->procedimentosRepository->create($input);

        return $this->sendResponse($procedimentos->toArray(), 'Procedimentos saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/procedimentos/{id}",
     *      summary="Display the specified Procedimentos",
     *      tags={"Procedimentos"},
     *      description="Get Procedimentos",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Procedimentos",
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
     *                  ref="#/definitions/Procedimentos"
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
        /** @var Procedimentos $procedimentos */
        $procedimentos = $this->procedimentosRepository->findWithoutFail($id);

        if (empty($procedimentos)) {
            return $this->sendError('Procedimentos not found');
        }

        return $this->sendResponse($procedimentos->toArray(), 'Procedimentos retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateProcedimentosAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/procedimentos/{id}",
     *      summary="Update the specified Procedimentos in storage",
     *      tags={"Procedimentos"},
     *      description="Update Procedimentos",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Procedimentos",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Procedimentos that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Procedimentos")
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
     *                  ref="#/definitions/Procedimentos"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateProcedimentosAPIRequest $request)
    {
        $input = $request->all();

        /** @var Procedimentos $procedimentos */
        $procedimentos = $this->procedimentosRepository->findWithoutFail($id);

        if (empty($procedimentos)) {
            return $this->sendError('Procedimentos not found');
        }

        $procedimentos = $this->procedimentosRepository->update($input, $id);

        return $this->sendResponse($procedimentos->toArray(), 'Procedimentos updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/procedimentos/{id}",
     *      summary="Remove the specified Procedimentos from storage",
     *      tags={"Procedimentos"},
     *      description="Delete Procedimentos",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Procedimentos",
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
        /** @var Procedimentos $procedimentos */
        $procedimentos = $this->procedimentosRepository->findWithoutFail($id);

        if (empty($procedimentos)) {
            return $this->sendError('Procedimentos not found');
        }

        $procedimentos->delete();

        return $this->sendResponse($id, 'Procedimentos deleted successfully');
    }
}