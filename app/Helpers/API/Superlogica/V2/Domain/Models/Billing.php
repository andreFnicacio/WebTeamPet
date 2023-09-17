<?php

namespace App\Helpers\API\Superlogica\V2\Domain\Models;

use App\Helpers\API\Superlogica\V2\Transformers\Transformable;
use App\Helpers\API\Superlogica\V2\Utils\Date;
use Carbon\Carbon;

class Billing extends Transformable
{

    public $id_sacado_sac;
    public $st_nomeref_sac;
    public $st_nome_sac;
    public $st_sincro_sac;
    public $dt_desativacao_sac;
    public $dt_recebimento_recb;
    public $dt_congelamento_sac;
    public $dt_ignorarstatus_sac;
    public $nm_anocartaovencimento_sac;
    public $nm_mescartaovencimento_sac;
    public $nm_cartao_sac;
    public $st_telefone_sac;
    public $st_email_sac;
    public $st_cep_sac;
    public $fl_pagamentopref_sac;
    public $fl_pessoajuridica_sac;
    public $st_banco_sac;
    public $st_cgc_sac;
    public $st_cartaobandeira_sac;
    public $id_recebimento_recb;
    public $dt_vencimento_recb;
    public $dt_geracao_recb;
    public $dt_liquidacao_recb;
    public $fl_status_recb;
    public $vl_total_recb;
    public $vl_emitido_recb;
    public $dt_cancelamento_recb;
    public $st_nossonumero_recb;
    public $st_observacaointerna_recb;
    public $st_observacaoexterna_recb;
    public $st_complementocomposicao_recb;
    public $id_formapagamento_recb;
    public $id_conta_cb;
    public $vl_txjuros_recb;
    public $vl_txmulta_recb;
    public $vl_txdesconto_recb;
    public $st_md5_recb;
    public $id_nota_not;
    public $st_nf_recb;
    public $st_cielotid_recb;
    public $st_cartaodetalhes_recb;
    public $fl_remessastatus_recb;
    public $st_label_recb;
    public $id_transacao_ctr;
    public $fl_cieloforcarpagamento_recb;
    public $dt_competencia_recb;
    public $st_documentoex_recb;
    public $fl_acordofrentedecaixa_recb;
    public $nm_visto_recb;
    public $fl_primeiranotificacao_recb;
    public $fl_segundanotificacao_recb;
    public $fl_terceiranotificacao_recb;
    public $fl_quintanotificacao_recb;
    public $fl_sextanotificacao_recb;
    public $id_filial_fil;
    public $st_errocartao_recb;
    public $st_tokenfacilitador_recb;
    public $vl_taxacobranca_recb;
    public $st_cielotidcancelamento_recb;
    public $st_label_mens;
    public $fl_ignorarbloqueioauto_recb;
    public $id_endereco_sen;
    public $id_forma_frecb;
    public $st_marcador_recb;
    public $fl_motivocancelar_recb;
    public $fl_remessastatuscr_recb;
    public $id_formaboleto_frecb;
    public $tx_remessamsg_recb;
    public $id_adesao_plc;
    public $st_motivocanceloutros_recb;
    public $st_tokendaconta_recb;
    public $nm_descontoatedia_recb;
    public $ar_nomeformas_calc;
    public $vl_valorcreditado_calc;
    public $id_pedido_ped;
    public $fl_conta_homologada;
    public $fl_cofre;
    public $st_descricao_cb;
    public $tipo_conta;
    public $nome_formatado;
    public $publickey;
    public $link_2via;
    public $publickey_json;
    public $link_2via_json;
    public $comconfirmacaoleitura;
    public $acessovistoonline;
    public $descontovalorfixo;
    public $msgdiasparadesconto;
    public $descontoatedia;
    public $vl_txdesconto_emp;
    public $vl_txjuros_emp;
    public $vl_txmulta_emp;
    public $tx_bancaria;


    public function __construct(array $billing=[])
    {
        $this->fromArray($billing);
    }
    public function fromArray(array $array)
    {
        foreach ($array as $key => $value)
        {
            $this->genericSetter($key, $value);
        }
    }

    public function genericSetter($key, $value) : void
    {
        if (property_exists($this, $key))
        {
            $this->$key = $value;
        }
    }

    public function setDateSettled(Carbon $date)
    {
        $this->dt_liquidacao_recb = $date->format(Date::FORMAT);
    }

    public function setDateCredit(Carbon $date)
    {
        $this->dt_recebimento_recb = $date->format(Date::FORMAT);
    }


}