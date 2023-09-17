<?php

namespace App\Models\CarteiraDigital;

use Illuminate\Database\Eloquent\Model;

class CarteiraDigitalTransacao extends Model
{
    protected $table = 'carteira_digital_transacoes';

    protected $fillable = [
        'valor',
        'tipo', // 1 = Débito, 2 = Crédito
        'descricao',
        'observacoes'
    ];
    
}
