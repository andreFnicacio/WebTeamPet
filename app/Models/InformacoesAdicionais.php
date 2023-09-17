<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class InformacoesAdicionais extends Model
{
//    use SoftDeletes;

    public $table = 'informacoes_adicionais';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'cor',
        'descricao_resumida',
        'descricao_completa',
        'icone',
        'prioridade'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'cor' => 'string',
        'descricao_resumida' => 'string',
        'descricao_completa' => 'string',
        'icone' => 'string',
        'prioridade' => 'integer'
    ];

    public static $cores = [
        "white","default","dark","blue","blue-madison","blue-chambray","blue-ebonyclay","blue-hoki","blue-steel","blue-soft","blue-dark","blue-sharp","blue-oleo","green","green-meadow","green-seagreen","green-turquoise","green-haze","green-jungle","green-soft","green-dark","green-sharp","green-steel","grey","grey-steel","grey-cararra","grey-gallery","grey-cascade","grey-silver","grey-salsa","grey-salt","grey-mint","red","red-pink","red-sunglo","red-intense","red-thunderbird","red-flamingo","red-soft","red-haze","red-mint","yellow","yellow-gold","yellow-casablanca","yellow-crusta","yellow-lemon","yellow-saffron","yellow-soft","yellow-haze","yellow-mint","purple","purple-plum","purple-medium","purple-studio","purple-wisteria","purple-seance","purple-intense","purple-sharp","purple-soft"
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
    public function informacoesAdicionaisVinculos()
    {
//        return $this->hasMany(\App\Models\InformacoesAdicionaisVinculo::class);
    }

}
