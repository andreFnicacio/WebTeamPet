<?php

namespace App\Repositories;

use App\Models\Ocorrencias;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class OcorrenciasRepository
 * @package App\Repositories
 * @version April 9, 2019, 2:42 pm -03
 *
 * @method Ocorrencias findWithoutFail($id, $columns = ['*'])
 * @method Ocorrencias find($id, $columns = ['*'])
 * @method Ocorrencias first($columns = ['*'])
*/
class OcorrenciasRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tipo',
        'assunto',
        'descricao',
        'data_ocorrencia',
        'id_colaborador'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Ocorrencias::class;
    }
}
