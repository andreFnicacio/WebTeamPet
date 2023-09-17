<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEspecialidadesAPIRequest;
use App\Http\Requests\API\UpdateEspecialidadesAPIRequest;
use App\Models\Especialidades;
use App\Repositories\EspecialidadesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class EspecialidadesController
 * @package App\Http\Controllers\API
 */

class EspecialidadesAPIController extends AppBaseController
{
    /** @var  EspecialidadesRepository */
    private $especialidadesRepository;

    public function __construct(EspecialidadesRepository $especialidadesRepo)
    {
        $this->especialidadesRepository = $especialidadesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/especialidades",
     *      summary="Get a listing of the Especialidades.",
     *      tags={"Especialidades"},
     *      description="Get all Especialidades",
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
     *                  @SWG\Items(ref="#/definitions/Especialidades")
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
        $this->especialidadesRepository->pushCriteria(new RequestCriteria($request));
        $this->especialidadesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $especialidades = $this->especialidadesRepository->all();

        return $this->sendResponse($especialidades->toArray(), 'Especialidades retrieved successfully');
    }

    /**
     * @param CreateEspecialidadesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/especialidades",
     *      summary="Store a newly created Especialidades in storage",
     *      tags={"Especialidades"},
     *      description="Store Especialidades",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Especialidades that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Especialidades")
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
     *                  ref="#/definitions/Especialidades"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateEspecialidadesAPIRequest $request)
    {
        $input = $request->all();

        $especialidades = $this->especialidadesRepository->create($input);

        return $this->sendResponse($especialidades->toArray(), 'Especialidades saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/especialidades/{id}",
     *      summary="Display the specified Especialidades",
     *      tags={"Especialidades"},
     *      description="Get Especialidades",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Especialidades",
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
     *                  ref="#/definitions/Especialidades"
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
        /** @var Especialidades $especialidades */
        $especialidades = $this->especialidadesRepository->findWithoutFail($id);

        if (empty($especialidades)) {
            return $this->sendError('Especialidades not found');
        }

        return $this->sendResponse($especialidades->toArray(), 'Especialidades retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateEspecialidadesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/especialidades/{id}",
     *      summary="Update the specified Especialidades in storage",
     *      tags={"Especialidades"},
     *      description="Update Especialidades",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Especialidades",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Especialidades that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Especialidades")
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
     *                  ref="#/definitions/Especialidades"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateEspecialidadesAPIRequest $request)
    {
        $input = $request->all();

        /** @var Especialidades $especialidades */
        $especialidades = $this->especialidadesRepository->findWithoutFail($id);

        if (empty($especialidades)) {
            return $this->sendError('Especialidades not found');
        }

        $especialidades = $this->especialidadesRepository->update($input, $id);

        return $this->sendResponse($especialidades->toArray(), 'Especialidades updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/especialidades/{id}",
     *      summary="Remove the specified Especialidades from storage",
     *      tags={"Especialidades"},
     *      description="Delete Especialidades",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Especialidades",
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
        /** @var Especialidades $especialidades */
        $especialidades = $this->especialidadesRepository->findWithoutFail($id);

        if (empty($especialidades)) {
            return $this->sendError('Especialidades not found');
        }

        $especialidades->delete();

        return $this->sendResponse($id, 'Especialidades deleted successfully');
    }
}