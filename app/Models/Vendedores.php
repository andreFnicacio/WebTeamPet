<?php

namespace App\Models;

use Illuminate\Database\Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Vendedores extends Model\Model
{
    use SoftDeletes;

    public $table = 'vendedores';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'tipo_pessoa',
        'cpf_cnpj',
        'nome',
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
        'crmv',
        'tipo',
        'id_usuario',
        'direto',
        'ativo'
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
        'nome' => 'string',
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
        'crmv' => 'string',
        'tipo' => 'string',
        'ativo' => 'boolean',
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
        return $this->belongsTo(\App\User::class, 'id_usuario');
    }


    public function getTelefonesAttribute() {
        $telefones = [];
        if(!empty(trim($this->telefone_fixo))) {
            $telefones[] =  $this->telefone_fixo;
        }
        if(!empty(trim($this->celular))) {
            $telefones[] =  $this->celular;
        }

        return join(" / ", $telefones);
    }

    public function getInsideSalesRules() {
        $email = (filter_var( $this->email_contato, FILTER_VALIDATE_EMAIL ));
        $usuario = ($this->user ? true : false);
        $permissao = (($this->user && $this->user->hasRole('INSIDE_SALES')) || $this->user->hasRole('ADMINISTRADOR'));
        $ativo = ($this->ativo ? true : false);
        
        return [
            'email' => [
                'status' => $email,
                'msg' => 'Email válido'
            ],
            'usuario' => [
                'status' => $usuario,
                'msg' => 'Usuário criado'
            ],
            'permissao' => [
                'status' => $permissao,
                'msg' => 'Permissão de acesso ao Inside Sales'
            ],
            'ativo' => [
                'status' => $ativo,
                'msg' => 'Vendedor ativo'
            ],
        ];
    }

    public function canUseInsideSales() {
        $regras = $this->getInsideSalesRules();

        foreach ($regras as $regra) {
            if (!$regra['status']) {
                return false;
            }
        }
        return true;
    }
}
