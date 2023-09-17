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
class Transaction {

    /**
     * O id da transação
     *
     * @var int
     */
    private $id;
    
    /**
     * O cliente vinculado à transação (Client Obj)
     *
     * @var Client
     */
    private $client;

    /**
     * O motivo da transação (Client obj
     *
     * @var TransactionReason
     */
    private $transactionReason;

    /**
     * O valor da transação
     *
     * @var float
     */
    private $value;

    /**
     * O tipo da transação (1 = Débito, 2 = Crédito)
     *
     * @var int
     */
    private $type;

    /**
     * A descrição da transação
     *
     * @var string
     */
    private $description;

    /**
     * (opcional) Os comentários e observações do objeto
     *
     * @var string
     */
    private $comments;


    /**
     * __construct
     *
     * @param array $data
     */
    public function __construct(array $data) {
        $this->populate($data);
    }

    /**
     * Get o id da transação
     *
     * @return  int
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set o id da transação
     *
     * @param  int  $id  O id da transação
     *
     * @return  self
     */ 
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get - Cliente vinculado à transação
     *
     * @return  Client
     */ 
    public function getClient()
    {
        return $this->clientId;
    }

    /**
     * Set - Cliente vinculado à transação (Client Obj)
     *
     * @param  Client  $client  O cliente vinculado à transação
     *
     * @return  self
     */ 
    public function setClient(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get - Motivo da transação (TransactionReason Obj)
     *
     * @return  TransactionReason
     */ 
    public function getTransactionReason()
    {
        return $this->transactionReason;
    }

    /**
     * Set - motivo da transação
     *
     * @param  TransactionReason  $transactionReason  O motivo da transação
     *
     * @return  self
     */ 
    public function setTransactionReasonId(TransactionReason $transactionReason)
    {
        $this->transactionReason = $transactionReason;

        return $this;
    }

    /**
     * Get - valor da transação
     *
     * @return  float
     */ 
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set - valor da transação
     *
     * @param  float  $value  O valor da transação
     *
     * @return  self
     */ 
    public function setValue(float $value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get - tipo da transação (1 = Débito, 2 = Crédito)
     *
     * @return  int
     */ 
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set - tipo da transação (1 = Débito, 2 = Crédito)
     *
     * @param  int  $type  O tipo da transação (1 = Débito, 2 = Crédito)
     *
     * @return  self
     */ 
    public function setType(int $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get - descrição da transação
     *
     * @return  string
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set - descrição da transação
     *
     * @param  string  $description  A descrição da transação
     *
     * @return  self
     */ 
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get - (opcional) Os comentários e observações do objeto
     *
     * @return  string
     */ 
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set - (opcional) Os comentários e observações do objeto
     *
     * @param  string  $comments  (opcional) Os comentários e observações do objeto
     *
     * @return  self
     */ 
    public function setComments(string $comments)
    {
        $this->comments = $comments;

        return $this;
    }


    
    /**
     * populate
     * Método que alimenta o objeto de acordo com o array passado
     * @param array $data
     * @return void
     */
    public function populate(array $data) : void {
        $this->validate($data);
        
        if(isset($data['id'])) {
            $this->setId($data['client_id']);
        }
        
        $this->setClientId($data['client_id']);
        $this->setTransactionReasonId($data['transaction_reason_id']);
        $this->setValue($data['value']);
        $this->setType($data['type']);
        $this->setDescription($data['description']);

        if(isset($data['comments'])) {
            $this->setComments($data['comments']);
        }
       
    }
    
    /**
     * validate
     * Método que valida os valores passados para o objeto e verifica
     * quais são obrigatórios
     * @param array $data
     * @return void
     */
    private function validate(array $data): void {
        if(!isset($data['client_id'])) {
            throw new TransactionException('É necessário que haja um cliente para vincular');
        }

        if(!isset($data['transaction_reason_id'])) {
            throw new TransactionException('É necessário que haja um motivo para a transação');
        }

        if(!isset($data['value'])) {
            throw new TransactionException('É necessário que haja um valor para a transação');
        }

        if(!isset($data['type'])) {
            throw new TransactionException('É necessário que o tipo da transação seja passado (1 = débito; 2 = crédito)');
        }

        if(!isset($data['description'])) {
            throw new TransactionException('É necessário que haja uma descrição para a transação');
        }
    }
}