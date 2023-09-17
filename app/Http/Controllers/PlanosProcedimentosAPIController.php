<?php

namespace App\Http\Controllers;

use App\Models\Planos;
use App\Models\PlanosProcedimentos;
use App\Models\Procedimentos;
use Illuminate\Http\Request;

class PlanosProcedimentosAPIController extends AppBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function findByProcedimento($id)
    {
        $procedimento = Procedimentos::find($id);

        $planos = Planos::all();

        $planosProcedimentos = $planos->map(function($p) use ($procedimento) {
            $mapped = new \stdClass();
            $mapped->plano = new \stdClass();
            $mapped->plano->nome_plano = $p->nome_plano;
            $mapped->plano->id = $p->id;
            $pp = PlanosProcedimentos::where('id_plano', $p->id)
                ->where('id_procedimento', $procedimento->id)->first();
            $mapped->vinculado = $mapped->valor_credenciado = $mapped->valor_cliente = null;
            if($pp){

                $mapped->id_vinculo = $pp->id;
                $mapped->vinculado = !is_null($pp);
                $mapped->valor_credenciado = $mapped->valor_credenciado_original = $pp->valor_credenciado;
                $mapped->valor_cliente = $mapped->valor_cliente_original = $pp->valor_cliente;
                $mapped->beneficio_tipo = $pp->beneficio_tipo;
                $mapped->beneficio_valor = $pp->beneficio_valor;
            }

            return $mapped;
        });

        return $planosProcedimentos;
    }

    public function findByVinculo($id)
    {
        $pp = PlanosProcedimentos::findOrFail($id);
        return self::formatJson($pp);
    }

    /**
     * @param Request $request
     */
    public function storeVinculo(Request $request)
    {
        if($request->get('id_planos_procedimentos', null)) {
            return $this->editVinculo($request);
        }

        $pp = new PlanosProcedimentos();
        $pp->fill($request->only([
            'id_plano',
            'id_procedimento',
            'valor_credenciado',
            'valor_cliente',
            'beneficio_tipo',
            'beneficio_valor'
        ]));

        $pp->save();
        return self::formatJson($pp);
    }

    public function editVinculo(Request $request)
    {
        $pp = PlanosProcedimentos::find($request->get('id_planos_procedimentos'));
        $pp->fill($request->only([
            'valor_credenciado',
            'valor_cliente',
            'beneficio_tipo',
            'beneficio_valor'
        ]));

        $pp->update();
        return self::formatJson($pp);
    }

    public function removeVinculo(Request $request)
    {
        $pp = PlanosProcedimentos::find($request->get('id_planos_procedimentos'));
        if($pp) {
            $pp->forceDelete();
        }

        return ['status' => true];
    }


    private static function formatJson(PlanosProcedimentos $pp)
    {
        $plano = $pp->plano()->first();
        $mapped = new \stdClass();
        $mapped->plano = new \stdClass();
        $mapped->plano->nome_plano = $plano->nome_plano;
        $mapped->plano->id = $plano->id;

        $mapped->vinculado = $mapped->valor_credenciado = $mapped->valor_cliente = null;
        if($pp){
            $mapped->id_vinculo = $pp->id;
            $mapped->vinculado = !is_null($pp);
            $mapped->valor_credenciado = $mapped->valor_credenciado_original = $pp->valor_credenciado;
            $mapped->valor_cliente = $mapped->valor_cliente_original = $pp->valor_cliente;
            $mapped->beneficio_tipo = $mapped->beneficio_tipo_original = $pp->beneficio_tipo;
            $mapped->beneficio_valor = $mapped->beneficio_valor_original = $pp->beneficio_valor;
        }

        return collect($mapped);
    }
}
