<?php

namespace App\Models\Integration;

use App\Models\Clientes;
use Illuminate\Database\Eloquent\Model;

class SfIntegration extends Model
{
    public $table = 'sf_integration';
    public $timestamps = false;

    protected $fillable = [
        'id_cliente'
    ];

    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'id_cliente', 'id');
    }
}
