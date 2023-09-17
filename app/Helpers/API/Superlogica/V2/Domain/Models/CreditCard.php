<?php


namespace App\Helpers\API\Superlogica\V2\Domain\Models;


use App\Helpers\API\Superlogica\V2\Transformers\Transformable;
use App\Helpers\Utils;

class CreditCard extends Transformable
{
    public $cardNumber;
    public $validMonth;
    public $validYear;
    public $cvv;
    public $holder;
    public $brand;

    /**
     * CreditCard constructor.
     * @param $cardNumber
     * @param $validMonth
     * @param $validYear
     * @param $cvv
     * @param $holder
     * @param $brand
     */
    public function __construct($cardNumber, $validMonth, $validYear, $cvv, $holder, $brand)
    {
        $this->cardNumber = $cardNumber;
        $this->validMonth = $validMonth;
        $this->validYear = $validYear;
        $this->cvv = $cvv;
        $this->holder = $holder;
        $this->brand = $brand;
    }

    public function getCardNumber()
    {
        return Utils::numberOnly($this->cardNumber);
    }

    public function getHashedCardNumber(): ?string
    {
        if($this->getCardNumber()) {
            return substr($this->getCardNumber(), 0, 4) . str_repeat('*', 12) . substr($this->getCardNumber(), -4);
        }

        return null;
    }
}