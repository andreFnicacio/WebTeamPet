<?php

namespace App\Helpers\API\Superlogica\V2\Transformers;

use App\Helpers\API\Superlogica\V2\Utils\Date;
use App\Models\Clientes;
use App\Models\Cobrancas;
use App\Models\PetsPlanos;
use Carbon\Carbon;

class Billing extends Transformable
{

    const ID_PRODUTO_COPARTICIPACAO = 4;

    const PAYMENT_TYPE_BOLETO = 0;
    const PAYMENT_TYPE_CHEQUE = 1;
    const PAYMENT_TYPE_DINHEIRO = 2;
    const PAYMENT_TYPE_CREDITO = 3;
    const PAYMENT_TYPE_DEBITO = 4;
    const PAYMENT_TYPE_DEPOSITO = 5;
    const PAYMENT_TYPE_CHEQUEPD = 6;
    const PAYMENT_TYPE_DEBITO_AU = 7;
    const PAYMENT_TYPE_TRANSFERENCIA = 8;
    const PAYMENT_TYPE_DOC = 9;
    const PAYMENT_TYPE_OUTROS = 10;


    public $ID_SACADO_SAC; //required. number. ID do cliente no Superlógica. Opcionalmente, você pode utilizar o campo "identificador".
    public $COMPO_RECEBIMENTO = []; // array => required. number. ID do serviço ou produto no Superlógica.
    public $VL_EMITIDO_RECB; // required. number. Valor total da cobrança.

    public $DT_VENCIMENTO_RECB; // required. string. Data de vencimento (padrão mm/dd/yyyy).
    public $ID_FORMAPAGAMENTO_RECB; // number. Forma de pagamento da cobrança (vide acima).
    public $ST_OBSERVACAOINTERNA_RECB; // string. Observação interna.
    public $ST_OBSERVACAOEXTERNA_RECB; // string. Observação externa (para o cliente)
    public $ID_CONTA_CB; // number. ID da conta bancária no Superlógica.
    public $COBRANCA_PARCELAS = []; // array
    public $ST_NOSSONUMEROFIXO_RECB; //string. Se a forma de pagamento for boleto, e se ele ja foi enviado para o cliente, informe-o aqui. Importante: o "nosso número" não pode se chocar com os NN do Superlógica.

    public $FL_CIELOFORCARPAGAMENTO_RECB;

    public function __construct($customer=null, $billing=null)
    {
        $this->loadCustomerData($customer);
        $this->loadBillingData($billing);
    }

    private function loadCustomerData($customer) {
        if ($customer instanceof Clientes)
        {
            $this->fromClientesModel($customer);
            return;
        }

    }

    private function loadBillingData($billing) {

        if ($billing instanceof Cobrancas)
        {
            $this->fromCobrancasModel($billing);
            return;
        }

        if ($billing instanceof PetsPlanos)
        {
            $this->fromPetsPlanosModel($billing);
            return;
        }


    }

    /**
     * @param Cobrancas $cobranca
     */
    public function fromCobrancasModel(Cobrancas $cobranca) : void
    {

        $this->setDueDate($cobranca->data_vencimento->format(Date::FORMAT));
        $this->setValue($cobranca->valor_original);

    }

    /**
     * @param PetsPlanos $petsPlanos
     */
    public function fromPetsPlanosModel(PetsPlanos $petsPlanos) : void
    {


    }


    public function fromClientesModel(Clientes $cliente) : void
    {

        $this->setCustomerId($cliente->id_superlogica);

    }

    public function setCOMPO_RECEBIMENTO($id_prd, $nm_qty, $vl_unit)
    {

        $this->COMPO_RECEBIMENTO[] = [
            'ID_PRODUTO_PRD' => $id_prd,
            'NM_QUANTIDADE_COMP' => $nm_qty,
            'VL_UNITARIO_PRD' => $vl_unit
        ];
    }

    public function setCustomerId($id) : void
    {
        $this->ID_SACADO_SAC = $id;
    }
    public function setDueDate($date) : void
    {
        $this->DT_VENCIMENTO_RECB = $date;
    }

    public function setValue($value) : void
    {

        $this->VL_EMITIDO_RECB = number_format($value, 2, '.', '');

    }
    public function setObservation(string $observation) : void
    {
        $this->setInternalObservation($observation);
        $this->setExternalObservation($observation);

    }

    public function setInternalObservation(string $observation) : void
    {
        $this->ST_OBSERVACAOINTERNA_RECB = $observation;
    }

    public function setExternalObservation(string $observation) : void
    {
        $this->ST_OBSERVACAOEXTERNA_RECB = $observation;
    }

    public function setInstallments($value, Carbon $dueDate, $observation) : void
    {
        $this->COBRANCA_PARCELAS[] = [
            'VL_EMITIDO_RECB'=>number_format($value, 2, '.', ''),
            'DT_VENCIMENTO_RECB' => $dueDate->format(Date::FORMAT),
            'ST_OBSERVACAOEXTERNA_RECB' => $observation
        ];
    }

    public function setPaymentType($paymentType) : void
    {
        $this->ID_FORMAPAGAMENTO_RECB = $paymentType;
    }

    public function setOurNumber($number) : void
    {
        $this->ST_NOSSONUMEROFIXO_RECB = $number;
    }
    


}