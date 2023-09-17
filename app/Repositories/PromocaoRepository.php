<?php

namespace App\Repositories;

use App\Models\Promocao;
use InfyOm\Generator\Common\BaseRepository;

class PromocaoRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'nome',
        'dt_inicio',
        'dt_termino',
        'cumulativo',
        'ativo',
        'tipo_desconto',
        'desconto'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Promocao::class;
    }
}
