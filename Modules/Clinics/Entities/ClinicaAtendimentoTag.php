<?php

namespace Modules\Clinics\Entities;

use Illuminate\Database\Eloquent\Model;

class ClinicaAtendimentoTag extends Model
{
    protected $table = "clinicas_atendimentos_tags";

    protected $fillable = [
        'nome',
        'created_by'
    ];
    
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'tag' => 'string|unique:clinicas_atendimentos_tags'
    ];

    public function selecionadas() {
        return $this->hasMany(\Modules\Clinics\Entities\ClinicaAtendimentoTagSelecionada::class, 'clinica_atendimento_tag_id');
    }
}
