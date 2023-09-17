<?php

namespace Modules\Veterinaries\Entities;

use Carbon\Carbon;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class FichasAvaliacoes extends Model
{
    use SoftDeletes;

    public $table = 'fichas_avaliacoes';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'id_pet',
        'id_clinica',
        'id_prestador',
        'porte',
        'pelagem',
        'numero_microchip',
        'assinatura_cliente',
        'assinatura_prestador'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_pet' => 'integer',
        'id_clinica' => 'integer',
        'id_prestador' => 'integer',
        'porte' => 'string',
        'pelagem' => 'string',
        'numero_microchip' => 'string',
        'assinatura_cliente' => 'string',
        'assinatura_prestador' => 'string'
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
    public function clinica()
    {
        return $this->belongsTo(\Modules\Clinics\Entities\Clinicas::class, 'id_clinica', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function pet()
    {
        return $this->belongsTo(\App\Models\Pets::class, 'id_pet', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function prestador()
    {
        return $this->belongsTo(\Modules\Veterinaries\Entities\Prestadores::class, 'id_prestador', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function respostas()
    {
        return $this->hasMany(\App\Models\FichasRespostas::class, 'id_ficha', 'id');
    }

    public function gerarAssinaturaCliente($meio = 1){
        $assinatura = $this->pet->cliente->id . $this->pet->id . $this->id;
        $this->assinatura_cliente = md5($assinatura);
        $this->data_assinatura_cliente = new Carbon();
        $this->meio_assinatura_cliente = $meio;
        $this->save();
    }

    public function gerarAssinaturaPrestador(){
        $assinatura = $this->prestador->id . $this->pet->id . $this->id;
        $this->assinatura_prestador = md5($assinatura);
        $this->data_assinatura_prestador = new Carbon();
        $this->save();
    }
}
