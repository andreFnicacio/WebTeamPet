<?php

namespace App\Models;

use Planos;
use Illuminate\Database\Eloquent\Model;

class PlanoConfiguracao extends Model
{
    protected $table = 'planos_configuracoes';

    public function plano()
    {
        return $this->belongsTo(Planos::class, 'id_plano');
    }
}
