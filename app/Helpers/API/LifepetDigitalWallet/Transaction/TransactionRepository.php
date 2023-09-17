<?php
/**
 * Created by VS Code.
 * User: Eric Moraes
 * Date: 07/08/20
 * Time: 18:15
 */

namespace App\Helpers\API\LifepetDigitalWallet\Transaction;

use App\Helpers\API\LifepetDigitalWallet\Core\Interfaces\RepositoryInterface;

use Throwable;

class TransactionRepository implements RepositoryInterface {

    /**
     * ORM Model da transação
     *
     * @var Model
     */
    private $model;

    public function __construct() {
        $this->model = new CarteiraDigitalTransacao();
    }

    public function getTotalByClient($id) {

    }
}