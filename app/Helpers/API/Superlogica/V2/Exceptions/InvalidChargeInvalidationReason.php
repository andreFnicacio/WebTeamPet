<?php


namespace App\Helpers\API\Superlogica\V2\Exceptions;


use Throwable;

class InvalidChargeInvalidationReason extends \Exception
{
    public $error;

    public function __construct($reason)
    {
        $message = "The reason ($reason) you choose is invalid or not registered. Try another.";
        $code = 0;
        $previous = null;
        parent::__construct($message, $code, $previous);
    }
}