<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class FichasRespostas extends Model
{
    use SoftDeletes;

    public $table = 'fichas_respostas';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'id_pergunta',
        'id_ficha',
        'resposta',
        'descricao'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_pergunta' => 'integer',
        'id_ficha' => 'integer',
        'resposta' => 'boolean',
        'descricao' => 'string'
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
    public function ficha()
    {
        return $this->belongsTo(\Modules\Veterinaries\Entities\FichasAvaliacoes::class, 'id_ficha', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function pergunta()
    {
        return $this->belongsTo(\App\Models\FichasPerguntas::class, 'id_pergunta', 'id');
    }
}
