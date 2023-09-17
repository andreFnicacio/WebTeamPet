<?php

namespace App\Helpers\API\LifepetIntegration\Repositories;
use App\Helpers\API\LifepetIntegration\Domains\Breed\Breed;
use App\Models\Raca;

class BreedRepository {

    public function getById($id) {
        $racas = $this->model()::find($id);
        //dd($planos);
        return $this->adapt($racas);
    }

    public function getBy($field, $value) {
		$breed = $this->model()::where($field, $value);
		if($breed->count() == 0) {
			return null;
		}
		return $this->adapt($breed->first());
	}

    public function adapt(Raca $breed) {
        $breedObj = new Breed();
   
        $breedObj->populate([
            'id' => $breed->id,
            'name' => $breed->nome,
            'species' => $breed->tipo,
            'mixed' => $breed->mistura
        ]);

        return $breedObj;
    }
    
    public function model() {
        return Raca::class;
    }
}