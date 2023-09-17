<?php


namespace App\Helpers\API\Superlogica\V2\Transformers;


use App\Helpers\API\Superlogica\V2\Plan;
use App\Helpers\API\Superlogica\V2\Utils\Date;
use App\Models\Pets;
use App\Models\PetsPlanos;
use Carbon\Carbon;

class MonthlySignature extends Transformable
{
    const ID_PRODUTO_MENSALIDADE = '999999982';

    public $ID_SACADO_SAC; //16030 string. ID do cliente;
    public $DT_CONTRATO_PLC; // 07/01/2019 string. data da contratação;
    public $PLANOS = [];
    public $OPCIONAIS = [];

    //public $PLANOS[0][]; //4 string. ID do plano no sistema;
    //public $PLANOS[0][FL_TRIAL_PLC]; // string. 0 para não incluir período de trial configurado e 1 para incluir.
    //public $PLANOS[0][QUANT_PARCELAS_ADESAO]; //string. Quantidade de parcelas de sua adesão.
    //public $PLANOS[0][ST_IDENTIFICADOR_PLC]; //0 string. Identificador do contrato.
    //public $PLANOS[0][FL_NOTIFICARCLIENTE]; //string. 1 para notificar, 0 para não;.
    //public $PLANOS[0][cupom]; //string. Cupom de desconto.
    //public $PLANOS[0][FL_MULTIPLO_COMPO]; //1 string. 1 - valor fixo.
    //public $OPCIONAIS[0][ID_PRODUTO_PRD]; //999999982 string. ID do serviço a ser contratado.
    //public $OPCIONAIS[0][SELECIONAR_PRODUTO]; //1 string. 1 - valor fixo;
    //public $OPCIONAIS[0][NM_QNTD_PLP]; //2 string. Quantidade.
    //public $OPCIONAIS[0][valor_unitario]; //45 string. Valor unitário.
    //public $OPCIONAIS[0][FL_RECORRENTE_PLP]; //1 string. 1 para recorrente e 0 para adesão cobrado apenas uma vez.

    public function __construct(Pets $pet)
    {
        $plano = $pet->plano();

        if(!$plano->id_superlogica) {
            $planService = new Plan();
            $planService->register($plano);
        }

        $this->ID_SACADO_SAC = $pet->cliente()->first()->id_superlogica;
        $signature = $pet->petsPlanosAtual()->first();
        if($signature->data_inicio_contrato) {
            $this->DT_CONTRATO_PLC = $signature->data_inicio_contrato->format(Date::FORMAT);
        } else {
            $this->DT_CONTRATO_PLC = (Carbon::now())->format(Date::FORMAT);
        }

        $this->PLANOS = [
            [
                'ID_PLANO_PLA' => $plano->id_superlogica,
                'FL_TRIAL_PLC' => 0,
                'QUANT_PARCELAS_ADESAO' => 1,
                'ST_IDENTIFICADOR_PLC' => $pet->getIdentificadorPlano(),
                'FL_NOTIFICARCLIENTE' => 0,
                'FL_MULTIPLO_COMPO' => 1
            ]
        ];

        $this->OPCIONAIS = [
            [
                'ID_PRODUTO_PRD' => self::ID_PRODUTO_MENSALIDADE,
                'SELECIONAR_PRODUTO' => 1,
                'NM_QNTD_PLP' => 1,
                'valor_unitario' => number_format($pet->getValorPlano(), 2, '.', ''),
                'FL_RECORRENTE_PLP' => 1
            ]
        ];
    }
}