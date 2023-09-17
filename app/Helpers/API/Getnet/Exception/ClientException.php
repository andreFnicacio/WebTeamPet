<?php


namespace App\Helpers\API\Getnet\Exception;


use Throwable;

class ClientException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}