<?php

namespace App\Models;

use Illuminate\Database\Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Clinics\Entities\Clinicas;


class Grupos extends Model\Model
{
    use SoftDeletes;

    public $table = 'grupos_carencias';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'nome_grupo'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'nome_grupo' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function planosGrupos()
    {
        return $this->hasMany(\App\Models\PlanosGrupos::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function procedimentos()
    {
        return $this->hasMany(\App\Models\Procedimentos::class, 'id_grupo');
    }

    public function limiteClinicas() {
        return $this->belongsToMany(Clinicas::class, 'clinicas_grupos_limites', 'id_grupo', 'id_clinica')->withPivot('limite');
    }
}