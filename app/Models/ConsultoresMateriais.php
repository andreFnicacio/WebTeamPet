<?php

namespace App\Models;

use Illuminate\Database\Eloquent as Model;

class ConsultoresMateriais extends Model\Model
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $connection = 'mysql_consultor';

    protected $table = 'materials';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'description',
        'file',
        'link',
        'category',
        'type',
        'active'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'description' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];
}
