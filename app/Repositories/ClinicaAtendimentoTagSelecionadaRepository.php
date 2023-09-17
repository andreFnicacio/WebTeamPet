<?php

namespace App\Repositories;

use Modules\Clinics\Entities\ClinicaAtendimentoTagSelecionada;


class ClinicaAtendimentoTagSelecionadaRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
      'tag'
    ];

    public function getAll() {
        $this->model()::get();
    }

    /**
     * Remove as tags da clínica que não estão no array de tags passado
     *
     * @param array $tagIds
     * @param integer $clinicId
     * @return void
     */
    public function deleteUnsentTags(array $tagIds, int $clinicId) {
        $this->model()::whereNotIn('clinica_atendimento_tag_id', $tagIds)
                    ->where('clinica_id', $clinicId)
                    ->delete();
    }

    /**
     * Remove todas as tags selecionadas da clínica
     *
     * @param integer $clinicId
     * @return void
     */
    public function deleteAllByClinicId(int $clinicId) {
        $this->model()::where('clinica_id', $clinicId)->delete();
    }

    /**
     * Vincula a tag à clínica caso ela já não esteja vinculada
     *
     * @param integer $tagId
     * @param integer $clinicId
     * @return void
     */
    public function storeIfNotExists(int $tagId, int $clinicId) {

        $this->model()::firstOrCreate(
            [
                'clinica_atendimento_tag_id' => $tagId,
                'clinica_id' => $clinicId,
            ],
            ['created_by' => auth()->user()->id]
        );

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
            $model->tag = $tag;
            $model->created_by = auth()->user->id;
            $model->save();

            return $model;
        } catch(\Throwable $e) {
            throw new \Exception('Falha ao salvar a tag no banco de dados');
        }
        
    }

    

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ClinicaAtendimentoTagSelecionada::class;
    }

}
