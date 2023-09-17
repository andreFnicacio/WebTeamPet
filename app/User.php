<?php

namespace App;

use App\Models\Clientes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Mail;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use Notifiable;
    use EntrustUserTrait;
    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function sendPasswordResetNotification($token)
    {

        $to      = $this->email;
        //$to      = "alexandre.moreira@lifepet.com.br";
        $subject = 'Recuperação de senha.';
        $data = [
            'token' => $token,
            'name' => $this->name,
            'email' => $this->email
        ];
        // $view  = view('mail.recuperar_senha')->with($data);
        // $message = $view->render();
        // $headers = 'From: Lifepet <auditoria@lifepet.com.br>' . "\r\n" .
        //     "Reply-To: auditoria@lifepet.com.br \r\n";
        // $headers .= "MIME-Version: 1.0\r\n";
        // $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n" .
        //     'X-Mailer: PHP/' . phpversion();

        // mail($to, $subject, $message, $headers);

        Mail::send('mail.recuperar_senha', $data, function(Mail $message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }

    /**
     * @return User|\Illuminate\Database\Eloquent\Model|null
     */
    public static function automacao()
    {
        return self::where('name', 'AUTOMAÇÃO')->first();
    }

    public function cliente() {
        return $this->hasOne(\App\Models\Clientes::class, 'id_usuario', 'id');
    }

    public function clinica() {
        return $this->hasOne(\Modules\Clinics\Entities\Clinicas::class, 'id_usuario', 'id');
    }

    public static function associateCustomer(Clientes $customer)
    {
        $user = self::where('email', $customer->email)->first();

        if ($user) {
            // Disable event listener during user association to avoid customer update on financial service
            Clientes::unsetEventDispatcher();
            $customer->id_usuario = $user->id;
            $customer->save();
        }
    }
}
