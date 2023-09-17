<?php
/**
 * Created by PhpStorm.
 * User: lifepet
 * Date: 14/08/17
 * Time: 15:16
 */

namespace App\Repositories;


use InfyOm\Generator\Common\BaseRepository;

abstract class LifepetRepository extends BaseRepository
{
    public function count(array $where = Array(), $columns = '*')
    {
        $this->applyCriteria();
        $this->applyScope();

        $results = $this->model->count();

        $this->resetModel();

        return $results;
    }
}