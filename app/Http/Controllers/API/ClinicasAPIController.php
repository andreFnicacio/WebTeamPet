<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateClinicasAPIRequest;
use App\Http\Requests\API\UpdateClinicasAPIRequest;
use App\Repositories\ClinicasRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Modules\Clinics\Entities\Clinicas;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ClinicasController
 * @package App\Http\Controllers\API
 */

class ClinicasAPIController extends AppBaseController
{
    /** @var  ClinicasRepository */
    private $clinicasRepository;

    public function __construct(ClinicasRepository $clinicasRepo)
    {
        $this->clinicasRepository = $clinicasRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/clinicas",
     *      summary="Get a listing of the Clinicas.",
     *      tags={"Clinicas"},
     *      description="Get all Clinicas",
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
     *                  @SWG\Items(ref="#/definitions/Clinicas")
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
        $this->clinicasRepository->pushCriteria(new RequestCriteria($request));
        $this->clinicasRepository->pushCriteria(new LimitOffsetCriteria($request));
        $clinicas = $this->clinicasRepository->all();

        return $this->sendResponse($clinicas->toArray(), 'Clinicas retrieved successfully');
    }

    /**
     * @param CreateClinicasAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/clinicas",
     *      summary="Store a newly created Clinicas in storage",
     *      tags={"Clinicas"},
     *      description="Store Clinicas",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Clinicas that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Clinicas")
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
     *                  ref="#/definitions/Clinicas"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateClinicasAPIRequest $request)
    {
        $input = $request->all();

        $clinicas = $this->clinicasRepository->create($input);

        return $this->sendResponse($clinicas->toArray(), 'Clinicas saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/clinicas/{id}",
     *      summary="Display the specified Clinicas",
     *      tags={"Clinicas"},
     *      description="Get Clinicas",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Clinicas",
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
     *                  ref="#/definitions/Clinicas"
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
        /** @var Clinicas $clinicas */
        $clinicas = $this->clinicasRepository->findWithoutFail($id);

        if (empty($clinicas)) {
            return $this->sendError('Clinicas not found');
        }

        return $this->sendResponse($clinicas->toArray(), 'Clinicas retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateClinicasAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/clinicas/{id}",
     *      summary="Update the specified Clinicas in storage",
     *      tags={"Clinicas"},
     *      description="Update Clinicas",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Clinicas",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Clinicas that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Clinicas")
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
     *                  ref="#/definitions/Clinicas"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateClinicasAPIRequest $request)
    {
        $input = $request->all();

        /** @var Clinicas $clinicas */
        $clinicas = $this->clinicasRepository->findWithoutFail($id);

        if (empty($clinicas)) {
            return $this->sendError('Clinicas not found');
        }

        $clinicas = $this->clinicasRepository->update($input, $id);

        return $this->sendResponse($clinicas->toArray(), 'Clinicas updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/clinicas/{id}",
     *      summary="Remove the specified Clinicas from storage",
     *      tags={"Clinicas"},
     *      description="Delete Clinicas",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Clinicas",
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
        /** @var Clinicas $clinicas */
        $clinicas = $this->clinicasRepository->findWithoutFail($id);

        if (empty($clinicas)) {
            return $this->sendError('Clinicas not found');
        }

        $clinicas->delete();

        return $this->sendResponse($id, 'Clinicas deleted successfully');
    }
}
