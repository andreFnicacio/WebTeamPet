<?php

namespace Modules\Subscriptions\Exceptions;

class FinancialCustomerException extends \Exception
{
    protected $message = "Falha na criação do usuário no serviço financeiro. Por favor, verifique os dados do cliente.";
}