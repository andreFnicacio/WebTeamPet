<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\ExtensaoProcedimento;

class ExtensaoProcedimentoController extends AppBaseController
{
    public function delete(Request $request, $id)
    {
        try {
            ExtensaoProcedimento::find($id)->delete();
            self::setSuccess('Exceção de cobertura deletada com sucesso');

        } catch (\Throwable $throwable) {
            self::setError('Erro na deleção');
        }
        return back();
    }
}
