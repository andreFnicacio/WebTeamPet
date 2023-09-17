<?php


namespace App\Helpers\API\Superlogica\V2;


use App\Helpers\API\Superlogica\V2\Domain\Models\CreditCard;
use App\Helpers\API\Superlogica\V2\Exceptions\IdDidNotMatchAnyCustomer;

class PaymentMethod
{
    const ENDPOINT = 'financeiro/clientes/formadepagamento';

    private $client;

    public function __construct()
    {
        $this->client = Client::getInstance();
    }

    /**
     * @throws IdDidNotMatchAnyCustomer
     * @throws Exceptions\InvalidCallException
     */
    public function addCard($customerId, CreditCard $creditCard): bool
    {
        //Verificar se o sacado existe.
        $customerService = new Customer();
        $customer = $customerService->getById($customerId);
        if(!$customer) {
            throw new IdDidNotMatchAnyCustomer($customerId);
        }
        try {
            $response = $this->client->put(self::ENDPOINT, [
                'form_params' => [
                    'ID_SACADO_SAC' => $customerId,
                    'FL_PAGAMENTOPREF_SAC' => 3,
                    'ST_CARTAOBANDEIRA_SAC' => $creditCard->brand,
                    'ST_CARTAO_SAC' => $creditCard->getCardNumber(),
                    'ST_MESVALIDADE_SAC' => $creditCard->validMonth,
                    'ST_ANOVALIDADE_SAC' => $creditCard->validYear,
                    'ST_NOMECARTAO_SAC' => $creditCard->holder,
                    'ST_SEGURANCACARTAO_SAC' => $creditCard->cvv
                ]
            ]);
        } catch (\Exception $e) {
            //Tratar exceções e retornar erro específico para tratamento.
            throw $e;
        }

        return true;
    }
}