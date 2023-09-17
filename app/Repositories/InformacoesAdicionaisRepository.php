<?php

namespace App\Repositories;

use App\Models\InformacoesAdicionais;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class InformacoesAdicionaisRepository
 * @package App\Repositories
 * @version May 24, 2018, 5:05 pm BRT
 *
 * @method InformacoesAdicionais findWithoutFail($id, $columns = ['*'])
 * @method InformacoesAdicionais find($id, $columns = ['*'])
 * @method InformacoesAdicionais first($columns = ['*'])
*/
class InformacoesAdicionaisRepository extends LifepetRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'cor',
        'descricao_resumida',
        'descricao_completa',
        'icone',
        'prioridade'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return InformacoesAdicionais::class;
    }
}
