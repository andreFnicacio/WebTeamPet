<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Contracts\Validation\Validator as Validator2;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/complementar';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return Validator2
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);
        self::notifyLifepet($data);
        return $user;
    }
    
    public static function notifyLifepet($data) {
//        $to      = "alexandre.moreira@lifepet.com.br";
//        $subject = 'Novo divulgador cadastrado!';
//        $message = "O divulgador '" . $data['name'] . "' acabou de se cadastrar com o email '" . 
//                    $data['email'] . "'";
//        $headers = 'From: Lifepet <alexandre.moreira@lifepet.com.br>' . "\r\n" .
//                   'Reply-To: cadastro@lifepet.com.br' . "\r\n" .
//                   'Cc: Thiago Batista <thiago@lifepet.com.br>' . "\r\n";
//        $headers .= "MIME-Version: 1.0\r\n";
//        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n" .
//                    'X-Mailer: PHP/' . phpversion();
//
//        mail($to, $subject, $message, $headers);
    }
}