<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultoresVendaStatusLog extends Model
{
    protected $connection = 'mysql_consultor';
    protected $table = 'sale_status_logs';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'status_from',
        'status_to',
        'status_reason',
        'date',
        'sellers_id',
        'sales_id'
    ];
}
