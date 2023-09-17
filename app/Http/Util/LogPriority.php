<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 13/11/2020
 * Time: 15:54
 */

namespace App\Http\Util;


class LogPriority
{
    const IMPORTANCIA = [
        'ALTA'  => 'ALTA',
        'MEDIA' => 'MÉDIA',
        'BAIXA' => 'BAIXA'
    ];

    const LOW = "BAIXA";
    const MEDIUM = "MÉDIA";
    const HIGH = "ALTA";
}