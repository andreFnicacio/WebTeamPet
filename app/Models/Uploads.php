<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Uploads extends Model
{
    use SoftDeletes;

    public $table = 'uploads';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'original_name',
        'mime',
        'description',
        'extension',
        'path',
        'size',
        'public',
        'bind_with',
        'binded_id',
        'user_id',
        'id_usuario_delete',
        'justificativa_delete'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'description' => 'string',
        'extension' => 'string',
        'path' => 'string',
        'size' => 'float',
        'public' => 'boolean',
        'bind_with' => 'string',
        'binded_id' => 'integer',
        'user_id' => 'integer',
        'id_usuario_delete' => 'integer',
        'justificativa_delete' => 'string'
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

    public static function makeUpload($file, $bind_with, $bind_id, $description = '', $publico = 1)
    {
        $upload = \App\Models\Uploads::create([
            'original_name' => $file->getClientOriginalName(),
            'mime'          => $file->getClientMimeType(),
            'description'   => $description,
            'extension'     => $file->extension(),
            'size'          => $file->getClientSize(),
            'public'        => $publico,
            'path'          => $file->store('uploads'),
            'bind_with'     => $bind_with,
            'binded_id'     => $bind_id,
            'user_id'       => auth()->user()->id
        ]);
    }

    public function scopeBindTable($query, $table)
    {
        return $query->where('bind_with', $table);
    }

    public function scopeBindId($query, $id) {
        return $query->where('binded_id', $id);
    }
}
