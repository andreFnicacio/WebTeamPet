<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento
 * Date: 02/04/19
 * Time: 18:29
 */

namespace App\Exceptions;


use Throwable;

class ClienteInadimplenteException extends \Exception
{
    const MESSAGE = "Esse cliente não poderá emitir guias. Peça para entrar em contato das 9h as 18h pelo chat em nosso site www.lifepet.com.br. Ao entrar em contato, informe o Erro FI-01.";
    public function __construct($message = self::MESSAGE, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}