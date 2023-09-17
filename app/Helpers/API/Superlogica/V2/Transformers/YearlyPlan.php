<?php


namespace App\Helpers\API\Superlogica\V2\Transformers;


use App\Models\Planos;

class YearlyPlan extends Transformable
{
    public $ST_NOME_PLA; //Plano mensal Required. number. ID do cliente no Superlógica. Opcionalmente, você pode utilizar o campo "identificador".
    public $ST_DESCRICAO_PLA; //Plano mensal String. Informe 1 para quando a contratação for de um trial
    public $FL_PERIODICIDADE_PLA = 0; //Plano mensal String. Informe 1 para quando a contratação for de um trial
    public $ID_GRADE_GPL = 1; //1 Required. number. ID do plano no Superlógica.
    public $ST_NOME_GPL = null;
    public $FL_DESTAQUE_PLA; //
    public $FL_BOLETO_PLA = 0; //1 Number. 1 para ativar pagamento por boleto e 0 para desativar.
    public $FL_CIELO_PLA = 1; //1 Number. 1 para ativar pagamento por cartão e 0 para desativar.
    public $FL_OUTRASFORMAS_PLA = 0; //1 Number. Notificar cliente sobre a contratação (Boolean 0 ou 1).
    public $FL_TRIAL_PLA = 0; //1 Number. Valor percentual de desconto a ser aplicado na assinatura
    public $FL_AUTOCONTRATAR_PLA = 1; //1 String. Cupom de desconto.
    public $NM_DIASTRIAL_PLA = 0; //1 String. Cupom de desconto.
    public $FL_PERMITIRCANCELAMENTO_PLA = 0; //1 String. Cupom de desconto.
    public $NM_PARCELAS_PLA = 1; //5 Number. Quantidade de parcelas da adesão do plano
    public $ST_URLCALLBACKCONTRATO_PLA; //String. Digite a URL de callback
    public $FL_NOTIFICARCLIENTE_PLA = 0; //1 Number. Digite 1 para notificar, 0 para não notificar.
    public $FL_EMAILCLIENTE_PLA = 0; //1 Number. Digite 1 para notificar, 0 para não notificar.
    public $FL_EMAILEMPRESA_PLA = 1; //Number. Digite 1 para notificar, 0 para não notificar.
    public $NM_CONTRATOFIM_PLA = null; //Number. Digite o prazo do contrato
    public $COMPO_RECEBIMENTO = null;
    public $COMPO_RECEBIMENTO_AVULSO = null;
    public $ADICIONAIS = [];
    public $ID_CONTA_CB = 1;
    public $FL_MULTIPLOSIDENTIFICADORES_PLA = 1;

    public function __construct(Planos $plano)
    {
        $nomePlano = "{$plano->nome_plano} - #{$plano->id} - ANUAL";
        $this->ST_NOME_PLA = $nomePlano;
        $this->ST_DESCRICAO_PLA = $nomePlano;
        $this->FL_DESTAQUE_PLA = 1;
        $this->FL_PERIODICIDADE_PLA = 0;

        $this->COMPO_RECEBIMENTO = [
            [
                'ID_PRODUTO_PRD' => '999999982',
                'NM_QUANTIDADE_COMP' => 1,
                'ST_COMPLEMENTO_COMP' => $nomePlano,
                'VL_UNITARIO_PRD' => 0
            ]
        ];

        $this->COMPO_RECEBIMENTO_AVULSO = [
            [
                'ID_PRODUTO_PRD' => '999999983',
                'ST_COMPLEMENTO_COMP' => $nomePlano,
                'NM_QUANTIDADE_COMP' => 1,
                'VL_UNITARIO_PRD' => number_format($plano->preco_plano_familiar, 2),
            ]
        ];
    }
}