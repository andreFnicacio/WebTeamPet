<?php

namespace Modules\Vindi\Services\Resources;

use App\Models\Clientes;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Modules\Vindi\DTO\Customer\CustomerDTO;
use Modules\Vindi\Mappers\AddressDataMapper;
use Modules\Vindi\Mappers\CustomerDataMapper;
use Modules\Vindi\Mappers\PhoneDataMapper;
use Vindi\Customer;
use Vindi\Exceptions\RateLimitException;
use Vindi\Exceptions\RequestException;

class CustomerResource extends AbstractResource
{
    public function __construct(Customer $service)
    {
        parent::__construct($service);
    }

    /**
     * @throws GuzzleException
     * @throws RateLimitException
     * @throws RequestException
     */
    public function createCustomer(array $request): CustomerDTO
    {
        $response = $this->service->create($request);
        return new CustomerDTO($this->toArray($response));
    }

    public function get($id)
    {
        try {
            $data = parent::get($id);
        } catch (\Exception $exception){
            return null;
        }
        if ($data) {

            $data = $this->toArray($data);

            if (is_array($data['phones'])) {
                $data['phones'] = null;
            }

            return new CustomerDTO($this->toArray($data));
        }

        return null;
    }

    public function getByCode($code): ?CustomerDTO
    {
        $data = parent::getByCode($code);
        if ($data) {
            return new CustomerDTO(current($data));
        }

        return null;
    }

    public function put(int $id, array $data)
    {
        $this->service->update($id, $data);
    }

    public function find(string $query): ?CustomerDTO
    {
        $customer = $this->service->all(['query' => $query]);

        if ($customer) {
            return new CustomerDTO($this->toArray(current($customer)));
        }

        return null;
    }

    public function findAll(string $query): array
    {
        return $this->service->all(['query' => $query]);
    }

    /**
     * @throws RequestException
     * @throws GuzzleException
     * @throws RateLimitException
     */
    public function findOrCreate(array $request): CustomerDTO
    {
        $customerDTO = $this->find("registry_code=" . $request['registry_code']);

        if (empty($customerDTO) || is_null($customerDTO)) {

            $customerDTO = $this->getByCode($request['code']);

            if (empty($customerDTO) || is_null($customerDTO)) {

                Log::debug("Financial customer NOT found. Creating...");

                $customerDTO = $this->createCustomer($request);

                /** @var Clientes $customer */
                $customer = Clientes::where('id', $customerDTO->code);
                if ($customer) {
                    $customer->update(['financial_id' => $customerDTO->id]);
                }
            }
        }

        Log::debug("Financial customer created: " . json_encode($customerDTO->toArray()));

        return $customerDTO;
    }

    public function updateCustomer(CustomerDTO $customerDTO)
    {
        $customer = $this->service->all([
            'query' => "email=" . $customerDTO->email. ' ' . "registry_code=" . $customerDTO->registry_code
        ]);

        $this->service->update(current($customer)->id, $customerDTO->toArray());
    }

    /**
     * @throws GuzzleException
     * @throws RateLimitException
     * @throws RequestException
     */
    public function delete($customerId)
    {
        $this->service->delete($customerId);
    }

    public function map(array $customerData): array
    {
        $customerMapper = new CustomerDataMapper();
        $mappedCustomer = $customerMapper->toRequest($customerData);

        $addressMapper = new AddressDataMapper();
        $mappedAddress = $addressMapper->toRequest($customerData);

        $phoneMapper = new PhoneDataMapper();
        $mappedPhones = $phoneMapper->toRequest($customerData);

        $mappedCustomer['address'] = $mappedAddress;
        $mappedCustomer['phones'] = $mappedPhones;

        $mappedCustomer['code'] = (string) $mappedCustomer['code'];
        return $mappedCustomer;
    }
}