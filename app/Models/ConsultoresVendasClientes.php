<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultoresVendasClientes extends Model
{
    protected $connection = 'mysql_consultor';
    protected $table = 'clients';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];
}
