<?php

namespace App\Models\CarteiraDigital;

use Illuminate\Database\Eloquent\Model;

class CarteiraDigitalTransacaoMotivo extends Model
{
    protected $table = 'carteira_digital_transacoes_motivos';

    protected $fillable = [
        'nome'
    ];

}
