<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Repositories\ClinicaAtendimentoTagRepository;
use App\Repositories\ClinicaAtendimentoTagSelecionadaRepository;

class ClinicaAtendimentoTagService {

    private $clinicId;

    private $tagRepo;
    private $tagSelecionadaRepo;

    public function __construct(int $clinicId) {

        //Log::useDailyFiles(storage_path().'/logs/clinicas-atendimentos-tags/log.log');

        $this->clinicId = $clinicId;

        $this->tagRepo = new ClinicaAtendimentoTagRepository();
        $this->tagSelecionadaRepo = new ClinicaAtendimentoTagSelecionadaRepository();
        
    }
    

    public function save(array $tags) {
        try {
            
            $tagIds = $this->saveAndBindTags($tags);
            $this->deleteUnsentTags($tagIds);
            
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            throw new \Exception('Falha ao verificar e salvar a tag de atendimento da clínica');
        }
        
    }

    public function deleteAll() {
        try {
            $this->tagSelecionadaRepo->deleteAllByClinicId($this->clinicId);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            throw new \Exception('Falha ao apagar as tags de atendimento da clínica');
        }
        
    }

    private function saveAndBindTags($tags) {
        foreach($tags as $tag) {
            $tagId = $this->tagRepo->storeIfNotExists($tag);
            
            $this->tagSelecionadaRepo->storeIfNotExists($tagId, $this->clinicId);
            $tagIds[] = $tagId;
        }

        return $tagIds;
    }

    private function deleteUnsentTags(array $tagIds) {
        $this->tagSelecionadaRepo->deleteUnsentTags($tagIds, $this->clinicId);
    }

   

}