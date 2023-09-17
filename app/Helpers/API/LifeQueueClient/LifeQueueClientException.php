<?php
namespace App\Helpers\API\LifeQueueClient;

class LifeQueueClientException extends \Exception {

    /**
     * Construtor
     *
     * @param string $msg
     */
    public function __construct($msg) {
        parent::__construct($msg);
    }
}