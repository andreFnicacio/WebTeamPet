<?php

namespace App;

use App\Models\LPTCodigosPromocionais;
use App\Models\Planos;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class LifepetCompraRapida
 * @package App
 * @property $nome
 * @property $email
 * @property $celular
 * @property $cpf
 * @property $hash
 * @property $pets
 * @property $concluido
 * @property $cep
 * @property $rua
 * @property $id_plano
 * @property array $pagamentos
 * @property boolean $pagamento_confirmado
 */
class LifepetCompraRapida extends Model
{
    protected $table = 'lifepet_compra_rapida';

    protected $fillable = [
        'nome',
        'email',
        'celular',
        'cpf',
        'hash',
        'pets',
        'cep',
        'rua',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'id_plano',
        'regime',
        'id_cupom',
    ];

    public function adapt(Request $request) {
        $input = [
            'nome' => $request->get('name'),
            'email' => $request->get('email'),
            'celular' => $request->get('celular'),
            'cpf' => $request->get('cpf'),
            'hash' => md5($request->get('cpf') . Carbon::now()->format('Ymdh')),
            'pets' => $request->get('amount'),
            'cep' => $request->get('cep'),
            'numero' => $request->get('address_number'),
            'rua' => $request->get('street'),
            'bairro' => $request->get('neighbourhood'),
            'cidade' => $request->get('city'),
            'estado' => $request->get('state'),
            'id_plano' => $request->get('id_plano'),
            'regime' => $request->get('regime', 'MENSAL')
        ];

        $this->fill($input);
    }

    public function setPagamentos($pagamentos)
    {
        if(!$this->pagamentos) {
            $p = [];
        } else {
            $p = json_decode($this->pagamentos);
        }

        $p[] = $pagamentos;
        $this->pagamentos = json_encode($p);

        return $p;
    }

    public function addTentativa()
    {
        if(!$this->tentativas) {
            $t = [];
        } else {
            $t = json_decode($this->tentativas);
        }

        $t[] = Carbon::now()->format('Y-m-d H:i:s');
        $this->tentativas = json_encode($t);

        return $t;
    }

    public function scopeConfirmado($query)
    {
        return $query->where('pagamento_confirmado', 1);
    }

    public function scopeConcluido($query)
    {
        return $query->where('concluido', 1);
    }

    public function cupom()
    {
        return $this->belongsTo(LPTCodigosPromocionais::class, 'id_cupom');
    }

    public function plano()
    {
        return $this->belongsTo(Planos::class, 'id_plano');
    }
}
