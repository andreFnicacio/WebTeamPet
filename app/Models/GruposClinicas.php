<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class GruposClinicas extends Model
{
    use SoftDeletes;

    public $table = 'grupos_clinicas';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'id_grupo_hospitalar',
        'id_clinica'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_grupo_hospitalar' => 'integer',
        'id_clinica' => 'integer'
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
    public function clinica()
    {
        return $this->belongsTo(\App\Models\Clinica::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function gruposHospitalare()
    {
        return $this->belongsTo(\App\Models\GruposHospitalare::class);
    }
}
