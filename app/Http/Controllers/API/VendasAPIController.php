<?php
/**
 * Created by PhpStorm.
 * User: criacao
 * Date: 12/03/18
 * Time: 14:54
 */

namespace App\Http\Controllers\API;


use App\Http\Util\CurlTrait;
use App\Http\Util\Superlogica\Curl;
use App\Models\VendasDivulgadores;

class VendasAPIController extends AppBaseController
{
    use CurlTrait;

    const VENDAS_URL = "https://www.lifepet.com.br/vendas/api/";

    public function __construct()
    {
        parent::__construct();

        $this->ch = curl_init();
    }

    public function checarVendedor($codigo) {
        $this->init();
        $this->getDefaults(self::VENDAS_URL . '/vendedor/' . $codigo . '/checar');
        return $this->execute();
    }

    public static function registrarVenda($codigo, $codigoExterno, $emailCliente)
    {
        $vd = VendasDivulgadores::create([
            'id_externo' => $codigoExterno ?: 0,
            'codigo' => $codigo,
            'email' => $emailCliente
        ]);

        return $vd;
    }

    public function vendas($codigo) {
        return VendasDivulgadores::where('codigo_vendedor', $codigo)->get();
    }
}