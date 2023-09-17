<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultoresExtratos extends Model
{
    protected $fillable = [
        'value',
        'total',
        'date',
        'status',
        'sellers_id',
        'sales_id'
    ];
}
