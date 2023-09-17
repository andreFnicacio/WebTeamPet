<?php
/**
 * Created by PhpStorm.
 * User: criacao
 * Date: 15/03/18
 * Time: 14:08
 */

namespace App\Http\Util;


use App\Models\Log;
use Carbon\Carbon;

class Logger
{
    private $area;
    private $tabela_relacionada;
    private $executor;

    public function __construct($area = null, $tabela_relacionada = null, $executor = null)
    {
        $this->area = $area;
        $this->$tabela_relacionada = $tabela_relacionada;
        $this->$executor = $executor;
    }

    public function register($evento, $importancia, $mensagem, $id_relacional = null, $tabela_relacionada = null, $executor = null, $area = null)
    {
        $l = Log::create([
            'evento' => $evento,
            'importancia' => $importancia,
            'mensagem' => $mensagem,
            'id_relacional' => $id_relacional,
            'created_at' => new Carbon(),
            'tabela_relacionada' => $this->tabela_relacionada ?: $tabela_relacionada,
            'executor' => $this->executor ?: $executor,
            'area' => $this->area ?: $area,
        ]);
    }

    public static function log($evento, $area, $importancia, $mensagem,
                               $executor = null, $tabela_relacionada = null, $id_relacional = null)
    {
        $l = Log::create([
            'evento' => $evento,
            'area' => $area,
            'importancia' => $importancia,
            'mensagem' => $mensagem,
            'executor' => $executor,
            'tabela_relacionada' => $tabela_relacionada,
            'id_relacional' => $id_relacional,
            'created_at' => new Carbon()
        ]);
    }
}