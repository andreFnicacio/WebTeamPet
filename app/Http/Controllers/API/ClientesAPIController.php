<?php

namespace App\Http\Controllers\API;

use App\Helpers\API\LifepetIntegration\Repositories\CustomerRepository;
use App\Http\Requests\API\CreateClientesAPIRequest;
use App\Http\Requests\API\UpdateClientesAPIRequest;
use App\Models\Clientes;
use App\Repositories\ClientesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ClientesController
 * @package App\Http\Controllers\API
 */

class ClientesAPIController extends AppBaseController
{
    /** @var  ClientesRepository */
    private $clientesRepository;

    /** @var CustomerRepository */
    private $customerRepository;

    public function __construct(
        ClientesRepository $clientesRepo,
        CustomerRepository $customerRepository
    ) {
        $this->clientesRepository = $clientesRepo;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/clientes",
     *      summary="Get a listing of the Clientes.",
     *      tags={"Clientes"},
     *      description="Get all Clientes",
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
     *                  @SWG\Items(ref="#/definitions/Clientes")
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
        $this->clientesRepository->pushCriteria(new RequestCriteria($request));
        $this->clientesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $clientes = $this->clientesRepository->all();

        return $this->sendResponse($clientes->toArray(), 'Clientes retrieved successfully');
    }

    /**
     * @param CreateClientesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/clientes",
     *      summary="Store a newly created Clientes in storage",
     *      tags={"Clientes"},
     *      description="Store Clientes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Clientes that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Clientes")
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
     *                  ref="#/definitions/Clientes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateClientesAPIRequest $request)
    {
        $input = $request->all();

        if (!isset($input['cpf'])) {
            return $this->sendError('Email is a required field');
        }

        $client = $this->clientesRepository->findByField('cpf', $input['cpf']);

        if ($client->count()) {
            return $this->sendResponse($client->toArray(), 'Client Already Exist');
        }

        $client = $this->clientesRepository->create($input);

        $this->customerRepository->addNote(
            $client->id,
            'Cliente, pet(s) e plano(s) cadastrados automaticamente via integração e-commerce.'
        );

        return $this->sendResponse($client->toArray(), 'Client saved successfully', 201);
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/clientes/{id}",
     *      summary="Display the specified Clientes",
     *      tags={"Clientes"},
     *      description="Get Clientes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Clientes",
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
     *                  ref="#/definitions/Clientes"
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
        /** @var Clientes $clientes */
        $clientes = $this->clientesRepository->findWithoutFail($id);

        if (empty($clientes)) {
            return $this->sendError('Clientes not found');
        }

        return $this->sendResponse($clientes->toArray(), 'Clientes retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateClientesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/clientes/{id}",
     *      summary="Update the specified Clientes in storage",
     *      tags={"Clientes"},
     *      description="Update Clientes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Clientes",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Clientes that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Clientes")
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
     *                  ref="#/definitions/Clientes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateClientesAPIRequest $request)
    {
        $input = $request->all();

        /** @var Clientes $clientes */
        $clientes = $this->clientesRepository->findWithoutFail($id);

        if (empty($clientes)) {
            return $this->sendError('Clientes not found');
        }

        $clientes = $this->clientesRepository->update($input, $id);

        return $this->sendResponse($clientes->toArray(), 'Clientes updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/clientes/{id}",
     *      summary="Remove the specified Clientes from storage",
     *      tags={"Clientes"},
     *      description="Delete Clientes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Clientes",
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
        /** @var Clientes $clientes */
        $clientes = $this->clientesRepository->findWithoutFail($id);

        if (empty($clientes)) {
            return $this->sendError('Clientes not found');
        }

        $clientes->delete();

        return $this->sendResponse($id, 'Clientes deleted successfully');
    }
}
