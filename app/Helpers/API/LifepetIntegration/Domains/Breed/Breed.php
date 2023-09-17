<?php

namespace App\Helpers\API\LifepetIntegration\Domains\Breed;

class Breed {

    private $id;
    private $name;
    private $species;
    private $mixed;

    public function setId(int $data) {
        $this->id = $data;
    }

    public function getId() {
        return $this->id;
    }

    public function setName($data) {
        $this->name = $data;
    }

    public function getName() {
        return $this->name;
    }

    public function setSpecies(string $data) {
       
        if(!in_array($data, ['TODOS', 'CACHORRO', 'GATO'])) {
            throw new \Exception("Breed::setSpecies: Não foi possível preencher a espécie/tipo. É necessário que o valor seja 'CACHORRO', 'GATO' ou 'TODOS' ");
        }

        $this->species = $data;
    }

    public function getSpecies() {
        return $this->species;
    }

    public function setMixed(bool $data) {

        if(!is_bool($data)) {
            throw new \Exception("Breed::setMixed: Não foi possível preencher o campo 'mistura'. É necessário que o valor seja verdadeiro ou falso");
        }

        $this->mixed = $data;
    }

    public function getMixed() {
        return $this->mixed;
    }

    public function populateErrorMessage(string $message) {
        return "Erro ao preencher o OBJ Breed: " . $message;
    }

    public function populate(array $data) {
        if(!isset($data['name'])) {
            throw new \Exception($this->populateErrorMessage('O campo name não foi encontrado!'));
        }

        if(!isset($data['species'])) {
            throw new \Exception($this->populateErrorMessage('O campo species (tipo/espécie) não foi encontrado!'));
        }

        if(!isset($data['mixed'])) {
            throw new \Exception($this->populateErrorMessage('O campo mixed (mistura) não foi encontrado!'));
        }

        if(isset($data['id'])) {
            $this->setId($data['id']);
        }

        $this->setName($data['name']);
        $this->setSpecies($data['species']);
        $this->setMixed($data['mixed']);
    }
}