<?php

namespace App\Repositories;

use App\Models\Urh;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class UrhRepository
 * @package App\Repositories
 * @version March 25, 2019, 12:12 pm -03
 *
 * @method Urh findWithoutFail($id, $columns = ['*'])
 * @method Urh find($id, $columns = ['*'])
 * @method Urh first($columns = ['*'])
*/
class UrhRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'valor_urh',
        'data_validade',
        'ativo'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Urh::class;
    }
}
