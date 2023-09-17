<?php


namespace App\Helpers\API\Superlogica\V2\Transformers;


use App\Helpers\API\Superlogica\V2\Domain\Models\CreditCard;
use App\LifepetCompraRapida;
use App\Models\Clientes;

class Checkout extends Transformable
{
    /**
     * Dados de cliente
     */
    public $ST_NOME_SAC;  //Kauan Henrique da Silva Obrigatório. String. Nome completo do cliente.
    public $ST_EMAIL_SAC; //teste1@teste1.com Obrigatório. String. E-mail do cliente (as notificações serão enviadas para este endereço).
    public $ST_CGC_SAC; //Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. CPF ou CNPJ do cliente.
    public $DT_NASCIMENTO_SAC; //01-01-2019 Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. Data de nascimento do cliente
    public $ST_RG_SAC = null; //319633524 Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. RG do cliente.
    public $ST_ORGAO_SAC = null; //SSP Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório.String. Nome do orgão que expediu o documento do cliente.

    /**
     * Dados de contato
     */
    public $ST_DDD_SAC; //19 Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. DDD do telefone do cliente
    public $ST_TELEFONE_SAC; //33447365 Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. Telefone do cliente
    public $ST_CELULAR_SAC; //19 99999-999 Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. Número do celular do cliente
    public $ST_FAX_SAC = null; //Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório.
    public $ST_PAIS_SAC = "Brasil"; //Brasil Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. País do cliente
    public $ST_CEP_SAC; //13100346 Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. CEP do cliente
    public $ST_ENDERECO_SAC; //Rua Serra Geral Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. Endereço do cliente
    public $ST_NUMERO_SAC; //546 Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. Número da residência do cliente
    public $ST_COMPLEMENTO_SAC; //Perto do posto Ipiranga Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. Complemento do endereço do cliente
    public $ST_BAIRRO_SAC; //Jardim São Fernando Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. Bairro do cliente
    public $ST_CIDADE_SAC; //Campinas Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. Cidade do cliente
    public $ST_ESTADO_SAC;  //SP Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. Estado do cliente
    public $FL_MESMOEND_SAC = 1; //1 Obrigatório. Number. Informe se "1" se o endereço de entrega é o mesmo do cadastrou ou "0" para cadastrar outro endereço
    public $ST_CEPENTREGA_SAC = null; //Obrigatório caso o campo FL_MESMOEND_SAC seja enviado como "0". String. CEP do endereço de entrega do cliente
    public $ST_ENDERECOENTREGA_SAC = null; //Obrigatório caso o campo FL_MESMOEND_SAC seja enviado como "0". String. Endereço de entrega do cliente
    public $ST_NUMEROENTREGA_SAC = null; //Obrigatório caso o campo FL_MESMOEND_SAC seja enviado como "0". String. Número do endereço de entrega do cliente
    public $ST_COMPLEMENTOENTREGA_SAC = null; //Obrigatório caso o campo FL_MESMOEND_SAC seja enviado como "0". String. Complemento do endereço de entrega do cliente
    public $ST_BAIRROENTREGA_SAC = null; //Obrigatório caso o campo FL_MESMOEND_SAC seja enviado como "0". String. Bairro do endereço de entrega do cliente
    public $ST_CIDADEENTREGA_SAC = null; //Obrigatório caso o campo FL_MESMOEND_SAC seja enviado como "0". String. Cidade do endereço de entrega do cliente
    public $ST_ESTADOENTREGA_SAC = null; //Obrigatório caso o campo FL_MESMOEND_SAC seja enviado como "0". String. Estado do endereço de entrega do cliente
    public $ST_PONTOREFERENCIAENTREGA_SAC =  null; //Obrigatório caso o campo FL_PAGAMENTOPREF_SAC seja enviado como "3". String. Ponto de referência do endereço de entrega do cliente

    /**
     * Dados de pessoa jurídica
     */
    public $ST_INSCRICAO_SAC = null; //Isento Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. Inscrição estadual do cliente pessoa jurídica.
    public $ST_INSCMUNICIPAL_SAC = null; //Isento Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. Inscrição municipal do cliente pessoa jurídica.
    public $FL_OPTANTESIMPLES_SAC = null; //1 Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. Number. Informe "1" se o cliente é do Simples Nacional ou "0" para não é do Simples Nacional.
    public $ISENTO_ICMS = null; //1 Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. Number. Informe "1" caso o cliente seja isento do ICMS ou "0" para não isento do ICMS

    /**
     * Dados de acesso
     */
    public $senha; //159753 Obrigatório. String. Senha informada pelo cliente.
    public $senha_confirmacao; //159753 Obrigatório. String. Confirmação da senha.

    /**
     * Dados de contrato
     */
    public $idplano;  //17 Obrigatório. Number. ID do plano no Superlógica.
    public $trial; //Não é obrigatório. String. informe 1 caso o plano contratado seja um trial.
    public $identificador; //kauan01 Não é obrigatório. String. Identificador do cadastro do cliente.
    public $identificadorContrato; //kauan01 Não é obrigatório. String. Identificador da assinatura do cliente.

    /**
     * Dados de pagamento
     */
    public $QUANT_PARCELAS_ADESAO = 0; //2 Não é obrigatório. Number. Número da quantidade de parcelas de adesão, se houver.
    public $FL_PAGAMENTOPREF_SAC = 3; //3 Obrigatório. Number. ID da forma de pagamento (0: boleto bancário, 3: cartão de crédito e 7 débito automático).
    public $ST_CARTAO_SAC; //4485225354867227 Obrigatório caso o campo FL_PAGAMENTOPREF_SAC seja enviado como "3". String. Número do cartão do cliente.
    public $ST_MESVALIDADE_SAC; //03 Obrigatório caso o campo FL_PAGAMENTOPREF_SAC seja enviado como "3". String. Mês de validade do cartão do cliente.
    public $ST_ANOVALIDADE_SAC; //2021 Obrigatório caso o campo FL_PAGAMENTOPREF_SAC seja enviado como "3". String. Ano de validade do cartão do cliente.
    public $ST_SEGURANCACARTAO_SAC; //323 Obrigatório caso o campo FL_PAGAMENTOPREF_SAC seja enviado como "3". String. Número de segurança do cartão do cliente.
    public $ST_CARTAOBANDEIRA_SAC; //visa Obrigatório caso o campo FL_PAGAMENTOPREF_SAC seja enviado como "3". String. Bandeira do cartão, deve ser enviado exatamente conforme os exemplos, se não a chamada não será aceita. Exemplos: visa, mastercard, diners, amex, elo - varia de acordo com seu contrato.
    public $ST_NOMECARTAO_SAC; //Kauan Henrique da Silva Obrigatório caso o campo FL_PAGAMENTOPREF_SAC seja enviado como "3". String. Nome que está no cartão do cliente


    public $CAMPO1 = null; //Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. Campo adicional. Informe o valor do campo conforme configurado no software
    public $CAMPO2 = null; //Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. Campo adicional. Informe o valor do campo conforme configurado no software
    public $CAMPO3 = null; //Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. Campo adicional. Informe o valor do campo conforme configurado no software
    public $CAMPO4 = null; //Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. Campo adicional. Informe o valor do campo conforme configurado no software
    public $cupom = null; //Não é obrigatório. String. Nome do cupom
    public $ID_VENDEDOR_FOR = null; //Não é obrigatório. Number. ID do cliente que fez a venda
    public $aceite_contrato = 1; //1 Obrigatório. Number. Informe 1 para aceitar os termos de contrato.

    public function __construct(LifepetCompraRapida $compraRapida, CreditCard $card)
    {
        $this->customerData($compraRapida);

        //Subscription data

        //Payment data
        $this->paymentData($card);

        //Superlogica Password
        $this->senha = $this->senha_confirmacao = $this->numberOnly($compraRapida->cpf);
    }

    /**
     * @param LifepetCompraRapida $compraRapida
     */
    public function customerData(LifepetCompraRapida $compraRapida): void
    {
        $this->ST_NOME_SAC = $compraRapida->nome;  //Kauan Henrique da Silva Obrigatório. String. Nome completo do cliente.
        $this->ST_EMAIL_SAC = $compraRapida->email; //teste1@teste1.com Obrigatório. String. E-mail do cliente (as notificações serão enviadas para este endereço).
        $this->ST_CGC_SAC = $this->numberOnly($compraRapida->cpf); //Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. CPF ou CNPJ do cliente.
        $this->DT_NASCIMENTO_SAC = null; //01-01-2019 Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. Data de nascimento do cliente


        $this->ST_DDD_SAC = Clientes::getDDD($compraRapida->celular); //19 Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. DDD do telefone do cliente
        $this->ST_TELEFONE_SAC = Clientes::getPhoneWithoutDDD($compraRapida->celular); //33447365 Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. Telefone do cliente
        $this->ST_CELULAR_SAC = $this->numberOnly($compraRapida->celular); //19 99999-999 Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. Número do celular do cliente
        $this->ST_PAIS_SAC = "Brasil"; //Brasil Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. País do cliente
        $this->ST_CEP_SAC = $compraRapida->cep; //13100346 Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. CEP do cliente
        $this->ST_ENDERECO_SAC = $compraRapida->rua; //Rua Serra Geral Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. Endereço do cliente
        $this->ST_NUMERO_SAC = $compraRapida->numero; //546 Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. Número da residência do cliente
        $this->ST_BAIRRO_SAC = $compraRapida->bairro; //Jardim São Fernando Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. Bairro do cliente
        $this->ST_CIDADE_SAC = $compraRapida->cidade; //Campinas Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. Cidade do cliente
        $this->ST_ESTADO_SAC = $compraRapida->estado;  //SP Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. Estado do cliente
        $this->ST_COMPLEMENTO_SAC = null; //Perto do posto Ipiranga Caso seja obrigatório nas configurações do plano, o envio deste campo é obrigatório. String. Complemento do endereço do cliente
    }

    /**
     * @param CreditCard $card
     */
    public function paymentData(CreditCard $card): void
    {
        $this->QUANT_PARCELAS_ADESAO = 0; //2 Não é obrigatório. Number. Número da quantidade de parcelas de adesão, se houver.
        $this->FL_PAGAMENTOPREF_SAC = 3; //3 Obrigatório. Number. ID da forma de pagamento (0: boleto bancário, 3: cartão de crédito e 7 débito automático).
        $this->ST_CARTAO_SAC = $card->numberOnly($card->number); //4485225354867227 Obrigatório caso o campo FL_PAGAMENTOPREF_SAC seja enviado como "3". String. Número do cartão do cliente.
        $this->ST_MESVALIDADE_SAC = $card->validMonth; //03 Obrigatório caso o campo FL_PAGAMENTOPREF_SAC seja enviado como "3". String. Mês de validade do cartão do cliente.
        $this->ST_ANOVALIDADE_SAC = $card->validYear; //2021 Obrigatório caso o campo FL_PAGAMENTOPREF_SAC seja enviado como "3". String. Ano de validade do cartão do cliente.
        $this->ST_SEGURANCACARTAO_SAC = $card->cvv; //323 Obrigatório caso o campo FL_PAGAMENTOPREF_SAC seja enviado como "3". String. Número de segurança do cartão do cliente.
        $this->ST_CARTAOBANDEIRA_SAC = $card->brand; //visa Obrigatório caso o campo FL_PAGAMENTOPREF_SAC seja enviado como "3". String. Bandeira do cartão, deve ser enviado exatamente conforme os exemplos, se não a chamada não será aceita. Exemplos: visa, mastercard, diners, amex, elo - varia de acordo com seu contrato.
        $this->ST_NOMECARTAO_SAC = $card->holder; //Kauan Henrique da Silva Obrigatório caso o campo FL_PAGAMENTOPREF_SAC seja enviado como "3". String. Nome que está no cartão do cliente
    }
}