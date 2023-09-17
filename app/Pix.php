<?php

namespace App;

use App\Contracts\Dispatchable;
use App\Models\Clientes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Mpdf\QrCode\Output\Png;
use Mpdf\QrCode\QrCode;

/**
 * @property int $id
* @property $order_id
* @property $payment_id
* @property $status
* @property $description
* @property $transaction_id
* @property $qr_code
* @property Carbon $creation_date_qrcode
* @property Carbon $expiration_date_qrcode
* @property $psp_code
* @property $callback_url
* @property $local_description
* @property $id_cliente
 * @property int $amount
 * @property Clientes $cliente
 * @property float $amountAsMoney
 *
 * @method static Builder|Pix paymentId($paymentId)
 * @method static Builder|Pix transactionId($transactionId)
 * @method static Builder|Pix orderId($orderId)
*/
class Pix extends Model
{
    use Dispatchable;

    const STATUS__APPROVED = 'APPROVED';
    const STATUS__WAITING =  'WAITING';

    protected $table = 'pix';
    protected $appends = ['rendered'];
    protected $dates = ['created_at', 'updated_at', 'creation_date_qrcode', 'expiration_date_qrcode'];
    /**
     * @param $pixData
     * @param $orderId
     * @param $localDescription
     * @param $idCliente
     * @param int $amount
     * @return Pix
     */
    public static function adapt($pixData, $orderId, $localDescription, $idCliente, int $amount): Pix
    {
        $pix = new self;
        $pix->order_id = $orderId;
        $pix->payment_id = $pixData->payment_id;
        $pix->status = $pixData->status;
        $pix->description = $pixData->description;
        $pix->transaction_id = $pixData->additional_data->transaction_id;
        $pix->qr_code = $pixData->additional_data->qr_code;
        $pix->creation_date_qrcode = Carbon::parse($pixData->additional_data->creation_date_qrcode);
        $pix->expiration_date_qrcode = Carbon::parse($pixData->additional_data->expiration_date_qrcode);
        $pix->psp_code = $pixData->additional_data->psp_code;
        //$pix->callback_url = $localData->callback_url;
        $pix->local_description = $localDescription;
        $pix->id_cliente = $idCliente;
        $pix->amount = $amount;

        $pix->save();

        return $pix;
    }

    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'id_cliente');
    }

    public function getRenderedAttribute()
    {
        try {
            $qr = new QrCode($this->qr_code);
            $output = (new Png())->output($qr,300);

            return base64_encode($output);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getAmountAsMoneyAttribute()
    {
        return $this->amount / 100;
    }

    public function scopePaymentId(Builder $query, $paymentId)
    {
        return $query->where('payment_id', $paymentId);
    }

    public function scopeTransactionId(Builder $query, $transactionId)
    {
        return $query->where('transaction_id', $transactionId);
    }

    public function scopeOrderId(Builder $query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    public function formatToLog()
    {
        return json_encode([
            'id' => $this->id,
            'payment_id' => $this->payment_id,
            'status' => $this->status,
            'description' => $this->description,
            'transaction_id' => $this->transaction_id,
            'qrcode' => $this->qr_code,
            'cliente' => [
                'id' => $this->cliente->id,
                'nome' => $this->cliente->nome_cliente,
                'cpf' => $this->cliente->cpf
            ]
        ]);
        return $query->where('order_id', $orderId);
    }

    public function scopeStatus(Builder $query, $status)
    {
        return $query->where('status', $status);
    }
}
