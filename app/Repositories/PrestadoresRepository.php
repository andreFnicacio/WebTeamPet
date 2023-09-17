<?php

namespace App\Repositories;

use InfyOm\Generator\Common\BaseRepository;
use Modules\Veterinaries\Entities\Prestadores;

/**
 * Class PrestadoresRepository
 * @package App\Repositories
 * @version August 22, 2017, 1:32 pm UTC
 *
 * @method Prestadores findWithoutFail($id, $columns = ['*'])
 * @method Prestadores find($id, $columns = ['*'])
 * @method Prestadores first($columns = ['*'])
*/
class PrestadoresRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id_clinica',
        'tipo_pessoa',
        'nome',
        'email',
        'telefone',
        'crmv',
        'especialista',
        'id_especialidade'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Prestadores::class;
    }
}