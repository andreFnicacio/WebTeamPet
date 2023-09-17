<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreatePrestadoresAPIRequest;
use App\Http\Requests\API\UpdatePrestadoresAPIRequest;
use App\Repositories\PrestadoresRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Modules\Veterinaries\Entities\Prestadores;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PrestadoresController
 * @package App\Http\Controllers\API
 */

class PrestadoresAPIController extends AppBaseController
{
    /** @var  PrestadoresRepository */
    private $prestadoresRepository;

    public function __construct(PrestadoresRepository $prestadoresRepo)
    {
        $this->prestadoresRepository = $prestadoresRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/prestadores",
     *      summary="Get a listing of the Prestadores.",
     *      tags={"Prestadores"},
     *      description="Get all Prestadores",
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
     *                  @SWG\Items(ref="#/definitions/Prestadores")
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
        $this->prestadoresRepository->pushCriteria(new RequestCriteria($request));
        $this->prestadoresRepository->pushCriteria(new LimitOffsetCriteria($request));
        $prestadores = $this->prestadoresRepository->all();

        return $this->sendResponse($prestadores->toArray(), 'Prestadores retrieved successfully');
    }

    /**
     * @param CreatePrestadoresAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/prestadores",
     *      summary="Store a newly created Prestadores in storage",
     *      tags={"Prestadores"},
     *      description="Store Prestadores",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Prestadores that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Prestadores")
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
     *                  ref="#/definitions/Prestadores"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePrestadoresAPIRequest $request)
    {
        $input = $request->all();

        $prestadores = $this->prestadoresRepository->create($input);

        return $this->sendResponse($prestadores->toArray(), 'Prestadores saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/prestadores/{id}",
     *      summary="Display the specified Prestadores",
     *      tags={"Prestadores"},
     *      description="Get Prestadores",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Prestadores",
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
     *                  ref="#/definitions/Prestadores"
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
        /** @var Prestadores $prestadores */
        $prestadores = $this->prestadoresRepository->findWithoutFail($id);

        if (empty($prestadores)) {
            return $this->sendError('Prestadores not found');
        }

        return $this->sendResponse($prestadores->toArray(), 'Prestadores retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePrestadoresAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/prestadores/{id}",
     *      summary="Update the specified Prestadores in storage",
     *      tags={"Prestadores"},
     *      description="Update Prestadores",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Prestadores",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Prestadores that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Prestadores")
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
     *                  ref="#/definitions/Prestadores"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePrestadoresAPIRequest $request)
    {
        $input = $request->all();

        /** @var Prestadores $prestadores */
        $prestadores = $this->prestadoresRepository->findWithoutFail($id);

        if (empty($prestadores)) {
            return $this->sendError('Prestadores not found');
        }

        $prestadores = $this->prestadoresRepository->update($input, $id);

        return $this->sendResponse($prestadores->toArray(), 'Prestadores updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/prestadores/{id}",
     *      summary="Remove the specified Prestadores from storage",
     *      tags={"Prestadores"},
     *      description="Delete Prestadores",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Prestadores",
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
        /** @var Prestadores $prestadores */
        $prestadores = $this->prestadoresRepository->findWithoutFail($id);

        if (empty($prestadores)) {
            return $this->sendError('Prestadores not found');
        }

        $prestadores->delete();

        return $this->sendResponse($id, 'Prestadores deleted successfully');
    }
}