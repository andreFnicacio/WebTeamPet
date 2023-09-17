<?php

namespace App\Repositories;

use App\Models\Role;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class RolesRepository
 * @package App\Repositories
 * @version November 8, 2017, 6:43 pm -02
 *
 * @method Roles findWithoutFail($id, $columns = ['*'])
 * @method Roles find($id, $columns = ['*'])
 * @method Roles first($columns = ['*'])
*/
class RolesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'display_name',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Role::class;
    }
}