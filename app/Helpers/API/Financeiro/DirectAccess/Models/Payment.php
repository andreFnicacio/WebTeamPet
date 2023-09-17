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
class Payment extends Model
{
    use SoftDeletes;

    protected $connection = 'financeiro';
    protected $table = 'payment';

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];


}