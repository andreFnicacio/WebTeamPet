<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 22/04/2021
 * Time: 12:09
 */

namespace App\Helpers\API\Financeiro\DirectAccess\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Customer
 * @package App\Helpers\API\Financeiro\DirectAccess\Models
 * @property int $id
 * @property string $payment_type
 * @property string $ref_code
 * @property string $cpf_cnpj
 * @property string $status
 * @property string $name
 */
class Customer extends Model
{
    use SoftDeletes;

    protected $connection = 'financeiro';
    protected $table = 'customer';

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function cards()
    {
        return $this->hasMany(CreditCard::class, 'customer_id', 'id');
    }

    public function scopeRefCode($query, $ref_code)
    {
        return $query->where('ref_code', $ref_code);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'A');
    }

    public function isActive()
    {
        return $this->status === 'A';
    }
}