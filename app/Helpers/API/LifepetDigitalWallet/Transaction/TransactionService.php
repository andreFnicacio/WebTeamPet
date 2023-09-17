<?php
/**
 * Created by VS Code.
 * User: Eric Moraes
 * Date: 07/08/20
 * Time: 16:21
 */


namespace App\Helpers\API\LifepetDigitalWallet\Transaction;

/**
 * Transaction
 * Classe que persiste os dados de UMA transação
 */
class TransactionService {

    /**
     * Repositório do serviço
     *
     * @var Repository
     */
    private $repository;

    public function __construct() {
        $this->repository = new TransactionRepository();
    }

    public function getTotalByClient($id) {
        $total = $this->repository->getTotalByClient($id);
    }


}