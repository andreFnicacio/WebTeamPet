<?php

namespace App\Helpers\API\LifepetIntegration\Persistences\Finance;

class FinanceException extends \Exception {
    private $prefixMessage = 'Sistema financeiro:';
    public function __construct($message, $code = 0, Throwable $previous = null){
        parent::__construct($this->prefixMessage . ' ' . $message, $code, $previous);
    }
}