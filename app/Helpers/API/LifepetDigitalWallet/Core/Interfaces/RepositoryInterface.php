<?php
/**
 * Created by VS Code.
 * User: Eric Moraes
 * Date: 07/08/20
 * Time: 16:21
 */


namespace App\Helpers\API\LifepetDigitalWallet\Core\Interfaces;

interface Repository
{
    public function list();
    public function getById($id);
    public function delete();
}