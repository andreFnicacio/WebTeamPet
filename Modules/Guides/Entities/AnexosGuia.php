<?php

namespace Modules\Guides\Entities;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class AnexosGuia extends Model
{
    use SoftDeletes;

    public $table = 'anexos_guia';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'original_name',
        'mime',
        'extension',
        'path',
        'size',
        'numero_guia',
        'user_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'extension' => 'string',
        'path' => 'string',
        'size' => 'float',
        'numero_guia' => 'string',
        'user_id' => 'integer',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public static function adicionarAnexos(array $anexos, string $numero_guia)
    {
        foreach($anexos as $anexo){
            $extension = $anexo->extension();
            $size = $anexo->getClientSize();
            $mime = $anexo->getClientMimeType();
            $originalName = $anexo->getClientOriginalName();
            $path = $anexo->store('anexosGuia');
            $upload = \App\Models\AnexosGuia::create([
                'original_name' => $originalName,
                'mime'          => $mime,
                'extension'     => $extension,
                'size'          => $size,
                'path'          => $path,
                'numero_guia'   => $numero_guia,
                'user_id'       => auth()->user()->id
            ]);
        }
    }
}
