<?php

namespace App\Repositories;

use Modules\Clinics\Entities\ClinicaAtendimentoTag;

class ClinicaAtendimentoTagRepository
{

    public function getAll() {
        $this->model()::get();
    }

    /**
     * Verifica se a tag já existe no banco de dados
     *
     * @param string $tag
     * @return ClinicaAtendimentoTag
     */
    public function getTagByName(string $tag) : ?ClinicaAtendimentoTag {
        $model = $this->model();
        $checkedTag = $model::where('nome', $tag)->first();
        
        if(!$checkedTag) {
           return null;
        }

        return $checkedTag;
    }

    /**
     * Insere no banco se não existir e retorna seu id
     *
     * @param string $tag
     * @return integer
     */
    public function storeIfNotExists(string $tag) : int {
        $checkedTag = $this->getTagByName($tag);
       
        if(!$checkedTag) {
            $checkedTag = $this->store($tag);
        }

        return $checkedTag->id;
    }

    /**
     * Insere a tag no banco de dados e retorna seu objeto
     *
     * @param string $tag
     * @return ClinicaAtendimentoTag
     */
    public function store(string $tag)  : ClinicaAtendimentoTag {
        
        try {
            $model = $this->model();
            $model = new $model();
            
            $model->nome = $tag;
            $model->created_by = auth()->user()->id;
            $model->save();
            
            return $model;
        } catch(\Throwable $e) {
            
            throw new \Exception($e->getMessage());
        }
        
    }

    

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ClinicaAtendimentoTag::class;
    }

}
