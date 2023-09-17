<?php
/**
 * Created by PhpStorm.
 * User: criacao
 * Date: 15/03/18
 * Time: 11:28
 */

namespace App\Http\Util;


class LogMessages
{
    const EVENTO = [
        'ALTERACAO' => "Alteração",
        'CRIACAO' => "Criação",
        'EXCLUSAO' => "Exclusão",
        'NOTICIA'  => "Notícia",
        'NOTIFICACAO'  => "Notificação"
    ];

    const IMPORTANCIA = [
        'ALTA'  => 'ALTA',
        'MEDIA' => 'MÉDIA',
        'BAIXA' => 'BAIXA'
    ];

    /**
     * Busca nas constantes, apenas.
     * @param $name
     * @return string
     */
    public function __get($name)
    {
        list($const, $index) = explode('_', $name, 2);
        $const = strtoupper($const);
        $index = strtoupper($index);
        $c = constant(self::class . "::$const");
        if(isset($c)) {

            if(isset($c[$index])) {
                return $c[$index];
            }
        }
    }
}