<?php

namespace App\Repositories;

use App\Models\Pets;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PetsRepository
 * @package App\Repositories
 * @version August 14, 2017, 5:04 pm UTC
 *
 * @method Pets findWithoutFail($id, $columns = ['*'])
 * @method Pets find($id, $columns = ['*'])
 * @method Pets first($columns = ['*'])
*/
class PetsRepository extends LifepetRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'nome_pet' => 'like',
        'numero_microchip' => 'like',
        'id' => '=',
        'cliente.nome_cliente' => 'like',
        'observacoes' => 'like'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Pets::class;
    }
}