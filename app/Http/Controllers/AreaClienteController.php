<?php

namespace App\Http\Controllers;

use App\Helpers\API\MailChimp\MailChimp;
use App\Http\Util\Logger;
use App\Http\Util\LogMessages;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Modules\Guides\Entities\HistoricoUso;

class AreaClienteController extends AppBaseController
{

    const ENVIAR_EMAIL_BOAS_VINDAS = true;
    public static $emailBoasVindasInsideSales = false;


    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    private function cliente()
    {
        $user = auth()->user();
        $cliente = \App\Models\Clientes::where('id_usuario', $user->id)->first();
        return $cliente;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $c = new HomeController();
        return $c->index();
    }

    public function dados()
    {
        $cliente = $this->cliente();
        return view('area_cliente.v2.dados', [
            'cliente' => $cliente,
            'ufs' => self::$ufs
        ]);
    }

    public function financeiro()
    {
        $cliente = $this->cliente();
        return view('area_cliente.v2.financeiro', [
            'cliente' => $cliente    
        ]);
    }

    public function pets()
    {
        $cliente = $this->cliente();
        return view('area_cliente.v2.pets', [
            'cliente' => $cliente    
        ]);
    }

    public function pet($id) {
        $cliente = $this->cliente();
        if(!$cliente) {
            self::setError('Acesso negado ao pet. Cliente não encontrado.');
            return back();
        }

        $pet = \App\Models\Pets::find($id);
        if(!$pet) {
            self::setError('Pet não encontrado.');
            return back();   
        }

        if($pet->id_cliente !== $cliente->id) {
            self::setError('O pet não pertence ao tutor atual.');
            return back();   
        }

        return view('area_cliente.v2.pet')->with('pets', $pet);
    }

    public function documentos()
    {
        $cliente = $this->cliente();
        return view('area_cliente.v2.documentos', [
            'cliente' => $cliente    
        ]);
    }

    public function contrato()
    {
        $file = file_get_contents(public_path('minuta_contrato.pdf'));
        if(!$file) {
            abort(404);
        }

        return (new \Illuminate\Http\Response($file, 200))
            ->header('Content-Type', 'application/pdf');
    }


    public function doLogin(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            $cliente = \App\Models\Clientes::where('id_usuario', auth()->user()->id)->first();
            if($cliente) {
                return redirect(route('cliente.home'));
            } else {
                self::setError('Cliente não vinculado. Entre em contato com a lifepet', 'Erro.');
                return $this->sendFailedLoginResponse($request);
            }
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    private function existentUser(Request $request)
    {
        $rawCpf = $request->get('cpf');
        $email = $request->get('email');
        //Filtra o campo de CPF
        $cpf = preg_replace("/(\.|\-|\,|\_|\*)/", "", $rawCpf);

        /**
         * Checa se existe um cliente com essas credenciais.
         * Verifica variações de CPF combinados com o email
         */
        $queryCliente = \App\Models\Clientes::where(function($query) use ($rawCpf, $cpf) {
            $query->where('cpf', $rawCpf)
                  ->orWhere('cpf', $cpf);
        })->where('email', $email);
        $sql = $queryCliente->toSql();
        $cliente = $queryCliente->first();

        if(!$cliente) {
            self::setError('Não foi encontrado um cadastro de cliente com o email e o CPF informado. Por favor, entre em contato com o atendimento para verificar o seu cadastro e atualizar suas informações.', 'Erro');
            return false;
        }

        $user = null;
        if(!$cliente->id_usuario) {
            //Cria o usuário e vincula com o cliente.
            $user = \App\User::create([
                'name'      => $request->get('name'),
                'email'     => $request->get('email'),
                'password'  => Hash::make($request->get('password'))
            ]);
            $cliente->id_usuario = $user->id;
            $user->attachRole(\App\Models\Role::where('name', 'CLIENTE')->first());
        } else {
            //No caso de um primeiro acesso, modifica a senha e retorna o usuário.

            $user = \App\User::find($cliente->id_usuario);
            if($cliente->primeiro_acesso) {
                $user->password = Hash::make($request->get('password'));
                $user->update();
                $cliente->primeiro_acesso = 0;
                $cliente->update();
            }
        }
        return $user;
    }

    public function registrar(Request $request)
    {
        $v = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required',
            'cpf' => 'required',
            'password' => 'required|min:6|confirmed',
            'email' => 'required|email',
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors());
        }

        $user = $this->existentUser($request);
        if(!$user) {
            return redirect()->back();
        }

        $this->guard()->login($user);

        return redirect(route('cliente.home'));
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect('/cliente/login');
    }

    /**
     * Cria uma senha padrão
     * Primeira letra do nome (maiuscula) + primeira letra do segundo nome (maiuscula) + 5 primeiros digitos do CPF
     *
     * @param $idCliente
     * @return string
     */
    public function getSenhaPadrao($idCliente)
    {
        $cliente = \App\Models\Clientes::findOrFail($idCliente);

        $names = explode(' ', $cliente->nome_cliente);
        if(count($names) > 1) {
            $prefix = $names[0][0] . $names[1][0];
            $prefix = strtoupper($prefix);
        } else {
            $prefix = strtoupper(substr($names[0], 0, 2));
        }
        $suffix = substr($cliente->cpf, 0, 5);
        $suffix = preg_replace("/(\.|\-|\,|\_|\*)/", "", $suffix);

        return $prefix . $suffix;
    }

    public static function doCreateUser($id)
    {

        if(!\Entrust::can('criar_usuario_cliente')) {
            return self::notAllowed();
        }

        $cliente = \App\Models\Clientes::findOrFail($id);

        if($cliente->id_usuario || \App\User::where('email', $cliente->email)->first()) {
            self::toast('Usuário já existente. Verificar cadastro.');
            return redirect()->back();
        }

        $senhaPadrao = (new AreaClienteController)->getSenhaPadrao($cliente->id);

        $user = (new \App\User)->create([
            'name'      => $cliente->nome_cliente,
            'email'     => $cliente->email,
            'password'  => Hash::make($senhaPadrao)
        ]);
        $cliente->id_usuario = $user->id;
        $user->attachRole((new \App\Models\Role)->where('name', 'CLIENTE')->first());

        if (self::$emailBoasVindasInsideSales) {
            MailChimp::novoClienteInsideSales($cliente, $senhaPadrao);
        } elseif(self::ENVIAR_EMAIL_BOAS_VINDAS) {
            MailChimp::novoCliente($cliente->email, $senhaPadrao);
        }

        return $cliente->update();
    }

    /**
     * Permite a criação de um usuário do tipo CLIENTE com acesso à área do cliente
     * @param $id
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function criarUsuario($id) {
        self::doCreateUser($id);

        self::toast('Usuário criado com ', 'SUCESSO');
        return redirect()->back();
    }

    /**
     * Retorna a senha do cliente para a padrão:
     */
    public function resetarSenhaCliente($idCliente)
    {
        $cliente = \App\Models\Clientes::findOrFail($idCliente);
        $senha = $this->getSenhaPadrao($idCliente);
        $hashSenha = Hash::make($senha);

        if($cliente->id_usuario) {
            $user = \App\User::find($cliente->id_usuario);
            $user->password = $hashSenha;
            $user->update();
            Logger::log(LogMessages::EVENTO['ALTERACAO'], 'Clientes',
                        'ALTA', 'A senha do cliente foi restaurada para o padrão.',
                        auth()->user()->id, 'clientes', $cliente->id);

            self::setSuccess('A senha do cliente foi restaurada');
            return back();
        } else {
            self::setError('O cliente não tem um usuário definido');
            return back();
        }
    }

    public function encaminhamentos()
    {
        $cliente = $this->cliente();

        return view('area_cliente.v2.encaminhamentos', [
            'cliente' => $cliente,
            'encaminhamentos' => $cliente->encaminhamentos()->map(function($e) {
                $e->credenciado = ' - ';
                if($e->id_solicitador !== $e->id_clinica) {
                    $e->credenciado = $e->clinica()->first()->nome_clinica;
                }

                return $e;
            })
        ]);
    }

    public function escolherCredenciado(Request $request)
    {
        $v = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'numero_guia' => 'required',
            'id_clinica' => 'exists:clinicas,id',
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors());
        }

        $guias = \Modules\Guides\Entities\HistoricoUso::where('numero_guia', $request->get('numero_guia'))->get();
        foreach($guias as $g) {
            $g->id_clinica = $request->get('id_clinica');
            $g->update();
        }

        self::setSuccess('Sua guia será agendada e liberada em breve. Você receberá a liberação por e-mail.', 'Clínica Modificada');

        return back();
    }

    public function definirCredenciadoEncaminhamento($id)
    {
        return view('area_cliente.v2.definir_credenciado')->with([
            'id_guia' => $id,
            'encaminhamento' => HistoricoUso::find($id)
        ]);
    }

    public function agendarEncaminhamento($id)
    {

    }
}
