<?php


namespace App\Helpers\API\Superlogica\V2\Exceptions;


use Throwable;

class InvalidCallException extends \Exception
{
    public $error;

    public function __construct($errors)
    {
        $this->error = $errors;
        $message = "The Superlógica server returned a bad response code.";
        $message .= " " . $errors;
        $code = 0;
        $previous = null;
        parent::__construct($message, $code, $previous);
    }
}