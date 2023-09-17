<?php

namespace App\Models;

use Eloquent as Model;

class Leads extends Model
{

    public $table = 'leads';
    
    const CREATED_AT = 'created_at';

    public $fillable = [
        'dados',
        'atendido',
        'convertido',
        'origem',
        'email',
        'id_vinculo'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'dados' => 'string',
        'origem' => 'string',
        'email' => 'string',
        'id_vinculo' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function vinculado() {
        return $this->belongsTo(\App\Models\Leads::class, 'id', 'id_vinculo')->first();
    }
}