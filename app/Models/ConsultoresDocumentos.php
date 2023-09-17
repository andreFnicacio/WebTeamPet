<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultoresDocumentos extends Model
{
    protected $connection = 'mysql_consultor';
    protected $table = 'documents';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];


}
