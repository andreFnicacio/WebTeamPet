<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;


class PlanosCredenciados extends \Illuminate\Database\Eloquent\Model
{
    use SoftDeletes;

    public $table = 'planos_credenciados';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'id_plano',
        'id_clinica',
        'habilitado'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_plano' => 'integer',
        'id_clinica' => 'integer',
        'habilitado' => 'boolean'
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
        return $this->belongsTo(\Modules\Clinics\Entities\Clinicas::class, 'id_clinica');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function plano()
    {
        return $this->belongsTo(\App\Models\Planos::class, 'id_plano');
    }
}