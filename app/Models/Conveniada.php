<?php

namespace App\Models;

use App\FaturaConveniada;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conveniada extends Model
{
    use SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    protected $table = 'conveniadas';

    protected $fillable = [
        'nome',
        'contato',
        'email',
        'telefone',
        'ativo',
        'tipo_desconto',
        'desconto',
        'id_externo'
    ];

    public function contratos() {
        return $this->hasMany(PetsPlanos::class, 'id_conveniada', 'id');
    }

    public function faturas() {
        return $this->hasMany(FaturaConveniada::class, 'id_conveniada');
    }

    public function contratosFiltrados()
    {
        return $this->contratos()
            ->whereNull('data_encerramento_contrato')->get()
            ->filter(function(PetsPlanos $c) {
                return ($c->pet->regime == 'MENSAL' && $c->pet->cliente->ativo);
            });
    }
}
