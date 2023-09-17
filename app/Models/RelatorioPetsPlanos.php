<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelatorioPetsPlanos extends Model
{
    protected $table = 'relatorio_pets_planos';
    
    protected $fillable = [
        'data',
        'qtde_total',
        'qtde_total_iniciados',
        'valor_total_iniciados',
        'qtde_total_encerrados',
        'valor_total_encerrados',
        'qtde_dia_iniciados',
        'valor_dia_iniciados',
        'qtde_dia_encerrados',
        'valor_dia_encerrados',
        'qtde_dia_downgrades',
        'qtde_dia_upgrades'
    ];
}
