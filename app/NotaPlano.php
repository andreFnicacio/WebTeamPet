<?php

namespace App;

use App\Models\Planos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotaPlano extends Model
{
    use SoftDeletes;

    public $table = 'notas_planos';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];


    public $fillable = [
        'corpo',
        'user_id',
        'plano_id'
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
        'plano_id' => 'integer'
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
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function plano()
    {
        return $this->belongsTo(Planos::class);
    }

    public static function registrar($corpo, Planos $plano, User $user = null) {
        if(!$user) {
            $user = auth()->user();
        }
        return self::create([
            'corpo' => $corpo,
            'user_id' => $user ? $user->id : 1,
            'plano_id' => $plano->id
        ]);
    }
}
