<?php


namespace App\Helpers\API\Superlogica\V2;


use App\Models\Clientes;
use App\Models\Pets;

class Recurrence
{
    const ENDPOINT = '/v2/financeiro/recorrencias/recorrenciasdeplanos';

    private $client;

    public function __construct()
    {
        $this->client = Client::getInstance();
    }

    public function getPendingPaymentSignatureId(Pets $pet)
    {
        try {
            $response = $this->client->get(self::ENDPOINT, [
                'query' => [
                    'tipo' => 'contratospendentes',
                    'gridMensalidadesAgrupadasPorPlano' => true,
                    'semTrial' => true,
                    'CLIENTES' => [
                        $pet->cliente->id_superlogica
                    ],
                    'pagina' => 1,
                    'itensPorPagina' => 50
                ]
            ]);

            if($response) {
                foreach ($response as $r) {
                    if (strtoupper($r->st_identificador_plc) === strtoupper($pet->getIdentificadorPlano())) {
                        return $r->id_planocliente_plc;
                    }
                }
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getActiveSignatureId(Pets $pet)
    {
        try {
            $response = $this->client->get(self::ENDPOINT, [
                'query' => [
                    'tipo' => 'contratos',
                    'gridMensalidadesAgrupadasPorPlano' => true,
                    'semTrial' => true,
                    'CLIENTES' => [
                        $pet->cliente->id_superlogica
                    ],
                    'pagina' => 1,
                    'itensPorPagina' => 50
                ]
            ]);

            if($response) {
                foreach ($response as $r) {
                    if (strtoupper($r->st_identificador_plc) === strtoupper($pet->getIdentificadorPlano())) {
                        return $r->id_planocliente_plc;
                    }
                }
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function alreadyHasSignature(Pets $pet)
    {
        $activeSignature = $this->getActiveSignatureId($pet);
        if($activeSignature) {
            return $activeSignature;
        }

        $pendingPaymentSignature = $this->getPendingPaymentSignatureId($pet);
        if($pendingPaymentSignature) {
            return $pendingPaymentSignature;
        }

        return false;
    }
}