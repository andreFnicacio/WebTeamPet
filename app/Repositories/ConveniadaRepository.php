<?php

namespace App\Repositories;

use App\Models\Conveniada;
use InfyOm\Generator\Common\BaseRepository;

class ConveniadaRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'nome',
        'contato',
        'email',
        'telefone',
        'ativo',
        'tipo_desconto',
        'desconto'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Conveniada::class;
    }
}
