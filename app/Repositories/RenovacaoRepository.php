<?php

namespace App\Repositories;

use App\Models\Renovacao;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class RenovacaoRepository
 * @package App\Repositories
 * @version December 16, 2020, 8:01 pm -02
 *
 * @method Renovacao findWithoutFail($id, $columns = ['*'])
 * @method Renovacao find($id, $columns = ['*'])
 * @method Renovacao first($columns = ['*'])
*/
class RenovacaoRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id_cliente',
        'id_pet',
        'id_plano',
        'status',
        'id_link_pagamento',
        'paid_at',
        'regime'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Renovacao::class;
    }
}
