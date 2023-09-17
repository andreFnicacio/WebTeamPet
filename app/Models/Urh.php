<?php

namespace App\Models;

use Eloquent as Model;


class Urh extends Model
{
//    use SoftDeletes;

    public $table = 'urh';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'id',
        'nome_urh',
        'valor_urh',
        'data_validade',
        'ativo'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'nome_urh' => 'string',
        'valor_urh' => 'float',
        'ativo' => 'boolean'
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
    public function clinicas()
    {
        return $this->hasMany(\Modules\Clinics\Entities\Clinicas::class, 'id_urh', 'id');
    }
    
}
