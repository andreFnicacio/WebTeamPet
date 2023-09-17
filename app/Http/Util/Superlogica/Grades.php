<?php

namespace App\Http\Util\Superlogica;

class Grades {

	private static $urls = [
		"grades" => "https://lifepet.superlogica.net/financeiro/atual/gradeplanos/"
	];

	/**
	 * Define que planos aparecerão para seleção
	 * @var array
	 */
	private static $avaliable = [
		4 => [4, "Plano Familiar - 2 pets", 2],
		5 => [5, "Plano Familiar - 3 pets", 3],
		6 => [6, "Plano Familiar - 4 pets", 4],
		7 => [7, "Plano Familiar - 5 pets", 5],
		9 => [9, "Plano Familiar - 6 pets", 6],
		10 => [10, "Plano Familiar - 7 pets", 7],
		11 => [11, "Plano Familiar - 8 pets", 8],
		12 => [12, "Plano Familiar - 9 pets", 9],
		8 => [8, "Plano Individual", 1],
		13 => [13, "Plano Familiar", 2],
		//1 => "Principal",
	];

	/**
	 * Obtém todas as grades
	 * @param  array
	 * @return [type]
	 */
	public function get($params = []) {
		$curl = new Curl;
		$curl->getDefaults(self::$urls["grades"] . Curl::params($params, Curl::QUERY));
		
		$response = $curl->execute();
		$curl->close();
		return $response;
	}

	/**
	 * Obtém a grade com o id definido
	 * @param  int
	 * @return object
	 */
	public function getById($id) {
		return self::get(["id" => $id]);
	}

	// public function getPlansByQuantity($quantity = 1)
	// {
	// 	$found = array_filter(self::$avaliable, function($g) use ($quantity) {
	// 		return $g[2] == $quantity;
	// 	});

	// 	if(count($found) < 1) {
	// 		return [];
	// 	}
	// 	$id = array_values($found)[0][0];
	// 	return $this->plans($id);
	// }

	public function getPlansByQuantity($quantity = 1)
	{
		if($quantity == 1) {
			$found = self::$avaliable[8];
		} else {
			$found = self::$avaliable[13];
		}

		return $this->plans($found[0]);
	}

	/**
	 * Obtém todos os planos de uma dada grade.
	 * @param  int
	 * @return array
	 */
	public function plans($id_grade) {
		return (new Plans)->allByGrade($id_grade);
	}

	/**
	 * Obtém as grades disponíveis para seleção
	 * @return array
	 */
	public function avaliable() {
		return self::$avaliable;
	}

	public static function filterPlanById($planos, $id_plano) {
		return array_filter($planos, function($plano) use ($id_plano) {
                    return $plano->id == $id_plano;
		});
	}
}