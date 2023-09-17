<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RDStationEnvio extends Model
{
    protected $table = 'rd_station_envios';
    
    protected $fillable = [
        'tabela',
        'tabela_id',
        'identificador',
        'descricao'
    ];
}
