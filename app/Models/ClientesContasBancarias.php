<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\Utils;


class ClientesContasBancarias extends Model
{
    use SoftDeletes;

    public $table = 'clientes_contas_bancarias';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];
    protected $appends = array('nome_banco');


    public $fillable = [
        'nome_completo',
        'tipo_pessoa',
        'cpf_cnpj',
        'banco',
        'agencia',
        'conta',
        'tipo_conta',
        'ativo',
        'id_cliente',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'nome_completo' => 'string',
        'tipo_pessoa' => 'string',
        'cpf_cnpj' => 'string',
        'banco' => 'string',
        'agencia' => 'string',
        'conta' => 'string',
        'ativo' => 'boolean',
        'tipo_conta' => 'string',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function cliente()
    {
        return $this->belongsTo(\App\Models\Clientes::class, 'id_cliente');
    }

    public function getNomeBancoAttribute()
    {
        $listaBancos = Utils::getBancos();
        return $listaBancos[$this->banco];
    }

}
