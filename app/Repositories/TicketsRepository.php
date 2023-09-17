<?php

namespace App\Repositories;

use App\Models\Tickets;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TicketsRepository
 * @package App\Repositories
 * @version January 10, 2019, 2:53 pm -02
 *
 * @method Tickets findWithoutFail($id, $columns = ['*'])
 * @method Tickets find($id, $columns = ['*'])
 * @method Tickets first($columns = ['*'])
*/
class TicketsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'titulo',
        'descricao',
        'categoria',
        'status',
        'finalizacao',
        'previsao_finalizacao',
        'gravidade',
        'urgencia',
        'tendencia',
        'ordem',
        'solicitante',
        'departamento',
        'atribuicao'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Tickets::class;
    }
}
