<?php

namespace App\Http\Controllers;

use App\Helpers\API\Superlogica\Client;
use App\Helpers\API\Superlogica\Plans;
use App\Helpers\API\Superlogica\Ticket;
//use App\Http\Controllers\API\VendasAPIController;
use App\Models\Leads;
use App\Models\LeadsDadosAdicionais;
use App\Models\Planos;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EcommerceController extends Controller
{
    const DESCONTO_ANUAL = 30;
    const VALOR_ADESAO = 150;
    const VALOR_ADESAO_PARTICIPATIVO = self::VALOR_ADESAO / 2;

    /**
     * Busca o Lead no superlógica
     */
    public static function findLead($email, $dadosCartao)
    {
        $lead = \App\Models\Leads::where('email', $email)->first();
        $usuarioSuperlogica = \App\Helpers\API\Superlogica\Client::exists($lead->email);
        if(empty($usuarioSuperlogica)) {
            $usuarioSuperlogica = self::createSuperlogicaUser($lead, $dadosCartao);
        }

        return $usuarioSuperlogica;
    }

    public static function createSuperlogicaUser($lead, $dadosCartao, $formaPagamento = 3) {
        $leadData = json_decode($lead->dados);
        $postData = [
            'ST_TELEFONE_SAC' => $dadosCartao['telefone'],
            'ST_NOME_SAC' => $leadData->nome,
            'ST_NOMEREF_SAC' => $leadData->nome,
            'ST_CGC_SAC' => $dadosCartao['cpf'],
            'ST_EMAIL_SAC' => $lead->email,
            'ID_GRUPO_GRP' => 1,
        ];
        $infoPagamento = [
            'FL_PAGAMENTOPREF_SAC' => $formaPagamento
        ];

        if($formaPagamento == 3) {
            $infoPagamento = array_merge($infoPagamento, [
                'ST_CARTAO_SAC' => preg_replace('/\s+/', '', $dadosCartao["numero_cartao"]),
                'ST_MESVALIDADE_SAC' => $dadosCartao["validade_mes"],
                'ST_ANOVALIDADE_SAC' => $dadosCartao["validade_ano"],
                'ST_SEGURANCACARTAO_SAC' => $dadosCartao["cvv"],
            ]);
        }

        if(Client::exists($lead->email)) {
            $client = (new Client)->get([
                'pesquisa' => "todosemails:" . $lead->email
            ]);
            if (is_array($client)) {
                $client = $client[0];
            }
            $response = (new Client)->edit($client->id_sacado_sac, $infoPagamento);
            if(is_array($response)) {
                return $response[0];
            }
            return $response;
        }
        $postData = array_merge($postData, $infoPagamento);

        $response = (new Client)->register($postData);
        if(!$response->status == "200") {

            if ($response->msg == "CPF/CNPJ inválido") {
                header('HTTP/1.1 500 Internal Server Error');
                header('Content-Type: application/json; charset=UTF-8');
                die(json_encode(array('message' => "CPF/CNPJ inválido")));
            } else if ($response->msg == "Cartão inválido.") {
                header('HTTP/1.1 500 Internal Server Error');
                header('Content-Type: application/json; charset=UTF-8');
                die(json_encode(array('message' => "Cartão inválido")));
            }
        }

        if(!empty($response)) {
            return $response;
        }

        return null;
    }

    public function assinar(Request $request) {
        //3: Cartão de Crédito, 0: Boleto
        $formaPagamento = $request->get('forma_pagamento', 3);
        $dadosCartao = [
            'telefone' => $request->get('telefone'),
            'cpf' => $request->get('cpf'),
            'numero_cartao' => $request->get('numero_cartao'),
            'validade_mes' => $request->get('validade_mes'),
            'validade_ano' => $request->get('validade_ano'),
            'cvv' => $request->get('cvv')
        ];
        $lead = Leads::where('email', $request->get('email'))->first();
        $quantidades = $request->get('pets');

        $usuarioSuperlogica = self::createSuperlogicaUser($lead, $dadosCartao, $formaPagamento);
        if($usuarioSuperlogica->status === "500") {
            return abort(500, $usuarioSuperlogica->msg);
        }
        $planoId = $request->get('plano');
        $plano = \App\Models\Planos::find($planoId);

        $idPlanoSuperlogica = $plano->id_superlogica;
        $idProduto = "999999982";
        $codVendedor = $request->get('codigo_vendedor');

        if($request->get('modalidade') === "anual") {
            $idPlanoSuperlogica = $plano->id_superlogica_anual;
            $idProduto = "3";
        }

        $preco = self::calcularPreco($request, $plano);

        $dados =  [
            'ID_SACADO_SAC' => $usuarioSuperlogica->data->id_sacado_sac,
            'DT_CONTRATO_PLC' => (new Carbon())->format('m/d/Y'),
            'PLANOS' => [],
            'OPCIONAIS' => [
                [
                    "ID_PRODUTO_PRD" => $idProduto,
                    "SELECIONAR_PRODUTO" => 1,
                    "NM_QNTD_PLP" => 1,
                    "valor_unitario" => $preco,
                    "FL_RECORRENTE_PLP" => $idProduto === "3" ? 0 : 1
                ],
            ]
        ];
        $participativo = $request->filled('participativo') ? $request->get('participativo') : false;

        if(self::VALOR_ADESAO > 0) {
            $dados['OPCIONAIS'][] = [
                "ID_PRODUTO_PRD" => 999999983,
                "SELECIONAR_PRODUTO" => 1,
                "NM_QNTD_PLP" => 1,
                "valor_unitario" => $participativo ? self::VALOR_ADESAO_PARTICIPATIVO : self::VALOR_ADESAO,
                "FL_RECORRENTE_PLP" => 0
            ];
        }

        for($i = 0; $i < $quantidades; $i++) {
            $identificador = "PLANO_" . $plano->nome_plano . (empty($codVendedor) ? "_Ecommerce" : "_" . $codVendedor . '_VD_Ecommerce') . time();
            $dados['PLANOS'][] = [
                "ST_IDENTIFICADOR_PLC" => $identificador,
                "ID_PLANO_PLA" => $idPlanoSuperlogica,
                "FL_NOTIFICARCLIENTE" => 0,
                "FL_MULTIPLO_COMPO" => 1
            ];
        }
        $PlansManager = new Plans();
        $response = $PlansManager->sign($dados);
        if(is_array($response)) {
            $response = $response[0];
        }
        if($response->status == "200") {
            $lead->convertido = 1;
            $lead->save();

            //IndicacoesController::comprar($request, $response->data->id_recebimento_recb);

            $this->notifyCompra([
                'valor' => $quantidades * $preco,
                'comprador' => $lead->nome,
                'pets' => $quantidades,
                'email' => $lead->email,
                'plano' => $plano->nome_plano,
                'telefone' => $dadosCartao['telefone']
            ]);

            //$codigoVendedor = $request->get('codigo_vendedor');

//            if($codigoVendedor) {
//                VendasAPIController::registrarVenda($codigoVendedor, $response->data->id_recebimento_receb, $lead->email);
//            }

            return [
                'link_boleto' => $response->data->link_boleto,
                'signed' => true
            ];
        } else if ($response->msg == "Cobrança não atingiu o valor mínimo para geração.") {
            return ['message' => "Cobrança não atingiu o valor mínimo para geração."];
        } else {
            return [
                "signed" => false,
                "response" => $response
            ];
        }
    }

    private static function calcularPreco(Request $request, Planos $plano) {
        $quantidades = $request->get('pets');
        $modalidade = $request->get('modalidade');
        $participativo = $request->filled('participativo') ? $request->get('participativo') : false;

        $cupom = $request->get('cupom');
        if(!empty($cupom)) {
            $cupom = Ticket::check($cupom);
        } else {
            $cupom = null;
        }

        if(is_array($cupom)) {
            $cupom = $cupom[0];
        }
        if(!$plano->ativo) {
            return [
                'status' => false,
                'message' => 'O plano não está ativo no momento.'
            ];
        }

        $familiar = false;
        $precoPlano = $plano->preco_plano_individual;
        if($quantidades > 1) {
            $familiar = true;
        }
        
        if($participativo) {
            if($plano->preco_participativo) {
                $precoPlano = $plano->preco_participativo;
            } else {
                //TODO: Checar essa regra de fallback no futuro
                $precoPlano =$precoPlano/2;
            }
        }

        if($familiar) {
//            $precoPlano = $precoPlano * 0.9;
            // Removido temporariamente o desconto de familiar
            $precoPlano = $precoPlano * 1;
        }

        $descontosPercentuais = 0;
        $decontosFixos = 0;

        if ($familiar) {
            $descontosPercentuais = 50;
        } else {
            if($modalidade === "anual") {
//            $descontosPercentuais = self::DESCONTO_ANUAL;
                $precoPlano = $precoPlano * 12;
                if ($plano->id == 36) { // PLANO BÁSICO
                    $descontosPercentuais = 20;
                }
                if ($plano->id == 37) { // PLANO ESSENCIAL
                    $descontosPercentuais = 20;
                }
                if ($plano->id == 38) { // PLANO PLATINUM
                    $descontosPercentuais = 30;
                }
                if ($plano->id == 39) { // PLANO BLACK
                    $descontosPercentuais = 30;
                }
                if ($plano->id == 40) { // PLANO SENIOR
                    $descontosPercentuais = 30;
                }
            }
        }

        if($cupom) {
            if($cupom->data->fl_percentual_cup) {
                $descontosPercentuais += $cupom->data->vl_desconto_cup;
            } else {
                $decontosFixos += $cupom->data->vl_desconto_cup;
            }
        }


        $precoFinal = ($precoPlano - $decontosFixos) * ((100 - $descontosPercentuais)/100);
        return $precoFinal;
    }

    public function notifyCompra($dadosCompra)
    {
        $data = $dadosCompra['data'] = (new Carbon())->format('d/m/Y h:i:s');
        $to      = "alexandre.moreira@lifepet.com.br";
        //$to      = "alexandre.moreira@lifepet.com.br";
        $subject = 'Compra realizada - Ecommerce - ' . $data;
        $view  = view('mail.nova_compra')->with($dadosCompra);
        $message = $view->render();
        $headers = 'From: Alexandre Moreira <alexandre.moreira@lifepet.com.br>' . "\r\n" .
                   'Reply-To: ' . "alexandre.moreira@lifepet.com.br" . "\r\n" .
                   'Cc: ' . "atendimento@lifepet.com.br" . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);
    }

    public function saveAdditionalData(Request $request) {
        $lead = Leads::where('email', $request->get('email'))->firstOrFail();

        $dadosAdicionais = LeadsDadosAdicionais::where('id_lead', $lead->id)->first();

        if(!$dadosAdicionais) {
            $dadosAdicionais = LeadsDadosAdicionais::create([
                'id_lead' => $lead->id,
                'logradouro' => $request->get('logradouro'),
                'numero' => $request->get('numero'),
                'bairro' => $request->get('bairro'),
                'cidade' => $request->get('cidade'),
                'uf' => $request->get('uf'),
                'cep' => $request->get('cep'),
                'pets' => $request->get('pets'),
            ]);
        }

        return $dadosAdicionais;
    }
}
