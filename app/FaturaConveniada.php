<?php

namespace App;

use App\Helpers\API\Financeiro\Financeiro;
use App\Models\Conveniada;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class FaturaConveniada extends Model
{
    protected $table = 'conveniadas_faturamentos';

    const COMPETENCIA_FORMAT = 'Y-m';

    const STATUS_ABERTA = 'ABERTA';
    const STATUS_FECHADA = 'FECHADA';
    const STATUS_PAGA = 'PAGA';

    protected $fillable = [
        'id_conveniada',
        'competencia',
        'vencimento',
        'status',
        'id_externo'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'vencimento'
    ];

    public function composicao() {
        return $this->hasMany(ItemFaturaConveniada::class, 'id_fatura_conveniada', 'id');
    }

    public function conveniada() {
        return $this->belongsTo(Conveniada::class, 'id_conveniada');
    }

    /**
     * Retorna a competência corrente
     * @return string
     */
    public static function competencia() {
        return Carbon::now()->format(self::COMPETENCIA_FORMAT);
    }

    public function addItem(array $item) {
        $item = ItemFaturaConveniada::create(array_merge([
            'id_fatura_conveniada' => $this->id,
        ], $item));

        return $item;
    }

    public function total() {
        return $this->composicao->sum(function($c) {
            return $c->valor;
        });
    }

    public function sync() {
        $form = $this->getAdaptedForm();

        $finance = new Financeiro();

        $invoice = $finance->post('/invoice', $form);

        if($invoice) {
            $this->id_externo = $invoice->id;
            $this->hash = $invoice->hash;
            $this->status = self::STATUS_FECHADA;
            $this->update();
        }

        return $this;
    }

    public function getAdaptedForm()
    {
        $id_externo = $this->conveniada->id_externo;

        if(!$id_externo) {
            throw new \Exception('A conveniada não foi relacionada com um cliente do SF.');
        }

        $competencia = explode('-', $this->competencia);

        $form = [
            'customer_id' => $id_externo,
            'status_code' => 2,
            'reference' => "{$competencia[1]}/{$competencia[0]}",
            'due_date' => $this->vencimento->format('d/m/Y'),
            'payment_type' => 'boleto',
            'item' => []
        ];

        foreach($this->composicao as $item) {
            $form['item'][] = [
                'name' => $item->descricao,
                'type' => $item->tipo,
                'price' => number_format($item->valor, 2),
                'quantity' => 1
            ];
        }

        return $form;
    }
}
