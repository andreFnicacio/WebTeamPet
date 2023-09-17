<?php
/**
 * Created by PhpStorm.
 * User: usuario
 * Date: 18/12/2017
 * Time: 15:06
 */

namespace App\Http\Controllers;


use Entrust;
use Illuminate\Support\Facades\Validator;

class PlanosCredenciadosController extends AppBaseController
{
    const CREDENCIADO = 'CREDENCIADO';
    const PLANO = 'PLANO';

    public static function habilitacao(\Illuminate\Http\Request $request) {
        if(!Entrust::can('habilitar_credenciado_plano')) {
            die(500);
        }

        $v = Validator::make($request->all(), [
            'id_clinica' => 'required|exists:clinicas,id',
            'id_plano' => 'required|exists:planos,id',
        ]);

        if(!$request->filled('habilitado')) {
            $habilitado = 0;
        } else {
            $habilitado = $request->get('habilitado');
        }
        $idPlano = $request->get('id_plano');
        $idClinica = $request->get('id_clinica');

        $pc = \App\Models\PlanosCredenciados::where('id_plano', $idPlano)
                                      ->where('id_clinica', $idClinica)->first();
        if($pc) {
            $pc->habilitado = $habilitado;
            $pc->update();
            return [
                'status' => true
            ];
        } else {
            $pc = \App\Models\PlanosCredenciados::create([
                'id_plano' => $idPlano,
                'id_clinica' => $idClinica,
                'habilitado' => $habilitado
            ]);

            return [
                'status' => true
            ];
        }
    }

    public static function inicializar($tipo, $id) {
        $tipo = strtoupper($tipo);
        if($tipo === self::CREDENCIADO) {
            $planos = \App\Models\Planos::all();
            $credenciado = \Modules\Clinics\Entities\Clinicas::findOrFail($id);
            foreach($planos as $p) {
                if(!\App\Models\PlanosCredenciados::where('id_plano', $p->id)
                                                  ->where('id_clinica', $credenciado->id)
                                                  ->exists())
                $pc = \App\Models\PlanosCredenciados::create([
                    'id_clinica' => $credenciado->id,
                    'id_plano' => $p->id,
                    'habilitado' => 1
                ]);
            }
        } else if($tipo === self::PLANO) {
            $credenciados = \Modules\Clinics\Entities\Clinicas::all();
            $p = \App\Models\Planos::findOrFail($id);
            foreach ($credenciados as $c) {
                if(!\App\Models\PlanosCredenciados::where('id_plano', $p->id)
                    ->where('id_clinica', $c->id)
                    ->exists())     {
                    $pc = \App\Models\PlanosCredenciados::create([
                        'id_clinica' => $c->id,
                        'id_plano' => $p->id,
                        'habilitado' => 1
                    ]);
                }
            }
        }
    }
}