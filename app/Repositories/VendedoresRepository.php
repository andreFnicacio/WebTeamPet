<?php

namespace App\Repositories;

use App\Models\Vendedores;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class VendedoresRepository
 * @package App\Repositories
 * @version August 21, 2017, 8:09 pm UTC
 *
 * @method Vendedores findWithoutFail($id, $columns = ['*'])
 * @method Vendedores find($id, $columns = ['*'])
 * @method Vendedores first($columns = ['*'])
*/
class VendedoresRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Vendedores::class;
    }
}
