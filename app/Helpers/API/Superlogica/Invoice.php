<?php

namespace App\Helpers\API\Superlogica;

use Carbon\Carbon;

class Invoice {

	private static $urls = [
		"cobrancas" => "https://api.superlogica.net/v2/financeiro/cobranca",
        "receita_eventual" => "https://api.superlogica.net/v2/financeiro/cobrancaitens"
	];

	const ALL = "todos";
	const OPEN = "pendentes";
	const PAID = "liquidadas";
	const CANCELED = "canceladas";

	public function get($id) {
        $curl = new Curl;
        $uri = self::$urls["cobrancas"] . Curl::params([
            "id" => $id
        ], Curl::QUERY);
        $curl->getDefaults($uri);
        $response = $curl->execute();
        $curl->close();
        return $response;
    }

    public function getByClientId($clientId, $status = self::ALL) {
        $curl = new Curl;
        $uri = self::$urls["cobrancas"] . Curl::params([
            "todasDoClienteComIdentificador" => $clientId,
            "status" => $status,
            "forcestatus" => $status
        ], Curl::QUERY);
        $curl->getDefaults($uri);
        $response = $curl->execute();
        $curl->close();
        return $response;
    }

    public function createEventual(\App\Models\Clientes $cliente, $valor, $competencia, $complemento)
    {
        $curl = new Curl;
        $uri = self::$urls["receita_eventual"];
        $fields = [
            "ID_SACADO_SAC" => $cliente->id_externo,
            "FL_PERIODICIDADE_MENS" => -1,
            "ST_VALOR_MENS" => $valor,
            'ST_MESANO_COMP' => Carbon::createFromFormat('Y-m', $competencia)->format('m/d/Y'),
            'ID_PRODUTO_PRD' => 9,
            'ST_COMPLEMENTO_MENS' => $complemento
        ];
        $curl->postDefaults($uri);
        $curl->postFields($fields);
        $response = $curl->execute();

        return $response;
    }

    public function charge(\App\Models\Clientes $cliente, $valor, $competencia, $complemento, $idProduto = 9)
    {
        if(!$competencia) {
            $competencia = (new Carbon())->format('Y-m');
        }
        $vencimento = Carbon::createFromFormat('Y-m', $competencia)->format('m/d/Y');
        $idCliente = $cliente->id_externo;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.superlogica.net/v2/financeiro/cobranca");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_POST, TRUE);

        curl_setopt($ch, CURLOPT_POSTFIELDS, "{
          \"ID_SACADO_SAC\": \"{$idCliente}\",
          \"COMPO_RECEBIMENTO\": [
            {
              \"ID_PRODUTO_PRD\": {$idProduto},
              \"NM_QUANTIDADE_COMP\": 1,
              \"VL_UNITARIO_PRD\": {$valor}
            }
          ],
          \"VL_EMITIDO_RECB\": \"{$valor}\",
          \"DT_VENCIMENTO_RECB\": \"{$vencimento}\",
          \"ID_FORMAPAGAMENTO_RECB\": \"3\",
          \"ST_OBSERVACAOINTERNA_RECB\": \"{$complemento}\",
          \"ST_OBSERVACAOEXTERNA_RECB\": \"{$complemento}\"
        }");

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "app_token: EAvu2fdfHEp0",
            "access_token: WgFn6Erz9svM"
        ));

        $response = curl_exec($ch);
        if($response) {
            $response = json_decode($response);
        }
        curl_close($ch);

        return $response;
    }
}