<?php

namespace App\Http\Middleware;

use Closure;

class HasCorretorData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        if($user) {
            $corretor = \App\Models\Corretor::where('user_id', $user->id)->first();
            if(empty($corretor)) {
                return redirect()->route('complementos.dados');
            }
            
            $verificacaoDocumentos = \App\Http\Controllers\DocumentoController::hasRequiredDocuments($corretor);
            if(!$verificacaoDocumentos['status']) {
                self::errors($verificacaoDocumentos['errors']);
                return redirect(route('complementos.documentos'))->with(compact('corretor'));
            }
        }
        
        return $next($request);
    }
    
    private static function errors(array $errors = []) {
        $message = "";
        foreach ($errors as &$error) {
            $message .= $error . "<br/>";
        }

        \App\Http\Controllers\Controller::setMessage($message, 'warning', 'Oops!');
    }
}
