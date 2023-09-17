<?php

namespace App\Repositories;

use App\Models\TabelasReferencia;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TabelasReferenciaRepository
 * @package App\Repositories
 * @version August 17, 2017, 6:08 pm UTC
 *
 * @method TabelasReferencia findWithoutFail($id, $columns = ['*'])
 * @method TabelasReferencia find($id, $columns = ['*'])
 * @method TabelasReferencia first($columns = ['*'])
*/
class TabelasReferenciaRepository extends BaseRepository
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
        return TabelasReferencia::class;
    }
}
