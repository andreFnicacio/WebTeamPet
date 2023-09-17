<?php

namespace App\Models;

use App\User;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Notas extends Model
{
    use SoftDeletes;

    public $table = 'notas';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['created_at','deleted_at'];


    public $fillable = [
        'corpo',
        'user_id',
        'cliente_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'corpo' => 'string',
        'user_id' => 'integer',
        'cliente_id' => 'integer'
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
    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function cliente()
    {
        return $this->belongsTo(\App\Models\Clientes::class);
    }

    public static function registrar($corpo, Clientes $clientes, User $user = null) {
        if(!$user) {
            $user = auth()->user();
        }
        return self::create([
            'corpo' => $corpo,
            'user_id' => $user ? $user->id : 1,
            'cliente_id' => $clientes->id
        ]);
    }
}