<?php

namespace App\Http\Middleware;

use Closure;

class PowerBIAccess {

    private $token = '$2a$10$VNG49K9dQrmh2MKBekMP/ulZrHl6ZM6.zI8uQ37b5an4P6A2NHsBO';
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
       
        if($this->token != $request->header( 'pbi_authorization' )) {
            return response()->json(['Acesso negado'], 401);
        }
        
        return $next($request);
    }
    
    
}
