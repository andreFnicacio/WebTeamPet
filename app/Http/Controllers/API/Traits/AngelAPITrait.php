<?php

namespace App\Http\Controllers\API\Traits;

use Illuminate\Support\Facades\{Auth,DB,Mail};
use Illuminate\Http\Request;
use App\Models\Clientes;
use Carbon\Carbon;

trait AngelAPITrait {

	public function assinarAngel(Request $request, $idPet)
	{
		$user = Auth::user();
		if ($user) {
			$cliente = (new Clientes())->where('id_usuario', $user->id)->first();
		}

		$pet = $cliente->pets->where('id', $idPet)->first();

		if (!$pet) {
			return response()->json(["msg" => "O pet informado não existe ou não é deste tutor!"], 404);
		}

		if ($pet->angel) {
			return response()->json(["msg" => "O pet selecionado já possui Plano Angel!"], 401);
		}

		if (!$request->get('regime')) {
			return response()->json(["msg" => "O campo regime é obrigatório"], 401);
		}

		$pet->assinarAngel($request->all());

		return response()->json([
			"msg" => "Assinatura confirmada!",
			"carencia_angel_restante" => $pet->carenciaAngelRestante()
		], 200);
	}

	public function getPetsValoresAngel()
	{
		$user = Auth::user();
		if ($user) {
			$cliente = (new Clientes())->where('id_usuario', $user->id)->first();
		}

		$pets_valores = $cliente->pets()->where('ativo', 1)->where('angel', 0)->get()->map(function ($pet) {
			$idade = $pet->data_nascimento->age;
			$valor_angel =  DB::table('plano_angel_valores')
								->where('idade_min', '<=', $idade)
								->where('idade_max', '>=', $idade)
								->first();
			return [
				'id' => $pet->id,
				'pet' => $pet->nome_pet,
				'tipo' => $pet->tipo,
				'raca' => $pet->raca->nome,
				'foto' => $pet->foto,
				'idade' => $idade,
				'valor_mensal' => $valor_angel->valor_mensal,
				'valor_anual' => $valor_angel->valor_anual,
			];
		});

		return response()->json([
			'pets' => $pets_valores
		]);
	}

}
