<?php

namespace App\Repositories;

use App\Models\Parametros;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ParametrosRepository
 * @package App\Repositories
 * @version January 9, 2019, 10:15 am -02
 *
 * @method Parametros findWithoutFail($id, $columns = ['*'])
 * @method Parametros find($id, $columns = ['*'])
 * @method Parametros first($columns = ['*'])
*/
class ParametrosRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'chave',
        'valor',
        'tipo',
        'descricao'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Parametros::class;
    }
}
