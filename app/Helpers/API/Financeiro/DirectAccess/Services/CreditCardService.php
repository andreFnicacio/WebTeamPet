<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 22/04/2021
 * Time: 11:17
 */

namespace App\Helpers\API\Financeiro\DirectAccess\Services;


use App\Helpers\API\Financeiro\DirectAccess\Models\CreditCard;
use App\Helpers\API\Financeiro\DirectAccess\Models\Customer;

class CreditCardService
{
    /**
     * @param Customer $customer
     * @param $creditcard_id
     * @throws \Exception
     */
    public static function setAsDefault(Customer $customer, $creditcard_id)
    {
        $creditCard = CreditCard::where('id', $creditcard_id)->ownedBy($customer->id)->first();
        if(!$creditCard) {
            throw new \Exception('Cartão não encontrado para o cliente informado.');
        }

        CreditCard::ownedBy($customer->id)->update([
            'default' => 'N'
        ]);

        CreditCard::where('id', $creditcard_id)->ownedBy($customer->id)->update([
            'default' => 'Y'
        ]);
    }

    public static function getDefaultCreditCard(Customer $customer)
    {
        $default = CreditCard::ownedBy($customer->id)->default()->first();
        if($default) {
            return $default;
        }

        $creditCard = CreditCard::ownedBy($customer->id)->orderBy('id', 'DESC')->first();
        if(!$creditCard) {
            throw new \Exception('Não há cartão principal para este cliente.');
        }
        $creditCard->default = 'Y';

        return $creditCard;
    }

    /**
     * @param Customer $customer
     * @return array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Relations\HasMany[]|\Illuminate\Support\Collection
     */
    public static function getCards(Customer $customer)
    {
        $cards = $customer->cards()->orderBy('id', 'DESC')->get();
        $default = $customer->cards()->default()->first();

        if(!$cards || count($cards) == 0) {
            return [];
        }

        if($default) {
            $cards = $cards->map(function($c) use ($default) {
                if($c->id !== $default->id) {
                    $c->default = 'N';
                }
                return $c;
            });

            return $cards;
        }

        $cards->first()->default = 'Y';

        return $cards;
    }

    /**
     * @param Customer $customer
     * @param $creditcard_id
     */
    public static function remove(Customer $customer, $creditcard_id)
    {
        $creditCard = CreditCard::where('id', $creditcard_id)->ownedBy($customer->id)->first();
        if(!$creditCard) {
            throw new \Exception('Cartão não encontrado para o cliente informado.');
        }

        $creditCard->delete();
    }
}