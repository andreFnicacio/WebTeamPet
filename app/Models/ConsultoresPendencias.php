<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultoresPendencias extends Model
{
    //use SoftDeletes;

    protected $connection = 'mysql_consultor';
    protected $table = 'pendencies';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'date',
        'description',
        'created_by',
        'sellers_id'
    ];

}
