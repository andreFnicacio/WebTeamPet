<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
      
        $this->redirectTo = url()->previous();
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            AppBaseController::setInfo('Você realizou muitas tentativas de login. Por favor, aguarde alguns minutos e retorne mais tarde.');
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            $cliente = \App\Models\Clientes::where('id_usuario', auth()->user()->id)->first();
            if($cliente) {
                if($cliente->primeiro_acesso) {
                    // $cliente->primeiro_acesso = 0;
                    // $cliente->update();
                    return view('clientes.mudar_senha')->with([
                        'primeiroAcesso' => true,
                        'email' => auth()->user()->email
                    ]);
                }

                return view('area_cliente.v2.home')->with(compact('cliente'));
            }
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);
        AppBaseController::setError('Email ou senha não estão corretos. Tente novamente.');
        return $this->sendFailedLoginResponse($request);
    }
}