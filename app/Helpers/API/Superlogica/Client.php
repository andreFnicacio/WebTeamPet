<?php

namespace App\Helpers\API\Superlogica;

class Client {

	private static $urls = [
		"clientes" => "https://api.superlogica.net/v2/financeiro/clientes",
		"ambiente_cartao" => "https://api.superlogica.net/v2/financeiro/clientes/urlcartao",
		"faturar" => "https://api.superlogica.net/v2/financeiro/faturar",
		"assinaturas" => "https://api.superlogica.net/v2/financeiro/assinaturas",
        "cobranca" => "https://api.superlogica.net/v2/financeiro/cobranca",
        "recorrencias" => "https://api.superlogica.net/v2/financeiro/recorrencias",
        "centrocustos" => "https://api.superlogica.net/v2/financeiro/centrocustos",
        "caixa" => "https://api.superlogica.net/v2/financeiro/caixa"
	];

	const PAGAMENTO_PENDENTE = 0;
	const PAGAMENTO_CONFIRMADO = 1;
	const PAGAMENTO_CANCELADO = 2;

	public static function register(array $fields)
	{
		$curl = new Curl;
		$curl->postDefaults(self::$urls["clientes"]);
                $curl->postFields($fields);

		$response = $curl->execute();
		$curl->close();
		if(is_array($response)) {
			return $response[0];
		}
		return $response;
	}

	public function get($params = []) {
		$curl = new Curl;
		$curl->getDefaults(self::$urls["clientes"] . Curl::params($params, true));

		$response = $curl->execute();
		$curl->close();
		return $response;
	}

	public function assinaturas(array $param) {
        $curl = new Curl;
        $uri = self::$urls["assinaturas"] . Curl::params($param, Curl::QUERY);
        $curl->getDefaults($uri);
        $response = $curl->execute();
        $curl->close();
        return $response;
    }

    public function cobrancas($idCliente) {
        $curl = new Curl;
        $uri = self::$urls["cobranca"] . Curl::params([
            "doClienteComId" => $idCliente
        ], Curl::QUERY);
        $curl->getDefaults($uri);
        $response = $curl->execute();
        $curl->close();
        return $response;
    }

    public function recorrencias($idCliente) {
        $curl = new Curl;
        $uri = self::$urls["recorrencias"] . Curl::params([
                "CLIENTES[0]" => $idCliente
            ], Curl::QUERY);
        $curl->getDefaults($uri);
        $response = $curl->execute();
        $curl->close();
        return $response;
    }

    public function centrocustos(array $param) {
        $curl = new Curl;
        $uri = self::$urls["centrocustos"] . Curl::params($param, Curl::QUERY);;
        $curl->getDefaults($uri);
        $response = $curl->execute();
        $curl->close();
        return $response;
    }

    public function caixa(array $param) {
        $curl = new Curl;
        $uri = self::$urls["caixa"] . Curl::params($param, Curl::QUERY);;
        $curl->getDefaults($uri);
        $response = $curl->execute();
        $curl->close();
        return $response;
        // $client->caixa(['favorecido' => 85]);
    }
        

	/**
	 * Obtém o link para um ambiente seguro para a digitação dos dados de cartão
	 * @return [type] [description]
	 */
	public function getSecureCardForm() {
		$curl = new Curl;
		$curl->getDefaults(self::$urls["ambiente_cartao"] . Curl::params([
			"bandeira" => $_POST["bandeira"],
			"email" => $_POST["email"],
			"callback" => "callback"
		], Curl::QUERY));
		
		$response = $curl->execute();
		$curl->close();
		return $response;
	}

	/**
	 * Salva o cliente recebido, no banco de dados.
	 * @return [type] [description]
	 */
	public function store($nome, $email, $telefone, $cidade, $pets) {
		$bind = array(
			":nome" => $nome,
			":email" => $email,
			":telefone" => $telefone,
			":cidade" => $cidade,
			":pets" => $pets
		);

		$query = "INSERT INTO clients (nome, email, telefone, cidade, pets) 
				  VALUES (:nome, :email, :telefone, :cidade, :pets);";

		try {
			$connection = Database::getConnection();
			$statement = $connection->prepare($query);
			return $statement->execute($bind);
		} catch (Exception $e) {
			error_log("Houve um erro na tentativa de salvar o cliente:\n" . $e->getMessage());
			return false;
		}
	}

	/**
	 * Guarda as informações de pagamento do usuário
	 * @param  [type] $info [description]
	 * @return [type]       [description]
	 */
	public function storeBillingInfo($info) {
            $dt = \DateTime::createFromFormat("d/m/Y", $info["data_nascimento"]);
            if($dt) {
                $info["data_nascimento"] = $dt->format("Y-m-d");
            }
            $bind = [
                ":id_cliente" => $info["id_cliente"],
                ":nome" => $info["nome"],
                ":cpf" => $info["cpf"],
                ":data_nascimento" => $info["data_nascimento"],
                ":email" => $info["email"],
                ":telefone_fixo" => $info["telefone_fixo"],
                ":celular" => $info["celular"],
                ":cep" => $info["cep"],
                ":rua" => $info["rua"],
                ":numero" => $info["numero"],
                ":complemento" => $info["complemento"],
                ":bairro" => $info["bairro"],
                ":estado" => $info["estado"],
                ":cidade" => $info["cidade"],
                ":codigo_vendedor" => $info["codigo_vendedor"]
            ];

            $query = "INSERT INTO billing_info (id_cliente, nome, cpf, data_nascimento, email, telefone_fixo, celular, cep, rua, numero, complemento, bairro, estado, cidade, codigo_vendedor) 
                     VALUES (:id_cliente, :nome, :cpf, :data_nascimento, :email, :telefone_fixo, :celular, :cep, :rua, :numero, :complemento, :bairro, :estado, :cidade, :codigo_vendedor);";
            
            $connection = Database::getConnection();
            $statement = $connection->prepare($query);
            return $statement->execute($bind);
	}

	/**
	 * Salva o registro de uma venda
	 * @param  [type] $identificador   [description]
	 * @param  [type] $valor_plano     [description]
	 * @param  [type] $forma_pagamento [description]
	 * @param  string $codigo_corretor [description]
	 * @return [type]                  [description]
	 */
	public static function storeSale($identificador, $valor_plano, $forma_pagamento, $codigo_corretor = "NENHUM") {
		$bind = [
			':st_identificador_plc' => $identificador,
			':valor_plano' => $valor_plano,
			':forma_pagamento' => $forma_pagamento,
			':codigo_corretor' => $codigo_corretor,
			':status_venda' => PAGAMENTO_PENDENTE
		];

		$query = "INSERT INTO sales (st_identificador_plc, valor_plano, forma_pagamento, codigo_corretor) 
                     VALUES (:st_identificador_plc, :valor_plano, :forma_pagamento, :codigo_corretor);";
            
        $connection = Database::getConnection();
        $statement = $connection->prepare($query);
        return $statement->execute($bind);
	}

	/**
	 * Obtém as vendas de dado corretor
	 * @param  [type] $codigo_corretor [description]
	 * @return [type]                  [description]
	 */
	public static function getSales($codigo_corretor) {
		$bind = [
			":codigo_corretor" => $codigo_corretor
		];
		$query = "SELECT * FROM sales WHERE codigo_corretor = :codigo_corretor";
		try {
			$connection = Database::getConnection();
			$statement = $connection->prepare($query);
			$statement->execute($bind);
			$sales = $statement->fetchAll(\PDO::FETCH_ASSOC);

			//FIXME: Operação altamente custosa
			foreach ($sales as &$sale) {
				$lastCheck = $sale['ultima_checagem'];
				if(!empty($lastCheck)) {
					$lastCheck = \DateTime::createFromFormat('Y-m-d H:i:s', $lastCheck);
					$now = new \DateTime();
					$now = $now->format('Y-m-d H:i:s');
					$hourdiff = round((strtotime("2017-08-04 12:25:45") - strtotime($now))/3600, 1);
					$verify = abs(floatval($hourdiff)) > 1;
				} else {
					$verify = true;
				}

				//Se a última verificação foi feita uma hora atrás
				if($verify) {
					if($sale["status_venda"] == PAGAMENTO_PENDENTE) {
						$status = self::checkSaleStatus($sale);
						if($sale['status_venda'] != $status) {
							$sale['status_venda'] = $status;
						}
					}
				}

				self::updateSaleStatus($sale);
			}
			
			return $sales;
		} catch (Exception $e) {
			error_log("Houve um erro na tentativa de obter as informações do cliente:\n" . $e->getMessage());
			return false;
		}
	}

	public static function checkSaleStatus(array $sale) {
		$curl = new Curl;
		$curl->getDefaults(self::$urls["assinaturas"] . Curl::params([
			'identificadorContrato' => $sale['st_identificador_plc']
		], Curl::QUERY));
		
		$response = $curl->execute();
		$curl->close();
		if(isset($response->data)) {
			$data = $response->data;
			if($data->st_identificador_plc == strtolower($sale['st_identificador_plc'])) {
				return !empty($data->fl_primeiropag_plc);
			}
		}
		return false;
	}

	public static function updateSaleStatus(array $sale)
	{
		$bind = [
			':id' => $sale['id'],
			':status_venda' => $sale['status_venda'],
			':ultima_checagem' => date('Y-m-d h:i:s')
		];
		$sql = "UPDATE sales SET status_venda = :status_venda AND ultima_checagem = :ultima_checagem WHERE sales.id = :id;";
		$connection = Database::getConnection();
		$statement = $connection->prepare($sql);
		return $statement->execute($bind);
	}
        
    public function savePets($id_client, $pets) {
        $query = "INSERT INTO pets (nome, idade, raca, doencas, sexo, id_cliente) "
                . "VALUES (:nome, :idade, :raca, :doencas, :sexo, :id_cliente);";
        foreach ($pets as $pet) {
            $bind = [
                ":nome" => $pet["nome"],
                ":idade" => $pet["idade"],
                ":raca" => $pet["raca"],
                ":doencas" => $pet["doencas"],
                ":sexo" => $pet["sexo"],
                ":id_cliente" => $id_client,
            ];

            $connection = Database::getConnection();
            $statement = $connection->prepare($query);
            $statement->execute($bind);
        }
    }

	public function findByEmail($email)
	{
		$bind = [
			":email" => $email
		];
		$query = "SELECT * FROM clients WHERE email = :email";
		try {
			$connection = Database::getConnection();
			$statement = $connection->prepare($query);
			$statement->execute($bind);
			return $statement->fetchAll(\PDO::FETCH_ASSOC);
		} catch (Exception $e) {
			error_log("Houve um erro na tentativa de obter as informações do cliente:\n" . $e->getMessage());
			return false;
		}
	}

	public function getBillingInfo($client_id)
	{
		$bind = [
                    ":client_id" => $client_id
		];
		$query = "SELECT * FROM billing_info WHERE id_cliente = :client_id";
		try {
			$connection = Database::getConnection();
			$statement = $connection->prepare($query);
			$statement->execute($bind);
			return $statement->fetchAll(\PDO::FETCH_ASSOC);
		} catch (Exception $e) {
			error_log("Houve um erro na tentativa obter as informações de pagamento do cliente:\n" . $e->getMessage());
			return false;
		}
	}

	public function setIdSuperlogica($email, $id_cliente_superlogica)
	{
		$bind = [
			":id_superlogica" => $id_cliente_superlogica,
			":email" => $email
		];

		$sql = "UPDATE clients SET id_superlogica = :id_superlogica WHERE email = :email";
                $connection = Database::getConnection();
                $statement = $connection->prepare($sql);
                $result = $statement->execute($bind);
                return $result;
	}
        
    public function edit($id_superlogica, $fields) {
        
        $fields = array_merge($fields, [
            "NM_CARTAO_SAC" => isset($fields["ST_CARTAO_SAC"]) ? $fields["ST_CARTAO_SAC"] : "" ,
            "NM_MESCARTAOVENCIMENTO_SAC" => isset($fields["ST_MESVALIDADE_SAC"]) ? $fields["ST_MESVALIDADE_SAC"] : "" ,
            "NM_ANOCARTAOVENCIMENTO_SAC" => isset($fields["ST_ANOVALIDADE_SAC"]) ? $fields["ST_ANOVALIDADE_SAC"] : "" ,
            "ID_SACADO_SAC" => $id_superlogica
        ]);
        
        $curl = new Curl;
        $curl->putDefaults(self::$urls["clientes"]);
        $curl->postFields($fields);

        $response = $curl->execute();
        $curl->close();
        return $response;
    }

    public static function exists($email) {
    	$curl = new Curl;
    	$curl->getDefaults(self::$urls['clientes'] . Curl::params([
			"pesquisa" => 	"todosemails:" . $email
		], Curl::QUERY));

		$response = $curl->execute();
		$curl->close();
		return $response;
    }
}