<?php


namespace App\Helpers\API\Superlogica\V2;


use Throwable;

class OverdueCustomerFound extends \Exception
{
    public function __construct($message = "An overdue customer is trying to sign another plan.", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}