<?php
/**
 * Created by VS Code.
 * User: Eric Moraes
 * Date: 07/08/20
 * Time: 16:33
 */

namespace App\Helpers\API\LifepetDigitalWallet\Transaction;

use Throwable;

class TransactionException extends \Exception {

    public function __construct(string $msg) {
        parent::__construct($msg);
    }

}