<?php

namespace App\Http\Util\Superlogica;

class Client {

	private static $urls = [
		"clientes" => "https://api.superlogica.net/v2/financeiro/clientes",
		"ambiente_cartao" => "https://api.superlogica.net/v2/financeiro/clientes/urlcartao",
		"faturar" => "https://api.superlogica.net/v2/financeiro/faturar",
		"cartao" => "https://api.superlogica.net/v2/financeiro/clientes/formadepagamento"
	];

	public function register(array $fields)
	{
		$curl = new Curl;
		$curl->postDefaults(self::$urls["clientes"]);
                $curl->postFields($fields);
		
		$response = $curl->execute();
		$curl->close();
		return $response;
	}

	public function get($params = []) {
		$curl = new Curl;
		$curl->getDefaults(self::$urls["clientes"]);
		
		$response = $curl->execute();
		$curl->close();
		return $response;
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
	
	public function updateCreditCard($data) {
		$curl = new Curl();
        $curl->putDefaults(self::$urls["cartao"]);
        $curl->option(CURLOPT_POSTFIELDS, http_build_query($data));

        $response = $curl->execute();
        $curl->close();
        return $response;
	}

	
}