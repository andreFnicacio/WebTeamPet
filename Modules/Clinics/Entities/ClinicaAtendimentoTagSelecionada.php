<?php

namespace Modules\Clinics\Entities;

use Illuminate\Database\Eloquent\Model;

class ClinicaAtendimentoTagSelecionada extends Model
{
    protected $table = "clinicas_atendimentos_tags_selecionadas";

    protected $fillable = [
        'clinica_id',
        'clinica_atendimento_tag_id',
        'created_by'
    ];

    public function tag() {
        return $this->belongsTo(\Modules\Clinics\Entities\ClinicaAtendimentoTag::class, 'clinica_atendimento_tag_id');
    }
}
