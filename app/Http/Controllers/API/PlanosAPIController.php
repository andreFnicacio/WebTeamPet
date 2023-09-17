<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePlanosAPIRequest;
use App\Http\Requests\API\UpdatePlanosAPIRequest;
use App\Models\Planos;
use App\Repositories\PlanosRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PlanosController
 * @package App\Http\Controllers\API
 */

class PlanosAPIController extends AppBaseController
{
    /** @var  PlanosRepository */
    private $planosRepository;

    public function __construct(PlanosRepository $planosRepo)
    {
        $this->planosRepository = $planosRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/planos",
     *      summary="Get a listing of the Planos.",
     *      tags={"Planos"},
     *      description="Get all Planos",
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
     *                  @SWG\Items(ref="#/definitions/Planos")
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
        $this->planosRepository->pushCriteria(new RequestCriteria($request));
        $this->planosRepository->pushCriteria(new LimitOffsetCriteria($request));
        $planos = $this->planosRepository->all();

        return $this->sendResponse($planos->toArray(), 'Planos retrieved successfully');
    }

    /**
     * @param CreatePlanosAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/planos",
     *      summary="Store a newly created Planos in storage",
     *      tags={"Planos"},
     *      description="Store Planos",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Planos that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Planos")
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
     *                  ref="#/definitions/Planos"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePlanosAPIRequest $request)
    {
        $input = $request->all();

        $planos = $this->planosRepository->create($input);

        return $this->sendResponse($planos->toArray(), 'Planos saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/planos/{id}",
     *      summary="Display the specified Planos",
     *      tags={"Planos"},
     *      description="Get Planos",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Planos",
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
     *                  ref="#/definitions/Planos"
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
        /** @var Planos $planos */
        $planos = $this->planosRepository->findWithoutFail($id);

        if (empty($planos)) {
            return $this->sendError('Planos not found');
        }

        return $this->sendResponse($planos->toArray(), 'Planos retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePlanosAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/planos/{id}",
     *      summary="Update the specified Planos in storage",
     *      tags={"Planos"},
     *      description="Update Planos",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Planos",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Planos that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Planos")
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
     *                  ref="#/definitions/Planos"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePlanosAPIRequest $request)
    {
        $input = $request->all();

        /** @var Planos $planos */
        $planos = $this->planosRepository->findWithoutFail($id);

        if (empty($planos)) {
            return $this->sendError('Planos not found');
        }

        $planos = $this->planosRepository->update($input, $id);

        return $this->sendResponse($planos->toArray(), 'Planos updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/planos/{id}",
     *      summary="Remove the specified Planos from storage",
     *      tags={"Planos"},
     *      description="Delete Planos",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Planos",
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
        /** @var Planos $planos */
        $planos = $this->planosRepository->findWithoutFail($id);

        if (empty($planos)) {
            return $this->sendError('Planos not found');
        }

        $planos->delete();

        return $this->sendResponse($id, 'Planos deleted successfully');
    }
}