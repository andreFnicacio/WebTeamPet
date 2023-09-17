<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 22/04/2021
 * Time: 12:13
 */

namespace App\Helpers\API\Financeiro\DirectAccess\Services;


use App\Helpers\API\Financeiro\DirectAccess\Models\Customer;
use App\Http\Util\LogEvent;
use App\Http\Util\Logger;
use App\Http\Util\LogPriority;
use App\Models\Clientes;

class CustomerService
{
    public static function getByRefcode($ref_code): Customer
    {
        return Customer::refCode($ref_code)->first();
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getById($id)
    {
        return Customer::find($id);
    }

    /**
     * Atua como um dicionário para as formas de pagamento do ERP com o SF
     * @param Clientes $cliente
     * @return string
     */
    public static function getPaymentType(Clientes $cliente)
    {
        switch ($cliente->forma_pagamento){
            case 'boleto':
                return 'boleto';
            case 'cartao':
                return 'creditcard';
            default:
                return 'boleto';
        }
    }

    /**
     * @param Clientes $cliente
     *
     * Procedimento para sincronizar alteração de nomes com o Sistema Financeiro.
     */
    public function syncNames(Clientes $cliente): void
    {
        $customer = self::getByRefcode($cliente->id_externo);
        if($customer) {
            if($customer->cpf_cnpj === $cliente->cpf) {

                $nomeCliente = strtoupper($cliente->nome_cliente);
                if(strtoupper($customer->name) !== $nomeCliente) {
                    $update = json_encode([
                        'name' => "$customer->name -> $nomeCliente"
                    ]);
                    $message = "O nome do cliente foi sincronizado com o sistema financeiro. $update";
                    $customer->name = $nomeCliente;
                    $customer->update();

                    Logger::log(LogEvent::NOTICE, 'CLIENTES', LogPriority::MEDIUM, $message, 1, 'clientes', $cliente->id);
                }
            }
        }
    }
}