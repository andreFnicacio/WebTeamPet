<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento
 * Date: 29/03/19
 * Time: 15:57
 */

namespace App\Helpers;


use Entrust;
use Illuminate\Http\Request;

class Permissions
{
    public static function podeEmitirGuiaAdministrativa()
    {
        return Entrust::hasRole(['ADMINISTRADOR', 'AUDITORIA', 'AUTORIZADOR', 'GERENCIAL']);
    }

    public static function podeEmitirGuiaRetroativa()
    {
        return Entrust::hasRole(['ADMINISTRADOR', 'AUDITORIA']);
    }
}