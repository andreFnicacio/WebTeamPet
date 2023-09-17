<?php

namespace App\Http\Middleware;

use Closure;

class IsCorretorActivated {

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
            if(!$corretor->aprovado) {
                self::errors(["O seu cadastro ainda não foi aprovado. Aguarde mais um pouco ou entre em contato com a Lifepet."]);
                return redirect()->route('getLogout');
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
