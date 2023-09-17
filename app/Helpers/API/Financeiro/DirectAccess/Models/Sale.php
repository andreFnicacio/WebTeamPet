<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 20/04/2021
 * Time: 14:40
 */

namespace App\Helpers\API\Financeiro\DirectAccess\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Sale
 * @package App\Helpers\API\Financeiro\DirectAccess\Models
 * @property $id
 * @property $invoice_id
 * @property $type
 * @property $due_date
 * @property $status_code
 * @property $status
 * @property $paid_at
 * @property $amount_paid
 * @property $amount
 * @property $discount
 * @property $tax
 * @property $installments
 * @property $amount_installments
 * @property $transaction_id
 * @property $hash
 * @property $customer_id
 * @property $reference
 * @property $authorization_code
 * @property $error
 * @property $retry
 * @property $tags
 * @property $last_retry
 * @property $description
 * @property $acquirer_transaction_id
 */
class Sale extends Model
{
    use SoftDeletes;

    protected $connection = 'financeiro';
    protected $table = 'payment';
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at', 'due_date'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->due_date = Carbon::now();
    }

    /**
     * @param $customer_id
     * @param $amount
     * @param $reference
     * @param string $description
     * @param array $tags
     * @param null $acquirer_transaction_id ID da transação
     * @return $this
     */
    public function picpay($customer_id, $amount, $reference, string $description = '', array $tags = [], $acquirer_transaction_id = null): Sale
    {
        $this->type = 'creditcard';
        $this->status_code = 7;
        $this->status = 'AVAILABLE';
        $this->paid_at = $this->due_date;
        $this->amount = $amount;
        $this->amount_paid = $amount;
        $this->discount = 0;
        $this->tax = 0;
        $this->installments = 0;
        $this->amount_installments = $amount;
        $this->customer_id = $customer_id;
        $this->reference = $reference;
        $this->tags = join(';', array_merge(['venda', 'participativo', 'atendimento', 'pagamento-direto'], $tags));
        $this->description = 'Pagamento direto recebido e cadastrado via ERP';
        if($description) {
            $this->description .= ' ' . $description;
        }
        $this->acquirer_transaction_id = $acquirer_transaction_id;

        return $this;
    }

    public function manual(Customer $customer, $amount, $reference, $due_date, $paid_at, $description = '', $tags = [])
    {
        $this->customer_id = $customer->id;
        $this->amount = $amount;
        $this->reference = $reference;
        $this->due_date = $due_date;
        $this->description = 'Lançamento manual via ERP.';
        $this->type = $customer->payment_type;
        $this->tags = join(';', array_merge(['manual', 'retroativo', 'atendimento', 'erp'], $tags));

        $this->status_code = 7;
        $this->status = 'AVAILABLE';
        $this->paid_at = $this->due_date;
        $this->amount_paid = $amount;
        $this->discount = 0;
        $this->tax = 0;
        $this->installments = 0;
        $this->amount_installments = $amount;

        if($description) {
            $this->description .= ' ' . $description;
        }

        return $this;
    }

    /**
     * @param Customer $customer
     * @param $amount
     * @param $reference
     * @param $due_date
     * @param string $description
     * @param array $tags
     * @return $this
     */
    public function pix($customer_id, $amount, $reference, $acquirer_transaction_id, string $description = '', array $tags = []): Sale
    {
        $this->customer_id = $customer_id;
        $this->amount = $amount;
        $this->reference = $reference;
        $this->description = 'Pagamento direto (PIX) registrado via ERP.';
        $this->type = 'pix';
        $this->tags = join(';', array_merge(['venda', 'participativo', 'atendimento', 'pagamento-direto', 'pix'], $tags));

        $this->status_code = 7;
        $this->status = 'AVAILABLE';
        $this->paid_at = $this->due_date;
        $this->amount_paid = $amount;
        $this->discount = 0;
        $this->tax = 0;
        $this->installments = 0;
        $this->amount_installments = $amount;
        $this->acquirer_transaction_id = $acquirer_transaction_id;

        if($description) {
            $this->description .= ' ' . $description;
        }

        return $this;
    }
}