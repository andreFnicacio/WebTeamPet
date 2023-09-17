<?php

namespace Modules\Vindi\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Vindi\DTO\Customer\CustomerAddressDTO;
use Modules\Vindi\DTO\Customer\CustomerDTO;
use Modules\Vindi\DTO\Customer\CustomerPhoneDTO;
use Modules\Vindi\DTO\Customer\CustomerPhoneDTOCollection;
use Modules\Vindi\Mappers\AddressDataMapper;
use Modules\Vindi\Mappers\CustomerDataMapper;
use Modules\Vindi\Mappers\PhoneDataMapper;
use Modules\Vindi\Services\VindiService;

class IndexController extends Controller
{
    private VindiService $financialService;

    public function __construct(VindiService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function index()
    {
        $customerAPIResponse = [
            'customer' => [
                "id" => 41761921,
                "name" => "Test1",
                "email" => "test1@test.com",
                "registry_code" => "96627505096",
                "address" => [
                    "street" => "Rua test",
                    "number" => "10",
                    "zipcode" => "12312-123",
                    "neighborhood" => "Praia de Itaparica",
                    "city" => "Vila Velha",
                    "state" => "ES"
                ],
                "phones" => [
                    0 => [
                        "phone_type" => "mobile",
                        "number" => "5561999044166"
                    ]
                ]
            ]
        ];

        $customerDataMapper = new CustomerDataMapper();
        $addressDataMapper = new AddressDataMapper();
        $phoneDataMapper = new PhoneDataMapper();

        $mappedCustomerResponse = $customerDataMapper->fromResponse($customerAPIResponse['customer']);
        $mappedCustomerAddress = $addressDataMapper->fromResponse($customerAPIResponse['customer']['address']);
        $mappedCustomerPhones = $phoneDataMapper->fromResponse($customerAPIResponse['customer']['phones']);

        $customer = $customerDataMapper->toRequest($mappedCustomerResponse);
        $customerAddress = $addressDataMapper->toRequest($mappedCustomerAddress);
        $customerPhone = $phoneDataMapper->toRequest($mappedCustomerPhones);

        $customer['address'] = $customerAddress;
        $customer['phones'] = [$customerPhone];

        $mappedCustomerResponse['endereco'] = $mappedCustomerAddress;
        $phoneCollection = new CustomerPhoneDTOCollection([$mappedCustomerPhones]);
        $mappedCustomerResponse['telefones'] = $phoneCollection;

        $customerDTO = new CustomerDTO($mappedCustomerResponse);

        dd($customerDTO, $customer);

    }

    public function customer()
    {
        $customerAPIResponse = $this->financialService->customer()->get(41761921);
        $customerAPIResponse = json_decode(json_encode($customerAPIResponse), true);

        $customerDataMapper = new CustomerDataMapper();
        $addressDataMapper = new AddressDataMapper();
        $phoneDataMapper = new PhoneDataMapper();

        $mappedCustomerResponse = $customerDataMapper->fromResponse($customerAPIResponse);
        $mappedCustomerAddress = $addressDataMapper->fromResponse($customerAPIResponse['address']);
        $mappedCustomerPhones = $phoneDataMapper->fromResponse($customerAPIResponse['phones']);

        $mappedCustomerResponse['endereco'] = $mappedCustomerAddress;
        $phoneCollection = new CustomerPhoneDTOCollection([$mappedCustomerPhones]);
        $mappedCustomerResponse['telefones'] = $phoneCollection;

        $customerDTO = new CustomerDTO($mappedCustomerResponse);

        dd($customerDTO);
    }

    public function createCustomer()
    {
        $data = [
            "id" => "1234",
            "nome_cliente" => "Test 11",
            "email" => "test11@test.com",
            "cpf" => "85713828085",
            "finance_id" => null,
            "data_nascimento" => "29/08/1988",
            "sexo" => "M",
            "celular" => "61999044166",
            "telefone" => "6123321312",
            "rua" => "Rua Test",
            "numero_endereco" => "10",
            "bairro" => "Praia de Itaparica",
            "cidade" => "Vila Velha",
            "estado" => "ES",
            "cep" => "12345-123"
        ];

        $customerMapper = new CustomerDataMapper();
        $mappedCustomer = $customerMapper->toRequest($data);

        $addressMapper = new AddressDataMapper();
        $mappedAddress = $addressMapper->toRequest($data);

        $phoneMapper = new PhoneDataMapper();
        $mappedPhones = $phoneMapper->toRequest($data);

        $mappedCustomer['address'] = $mappedAddress;
        $mappedCustomer['phones'] = $mappedPhones;

        $dto = new CustomerDTO($mappedCustomer);

        $this->financialService->customer()->findOrCreate($dto);
    }
}