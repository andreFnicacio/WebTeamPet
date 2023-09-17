<?php

namespace App;

use App\Helpers\API\LifepetIntegration\Domains\Pet\Pet;
use App\Models\Clientes;
use App\Models\Pets;
use App\Models\Planos;
use Illuminate\Database\Eloquent\Model;

class ItemFaturaConveniada extends Model
{
    protected $table = 'conveniadas_faturamentos_itens';

    protected $fillable = [
        'id_fatura_conveniada',
        'id_cliente',
        'id_pet',
        'id_plano',
        'valor',
        'tipo',
        'descricao'
    ];

    public function fatura() {
        return $this->belongsTo(FaturaConveniada::class, 'id_fatura_conveniada');
    }

    public function pet() {
        return $this->belongsTo(Pets::class, 'id_pet');
    }

    public function cliente() {
        return $this->belongsTo(Clientes::class, 'id_cliente');
    }

    public function plano() {
        return $this->belongsTo(Planos::class, 'id_plano');
    }
}
