<?php


namespace App\Helpers\API\Superlogica\V2;


use Throwable;

class CreditCardRequiredException extends \Exception
{
    public function __construct($message = "A credit card is required for register this client.", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}