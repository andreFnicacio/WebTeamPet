<?php

namespace App\Repositories;

use App\Models\Procedimentos;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ProcedimentosRepository
 * @package App\Repositories
 * @version August 17, 2017, 8:07 pm UTC
 *
 * @method Procedimentos findWithoutFail($id, $columns = ['*'])
 * @method Procedimentos find($id, $columns = ['*'])
 * @method Procedimentos first($columns = ['*'])
*/
class ProcedimentosRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'cod_procedimento',
        'nome_procedimento',
        'especialista',
        'intervalo_usos',
        'valor_base',
        'id_grupo'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Procedimentos::class;
    }
}