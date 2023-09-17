<?php
/**
 * Created by VS Code.
 * User: Eric Moraes
 * Date: 07/08/20
 * Time: 17:24
 */


namespace App\Helpers\API\LifepetDigitalWallet\Transaction\Reason;

/**
 * Classe que persiste os dados do motivo da transação
*/

class Reason {
    
    /**
     * id do motivo da transação
     *
     * @var int
     */
    private $id;

    /**
     * motivo da transação (nome, descrição, etc)
     *
     * @var string
     */
    private $name;

    


    /**
     * Get id do motivo da transação
     *
     * @return  int
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id do motivo da transação
     *
     * @param  int  $id  id do motivo da transação
     *
     * @return  self
     */ 
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get motivo da transação (nome, descrição, etc)
     *
     * @return  string
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set motivo da transação (nome, descrição, etc)
     *
     * @param  string  $name  motivo da transação (nome, descrição, etc)
     *
     * @return  self
     */ 
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

     /**
     * populate
     * Método que alimenta o objeto de acordo com o array passado
     * @param array $data
     * @return void
     */
    public function populate(string $name) : void
    {
        $this->validate($data);
        
        if(isset($data['id'])) {
            $this->setId($data['client_id']);
        }

        $this->setName($data['name']);
    }

    /**
     * validate
     * Método que valida os valores passados para o objeto e verifica
     * quais são obrigatórios
     * @param array $data
     * @return void
     */
    private function validate(array $data): void {
        
        if(!isset($data['name'])) {
            throw new TransactionException('É necessário que haja um nome do motivo');
        }
    }
}