<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultoresEmail extends Model
{
    protected $connection = 'mysql_consultor';
    protected $table = 'email_logs';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];


    protected $fillable = [
        'date',
        'to',
        'subject',
        'message',
        'description',
        'info',
        'sender',
        'created_by',
        'sellers_id'
    ];
}
