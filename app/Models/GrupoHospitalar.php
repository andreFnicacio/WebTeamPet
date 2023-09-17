<?php

namespace App\Models;

use Eloquent as Model;


class GrupoHospitalar extends Model
{
    //use SoftDeletes;

    public $table = 'grupos_hospitalares';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    //protected $dates = ['deleted_at'];


    public $fillable = [
        'tipo_pessoa',
        'cpf_cnpj',
        'nome_grupo',
        'contato_principal',
        'email_contato',
        'cep',
        'rua',
        'numero_endereco',
        'bairro',
        'cidade',
        'estado',
        'complemento_endereco',
        'telefone_fixo',
        'celular',
        'email_secundario',
        'banco',
        'agencia',
        'numero_conta',
        'id_usuario'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'tipo_pessoa' => 'string',
        'cpf_cnpj' => 'string',
        'nome_grupo' => 'string',
        'contato_principal' => 'string',
        'email_contato' => 'string',
        'cep' => 'string',
        'rua' => 'string',
        'numero_endereco' => 'string',
        'bairro' => 'string',
        'cidade' => 'string',
        'estado' => 'string',
        'complemento_endereco' => 'string',
        'telefone_fixo' => 'string',
        'celular' => 'string',
        'email_secundario' => 'string',
        'banco' => 'string',
        'agencia' => 'string',
        'numero_conta' => 'string',
        'id_usuario' => 'integer'
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
        return $this->belongsTo(\App\Models\User::class, 'id_usuario');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function clinicas()
    {
        return $this->belongsToMany(\Modules\Clinics\Entities\Clinicas::class, 'grupos_clinicas', 'id_grupo_hospitalar', 'id_clinica');
    }
}
