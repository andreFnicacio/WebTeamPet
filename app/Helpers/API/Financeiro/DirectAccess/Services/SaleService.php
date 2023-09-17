<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 23/04/2021
 * Time: 11:19
 */

namespace App\Helpers\API\Financeiro\DirectAccess\Services;


use App\Helpers\API\Financeiro\DirectAccess\Models\Sale;
use Carbon\Carbon;

class SaleService
{
    public function retroactive()
    {
        $records = [
//            [
//                'refcode' => '4194',
//                'value' => '500.00',
//            ],
//            [
//                'refcode' => '1020757053',
//                'value' => '450.00'
//            ],
//            [
//                'refcode' => '1020757053',
//                'value' => '200.00'
//            ],
//            [
//                'refcode' => '1020757053',
//                'value' => '24.00'
//            ]
        ];

        foreach($records as $record) {
            $sale = new Sale();
            $customer = CustomerService::getByRefcode($record['refcode']);
            $sale->picpay($customer->id, $record['value'], Carbon::now()->format('m/Y'), "Inserção manual de pagamento recebido via Picpay.", ["manual", "retroativo"]);
            $sale->save();
        }
    }
}