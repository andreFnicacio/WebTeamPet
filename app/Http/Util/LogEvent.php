<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 13/11/2020
 * Time: 15:52
 */

namespace App\Http\Util;


class LogEvent
{
    const UPDATE = "Alteração";
    const CREATE = "Criação";
    const DELETE = "Exclusão";
    const NOTICE = "Notícia";
    const NOTIFY = "Notificação";
    const WARNING = "Alerta";
    const ERROR = "Erro";
}