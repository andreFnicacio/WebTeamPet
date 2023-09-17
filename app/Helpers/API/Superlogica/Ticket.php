<?php

namespace App\Helpers\API\Superlogica;

class Ticket {

	private static $urls = [
		"cupom" => "http://api.superlogica.net/v2/areadocliente/publico/cupom",
	];

	public static function check($code, $id_plano = null) {
		$params = [
			"cupom" => $code,
			"id_plano" => $id_plano
		];

		$curl = new Curl;
		$url = self::$urls["cupom"] . Curl::params($params, Curl::QUERY);
		$curl->getDefaults($url);
		
		$response = $curl->execute();
		$curl->close();
		return $response;
	}
        
    public static function apply($code, $plans) {
        $ticket = self::check($code);
        if($ticket->status == "200") {
            if($ticket->data->fl_percentual_cup) {
                $tipo = "percentual";
                $valor = $ticket->data->vl_desconto_cup;
                $valor = floatval($valor)/100.0;
            } else {
                $tipo = "fixo";
                $valor = $ticket->data->vl_desconto_cup;
            }
            
            $plans = array_map(function($p) use ($tipo, $valor) {
                if($tipo == "percentual") {
                    $p->mensalidade_cupom = $p->mensalidade * (1-$valor);
                    $p->adesao_cupom = $p->adesao * (1-$valor);
                } else {
                    if($p->adesao != 0 && $p->mensalidade != 0) {
                        $p->adesao_cupom = $p->adesao - ($valor/2);
                        $p->mensalidade_cupom = $p->mensalidade - ($valor/2);
                    } else if ($p->adesao != 0) {
                        $p->adesao_cupom = $p->adesao - $valor;
                    } else if ($p->mensalidade != 0) {
                        $p->mensalidade_cupom = $p->mensalidade - $valor;
                    }
                }
                return $p;
            }, $plans);
        }
        return $plans;
    }
}