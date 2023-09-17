<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API CLIENTE APP Routes
|--------------------------------------------------------------------------
*/


Route::post('/login', 'AppClienteAPIController@login');

Route::get('/getBancos', 'AppClienteAPIController@getBancos');
Route::get('/redeCredenciada', 'AppClienteAPIController@redeCredenciada');
Route::get('/redeCredenciada/pet/{idPet}', 'AppClienteAPIController@redeCredenciadaPorPet');
Route::get('/webviews', 'AppClienteAPIController@webviews');
Route::get('/parametros', 'AppClienteAPIController@parametros');
Route::get('/cron', 'AppClienteAPIController@cronPushNotifications');

Route::get('/cobrancasPorCpf/{cpf}', 'AppClienteAPIController@cobrancasPorCpf');

Route::post('/primeiroAcesso', 'AppClienteAPIController@primeiroAcesso');
Route::post('/recuperarSenha', 'AppClienteAPIController@recuperarSenha');
Route::post('/novaSenha', 'AppClienteAPIController@novaSenha');

Route::group([
    'middleware' => [
        \Barryvdh\Cors\HandleCors::class,
        'auth:api'
    ],
    'prefix' => 'cliente'
], function() {

    Route::get('/home', 'AppClienteAPIController@home');
    Route::get('/inicial', 'AppClienteAPIController@inicial');
    Route::get('/avisos', 'AppClienteAPIController@avisos');


    Route::get('/dados', 'AppClienteAPIController@clienteDados');
    Route::post('/atualizaTokenFirebase', 'AppClienteAPIController@atualizaTokenFirebase');
    Route::post('/assinarGuia', 'AppClienteAPIController@assinarGuia');
    Route::post('/avaliarNps', 'AppClienteAPIController@avaliarNps');

    Route::get('/financeiro/cartoes', 'AppClienteAPIController@getCartoesCredito');
    Route::post('/financeiro/cartoes', 'AppClienteAPIController@inserirCartao');
    Route::get('/financeiro/cartoes/principal', 'AppClienteAPIController@getCartaoPrincipal');
    Route::post('/financeiro/cartoes/principal', 'AppClienteAPIController@setCartaoPrincipal');
    Route::post('/financeiro/cartoes/inativar', 'AppClienteAPIController@inativarCartao');
    Route::get('/financeiro/forma-pagamento', 'AppClienteAPIController@getFormaPagamento');

    Route::get('/pets', 'AppClienteAPIController@meusPets');
    Route::get('/pet/{idPet}/guias', 'AppClienteAPIController@petGuias');
    Route::get('/pet/{idPet}/guia/{numeroGuia}', 'AppClienteAPIController@petGuiaDetalhes');
    Route::get('/pet/{idPet}/cobertura', 'AppClienteAPIController@petPlanoCobertura');
    Route::get('/pet/{idPet}/cobertura/{idGrupo}/procedimentos', 'AppClienteAPIController@petPlanoCoberturaProcedimentos');
    Route::get('/pet/{idPet}/dados', 'AppClienteAPIController@petDados');
    Route::post('/pet/{idPet}/addFoto', 'AppClienteAPIController@addFoto');
    Route::post('/pet/{idPet}/solicitacaoCarteirinha', 'AppClienteAPIController@solicitacaoCarteirinha');
    Route::post('/pet/{idPet}/avaliacaoPrestador', 'AppClienteAPIController@avaliacaoPrestador');
    Route::post('/pet/{idPet}/assinarAngel', 'AppClienteAPIController@assinarAngel');


    Route::post('/pet/{idPet}/solicitarReembolso', 'AppClienteAPIController@reembolsoSolicitar');
    Route::get('/reembolso/listarPets', 'AppClienteAPIController@reembolsoListarPet');
    Route::get('/reembolso/pet/{idPet}', 'AppClienteAPIController@reembolsoPet');
    Route::get('/reembolso/contasBancarias', 'AppClienteAPIController@reembolsoContasBancarias');
    Route::post('/reembolso/salvarContaBancaria', 'AppClienteAPIController@reembolsoSalvarContaBancaria');
    Route::post('/reembolso/atualizarContaBancaria/{idConta}', 'AppClienteAPIController@reembolsoAtualizarContaBancaria');
    Route::post('/reembolso/excluirContaBancaria/{idConta}', 'AppClienteAPIController@reembolsoExcluirContaBancaria');
    Route::post('/reembolso/enviarEmailFormulario', 'AppClienteAPIController@reembolsoEnviarEmailFormulario');
    Route::get('/reembolso/solicitacao/{idSolicitacao}', 'AppClienteAPIController@reembolsoSolicitacao');
    Route::post('/reembolso/cancelarSolicitacao/{idSolicitacao}', 'AppClienteAPIController@reembolsoCancelarSolicitacao');


    Route::get('/cobrancas', 'AppClienteAPIController@clienteCobrancas');
    Route::get('/cobranca/{idCobranca}/dados', 'AppClienteAPIController@clienteCobrancas');


    Route::get('/documentos', 'AppClienteAPIController@clienteDocumentos');
    Route::get('/statusDocumentacaoCliente', 'AppClienteAPIController@statusDocumentacaoCliente');
    Route::get('/documentosCadastrais', 'AppClienteAPIController@clienteDocumentosCadastrais');
    Route::post('/documentosCadastrais/enviar', 'AppClienteAPIController@clienteDocumentosCadastraisEnviar');


    Route::get('/indicacoes', 'AppClienteAPIController@clienteIndicacoes');
    Route::post('/indicar', 'AppClienteAPIController@clienteIndicar');

    Route::get('/planos/documentos', 'AppClienteAPIController@planosDocumentos');

    Route::post('/criarDocumentosClientesPets/{tabela}', function($tabela) {
        if ($tabela == 'clientes') {
            $clientes = (new App\Models\Clientes)::where('ativo', 1)->get();
            foreach ($clientes as $cliente) {
                foreach ($cliente::DOCUMENTOS_OBRIGATORIOS as $tipo => $doc) {
                    (new App\Models\DocumentosClientes)::create([
                        'tipo' => $tipo,
                        'nome' => $doc,
                        'status' => 'APROVADO',
                        'avaliacao_obrigatoria' => 1,
                        'id_cliente' => $cliente->id
                    ]);
                }
            }
        }
        if ($tabela == 'pets') {
            $pets = (new App\Models\Pets)::where('ativo', 1)->get();
            foreach ($pets as $pet) {
                (new App\Models\DocumentosPets)::create([
                    'tipo' => 'carteirinha_vacinacao',
                    'nome' => 'Carteirinha de Vacinação',
                    'status' => 'APROVADO',
                    'avaliacao_obrigatoria' => 0,
                    'id_pet' => $pet->id
                ]);
            }
        }
    });
});
