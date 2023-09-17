<?php

namespace App\Repositories;

use App\Models\Permission;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PermissionRepository
 * @package App\Repositories
 * @version November 8, 2017, 3:06 pm -02
 *
 * @method Permission findWithoutFail($id, $columns = ['*'])
 * @method Permission find($id, $columns = ['*'])
 * @method Permission first($columns = ['*'])
*/
class PermissionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'display_name',
        'menu',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Permission::class;
    }
}