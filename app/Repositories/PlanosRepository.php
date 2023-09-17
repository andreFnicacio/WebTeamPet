<?php

namespace App\Repositories;

use App\Models\Planos;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PlanosRepository
 * @package App\Repositories
 * @version August 22, 2017, 8:25 pm UTC
 *
 * @method Planos findWithoutFail($id, $columns = ['*'])
 * @method Planos find($id, $columns = ['*'])
 * @method Planos first($columns = ['*'])
*/
class PlanosRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'nome_plano',
        'preco_plano_familiar',
        'preco_plano_individual',
        'data_vigencia',
        'data_inatividade',
        'ativo'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Planos::class;
    }
}