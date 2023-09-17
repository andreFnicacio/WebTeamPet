<?php


namespace App\Helpers\API\Superlogica\V2\Transformers;


use App\Helpers\API\Superlogica\V2\CreditCardRequiredException;
use App\Helpers\API\Superlogica\V2\Domain\Models\CreditCard;
use App\Helpers\Utils;
use App\LifepetCompraRapida;
use App\Models\Clientes;
use Carbon\Carbon;

class Customer extends Transformable
{
    public $ST_NOME_SAC; //Obrigatório. String. Nome ou razão social do cliente.
    public $ST_NOMEREF_SAC; //Obrigatório. String. Nome fantasia do cliente
    public $ST_DIAVENCIMENTO_SAC; //Não é obrigatório. Number. Data de vencimento do cliente.
    public $ST_CGC_SAC; //Não é obrigatório. String. Número do CNPJ ou CPF do cliente, você pode passar um ou outro caso o cliente for pessoa física ou jurídica. Nesse campo o sistema realiza a validação do calcúlo dos caracteres.
    public $ST_RG_SAC; //352936125 Não é obrigatório. String Número do RG, se necessário.
    public $ST_ORGAO_SAC; //Não é obrigatório. String. Orgão exp.
    public $ST_SINCRO_SAC; //Não é obrigatório. String. Campo de identificador (ID) do cliente em outro sistema, é muito utilizado caso queira referenciar o cadastro desse cliente do sistema Superlógica para o seu outro sistema.
    public $ST_INSCMUNICIPAL_SAC; //Não é obrigatório. String. Inscrição Municipal, importante caso o cliente for CNPJ e emitir nota fiscal e for da mesma cidade que a empresa.
    public $ST_INSCRICAO_SAC; //Não é obrigatório. String. Inscrição Estadual, importante caso o cliente for CNPJ e para emissão de notas de produto.
    public $ISENTO_ICMS; //1 Não é obrigatório. Number. Isento de ICMS, informar o valor 1 para positivo e 0 para negativo. Se não quiser setar o campo de isento, não é necessário informar este campo no código.
    public $FL_OPTANTESIMPLES_SAC; //1 Não é obrigatório. Number. Optante simples, informar o valor 1 para positivo e 0 para negativo. Se não quiser setar o campo de optante pelo simples, não é necessário informar este campo no código.
    public $ST_SUFRAMA_SAC; //Não é obrigatório. String. Suframa.
    public $ST_CEP_SAC; //69072026 string. CEP do cliente,
    public $ST_ENDERECO_SAC; //Travessa Içanã string. Endereço do cliente.
    public $ST_NUMERO_SAC; //123 string. Número do endereço.
    public $ST_BAIRRO_SAC; //Vila Buriti string. Bairro.
    public $ST_COMPLEMENTO_SAC; //Apto. 5 string. Complemento.
    public $ST_CIDADE_SAC; //Manaus string. Cidade.
    public $ST_ESTADO_SAC; //AM string. Estado.

    public $FL_MESMOEND_SAC = 1; //1 number. (Boolean 0 ou 1). Caso queira informar um endereço de entrega diferente do endereço de cobrança, você pode ativar esse campo de mesmo endereço de cobrança. Ao ativar esse campo os próximos necessitam ser preenchidos. Se não quiser setar o campo, não é necessário informar no código.
    public $ST_CEPENTREGA_SAC = null; //string. CEP de entrega.
    public $ST_ENDERECOENTREGA_SAC = null; //string. Endereço da entrega.
    public $ST_NUMEROENTREGA_SAC = null; //string. Número da entrega.
    public $ST_COMPLEMENTOENTREGA_SAC = null; //string. Complemento da entrega.
    public $ST_BAIRROENTREGA_SAC = null; //string. Bairro da entrega.
    public $ST_CIDADEENTREGA_SAC = null; //string. Cidade da entrega.
    public $ST_ESTADOENTREGA_SAC = null; //string. Estado da entrega.
    public $ST_PONTOREFERENCIAENTREGA_SAC = null; //string. Ponto de referência.

    public $ST_EMAIL_SAC; //string. E-mail principal. Notificações gerais serão enviadas para este endereço.
    public $SENHA; //string. Necessária para o cliente acessar a área do cliente.
    public $SENHA_CONFIRMACAO; //string. Confirmação para validar a senha.
    public $ST_DDD_SAC; //string. DDD do telefone fixo, específico para o campo de telefone abaixo.
    public $ST_TELEFONE_SAC; //string. Telefone.
    public $ST_FAX_SAC; //string. Celular.
    public $DESABILITAR_MENSALIDADE = 0; //number. (Boolean 0 ou 1) Desabilitar geração de cobranças. Esse campo retira a data de vencimento do cliente para que as cobranças não sejam geradas, no sistema as cobranças são geradas apenas para clientes com dia de vencimento configurado.

    public $FL_PAGAMENTOPREF_SAC = null; //string. Enviar como "3" (fixo) APENAS caso esteja passando as informações do cartão.
    public $ST_CARTAO_SAC = null; //string. Cartão de crédito.
    public $ST_MESVALIDADE_SAC = null; //number. Mês de vencimento.
    public $ST_ANOVALIDADE_SAC = null; //number. Ano de vencimento.
    public $ST_SEGURANCACARTAO_SAC = null; //number. Código de segurança do cartão.

    public $ST_CODIGOCONTABIL_SAC = null; //string. Código no sistema contábil. Código da conta contábil desse cliente para exportação dos dados para contabilidade.
    public $FL_RETERISSQN_SAC = 0; //number. (Boolean 0 ou 1). Reter ISSQN. Caso marcado informa o desconto do imposto na cobrança assim que ela for gerada.
    public $TX_OBSERVACAO_SAC = null; //string. Observações do cadastro do cliente.
    public $FL_SINCRONIZARFORNECEDOR_SAC = 0; //number. (Boolean 0 ou 1) Esse campo informa ao Superlógica para cadastrar os dados desse cliente como um fornecedor no sistema.
    public $DT_CADASTRO_SAC; //string. Data de cadastro do cliente.

    /**
     */
    private function __construct()
    {

    }

    public static function fromClientData(Clientes $cliente, CreditCard $card = null): Customer
    {
        $customer = new self;

        $customer->ST_NOME_SAC = $cliente->nome_cliente;
        $customer->ST_NOMEREF_SAC = $cliente->nome_cliente;

        $customer->ST_DIAVENCIMENTO_SAC = "$cliente->dia_vencimento"; //Não é obrigatório. Number. Data de vencimento do cliente.
        $customer->ST_CGC_SAC = $customer->numberOnly($cliente->cpf); //Não é obrigatório. String. Número do CNPJ ou CPF do cliente, você pode passar um ou outro caso o cliente for pessoa física ou jurídica. Nesse campo o sistema realiza a validação do calcúlo dos caracteres.
        $customer->ST_RG_SAC = $customer->numberOnly($cliente->rg); //352936125 Não é obrigatório. String Número do RG, se necessário.
        $customer->ST_EMAIL_SAC = Clientes::getFirstEmail($cliente->email);
        $customer->ST_ORGAO_SAC = null; //Não é obrigatório. String. Orgão exp.
        $customer->ST_SINCRO_SAC = $cliente->id; //Não é obrigatório. String. Campo de identificador (ID) do cliente em outro sistema, é muito utilizado caso queira referenciar o cadastro desse cliente do sistema Superlógica para o seu outro sistema.
        $customer->ST_INSCMUNICIPAL_SAC = null; //Não é obrigatório. String. Inscrição Municipal, importante caso o cliente for CNPJ e emitir nota fiscal e for da mesma cidade que a empresa.
        $customer->ST_INSCRICAO_SAC = null; //Não é obrigatório. String. Inscrição Estadual, importante caso o cliente for CNPJ e para emissão de notas de produto.
        $customer->ISENTO_ICMS = null; //1 Não é obrigatório. Number. Isento de ICMS, informar o valor 1 para positivo e 0 para negativo. Se não quiser setar o campo de isento, não é necessário informar este campo no código.
        $customer->FL_OPTANTESIMPLES_SAC = null; //1 Não é obrigatório. Number. Optante simples, informar o valor 1 para positivo e 0 para negativo. Se não quiser setar o campo de optante pelo simples, não é necessário informar este campo no código.
        $customer->ST_SUFRAMA_SAC = null; //Não é obrigatório. String. Suframa.
        $customer->ST_CEP_SAC = $customer->numberOnly($cliente->cep); //69072026 string. CEP do cliente,
        $customer->ST_ENDERECO_SAC = $cliente->rua; //Travessa Içanã string. Endereço do cliente.
        $customer->ST_NUMERO_SAC = substr($cliente->numero_endereco, 0, 10); //123 string. Número do endereço.
        $customer->ST_BAIRRO_SAC = $cliente->bairro; //Vila Buriti string. Bairro.
        $customer->ST_COMPLEMENTO_SAC = Utils::excerpt($cliente->complemento_endereco, 60, ''); //Apto. 5 string. Complemento.
        $customer->ST_CIDADE_SAC = $cliente->cidade; //Manaus string. Cidade.
        $customer->ST_ESTADO_SAC = $cliente->estado; //AM string. Estado.

        $customer->ST_DDD_SAC = Clientes::getDDD($cliente->telefone_fixo); //string. DDD do telefone fixo, específico para o campo de telefone abaixo.
        $customer->ST_TELEFONE_SAC = Clientes::getPhoneWithoutDDD($cliente->telefone_fixo); //string. Telefone.
        $customer->ST_FAX_SAC = $cliente->celular; //string. Celular.

        if($cliente->forma_pagamento === Clientes::FORMA_PAGAMENTO_CARTAO) {
            $customer->FL_PAGAMENTOPREF_SAC = 3;
        }

        if($card) {
            $customer->ST_CARTAO_SAC = $card->cardNumber; //string. Cartão de crédito.
            $customer->ST_MESVALIDADE_SAC = $card->validMonth; //number. Mês de vencimento.
            $customer->ST_ANOVALIDADE_SAC = $card->validYear; //number. Ano de vencimento.
            $customer->ST_SEGURANCACARTAO_SAC = $card->cvv; //number. Código de segurança do cartão.
        }

        $customer->DT_CADASTRO_SAC = Carbon::now()->format('m/d/Y');

        return $customer;
    }
}