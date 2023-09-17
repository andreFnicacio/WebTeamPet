<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateGruposAPIRequest;
use App\Http\Requests\API\UpdateGruposAPIRequest;
use App\Models\Grupos;
use App\Repositories\GruposRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class GruposController
 * @package App\Http\Controllers\API
 */

class GruposAPIController extends AppBaseController
{
    /** @var  GruposRepository */
    private $gruposRepository;

    public function __construct(GruposRepository $gruposRepo)
    {
        $this->gruposRepository = $gruposRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/grupos",
     *      summary="Get a listing of the Grupos.",
     *      tags={"Grupos"},
     *      description="Get all Grupos",
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
     *                  @SWG\Items(ref="#/definitions/Grupos")
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
        $this->gruposRepository->pushCriteria(new RequestCriteria($request));
        $this->gruposRepository->pushCriteria(new LimitOffsetCriteria($request));
        $grupos = $this->gruposRepository->all();

        return $this->sendResponse($grupos->toArray(), 'Grupos retrieved successfully');
    }

    /**
     * @param CreateGruposAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/grupos",
     *      summary="Store a newly created Grupos in storage",
     *      tags={"Grupos"},
     *      description="Store Grupos",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Grupos that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Grupos")
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
     *                  ref="#/definitions/Grupos"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateGruposAPIRequest $request)
    {
        $input = $request->all();

        $grupos = $this->gruposRepository->create($input);

        return $this->sendResponse($grupos->toArray(), 'Grupos saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/grupos/{id}",
     *      summary="Display the specified Grupos",
     *      tags={"Grupos"},
     *      description="Get Grupos",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Grupos",
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
     *                  ref="#/definitions/Grupos"
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
        /** @var Grupos $grupos */
        $grupos = $this->gruposRepository->findWithoutFail($id);

        if (empty($grupos)) {
            return $this->sendError('Grupos not found');
        }

        return $this->sendResponse($grupos->toArray(), 'Grupos retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateGruposAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/grupos/{id}",
     *      summary="Update the specified Grupos in storage",
     *      tags={"Grupos"},
     *      description="Update Grupos",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Grupos",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Grupos that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Grupos")
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
     *                  ref="#/definitions/Grupos"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateGruposAPIRequest $request)
    {
        $input = $request->all();

        /** @var Grupos $grupos */
        $grupos = $this->gruposRepository->findWithoutFail($id);

        if (empty($grupos)) {
            return $this->sendError('Grupos not found');
        }

        $grupos = $this->gruposRepository->update($input, $id);

        return $this->sendResponse($grupos->toArray(), 'Grupos updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/grupos/{id}",
     *      summary="Remove the specified Grupos from storage",
     *      tags={"Grupos"},
     *      description="Delete Grupos",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Grupos",
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
        /** @var Grupos $grupos */
        $grupos = $this->gruposRepository->findWithoutFail($id);

        if (empty($grupos)) {
            return $this->sendError('Grupos not found');
        }

        $grupos->delete();

        return $this->sendResponse($id, 'Grupos deleted successfully');
    }
}