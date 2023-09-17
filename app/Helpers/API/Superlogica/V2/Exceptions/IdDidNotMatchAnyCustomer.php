<?php


namespace App\Helpers\API\Superlogica\V2\Exceptions;


class IdDidNotMatchAnyCustomer extends \Exception
{
    public function __construct($id, $message = "We can't find a customer record with the given id: ", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message . $id, $code, $previous);
    }
}