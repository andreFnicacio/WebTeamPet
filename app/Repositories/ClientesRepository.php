<?php

namespace App\Repositories;

use App\Models\Clientes;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ClientesRepository
 * @package App\Repositories
 * @version August 8, 2017, 2:57 pm UTC
 *
 * @method Clientes findWithoutFail($id, $columns = ['*'])
 * @method Clientes find($id, $columns = ['*'])
 * @method Clientes first($columns = ['*'])
*/
class ClientesRepository extends LifepetRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'nome_cliente' => 'like',
        'cpf' => 'like',
        'email' => 'like',
        'telefone_fixo' => 'like',
        'celular' => 'like'
    ];


    /**
     * Configure the Model
     **/
    public function model()
    {
        return Clientes::class;
    }
}
