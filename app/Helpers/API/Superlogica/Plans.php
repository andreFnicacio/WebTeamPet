<?php

namespace App\Helpers\API\Superlogica;

class Plans {
	private static $plans = [];
	private static $urls = [
		"planos" => "https://lifepet.superlogica.net/financeiro/atual/planos/",
		"checkout" => "https://api.superlogica.net/v2/financeiro/checkout",
		"assinar" => "https://api.superlogica.net/v2/financeiro/assinaturas",
		"cobranca" => "https://api.superlogica.net/v2/financeiro/cobranca"
	];
	private static $hideInactive = true;

	/**
	 * Carrega os dados de planos para a memória
	 * @return void
	 */
	private function load() {
		$curl = new Curl;
		$curl->getDefaults(self::$urls["planos"]);
		
		$response = $curl->execute();
		$curl->close();
		if(self::$hideInactive) {
			$response = array_filter($response, function($grade) {
				$grade->resumo = array_filter($grade->resumo, function($plano) {
					return $plano->desativado != 1;
				});

				return $grade;
			});
		}
		self::$plans = $response;
	}

	/**
	 * Obtém todos os planos
	 * @return array
	 */
	public function all() {
		if(empty($this->plans)) {
			$this->load();
		}

		return self::$plans;
	}

	/**
	 * Filtra os planos pela grade dada.
	 * @param  int
	 * @return array
	 */
	public function allByGrade($id_grade) {
		$rawPlans = $this->all();

		$filtered = array_filter($rawPlans, function($p) use ($id_grade) {
			return $p->id_grade_gpl == $id_grade;
		});

		$mapped = array_map(function($p) {
			return $p->resumo;
		}, $filtered);

		return $mapped;
	}

	public function getById($id){
		$curl = new Curl;
		$query = self::$urls["planos"] . Curl::params(["id" => $id], Curl::QUERY);
		$curl->getDefaults($query);
		
		$response = $curl->execute();
		$curl->close();
		return $response;
	}

	/**
	 * Necessário adicionar o id_plano no array antes de enviar para
	 * o método.
	 * @param  [type] $info [description]
	 * @return [type]       [description]
	 */
	// public function signRegistering($info) {
	// 	$fields = [
	// 		"ST_NOME_SAC"=> $info["nome"],
 //            "ST_EMAIL_SAC"=> $info["email"],
 //            "ST_TELEFONE_SAC"=> $info["telefone_fixo"],
 //            "ST_CEP_SAC"=> $info["cep"],
 //            "ST_ENDERECO_SAC"=> $info["endereco"],
 //            "ST_NUMERO_SAC"=> $info["numero"],
 //            "ST_BAIRRO_SAC"=> $info["bairro"],
 //            "ST_CIDADE_SAC"=> $info["cidade"],
 //            "ST_ESTADO_SAC"=> $info["estado"],
 //            "FL_MESMOEND_SAC"=> "1",
 //            "senha"=> $info["cpf"],
 //            "senha_confirmacao"=> $info["cpf"],
 //            "FL_FORMAPAGAMENTO" => "3",
 //            "idplano"=> $info["id_plano"],
	// 	];
	// 	$curl = new Curl;
	// 	$curl->postDefaults(self::$urls["checkout"])
	// 		 ->postFields($fields);
		
	// 	$response = $curl->execute();
	// 	$curl->close();
	// 	return $response;
	// }
	// 
	public function sign($planos)
	{
        $curl = new Curl();
        $curl->postDefaults(self::$urls["assinar"]);
        $curl->option(CURLOPT_POSTFIELDS, http_build_query($planos));

        $response = $curl->execute();
        $curl->close();
        return $response;
	}

	public function getPlan($identificador, $idSacadoSac) {
		$curl = new Curl();
        $curl->getDefaults(self::$urls["assinar"] . "?identificadorContrato=".$identificador."&identificadorCliente=&ID_SACADO_SAC=".$idSacadoSac."&pagina=1&itensPorPagina=50");
        //$curl->option(CURLOPT_POSTFIELDS, http_build_query($planos));

        $response = $curl->execute();
        $curl->close();
        return $response;
	}

	
	   
	public function getCharge($identificador, $idSacadoSac) {
		$curl = new Curl();
        $curl->getDefaults(self::$urls["cobranca"] . "?CLIENTES[0]=".$idSacadoSac."&dtInicio&dtFim&semFiltroPorData&todasDoClienteComIdentificador&status&forcarstatus&pagina=1&itensPorPagina=50");
        //$curl->option(CURLOPT_POSTFIELDS, http_build_query($planos));

        $response = $curl->execute();
        $curl->close();
        return $response;
	}

    public static function http_build_for_curl( $arrays, &$new = array(), $prefix = null ) {

        if ( is_object( $arrays ) ) {
            $arrays = get_object_vars( $arrays );
        }

        foreach ( $arrays AS $key => $value ) {
            $k = isset( $prefix ) ? $prefix . '[' . $key . ']' : $key;
            if ( is_array( $value ) OR is_object( $value )  ) {
                self::http_build_for_curl( $value, $new, $k );
            } else {
                $new[$k] = $value;
            }
        }
    }
        
    public static function isAnual($plan) {
        if($plan->mensalidade == 0 && $plan->adesao > 0) {
            return true;
        }

        return false;
    }
        
    public static function isMensal($plan) {
        return !self::isAnual($plan);
    }

	public static function contratarPersonalizado($dados) {
        $curl = new Curl();
        $curl->option(CURLOPT_POSTFIELDS, http_build_query($dados));
    }
}