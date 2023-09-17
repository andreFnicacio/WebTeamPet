<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\PetsGrupos;

class PetsGruposController extends AppBaseController
{
    public function delete(Request $request, $id)
    {
        try {
            PetsGrupos::find($id)->delete();
            self::setSuccess('Exceção de grupo deletada com sucesso');

        } catch (\Throwable $throwable) {
            self::setError('Erro na deleção');
        }
        return back();
    }
}
