<?php

namespace App\Repositories;

use App\Models\Pagamentos;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PagamentosRepository
 * @package App\Repositories
 * @version September 20, 2017, 5:24 pm BRT
 *
 * @method Pagamentos findWithoutFail($id, $columns = ['*'])
 * @method Pagamentos find($id, $columns = ['*'])
 * @method Pagamentos first($columns = ['*'])
*/
class PagamentosRepository extends LifepetRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id_cobranca',
        'data_pagamento',
        'complemento',
        'forma_pagamento',
        'valor_pago'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Pagamentos::class;
    }
}