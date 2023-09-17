<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 22/04/2021
 * Time: 11:09
 */

namespace App\Helpers\API\Financeiro\DirectAccess\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CreditCard
 * @package App\Helpers\API\Financeiro\DirectAccess\Models
 * @property int $id
 * @property string $holder
 * @property int $number
 * @property string $token
 * @property string $status
 * @property int $customer_id
 * @property string $expire_in
 * @property string $brand
 * @property string $card_id
 * @property string $default
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 */
class CreditCard extends Model
{
    use SoftDeletes;

    protected $connection = 'financeiro';
    protected $table = 'creditcard';

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = ['default'];
    protected $appends = ['default'];
    protected $hidden = ['customer_id','token', 'card_id', 'deleted_at', 'updated_at'];

    public function scopeOwnedBy($query, $customer_id)
    {
        return $query->where('customer_id', $customer_id)->where('status', 'A');
    }

    public function scopeDefault($query)
    {
        return $query->where('default', 'Y')->orderBy('id', 'DESC');
    }

    public function getDefaultAttribute()
    {
        return $this->attributes['default'] === 'Y' ? true : false;
    }
}