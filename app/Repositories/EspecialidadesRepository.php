<?php

namespace App\Repositories;

use App\Models\Especialidades;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EspecialidadesRepository
 * @package App\Repositories
 * @version August 7, 2017, 5:32 pm UTC
 *
 * @method Especialidades findWithoutFail($id, $columns = ['*'])
 * @method Especialidades find($id, $columns = ['*'])
 * @method Especialidades first($columns = ['*'])
*/
class EspecialidadesRepository extends LifepetRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'nome'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Especialidades::class;
    }
}