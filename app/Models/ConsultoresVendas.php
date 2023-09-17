<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultoresVendas extends Model
{
    protected $connection = 'mysql_consultor';
    protected $table = 'sales';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    
    public function getDateCreatedAttribute($value)
    {
    
        $time = strtotime($value);

        return date('d/m/Y H:i:s',$time);

    }

    public function getTotalAttribute($value)
    {
    
        return floatval($value);

    }

    public function getTotalDiscountAttribute($value)
    {
    
        return floatval($value);

    }

    public function getComissionAttribute($value)
    {
    
        return floatval($value);

    }

    public function cliente()
    {
        return $this->belongsTo('App\Models\ConsultoresVendasClientes', 'clients_id');
    }
    
}
