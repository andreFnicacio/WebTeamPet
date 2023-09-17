<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    protected $table = 'sms';
    protected $dates = [
        'created_at',
        'deleted_at',
        'updated_at'
    ];

    protected $fillable = [
        'mobile',
        'body',
        'response',
        'finalidade',
        'numero_guia',
    ];

}
