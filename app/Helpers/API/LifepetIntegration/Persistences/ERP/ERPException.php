<?php

namespace App\Helpers\API\LifepetIntegration\Persistences\ERP;

class ERPException extends \Exception {
    private $prefixMessage = 'ERP:';
    public function __construct($message, $code = 0, Throwable $previous = null){
        parent::__construct($this->prefixMessage . ' ' . $message, $code, $previous);
    }
}