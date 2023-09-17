<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Models\ConsultoresVendas;
use App\Models\ConsultoresVendaStatusLog;
use Carbon\Carbon;
use Auth;

class Consultores extends Model
{
    //use SoftDeletes;

    protected $connection = 'mysql_consultor';
    protected $table = 'sellers';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'status',
        'status_reason',
        'status_date',
        'name',
        'email',
        'rg',
        'cpf',
        'phone',
        'phone2',
        'waiting_days'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name'          => 'string',
        'users_id'      => 'integer',
        'email'         => 'string',
        'rg'            => 'string',
        'cpf'           => 'string',
        'cel'           => 'string',
        'tel'           => 'string',
        'cep'           => 'string',
        'address'       => 'string',
        'number'        => 'string',
        'complement'    => 'string',
        'district'      => 'string',
        'city'          => 'string',
        'uf'            => 'string',
        'code_link'     => 'string',
        'active'        => 'string',
        'grace_days'    => 'integer',
        'total_balance' => 'decimal',
        'total_gain'    => 'decimal',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime'
    ];


    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function documentos()
    {
        return $this->hasMany('App\Models\ConsultoresDocumentos', 'sellers_id');
    }

    public function vendas()
    {
        return $this->hasMany('App\Models\ConsultoresVendas', 'sellers_id');
    }

    public function endereco() {
        return $this->hasOne('App\Models\ConsultoresEnderecos', 'sellers_id');
    }

    public function atualizarComissao(ConsultoresVendas $venda, $statusAnterior) {

        if($statusAnterior == 'waiting_confirmation') {
            
            if ($venda->status == 'confirmed') {
                $this->adicionarExtrato($venda);
                $this->adicionarComissaoPendente($venda);
                
                return true;
            }

        }

        if($statusAnterior == 'confirmed' && $venda->status == 'canceled') {
            $this->removerComissao($venda);
        }
        
    }

    public function adicionarExtrato(ConsultoresVendas $venda, $positive = true) {

        $extrato = ConsultoresExtratos::create([
            'value' => ($positive ? $venda->comission : gmp_neg($venda->comission)),
            'total' => ($positive ? $this->total_balance + $venda->comission : $this->total_balance - $venda->comission),
            'date' => Carbon::now(),
            'status' => $venda->status,
            'sellers_id' => $this->id,
            'sales_id' => $venda->id,
            'created_by' => Auth::user()->id
        ]);
        
        if(!$extrato) {
            throw new Exception('Falha ao salvar o extrato');
        }
    }

    public function adicionarComissaoPendente(ConsultoresVendas $venda) {

        ConsultoresComissoesPendentes::create([
            'comission' => $venda->comission,
            'date_init' => $venda->payment_confirmation_date,
            'date_end' => Carbon::parse($venda->payment_confirmation_date)->addDays($venda->waiting_days),
            'waiting_days' => $venda->waiting_days,
            'sellers_id' => $this->id,
            'sales_id' => $venda->id,
            'created_by' => Auth::user()->id
        ]);

        if(!$comissao) {
            throw new Exception('Falha ao salvar a comissão');
        }

        $this->total_pending = $this->total_pending + $venda->comission;
        $this->save();
    }

    public function removerComissao(ConsultoresVendas $venda) {

        $paymentConfirmationDate = new Carbon($venda->payment_confirmation_date);
        $now = Carbon::now();

        // Se o cancelamento for realizado antes do pagamento completar 7 dias
        // é cancelado 100% da comissão
        if($paymentConfirmationDate->diff($now)->days <= 7) {
            $this->adicionarExtrato($venda, false);
            
            if($venda->id > 0) {
                ConsultoresComissoesPendentes::where('sales_id', $venda->id)->delete();
                ConsultoresComissoesConfirmadas::where('sales_id', $venda->id)->delete();    
            }
            
            $this->total_pending;
        }

    }

}
