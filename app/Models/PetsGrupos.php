<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PetsGrupos extends Model
{
    public $table = 'pets_grupos';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'dias_carencia',
        'quantidade_usos',
        'liberacao_automatica',
        'id_pet',
        'id_grupo'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'dias_carencia' => 'integer',
        'quantidade_usos' => 'integer',
        'liberacao_automatica' => 'boolean',
        'id_pet' => 'integer',
        'id_grupo' => 'integer'
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
    public function grupo()
    {
        return $this->belongsTo(\App\Models\Grupos::class, 'id_grupo');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function pet()
    {
        return $this->belongsTo(\App\Models\Pets::class, 'id_pet');
    }
}
