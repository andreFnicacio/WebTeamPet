<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultoresEnderecos extends Model
{
    protected $connection = 'mysql_consultor';
    protected $table = 'addresses';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];


    protected $fillable = [
        'cep',
        'address',
        'number',
        'complement',
        'district',
        'city',
        'uf'    
    ];
}
