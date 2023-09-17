<?php

namespace App;

use App\Http\Util\CurlTrait;
use App\Models\Clientes;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

/**
 * Class LinkPagamento
 * @package App
 * @property Clientes $cliente
 * @property string descricao
 * @property int parcelas
 * @property float valor
 * @property Carbon expires_at
 * @property string tags
 * @property string status
 * @property string hash
 * @property int id
 */

class LinkPagamento extends Model
{

    const STATUS_ABERTO = 'ABERTO';
    const STATUS_CANCELADO = 'CANCELADO';
    const STATUS_PAGO = 'PAGO';

    protected $table = 'links_pagamento';

    protected $fillable = [
        'id_cliente',
        'valor',
        'parcelas',
        'expires_at',
        'tags',
        'descricao',
        'status',
        'hash',
        'callback_url'
    ];

    protected $dates = ['created_at', 'updated_at', 'expires_at'];

    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'id_cliente');
    }

    public function setExpiresAtAttribute($value)
    {
        $this->attributes['expires_at'] = Carbon::createFromFormat('d/m/Y', $value);
    }

    public function dispatch()
    {
        if($this->callback_url) {
            try {
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $this->callback_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                curl_setopt($ch,CURLOPT_CUSTOMREQUEST, "POST");

                $headers = array(
                    "Content-Type: application/x-www-form-urlencoded",
                );

                curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);

                $result = curl_exec($ch);
                $result = json_decode($result);

                return ['status' => true, 'data' => $result];
            } catch (\Exception $e) {
                return ['status' => false, 'data' => $e];
            }
        }
    }

    public function link()
    {
        return route('links-pagamento.form-pagamento', ['hash' => $this->hash]);
    }

    public static function createForRenovacao(array $input)
    {
        $v = Validator::make($input, [
            'id_cliente' => 'required|exists:clientes,id',
            'valor' => 'required|min:1|numeric',
            'parcelas' => 'required|min:1|max:12|numeric',
            'tags' => 'required|array',
            'descricao' => 'required',
            'expires_at' => 'required|date_format:d/m/Y|after:today'
        ]);

        if ($v->fails()) {
            $messages = join("\n", $v->getMessageBag()->all());
            throw new \Exception("Não foi possível criar o link de pagamento.\n" . $messages);
        }

        $input['hash'] = md5(Carbon::now()->format('dmYhis') . $input['id_cliente']);
        $input['status'] = LinkPagamento::STATUS_ABERTO;
        $input['tags'] = join(';', $input['tags']);

        $link = LinkPagamento::create($input);
        return $link;
    }

    public function getIndisponivelAttribute()
    {
        if($this->status === 'ABERTO') {
            return true;
        }

        return false;
    }

    public function invalidar()
    {
        if($this->status !== 'PAGO') {
            $this->status = self::STATUS_CANCELADO;
            $this->save();
        }
    }
}
