<?php

namespace App\Repositories;

use App\Models\Cobrancas;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CobrancasRepository
 * @package App\Repositories
 * @version September 20, 2017, 5:24 pm BRT
 *
 * @method Cobrancas findWithoutFail($id, $columns = ['*'])
 * @method Cobrancas find($id, $columns = ['*'])
 * @method Cobrancas first($columns = ['*'])
*/
class CobrancasRepository extends LifepetRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id_cliente',
        'competencia',
        'valor_original',
        'data_vencimento',
        'complemento',
        'status'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Cobrancas::class;
    }
}
