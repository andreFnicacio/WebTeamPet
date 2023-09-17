<?php

namespace App\Repositories;

use InfyOm\Generator\Common\BaseRepository;
use Modules\Clinics\Entities\Clinicas;

/**
 * Class ClinicasRepository
 * @package App\Repositories
 * @version August 21, 2017, 8:09 pm UTC
 *
 * @method Clinicas findWithoutFail($id, $columns = ['*'])
 * @method Clinicas find($id, $columns = ['*'])
 * @method Clinicas first($columns = ['*'])
*/
class ClinicasRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tipo_pessoa',
        'cpf_cnpj',
        'nome_clinica',
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
        'id_tabela'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Clinicas::class;
    }
}
