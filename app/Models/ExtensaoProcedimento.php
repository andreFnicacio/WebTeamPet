<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ExtensaoProcedimento extends Model
{
    public $table = 'pets_extensao_procedimentos';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    public $fillable = [
        'id_pet',
        'id_procedimento'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_pet' => 'integer',
        'id_procedimento' => 'integer'
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
    public function pet()
    {
        return $this->belongsTo(\App\Models\Pets::class, 'id_pet');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function procedimento()
    {
        return $this->belongsTo(\App\Models\Procedimentos::class, 'id_procedimento');
    }
}
