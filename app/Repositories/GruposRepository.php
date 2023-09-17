<?php

namespace App\Repositories;

use App\Models\Grupos;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class GruposRepository
 * @package App\Repositories
 * @version August 17, 2017, 8:15 pm UTC
 *
 * @method Grupos findWithoutFail($id, $columns = ['*'])
 * @method Grupos find($id, $columns = ['*'])
 * @method Grupos first($columns = ['*'])
*/
class GruposRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'nome_grupo'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Grupos::class;
    }
}