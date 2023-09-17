<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Timesheet extends Model
{
    //use SoftDeletes;

    public $table = 'timesheets';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['inicio', 'fim'];


    public $fillable = [
        'id_tarefa',
        'id_usuario',
        'inicio',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_tarefa' => 'integer',
        'id_usuario' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function tarefa()
    {
        return $this->belongsTo(\App\Models\Tarefa::class, 'id_tarefa');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_usuario');
    }
}
