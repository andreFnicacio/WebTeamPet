<?php

namespace App\Http\Controllers\API;

use App\Helpers\API\LifepetIntegration\Domains\Pet\Pet;
use App\Helpers\API\LifepetIntegration\Repositories\CustomerRepository;
use App\Helpers\API\LifepetIntegration\Repositories\PetPlanRepository;
use App\Helpers\API\LifepetIntegration\Repositories\PetRepository;
use App\Http\Requests\API\CreatePetsAPIRequest;
use App\Http\Requests\API\UpdatePetsAPIRequest;
use App\Models\Pets;
use App\Models\PetsPlanos;
use App\Repositories\PetsRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PetsController
 * @package App\Http\Controllers\API
 */

class PetsAPIController extends AppBaseController
{
    /** @var  PetsRepository */
    private $petsRepository;

    /** @var PetPlanRepository */
    private $petPlanRepository;

    /** @var PetRepository */
    private $petRepository;

    /** @var CustomerRepository */
    private $customerRepository;

    public function __construct(
        PetsRepository $petsRepo,
        PetRepository $petRepository,
        PetPlanRepository $petPlanRepository,
        CustomerRepository $customerRepository
    ) {
        $this->petsRepository = $petsRepo;
        $this->petRepository = $petRepository;
        $this->petPlanRepository = $petPlanRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pets",
     *      summary="Get a listing of the Pets.",
     *      tags={"Pets"},
     *      description="Get all Pets",
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
     *                  @SWG\Items(ref="#/definitions/Pets")
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
        $this->petsRepository->pushCriteria(new RequestCriteria($request));
        $this->petsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pets = $this->petsRepository->all();

        return $this->sendResponse($pets->toArray(), 'Pets retrieved successfully');
    }

    /**
     * @param CreatePetsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pets",
     *      summary="Store a newly created Pets in storage",
     *      tags={"Pets"},
     *      description="Store Pets",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Pets that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Pets")
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
     *                  ref="#/definitions/Pets"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePetsAPIRequest $request)
    {
        $input = $request->all();

        $planId = $input['id_plano'];
        $paidPrice = $input['paid_price'];
        $hiredAt = $input['hired_at'];

        $petObj = new Pet();
        $petObj->populate([
            'name' => $input['nome_pet'],
            'species' => $input['tipo'],
            'breed_id' => (int) $input['id_raca'],
            'sex' => $input['sexo'],
            'birthdate' => Carbon::now()->format('Y-m-d'),
            'customer_id' => $input['id_cliente'],
            'active' => (int) $paidPrice == 0,
            'contains_pre_existing_disease' => false,
            'exam_last_12_months' => false,
            'familiar' => false,
            'participative' => 1,
            'payment_readjustment_month' => Carbon::createFromFormat('Y-m-d', $hiredAt)->format('n'),
            'obs' => 'Pet cadastrado automaticamente via integração e-commerce'
        ]);

        $petId = $this->petRepository->save($petObj);

        $petObj->setId($petId);

        $petObj->plan->populate([
            'pet_id' => (int) $petId,
            'plan_id' => (int) $planId,
            'payment_value' => $paidPrice,
            'date_init_contract' => $hiredAt,
            'status' => PetsPlanos::STATUS_PRIMEIRO_PLANO,
            'participative' => 1
        ]);

        PetsPlanos::unsetEventDispatcher();
        $petPlanId = $this->petPlanRepository->save($petObj->plan, PetsPlanos::TRANSICAO__NOVA_COMPRA);

        $petObj->setPetPlanId($petPlanId);
        $petObj->setMicrochipNumber('PT'.$petId);

        $this->petRepository->save($petObj);

        return $this->sendResponse($request->toArray(), 'Pet saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pets/{id}",
     *      summary="Display the specified Pets",
     *      tags={"Pets"},
     *      description="Get Pets",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Pets",
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
     *                  ref="#/definitions/Pets"
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
        /** @var Pets $pets */
        $pets = $this->petsRepository->findWithoutFail($id);

        if (empty($pets)) {
            return $this->sendError('Pets not found');
        }

        return $this->sendResponse($pets->toArray(), 'Pets retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePetsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pets/{id}",
     *      summary="Update the specified Pets in storage",
     *      tags={"Pets"},
     *      description="Update Pets",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Pets",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Pets that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Pets")
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
     *                  ref="#/definitions/Pets"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePetsAPIRequest $request)
    {
        $input = $request->all();

        /** @var Pets $pets */
        $pets = $this->petsRepository->findWithoutFail($id);

        if (empty($pets)) {
            return $this->sendError('Pets not found');
        }

        $pets = $this->petsRepository->update($input, $id);

        return $this->sendResponse($pets->toArray(), 'Pets updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pets/{id}",
     *      summary="Remove the specified Pets from storage",
     *      tags={"Pets"},
     *      description="Delete Pets",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Pets",
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
        /** @var Pets $pets */
        $pets = $this->petsRepository->findWithoutFail($id);

        if (empty($pets)) {
            return $this->sendError('Pets not found');
        }

        $pets->delete();

        return $this->sendResponse($id, 'Pets deleted successfully');
    }
}