<?php

use App\Helpers\API\LifeQueueClient\Environment;
use App\Helpers\API\LifeQueueClient\LifeQueueClient;
use App\Helpers\API\LifeQueueClient\LifeQueueClientException;
use App\Http\Controllers\DadosTemporaisController;
use App\Http\Util\Logger;
use App\Http\Util\LogMessages;
use App\Models\Cancelamento;
use App\Models\Clientes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Modules\Guides\Http\Controllers\AutorizadorController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * Main Routes
 */
Route::get('/', function () {
    return redirect('home');
});

Route::get('/home', 'HomeController@index');

/**
 * Login
 */
Route::get('/clientes/login', function () {
    return view('area_cliente.login');
});

Route::get('/clientes/resetarSenha', function () {
    return view('area_cliente.v2.mudar_senha');
})->name('cliente.resetarSenha');

Route::post('/clientes/mudarSenha', 'ClientesController@resetPassword')->name('clientes.mudarSenha');

Auth::routes();

Route::get('/logout', "Auth\LoginController@logout")->name('getLogout');

Route::options('{path?}', function ($path) { })->where('path', '.');


/**
 * Clients
 */
Route::get('clientes/{id}/proposta/{numProposta}', 'ClientesController@proposta')->name('clientes.proposta');
Route::get('clientes/{id}/{file}', 'ClientesController@assinatura')->where('file', 'assinatura.+');
Route::post('clientes/deleteUpload', 'ClientesController@deleteUpload')->name('clientes.deleteUpload');
Route::get('clientes/aprovacao', 'ClientesController@notApproved');
Route::post('clientes/{idCliente}/aprovar', 'ClientesController@approve')->name('clientes.approve');
Route::get('clientes/{cpf}/checkClienteCpf', 'ClientesController@checkClienteCpf')->name('clientes.checkClienteCpf');
Route::post('clientes/{idCliente}/atualizarDocumentos', 'ClientesController@atualizarDocumentos')->name('clientes.atualizarDocumentos');
Route::post('clientes/{idCliente}/enviarDocumentos', 'ClientesController@enviarDocumentos')->name('clientes.enviarDocumentos');
Route::get('clientes/{id}/sync/finance', 'ClientesController@syncWithFinance')->name('clientes.finance.sync');
Route::get('clientes/{id}/sync/superlogica', 'ClientesController@syncSuperlogica')->name('clientes.superlogica.refresh');
Route::get('clientes/{id}/credit-card/add', 'ClientesController@addCreditCardForm')->name('clientes.credit-card.add.form');
Route::post('clientes/{id}/credit-card/add', 'ClientesController@addCreditCard')->name('clientes.credit-card.add');
Route::get('clientes/credit-card/success', 'ClientesController@creditCardAddSuccessPage')->name('clientes.credit-card.add.success');
Route::get('clientes/{id}/downloadProposta/{numProposta}', 'ClientesController@downloadProposta')->name('clientes.downloadProposta');
Route::post('clientes/{id}/aceiteProposta/{numProposta}', 'ClientesController@aceiteProposta')->name('clientes.aceiteProposta');

/**
 * Payment
 */
Route::get('links-pagamento/pagar/{hash}', 'LinksPagamentoController@formPagamento')->name('links-pagamento.form-pagamento');
Route::post('links-pagamento/pagar/{hash}', 'LinksPagamentoController@pagar')->name('links-pagamento.pagar');
Route::get('links-pagamento/sucesso', 'LinksPagamentoController@sucesso')->name('links-pagamento.sucesso');
Route::post('links-pagamento/{id}/', 'LinksPagamentoController@sucesso')->name('links-pagamento.sucesso');

/**
 * Pets
 */
Route::get('pets/{id}/{file}', 'PetsController@foto')->where('file', 'foto.+');

/**
 * Authenticated routes
 */
Route::group(['middleware' => ['auth']], function () {

    /**
     * Clients
     */
    Route::resource('clientes', 'ClientesController');
    Route::post('clientes/{id}/boleto-avulso', 'ClientesController@boletoAvulso')->name('clientes.boletoAvulso');
    Route::post('clientes/forcarDebito', 'ClientesController@forcarDebito')->name('clientes.forcarDebito');
    Route::post('clientes/cartoes/excluir/', 'ClientesController@excluirCartao')->name('clientes.cartoes.excluir');
    Route::post('clientes/cartoes/principal/', 'ClientesController@definirCartaoPrincipal')->name('clientes.cartoes.principal');
    Route::post('clientes/{id}/cobrancas/manual', 'ClientesController@registrarCobrancaManualmente')->name('clientes.cobrancas.manual');
    Route::get('/clientes/{id}/pets', 'ClientesController@pets')->name('clientes.pets');
    Route::get('clientes/{id}/fatura/{id_cobranca}', 'ClientesController@abrirFatura')->name('clientes.verFatura');

    /**
     * Partners
     */
    Route::resource('conveniadas', 'ConveniadaController');
    Route::get('/conveniadas/faturamento/{id}', 'ConveniadaController@faturamento')->name('conveniadas.faturamento');
    Route::post('/conveniadas/faturamento/{id}', 'ConveniadaController@faturar')->name('conveniadas.faturar');
    Route::get('/conveniadas/{conveniada}/fatura/{fatura}', 'ConveniadaController@fatura')->name('conveniadas.fatura');
    Route::post('/conveniadas/{conveniada}/fatura/{fatura}/deletar', 'ConveniadaController@delete')->name('conveniadas.fatura.deletar');
    Route::post('/conveniadas/{conveniada}/fatura/{fatura}/sincronizar', 'ConveniadaController@sync')->name('conveniadas.fatura.sincronizar');


    Route::resource('promocoes', 'PromocaoController');
    Route::resource('especialidades', 'EspecialidadesController');

    /**
     * Pets
     */
    Route::resource('pets', 'PetsController');
    Route::post('/pets/{idPet}/cancelamento', 'PetsController@cancelamento')->name('pets.cancelamento');
    Route::post('/pets/{idPet}/revogarCancelamento', 'PetsController@revogarCancelamento')->name('pets.revogarCancelamento');
    Route::get('/pets/{idPet}/reativarPet', 'PetsController@reativarPet')->name('pets.reativarPet');
    Route::get('/pets/{idPet}/ficha_avaliacao', 'PetsController@fichaAvaliacao')->name('pets.fichaAvaliacao');
    Route::get('/pets/ficha_avaliacao/buscar', 'PetsController@fichaAvaliacaoBuscar')->name('pets.fichaAvaliacaoBuscar');
    Route::post('/pets/{idPet}/store_ficha_avaliacao', 'PetsController@fichaAvaliacaoStore')->name('pets.fichaAvaliacaoStore');
    Route::post('pets/avatarCropUpload', 'PetsController@avatarCropUpload')->name('pets.avatarCropUpload');
    Route::post('pets/{idPet}/cadastrar_exame_inicial', 'PetsController@cadastrarExameInicial')->name('pets.cadastrarExameInicial');
    Route::post('pets/{idPet}/analisar_exame_inicial', 'PetsController@analisarExameInicial')->name('pets.analisarExameInicial');
    Route::delete('pets-grupos/{id}', 'PetsGruposController@delete')->name('petsGrupos.delete');
    Route::post('criarExcecaoGrupo', 'PetsController@criarExcecaoGrupo')->name('pets.criarExcecaoGrupo');
    Route::post('criarExtensaoProcedimento', 'PetsController@criarExtensaoProcedimento')->name('pets.criarExtensaoProcedimento');

    /**
     * Procedures
     */
    Route::resource('procedimentos', 'ProcedimentosController');
    Route::delete('extensao-procedimentos/{id}', 'ExtensaoProcedimentoController@delete')->name('extensaoProcedimento.delete');
    Route::get('tabelasReferencias/procedimentos/{idTabela}', 'TabelasReferenciaController@procedimentos')->name('tabelasReferencias.procedimentos');
    Route::resource('tabelasReferencias', 'TabelasReferenciaController');
    Route::post('tabelasProcedimentos/delete', function (Request $request) {
        $id = $request->get('id');
        $tabProcedimento = \App\Models\TabelasProcedimentos::findOrFail($id);
        $tabProcedimento->delete();
    })->name('tabelasProcedimentos.delete');

    Route::post('tabelasProcedimentos/create', function (Request $request) {
        $id_procedimento = $request->input('id_procedimento');
        $id_tabela_referencia = $request->input('id_tabela_referencia');
        $valor = $request->input('valor');
        $id = $request->input('id');

        $msg = [
            'type' => 'success',
            'title' => 'Sucesso!',
            'text' => 'Procedimento salvo!'
        ];

        $tabelasProcedimentos = null;

        if ($valor) {
            $tabelasProcedimentosExistente = \App\Models\TabelasProcedimentos::where('id_procedimento', $id_procedimento)->where('id_tabela_referencia', $id_tabela_referencia)->count();
            if ($id || $tabelasProcedimentosExistente == 0) {
                if ($id) {
                    $tabelasProcedimentos = \App\Models\TabelasProcedimentos::find($id);
                } else {
                    $tabelasProcedimentos = new \App\Models\TabelasProcedimentos;
                }
                $data = [
                    'id_procedimento' => $id_procedimento,
                    'id_tabela_referencia' => $id_tabela_referencia,
                    'valor' => $valor
                ];
                $tabelasProcedimentos->fill($data);
                $tabelasProcedimentos->save();
            } else {
                $msg = [
                    'type' => 'warning',
                    'title' => 'Atenção!',
                    'text' => 'Este procedimento já foi cadastrado nesta tabela!'
                ];
            }
        } else {
            $msg = [
                'type' => 'error',
                'title' => 'Atenção!',
                'text' => 'Informe um valor para este procedimento!'
            ];
        }

        return [
            //        'status' => $tabelasProcedimentos->save(),
            'data'   => $tabelasProcedimentos,
            'msg' => $msg
        ];
    })->middleware('permission:create_procedimentos');

    Route::post('pets_planos/reverter/', function (Request $request) {
        $idPetsPlanos = $request->input('id_pets_planos');
        $petsPlanos = \App\Models\PetsPlanos::findOrFail($idPetsPlanos);
        $dataAnterior = $petsPlanos->data_encerramento_contrato->format('d/m/Y');
        $petsPlanos->data_encerramento_contrato = null;
        $petsPlanos->update();
        Logger::log(
            LogMessages::EVENTO['ALTERACAO'],
            'Pets Planos',
            'ALTA',
            "O cancelamento do vínculo de plano #" . $idPetsPlanos . " foi revertido. Data de cancelamento anterior: " . $dataAnterior,
            auth()->user()->id,
            'pets_planos',
            $idPetsPlanos
        );
        \App\Http\Controllers\AppBaseController::toast('Cancelamento revertido');


        return back();
    })->name('petsPlanos.reverter');
    Route::post('pets_planos/create', 'PetsController@createPetsPlanos')->name('pets_planos.create')->middleware('permission:edit_pets');
    Route::post('pets_planos/delete', 'PetsController@deletePetsPlanos')->name('pets_planos.delete')->middleware('permission:edit_pets');

    Route::resource('grupos', 'GruposController');

    Route::post('planos/addProcedimentosGrupo', 'PlanosController@addProcedimentosGrupo')->name('planos.addProcedimentosGrupo');
    Route::post('planos/addNovoGrupo', 'PlanosController@addNovoGrupo')->name('planos.addNovoGrupo');
    Route::post('planos/editGrupo', 'PlanosController@editGrupo')->name('planos.editGrupo');
    Route::post('planos/deleteGrupo', 'PlanosController@deleteGrupo')->name('planos.deleteGrupo');
    Route::post('planos/checarProcedimentosPlano', 'PlanosController@checarProcedimentosPlano')->name('planos.checarProcedimentosPlano');
    Route::get('planos/{id}/corrigirInconsistencias', 'PlanosController@corrigirInconsistencias')->name('planos.corrigirInconsistencias');
    Route::resource('planos', 'PlanosController');

    Route::resource('cobrancas', 'CobrancasController');
    Route::post('cobrancas/cancelar', 'CobrancasController@cancelar')->name('cobrancas.cancelar');

    Route::resource('pagamentos', 'PagamentosController');


    Route::resource('links-pagamento', 'LinksPagamentoController');



    Route::get('renovacao/controle', 'RenovacaoController@controle')->name('renovacao.controle');
    Route::resource('renovacao', 'RenovacaoController', [
        'middleware' => ['auth']
    ]);

    Route::get('renovacao/v2/validar', 'RenovacaoController@remake__index');
    Route::get('renovacao/v2/previews', 'RenovacaoController@remake__previews');

    Route::post('renovacao/api/criar', 'RenovacaoController@apiCriar')->name('renovacao.api.criar');


    Route::group(['prefix' => 'informacoes_adicionais'], function () {
        Route::get('/', "InformacoesAdicionaisController@index")->name('informacoesAdicionais.index');
        Route::get('/create', "InformacoesAdicionaisController@create")->name('informacoesAdicionais.create');
        Route::post('/create', "InformacoesAdicionaisController@store")->name('informacoesAdicionais.store');
        Route::post('/{id}/delete', "InformacoesAdicionaisController@delete")->name('informacoesAdicionais.destroy');
        Route::get('/{id}/edit/', "InformacoesAdicionaisController@edit")->name('informacoesAdicionais.edit');
        Route::post('/{id}/update', "InformacoesAdicionaisController@edit")->name('informacoesAdicionais.update');
        Route::post('/vincular', "InformacoesAdicionaisController@vincular")->name('informacoesAdicionais.vincular');
    });

    Route::group(['prefix' => 'relatorios'], function () {
        Route::get('sinistralidadesGrupos', 'RelatoriosController@sinistralidadeGrupos')->name('relatorios.sinistralidadeGrupos');
        Route::get('sinistralidadesGrupos/download', 'RelatoriosController@sinistralidadeGruposDownload')->name('relatorios.sinistralidadeGrupos.download');
        Route::get('sinistralidades', 'RelatoriosController@sinistralidade')->name('relatorios.sinistralidade');
        Route::get('sinistralidades/download', 'RelatoriosController@sinistralidadesDownload')->name('relatorios.sinistralidade.download');
        Route::get('participativos', 'RelatoriosController@participativos')->name('relatorios.participativo');
        Route::get('participativos/download', 'RelatoriosController@participativosDownload')->name('relatorios.participativo.download');
        Route::get('reajustes', 'RelatoriosController@reajustes')->name('relatorios.reajustes');
        Route::get('reajustes/download', 'RelatoriosController@reajustesDownload')->name('relatorios.reajustes.download');
        Route::get('pets_sem_microchip', 'RelatoriosController@petsSemMicrochip')->name('relatorios.pets_sem_microchip');
        Route::get('pets_sem_microchip/download', 'RelatoriosController@petsSemMicrochipDownload')->name('relatorios.pets_sem_microchip.download');
        Route::get('cancelamento', 'RelatoriosController@cancelamento')->name('relatorios.cancelamento');
        Route::get('cancelamento/download', 'RelatoriosController@cancelamentoDownload')->name('relatorios.cancelamento.download');
        Route::get('coparticipacao_procedimentos_planos/{id_plano}/download', 'RelatoriosController@coparticipacaoProcedimentosPlanosDownload')->name('relatorios.coparticipacaoProcedimentosPlanos.download');

        Route::get('receitas', 'RelatoriosController@receitas')->name('relatorios.receitas');
        Route::get('receitas/download', 'RelatoriosController@receitasDownload')->name('relatorios.receitas.download');
        Route::get('receitas-picpay', 'RelatoriosController@receitasPicpay')->name('relatorios.receitas-picpay');
        Route::get('receitas-picpay/download', 'RelatoriosController@receitasPicpayDownload')->name('relatorios.receitas-picpay.download');
        Route::get('receitas-link-pagamento', 'RelatoriosController@receitasLinkPagamento')->name('relatorios.receitas-link-pagamento');
        Route::get('receitas-link-pagamento/download', 'RelatoriosController@receitasLinkPagamentoDownload')->name('relatorios.receitas-link-pagamento.download');

        Route::get('clientes', 'RelatoriosController@clientes')->name('relatorios.clientes');
        Route::get('clientes/download', 'RelatoriosController@clientesDownload')->name('relatorios.clientes.download');

        Route::get('inadimplentes', 'RelatoriosController@inadimplentes')->name('relatorios.inadimplentes');
        Route::get('inadimplentes/download', 'RelatoriosController@inadimplentesDownload')->name('relatorios.inadimplentes.download');

        Route::get('pets', 'RelatoriosController@pets')->name('relatorios.pets');
        Route::get('pets/download', 'RelatoriosController@petsDownload')->name('relatorios.pets.download');

        Route::get('compra-rapida', 'RelatoriosController@compraRapida')->name('relatorios.compraRapida');
        Route::get('indicacoes', 'RelatoriosController@indicacoes')->name('relatorios.indicacoes');
        Route::get('clientes-sem-fatura-por-competencia', 'RelatoriosController@clientesSemFaturaCompetencia')->name('relatorios.clientesSemFaturaCompetencia');
    });

    Route::resource('permissoes', 'PermissionController');
    Route::resource('papeis', 'RolesController');
    Route::post('/papeis_permissoes/{role_id}/{permission_id}/{operation}', 'RolesController@handlePermission');
    Route::post('/usuarios_papeis/{user_id}/{role_id}/{operation}', 'RolesController@handleUserRole');

    Route::group(['prefix' => 'usuarios'], function () {
        Route::get('/', "UsuariosController@index")->name('usuarios.index');
        Route::get('/create', "UsuariosController@create")->name('usuarios.create');
        Route::post('/create', "UsuariosController@store")->name('usuarios.store');
        Route::post('/{id}/delete', "UsuariosController@delete")->name('usuarios.destroy');
        Route::get('/{id}/edit/', "UsuariosController@edit")->name('usuarios.edit');
        Route::post('/{id}/update', "UsuariosController@edit")->name('usuarios.update');
        Route::get('/mudarsenha', "UsuariosController@mudarsenha")->name('usuarios.mudarsenha');
        Route::post('/updatesenha', "UsuariosController@updatesenha")->name('usuarios.updatesenha');
    });

    Route::group(['prefix' => 'extras'], function () {
        Route::get('/boletos/bichos', 'ClientesController@boletosBichos');
    });

    Route::group(['prefix' => 'documentos_internos'], function () {
        Route::get('/', 'DocumentosInternosController@index')->name('documentos_internos.index');
        Route::post('/upload', 'DocumentosInternosController@upload')->name('documentos_internos.upload');
    });

    Route::group(['prefix' => 'ajuda'], function () {
        Route::group(['prefix' => 'sugestoes'], function () {
            Route::get('/', 'SugestoesController@index')->name('ajuda.sugestoes.index');
            Route::post('/resolver/{id}', 'SugestoesController@resolver')->name('ajuda.sugestoes.realizar');
            Route::post('/ler/{id}', 'SugestoesController@ler')->name('ajuda.sugestoes.ler');
            Route::post('/priorizar', 'SugestoesController@priorizar')->name('ajuda.sugestoes.priorizar');
            Route::post('/arquivar/{id}', 'SugestoesController@arquivar')->name('ajuda.sugestoes.arquivar');
            Route::post('/create', 'SugestoesController@store')->name('ajuda.sugestoes.store');
        });
    });

    Route::group(['prefix' => 'planos_credenciados'], function () {
        Route::post('/habilitacao', 'PlanosCredenciadosController@habilitacao')->name('planosCredenciados.habilitacao');
    });

    Route::group(['prefix' => 'indicacoes'], function () {
        Route::post('/indicar', 'IndicacoesController@indicarVarios')->name('indicacoes.indicar');
        Route::get('/indicar', 'IndicacoesController@indique')->name('indicacoes.indique');
        Route::get('/', 'IndicacoesController@listar')->name('indicacoes.listar');
    });

    Route::group(['prefix' => 'homologacao'], function () { });


    Route::group(['prefix' => 'facil'], function () {
        Route::get('/', 'FreemiumController@cadastro')->name('freemium.index');
        //    Route::get('/cadastrar', 'FreemiumController@cadastro')->name('freemium.cadastro');
        Route::post('/cadastrar', 'FreemiumController@cadastrar')->name('freemium.cadastrar');
        Route::get('/indicacoes/{idCliente}', 'FreemiumController@indicacoes')->name('freemium.indicacoes');
        Route::post('/indicar', 'FreemiumController@indicar')->name('freemium.indicar');
        Route::get('/sucesso', 'FreemiumController@sucesso')->name('freemium.sucesso');
    });

    Route::group(['prefix' => 'plano_free'], function () {
        Route::get('/brasil', 'FreemiumController@plano_free')->name('clientes.plano_free');
        Route::get('/sucesso', 'FreemiumController@sucesso_plano_free')->name('clientes.sucesso_plano_free');
        Route::post('/adesao', 'FreemiumController@adesao_plano_free')->name('clientes.adesao_plano_free');
    });

    Route::group(['prefix' => 'ajudes'], function() {
        Route::get('/', 'AjudesController@cadastro')->name('ajudes.index');
        //    Route::get('/cadastrar', 'FreemiumController@cadastro')->name('freemium.cadastro');
        Route::post('/cadastrar', 'AjudesController@cadastrar')->name('ajudes.cadastrar');
        Route::get('/indicacoes/{idCliente}', 'AjudesController@indicacoes')->name('ajudes.indicacoes');
        Route::post('/indicar', 'AjudesController@indicar')->name('ajudes.indicar');
        Route::get('/sucesso', 'AjudesController@sucesso')->name('ajudes.sucesso');
    });

    Route::resource('parametros', 'ParametrosController');
    });

Route::group(['prefix' => 'tickets'], function () {
    Route::get('/meusTickets', 'TicketsController@meusTickets')->name('tickets.meusTickets');
    Route::get('/meusTicketsFinalizados', 'TicketsController@meusTicketsFinalizados')->name('tickets.meusTicketsFinalizados');
    Route::post('/atribuirTicket', 'TicketsController@atribuirTicket')->name('tickets.atribuirTicket');
    Route::post('/iniciarTicket', 'TicketsController@iniciarTicket')->name('tickets.iniciarTicket');
    Route::post('/finalizarTicket', 'TicketsController@finalizarTicket')->name('tickets.finalizarTicket');
    Route::post('/responderTicket', 'TicketsController@responderTicket')->name('tickets.responderTicket');
});

Route::group(['prefix' => 'solicitacoes_reembolso'], function () {
    Route::get('/', 'SolicitacoesReembolsoController@index')->name('solicitacoes_reembolso.index');
    Route::get('/{id}/edit/', 'SolicitacoesReembolsoController@edit')->name('solicitacoes_reembolso.edit');
    Route::get('/{id}/analisar/', 'SolicitacoesReembolsoController@analisar')->name('solicitacoes_reembolso.analisar');
    Route::get('/{id}/cancelar/', 'SolicitacoesReembolsoController@cancelar')->name('solicitacoes_reembolso.cancelar');
    Route::post('/{id}/aprovar_auditoria/', 'SolicitacoesReembolsoController@aprovar_auditoria')->name('solicitacoes_reembolso.aprovar_auditoria');
    Route::post('/{id}/recusar_auditoria/', 'SolicitacoesReembolsoController@recusar_auditoria')->name('solicitacoes_reembolso.recusar_auditoria');
    Route::post('/{id}/aprovar_financeiro/', 'SolicitacoesReembolsoController@aprovar_financeiro')->name('solicitacoes_reembolso.aprovar_financeiro');
    Route::post('/{id}/recusar_financeiro/', 'SolicitacoesReembolsoController@recusar_financeiro')->name('solicitacoes_reembolso.recusar_financeiro');
    Route::get('/{id}/pagar/', 'SolicitacoesReembolsoController@pagar')->name('solicitacoes_reembolso.pagar');
});

Route::group(['prefix' => 'comercial', 'middleware' => ['auth']], function () {
    Route::get('/inside_sales', 'ComercialController@create')->name('comercial.inside_sales');
    Route::post('/inside_sales/cadastro', 'ComercialController@inside_sales_cadastro')->name('comercial.inside_sales_cadastro');
});

Route::resource('urh', 'UrhController');

Route::group(['prefix' => 'comunicados_credenciados'], function() {
    Route::post('/salvar', 'ComunicadosCredenciadosController@save')->name('comunicados_credenciados.salvar');
    Route::get('/', 'ComunicadosCredenciadosController@listar')->name('comunicados_credenciados.listar');
    Route::get('/criar', 'ComunicadosCredenciadosController@create')->name('comunicados_credenciados.criar');
});

Route::group(['prefix' => 'renewals/v2'], function() {
    Route::group(['prefix' => 'api'], function() {
        Route::get('previews', 'RenovacaoController@remake__previews');
        Route::get('details', 'RenovacaoController@remake__renewal_details');
        Route::post('new', 'RenovacaoController@remake__store_new_renewal');
        Route::post('skip', 'RenovacaoController@remake__store_skip_renewal');
    });
});

Route::get('renovacao/v2/previews', 'RenovacaoController@remake__previews');
Route::get('renovacao/v2/details', 'RenovacaoController@remake__renewal_details');

Route::get('vendedores/{id}/assinatura', 'VendedoresController@assinatura')->name('vendedores.assinatura');
Route::get('vendedores/{id}/avatar', 'VendedoresController@avatar')->name('vendedores.avatar');
Route::resource('vendedores', 'VendedoresController');

Route::get('autorizador/login', 'AutorizadorController@login')->name('autorizador.login');
Route::post('autorizador/login', 'AutorizadorController@tryLogin')->name('autorizador.tryLogin');

Route::group(['prefix' => 'notas', 'middleware' => ['auth']], function() {
    Route::post('/excluir', function (Request $request) {
        if (!$request->filled('idNota')) {
            abort(404);
        }

        $idNota = $request->get('idNota');

        $nota = \App\Models\Notas::find($idNota);
        if (!$nota) {
            return back();
        }
        $dadosJson = json_encode([
            'id' => $nota->id,
            'cliente' => [
                'id' => $nota->cliente->id,
                'nome' => $nota->cliente->nome_cliente
            ],
            'corpo' => $nota->corpo
        ]);


        if ($nota->delete()) {

            $mensagem = "Uma nota foi excluída do sistema. {$dadosJson}";
            Logger::log(
                LogMessages::EVENTO['EXCLUSAO'],
                'Notas',
                'MEDIA',
                $mensagem,
                auth()->user()->id,
                'notas',
                $idNota
            );


            return ['success' => true];
        }

        return ['success' => false];
    });
    Route::get('/{idCliente}', function ($idCliente) {
        return \App\Models\Notas::where('cliente_id', $idCliente)->orderBy('created_at', 'desc')->get()->map(function (\App\Models\Notas $nota) {
            return [
                'id'    => $nota->id,
                'created_at' => $nota->created_at->format('d/m/Y H:i:s'),
                'corpo' => $nota->corpo,
                'autor' => $nota->user()->first()->name
            ];
        });
    });
    Route::post('/{idCliente}', function (Request $request, $idCliente) {
        $cliente = \App\Models\Clientes::find($idCliente);
        if (!$idCliente) {
            return \Illuminate\Http\Response::json(array(
                'code'      =>  500,
                'message'   =>  'Usuário não encontrado'
            ), 500);
        }

        return \App\Models\Notas::create([
            'user_id' => Auth::user()->id,
            'cliente_id' => $cliente->id,
            'corpo' => $request->get('corpo')
        ]);
    });
});

// NOTAS DE PLANOS
Route::post('/notas-planos/excluir', function (Request $request) {
    if (!$request->filled('idNota')) {
        abort(404);
    }

    $idNota = $request->get('idNota');

    $nota = \App\NotaPlano::find($idNota);
    if (!$nota) {
        return back();
    }

    if ($nota->delete()) {
        $mensagem = 'A nota #' . $idNota . " foi excluída.";
        Logger::log(
            LogMessages::EVENTO['EXCLUSAO'],
            'Notas',
            'MEDIA',
            $mensagem,
            auth()->user()->id,
            'notas',
            $idNota
        );

        return ['success' => true];
    }

    return ['success' => false];
});
Route::get('/notas-planos/{idPlano}', function ($idPlano) {
    return \App\NotaPlano::where('plano_id', $idPlano)->orderBy('created_at', 'desc')->get()->map(function (\App\NotaPlano $nota) {
        return [
            'id'    => $nota->id,
            'created_at' => $nota->created_at->format('d/m/Y H:i:s'),
            'corpo' => $nota->corpo,
            'autor' => $nota->user()->first()->name
        ];
    });
});
Route::post('/notas-planos/{idPlano}', function (Request $request, $idPlano) {
    $plano = \App\Models\Planos::find($idPlano);
    if (!$idPlano) {
        return \Illuminate\Http\Response::json(array(
            'code'      =>  500,
            'message'   =>  'Usuário não encontrado'
        ), 500);
    }

    return \App\NotaPlano::create([
        'user_id' => Auth::user()->id,
        'plano_id' => $plano->id,
        'corpo' => $request->get('corpo')
    ]);
});

Route::get('/clientes/{idCliente}/imprimir', function ($idCliente) {
    $cliente = \App\Models\Clientes::find($idCliente);
    $pets = $cliente->pets()->get();
    $dados = [
        'cliente' => $cliente,
        'pets' => $pets
    ];
    return view('clientes.imprimir')->with($dados);
})->name('clientes.imprimir');
Route::post('/clientes/{idCliente}/upload', 'ClientesController@upload')->name('clientes.upload');

Route::group(['prefix' => 'cliente'], function () {
    Route::get('/', 'AreaClienteController@index')->name('cliente.home');
    Route::get('/dados', 'AreaClienteController@dados')->name('cliente.dados');
    Route::get('/coberturas', 'AreaClienteController@coberturas')->name('cliente.coberturas');
    Route::get('/pets', 'AreaClienteController@pets')->name('cliente.pets');
    Route::get('/pet/{id}', 'AreaClienteController@pet')->name('cliente.pet');
    Route::get('/financeiro', 'AreaClienteController@financeiro')->name('cliente.financeiro');
    Route::post('/alterar_dados', 'AreaClienteController@alterarDados')->name('cliente.alterarDados');
    Route::post('/solicitar_reembolso', 'AreaClienteController@solicitarReembolso')->name('cliente.solicitarReembolso');
    Route::get('/documentos', 'AreaClienteController@documentos')->name('cliente.documentos');
    Route::get('/encaminhamentos', 'AreaClienteController@encaminhamentos')->name('cliente.encaminhamentos');
    Route::post('/escolherCredenciado', 'AreaClienteController@escolherCredenciado')->name('cliente.escolherCredenciado');
    Route::get('/contrato', 'AreaClienteController@contrato')->name('cliente.contrato');
    Route::post('/segundoResponsavel', 'ClientesController@saveSegundoResponsavel')->name('cliente.salvarSegundoResponsavel');

    Route::get('login', function() {
        return view('area_cliente.v2.login');
    })->name('cliente.login');
    Route::post('login', function (Request $request) {
        $c = new \App\Http\Controllers\AreaClienteController();
        return $c->doLogin($request);
    })->name('cliente.doLogin');
    Route::post('registrar', function (Request $request) {
        $c = new \App\Http\Controllers\AreaClienteController();
        return $c->registrar($request);
    })->name('cliente.registrar');
    Route::get('logout', function (Request $request) {
        $c = new \App\Http\Controllers\AreaClienteController();
        return $c->logout($request);
    })->name('cliente.logout');
    Route::post('/criarUsuario/{id}', 'AreaClienteController@criarUsuario')->name('cliente.criarUsuario');
    Route::post('/resetarSenhaCliente/{id}', 'AreaClienteController@resetarSenhaCliente')->name('cliente.resetarSenhaCliente');
    Route::get('/home', function () {
        if (auth()->user()) {
            return redirect(route('cliente.home'));
        }
        return redirect(route('cliente.login'));
    });
});

Route::group(['prefix' => 'pet'], function() {
    Route::get('/{microchip}', function($microchip) {
        return \App\Models\Pets::where('numero_microchip', $microchip)->get()->map(function(\App\Models\Pets   $pet) {
            $publicPet = new \stdClass();
            $publicPet->id = $pet->id;
            $publicPet->nome_pet = $pet->nome_pet;
            $publicPet->id_cliente = $pet->id_cliente;
            $publicPet->nome_cliente = $pet->cliente()->first()->nome_cliente;
            $publicPet->ativo = $pet->ativo;
            $publicPet->statusPagamento = $pet->statusPagamento();
            $publicPet->bichos = $pet->isBichos();
            $publicPet->doencasPreExistentes = $pet->doencas_pre_existentes;
            $publicPet->id_plano = $pet->plano()->id;
            $publicPet->nome_plano = $pet->plano()->nome_plano;
            $publicPet->planoHabilitadoParaClinica = $pet->plano()->isHabilitadoParaClinica(auth()->user()->clinica);
            $publicPet->isento = $pet->plano()->isento;
            $publicPet->pago = false;
            $publicPet->restricaoProcedimentos = false;
            if(auth()->user()->clinica) {
                $publicPet->restricaoProcedimentos = auth()->user()->clinica->restricao_procedimentos ?: false;
            }

            $gruposInternacao = ['20100','99914','99917','99920', '10101037'];

            $plano = $pet->plano();

            if(\Entrust::hasRole('ADMINISTRADOR')){
                $publicPet->procedimentos = \App\Models\Procedimentos::where('ativo',1)->get()->map(function($p) use ($plano) {
                    $p->valor_cliente = \App\Models\PlanosProcedimentos::getValorCliente($p, $plano);
                    $planosProcedimentos = \App\Models\PlanosProcedimentos::where('id_plano', $plano->id)->where('id_procedimento', $p->id)->first();

                    $p->pivot = [
                        'observacao' => $planosProcedimentos ? $planosProcedimentos->observacao : '',
                        'beneficio_tipo' => $planosProcedimentos ? $planosProcedimentos->beneficio_tipo : 'FIXO',
                        'beneficio_valor' => $planosProcedimentos ? $planosProcedimentos->beneficio_valor : 0
                    ];

                    return $p;
                });

                $publicPet->procedimentoInternacao = \App\Models\Procedimentos::whereIn('id_grupo', ['20100','99914','99917','99920', '10101037'])->get();
            }else{
                $queryProcedimentos = $pet->plano()->procedimentos()->where('ativo', 1);

                if(!auth()->user()->clinica || auth()->user()->clinica->id !== 237) {
                     $queryProcedimentos->where('id_procedimento', '<>', 101011925);
                }
                $publicPet->procedimentos = $queryProcedimentos->get()->map(function($p) use ($plano, $pet) {
                    $p->valor_cliente = \App\Models\PlanosProcedimentos::getValorCliente($p, $plano);
                    
                    //Verificar a carência
                    $p->carencia_cumprida = $pet->validarCarenciaProcedimento($p);
                    return $p;
                });

                $publicPet->procedimentos = $publicPet->procedimentos->toArray();

                $publicPet->procedimentoInternacao = $pet->plano()->procedimentos()->whereIn('id_grupo', ['20100','99914','99917','99920', '10101037'])->get();
            }

            $publicPet->nome_plano = $pet->plano()->nome_plano;
            $publicPet->participativo = $pet->plano()->participativo;

            return $publicPet;
        });
    });
});

Route::get('/uploads/{file}', function ($fileName) {
    $upload = \App\Models\Uploads::where('path', 'uploads/' . $fileName)->first();
    if (!$upload) {
        abort(404);
    }
    $file = \Illuminate\Support\Facades\Storage::disk('local')->get($upload->path);
    return (new \Illuminate\Http\Response($file, 200))
        ->header('Content-Type', $upload->mime);
});

Route::group(['prefix' => 'alteracoes_cadastrais', 'middleware' => ['auth']], function () {
    Route::get('/', 'AreaClienteController@alteracoesCadastrais')->name('alteracoesCadastrais.index');
    Route::post('/{id}/ler', 'AlteracoesCadastraisController@ler')->name('alteracoesCadastrais.ler');
    Route::post('/{id}/resolver', 'AlteracoesCadastraisController@resolver')->name('alteracoesCadastrais.resolver');
});

Route::group(['prefix' => 'app_cadastro_cliente', 'middleware' => ['auth']], function () {
    Route::get('/', 'AppCadastroClienteController@index')->name('app_cadastro_cliente.index');
    Route::get('/resumo/{idCliente}', 'AppCadastroClienteController@resumo')->name('app_cadastro_cliente.resumo');
    Route::get('/resumoPets/{idCliente}', 'AppCadastroClienteController@resumo_pets')->name('app_cadastro_cliente.resumo_pets');
    Route::get('/cadastroPets/{idCliente}/{qtdPets}', 'AppCadastroClienteController@cadastroPets')->name('app_cadastro_cliente.cadastro_pets');
    Route::get('/cadastro', 'AppCadastroClienteController@cadastro')->name('app_cadastro_cliente.cadastro');
    Route::post('/cadastrar', 'AppCadastroClienteController@cadastrar')->name('app_cadastro_cliente.cadastrar');
    //    Route::post('/confirmarVenda', 'AppCadastroClienteController@confirmarVenda')->name('app_cadastro_cliente.confirmarVenda');
    Route::post('/cadastrarEndereco', 'AppCadastroClienteController@cadastrarEndereco')->name('app_cadastro_cliente.cadastrarEndereco');
    Route::post('/cadastrarPet', 'AppCadastroClienteController@cadastrarPet')->name('app_cadastro_cliente.cadastrarPet');
    Route::post('/salvarDadosResumo', 'AppCadastroClienteController@salvarDadosResumo')->name('app_cadastro_cliente.salvarDadosResumo');
    Route::get('/indicacoes/{idCliente}', 'AppCadastroClienteController@indicacoes')->name('app_cadastro_cliente.indicacoes');
    Route::post('/indicar', 'AppCadastroClienteController@indicar')->name('app_cadastro_cliente.indicar');
    Route::get('/sucesso', 'AppCadastroClienteController@sucesso')->name('app_cadastro_cliente.sucesso');
    Route::get('/proposta/{idCliente}', 'AppCadastroClienteController@proposta')->name('app_cadastro_cliente.proposta');
    Route::post('/propostaPdf/{idCliente}', 'AppCadastroClienteController@propostaPdf')->name('app_cadastro_cliente.propostaPdf');
    Route::get('/propostaManual/{idCliente}', 'AppCadastroClienteController@propostaManual')->name('app_cadastro_cliente.propostaManual');
});

Route::group(['prefix' => 'bichos'], function () {
    Route::get('/matricular', function () {
        $ids = \App\Models\Planos::where('bichos', 1)->get(['id'])->pluck('id');
        $petsPlanos = \App\Models\PetsPlanos::whereIn('id_plano', $ids)->groupBy('id_pet')->orderBy('created_at', 'DESC')->get();
        $chips = [];
        foreach ($petsPlanos as $pp) {
            $pet = $pp->pet()->first();

            if (!$pet->numero_microchip) {
                $pet->numero_microchip = "CDB" . $pet->id;
                $pet->update();
            }
        }

        \App\Http\Controllers\AppBaseController::setSuccess('Matrículas geradas!');
    });

    Route::get('/criar_usuarios', function () {
        $ids = \App\Models\Planos::where('bichos', 1)->get(['id'])->pluck('id');
        $petsPlanos = \App\Models\PetsPlanos::whereIn('id_plano', $ids)->groupBy('id_pet')->orderBy('created_at', 'DESC')->get();

        foreach ($petsPlanos as $pp) {
            $pet = $pp->pet()->first();
            $cliente = $pet->cliente()->first();
            if (!$cliente->hasUser()) {
                if (!$cliente->email) {
                    $errorBag[] = [
                        'erro' => 'Email inválido',
                        'id' => $cliente->id,
                        'nome' => $cliente->nome_cliente
                    ];
                } else {
                    \App\Http\Controllers\AreaClienteController::doCreateUser($cliente->id);
                }
            }
        }


        if (count($errorBag)) {
            \App\Http\Controllers\AppBaseController::setSuccess('Alguns usuários foram gerados mas houveram erros em alguns cadastros. Verifique com atenção: ' . addslashes(json_encode($errorBag)));
        } else {
            \App\Http\Controllers\AppBaseController::setSuccess('Usuários gerados!');
        }

        return redirect('home');
    });
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth']], function () {
    Route::get('/', 'DashboardController@home')->name('dashboard.home');
    Route::group(['prefix' => 'api'], function () {
        Route::get('/vidasAtivas', 'DashboardController@vidasAtivas')->name('dashboard.vidasAtivas');
        Route::get('/vidasAtivasMensais', 'DashboardController@vidasAtivasMensais')->name('dashboard.vidasAtivasMensais');
        Route::get('/vidasAtivasAnuais', 'DashboardController@vidasAtivasAnuais')->name('dashboard.vidasAtivasAnuais');
        Route::get('/vidasInativas', 'DashboardController@vidasInativas')->name('dashboard.vidasInativas');
        Route::get('/vendas', 'DashboardController@vendas')->name('dashboard.vendas');
        Route::get('/cancelamentos', 'DashboardController@cancelamentos')->name('dashboard.cancelamentos');
        Route::get('/cancelamentosSerial', 'DashboardController@cancelamentosSerial')->name('dashboard.cancelamentosSerial');
        Route::get('/novasVidasSerial', 'DashboardController@novasVidasSerial')->name('dashboard.novasVidasSerial');
        Route::get('/sinistralidadeDiaria', 'DashboardController@sinistralidadeDiaria')->name('dashboard.sinistralidadeDiaria');
        Route::get('/sinistralidadeMensal', 'DashboardController@sinistralidadeMensal')->name('dashboard.sinistralidadeMensal');
        Route::get('/atrasoMensal', 'DashboardController@atrasoMensal')->name('dashboard.atrasoMensal');
        Route::get('/faturamentoMensal', 'DashboardController@faturamentoMensal')->name('dashboard.faturamentoMensal');
        Route::get('/sinistralidadePorCredenciada', 'DashboardController@sinistralidadePorCredenciada')->name('dashboard.sinistralidadePorCredenciada');
        Route::get('/petsAniversariantes', 'DashboardController@petsAniversariantes')->name('dashboard.petsAniversariantes');
        Route::get('/mediaRecorrenteMensal', 'DashboardController@mediaRecorrenteMensal')->name('dashboard.mediaRecorrenteMensal');
        Route::get('/clientesAniversariantes', 'DashboardController@clientesAniversariantes')->name('dashboard.clientesAniversariantes');
        Route::get('/caes', 'DashboardController@caes')->name('dashboard.caes');
        Route::get('/gatos', 'DashboardController@gatos')->name('dashboard.gatos');
        Route::get('/petsPorBairro', 'DashboardController@petsPorBairro')->name('dashboard.petsPorBairro');
        Route::get('/petsPorCidade', 'DashboardController@petsPorCidade')->name('dashboard.petsPorCidade');
        Route::get('/petsInativosPorBairro', 'DashboardController@petsInativosPorBairro')->name('dashboard.petsInativosPorBairro');
        Route::get('/petsInativosPorCidade', 'DashboardController@petsInativosPorCidade')->name('dashboard.petsInativosPorCidade');
        Route::get('/sinistralidadePorPrestador', 'DashboardController@sinistralidadePorPrestador')->name('dashboard.sinistralidadePorPrestador');
        Route::get('/faturamentoMensalPrevisto', 'DashboardController@faturamentoMensalPrevisto')->name('dashboard.faturamentoMensalPrevisto');
        Route::get('/petsParticipativosVersusIntegrais', 'DashboardController@petsParticipativosVersusIntegrais')->name('dashboard.petsParticipativosVersusIntegrais');
        Route::get('/petsPorPlano', 'DashboardController@petsPorPlano')->name('dashboard.petsPorPlano');
        Route::get('/petsPorIdade', 'DashboardController@petsPorIdade')->name('dashboard.petsPorIdade');
        Route::get('/vendasPorVendedor', 'DashboardController@vendasPorVendedor')->name('dashboard.vendasPorVendedor');
        Route::get('/controleVacinas', 'DashboardController@controleVacinas')->name('dashboard.controleVacinas');
        Route::get('/vencimentoVacinas', 'DashboardController@vencimentoVacinas')->name('dashboard.vencimentoVacinas');
        Route::get('/rankingVendedores', 'DashboardController@rankingVendedores')->name('dashboard.rankingVendedores');
        Route::get('/comissaoVendas', 'DashboardController@comissaoVendas')->name('dashboard.comissaoVendas');
        Route::get('/npsSurveyMonkey', 'DashboardController@npsSurveyMonkey')->name('dashboard.npsSurveyMonkey');
        Route::get('/nps', 'DashboardController@nps')->name('dashboard.nps');
        Route::get('/participativos', 'DashboardController@participativos')->name('dashboard.participativos');
        Route::get('/rentabilidadeDePlano', 'DashboardController@rentabilidadeDePlano')->name('dashboard.rentabilidadeDePlano');
        Route::get('/castradosVersusNaoCastrados', 'DashboardController@castradosVersusNaoCastrados')->name('dashboard.castradosVersusNaoCastrados');
        Route::get('/rankingProcedimentos', 'DashboardController@rankingProcedimentos')->name('dashboard.rankingProcedimentos');
        Route::get('/statusPetsPlanos/{status}', 'DashboardController@statusPetsPlanos')->name('dashboard.statusPetsPlanos');
    });
});

Route::post('cron/superlogica', function (Request $request) {
    set_time_limit(400);
    //Realiza os registros de dados temporais
    DadosTemporaisController::cronSuperlogica();

    $status = Logger::log(
        LogMessages::EVENTO['CRIACAO'],
        'cron',
        LogMessages::IMPORTANCIA['BAIXA'],
        "As métricas financeiras do Superlógica do dia foram gravados com sucesso."
    );

    return ['status' => 'ok'];
});

Route::post('cron', function (Request $request) {

    //Realiza os registros de dados temporais
    DadosTemporaisController::cron();
    $dataReferencia = $request->get('dataReferencia', null);
    if (!$dataReferencia) {
        $dataReferencia = (new Carbon());
    } else {
        $dataReferencia = Carbon::createFromFormat('Y-m-d', $dataReferencia);
    }

    AutorizadorController::invalidarEncaminhamentosExpirados($dataReferencia);
    //Invalida as guias expiradas de encaminhamento
    Logger::log(
        LogMessages::EVENTO['CRIACAO'],
        'cron',
        LogMessages::IMPORTANCIA['BAIXA'],
        "Os dados do dia " . $dataReferencia->format('d/m/Y') . " foram gravados com sucesso."
    );

    return ['status' => 'ok'];
});

Route::post('cron/cancelamentos-agendados', function(Request $request) {
    //Realiza os cancelamentos agendados com a data do dia
    (new \App\Http\Controllers\PetsController(new \App\Repositories\PetsRepository(app())))->realizarCancelamentosAgendados();
});

Route::post('cron/renovacoes', function(Request $request) {
    $date = $request->get('date', null);
    if($date) {
        $date = Carbon::createFromFormat('Y-m-d', $date);
    }

    (new \App\Http\Controllers\RenovacaoController(new \App\Repositories\RenovacaoRepository(app())))->realizarRenovacoesAgendadas($date);
    (new \App\Http\Controllers\RenovacaoController(new \App\Repositories\RenovacaoRepository(app())))->converterAnuais($date);

    return [
        'success' => true
    ];
});

Route::get('cron/avaliacoes', function(Request $request) {

    $horas = 3;
    $start = Carbon::now()->subHours($horas);
    $end = Carbon::now();

    $historicoUso = \Modules\Guides\Entities\HistoricoUso::where('status', \Modules\Guides\Entities\HistoricoUso::STATUS_LIBERADO)
        ->where(function($query) use ($start, $end) {
            $query->where(function($query) use ($start, $end) {
                $query->where('tipo_atendimento', "!=", \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                    ->whereBetween('historico_uso.created_at', [$start, $end]);
            });
            $query->orWhere(function($query) use ($start, $end) {
                $query->where('tipo_atendimento', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                    ->whereBetween('historico_uso.realizado_em', [$start, $end]);
            });
        })
        ->whereHas('procedimento', function ($query) {
            $query->whereIn('id_grupo', array_merge(
                \App\Models\Procedimentos::$gruposConsulta,
                \App\Models\Procedimentos::$gruposEspecialistas
            ));
        })
        ->get();

    foreach ($historicoUso as $hu) {
        $hu->sendSmsAvaliacao();
        $hu->sendMailAvaliacao();
    }

    $mensagemLog = "Foram enviados os pedidos de avaliação de prestadores para os clientes das consultas de " .
        $start->format('d/m/Y H:i:s') . " até " . $end->format('d/m/Y H:i:s') .
        " foram gravados com sucesso.";
    Logger::log(
        LogMessages::EVENTO['CRIACAO'],
        'cron',
        LogMessages::IMPORTANCIA['MEDIA'],
        $mensagemLog);

    return ['status' => 'ok'];
});

Route::post('cron/sincronizar/lifepet-angels', function() {
    $angels = \App\Models\Pets::where('angel', 1)->where('ativo', 1);

    foreach($angels as $angel) {
        $cliente = $angel->cliente;
        if(!$cliente->id_externo) {
            $mensagemLog = "O cliente {$cliente->nome_cliente} não possui código de vínculo com o SF. Não foi possível lançar a cobrança do plano 'Angels'";

            Logger::log(
                \App\Http\Util\LogEvent::WARNING,
                'clientes',
                LogMessages::IMPORTANCIA['MEDIA'],
                $mensagemLog,
                null,
                'clientes',
                $cliente->id);
            return;
        }

        $financeiro = new \App\Helpers\API\Financeiro\Financeiro();

        try {
            $customer = $financeiro->get('customer/refcode/' . $cliente->id_externo);
        } catch (Exception $e) {
            $mensagemLog = "O cliente {$cliente->nome_cliente} não pôde ser encontrado no SF com o 'refcode' informado. Não foi possível lançar a cobrança do plano 'Angels'";

            Logger::log(
                \App\Http\Util\LogEvent::WARNING,
                'clientes',
                LogMessages::IMPORTANCIA['MEDIA'],
                $mensagemLog,
                null,
                'clientes',
                $cliente->id);

            return;
        }

        try {
            $invoice = $financeiro->get("customer/{$customer->data->id}/invoice-in-progress");
            $hasAngel = false;
            $angelKey = 'PLANO ANGEL - ' . $angel->id;
            foreach($invoice->data->itens as $item) {
                if($item->name == $angelKey) {
                    $hasAngel = true;
                }
            }

            if($hasAngel) {
                //Item já lançado na fatura aberta.
                return;
            }
        } catch (Exception $e) {
            $mensagemLog = "Não foi possível encontrar uma fatura aberta para o cliente {$cliente->nome_cliente}. Não foi possível lançar a cobrança do plano 'Angels'";

            Logger::log(
                \App\Http\Util\LogEvent::WARNING,
                'clientes',
                LogMessages::IMPORTANCIA['MEDIA'],
                $mensagemLog,
                null,
                'clientes',
                $cliente->id);

            return;
        }

        try {
            $form = [
                "item" => [
                    [
                        'type' => 'D',
                        'name' => $angelKey,
                        'quantity' => 1,
                        'price' => number_format($angel->valor_angel, 2)
                    ]
                ]
            ];

            $financeiro->post('invoice/' . $invoice->data->id, $form);
        } catch (Exception $e) {
            $mensagemLog = "Não foi possível lançar um novo item na fatura aberta do cliente {$cliente->nome_cliente}. Não foi possível lançar a cobrança do plano 'Angels'";

            Logger::log(
                \App\Http\Util\LogEvent::WARNING,
                'clientes',
                LogMessages::IMPORTANCIA['MEDIA'],
                $mensagemLog,
                null,
                'clientes',
                $cliente->id);

            return;
        }

        $mensagemLog = "Lifepet Angel do cliente {$cliente->nome_cliente}, PET {$angel->nome_pet} lançado com sucesso.";

        Logger::log(
            \App\Http\Util\LogEvent::NOTICE,
            'clientes',
            LogMessages::IMPORTANCIA['BAIXA'],
            $mensagemLog,
            null,
            'clientes',
            $cliente->id);

        return [
            'success' => true
        ];
    }
});

Route::post('cron/finance/sync/names', function() {
    /**
     * @var Clientes[]
     */
    $activeClients = \App\Models\Clientes::query()->where('ativo', 1)->where('updated_at', '>=', '2021-03-01')->get(['nome_cliente', 'id_externo']);

    dispatch(new \App\Jobs\SyncClientInfoWithFinance($activeClients->all()));

    return [
        'clients' => $activeClients->map(function($c) { return $c->nome_cliente; })
    ];
});

Route::get('cron/cancelamento-automatico', function() {
    //TODO: Verificação minuciosa.
    return;
});

Route::group(['prefix' => 'timesheet', 'middleware' => ['auth']], function () {
    Route::get('/', function () {
        return view('timesheet.index');
    })->name('timesheet.index');

    Route::group(['prefix' => 'api'], function () {
        Route::get('/departamentos', 'TimesheetController@departamentos')->name('timesheet.api.departamentos');
        Route::get('/projetos', 'TimesheetController@projetos')->name('timesheet.api.projetos');
        Route::post('/projeto', 'TimesheetController@projeto')->name('timesheet.api.projeto');
        Route::get('/tarefas', 'TimesheetController@tarefas')->name('timesheet.api.tarefas');
        Route::post('/tarefa', 'TimesheetController@tarefa')->name('timesheet.api.tarefa');
        Route::get('/status', 'TimesheetController@status')->name('timesheet.api.status');
        Route::get('/corrente', 'TimesheetController@corrente')->name('timesheet.api.corrente');
        Route::post('/iniciar', 'TimesheetController@iniciar')->name('timesheet.api.iniciar');
        Route::post('/parar', 'TimesheetController@parar')->name('timesheet.api.parar');
        Route::get('/historico', 'TimesheetController@historico')->name('timesheet.api.historico');
        Route::post('/timesheet', 'TimesheetController@timesheet')->name('timesheet.api.timesheet');
    });
});

Route::group(['prefix' => 'log', 'middleware' => ['auth']], function() {
    Route::get('/', 'LogController@index')->name('log.index');

    Route::get('/live', 'LogController@index')->name('log.live');

    Route::group(['prefix' => 'api'], function() {
        Route::get('/search', 'LogController@search')->name('log.search');
        Route::get('/parameters', 'LogController@parameters')->name('log.parameters');
        Route::get('/ecommerce', 'LogController@ecommerce')->name('log.ecommerce');
    });
});

Route::group([
    'prefix' => 'cron/relatorios'
], function() {
    Route::get('/criar_relatorio_pets_planos', 'RelatoriosController@criarRelatorioPetsPlanosRetroativo');
});

Route::get('/helpers/push', function() {
    $queque = new LifeQueueClient(Environment::PRODUCTION);

    $token = 'e_a3eMmWoTA:APA91bG4ce9a2xX3HG59XKqh20Y8KJhUCnZG3jnPCe3Ocxh0KCL0VbjpaOpyieMQjysp7fjgWV9gMPImEjciJlrzd5SMnN0Ac-R4D6jC3GdO33g61bQapPwvEcXElU4wLo2-VvJRhsCp';
    $title = "TESTE - Notificação Lifepet!";
    $message = "Apenas um teste de notificação.";

    try {
        $queque->push($token, $title, $message);
        $queque->toQueue();
        echo 'Queue ID: ',$queque->response()->message->id,"\n";
    }
    catch (LifeQueueClientException $e){
        echo $e->getMessage();
    }
});

Route::get('/push/report', function() {
    ini_set('max_execution_time', 60 * 60);

    DB::disconnect();
    Config::set('database.default', 'production');
    DB::reconnect();

    $filename = "clientes-push-20201230";
    $file =  storage_path("csv\\{$filename}.csv");

    $clients = \App\Helpers\Utils::csvToArray($file, ";");
    $clientsElegible = [];
    $row = 'Cliente;' .  "\n";
    foreach($clients as $client) {
        $c = Clientes::where('nome_cliente', $client['Cliente'])
            ->whereNotNull('token_firebase')->first();
        if($c) {
            $clientsElegible[] = $c;
            $row .= $c->nome_cliente . ";\n";
        };
        $c = null;
    }

    $filename .= "-ENVIADOS.csv";

    file_put_contents(storage_path('csv/' . $filename), $row);

    dd($row);
});

Route::get('/push/csv', function() {
    ini_set('max_execution_time', 60 * 60);

    DB::disconnect();
    Config::set('database.default', 'production');
    DB::reconnect();

    //$queque = new LifeQueueClient(Environment::PRODUCTION);
    $file =  storage_path("csv\clientes-push-20201230.csv");

    $clients = \App\Helpers\Utils::csvToArray($file, ";");
    $clientsElegible = [];
    $clientsElegible = [];

    foreach($clients as $client) {
        $c = Clientes::where('nome_cliente', $client['Cliente'])
            ->whereNotNull('token_firebase')->first();
        if($c) {
            $clientsElegible[] = $c;
            //echo $c->nome_cliente . "\n";
        };
        $c = null;
    }


    //return;
    //$clientsElegible[] = Clientes::find('1020753595');

    $title = "Lifepet";
    $message = "Ganhe 10% OFF no seu boleto de janeiro/21 pagando hoje (30/12). Acesse o app ou e-mail e confira a fatura!";
    try {
        foreach($clientsElegible as $c) {
            $pushNotification = (new \Modules\Mobile\Services\PushNotificationService($c, $title, $message, []));
            $pushNotification->send();

            echo $c->nome_cliente . "\n";
        }
    }
    catch (Exception $e){
        echo $e->getMessage();
    }
});

Route::get('financeiro/clientes/sincronizar', function() {
    $query = Clientes::query();
    $clientes = $query->where(function(\Illuminate\Database\Eloquent\Builder $query) {
        return $query->whereNull('id_externo')
            ->orWhereNull('dia_vencimento');
    })->whereDate('created_at', '>', '2019-06-01 00:00:00')
        ->where('ativo', 1)
        ->whereNull('id_conveniado')->get();

    $financeiro = new \App\Helpers\API\Financeiro\Financeiro();

    foreach($clientes as $c) {
        $c->syncWithFinance();
    }
});

Route::get('/gerarNps', function() {
    //Obter ativosss
    $clientes = DB::select('SELECT id FROM clientes WHERE ativo = 1 AND deleted_at IS NULL AND classificacao IS NULL');
    if(count($clientes)) {
        $clientes = collect($clientes);
    } else {
        return [
            'status' => false,
            'message' => 'Nenhum cliente disponível'
        ];
    }


    $clientes = $clientes->map(function($i) {return $i->id;})->shuffle();

    $perPage = ceil($clientes->count() / 3);
    $paginated = [];
    $paginated[] = $clientes->forPage(1, $perPage)->toArray();
    $paginated[] = $clientes->forPage(2, $perPage)->toArray();
    $paginated[] = $clientes->forPage(3, $perPage)->toArray();

    $categories = ['A', 'B', 'C'];
    foreach($categories as $i => $c) {
       Clientes::whereIn('id', $paginated[$i])->update([
           'classificacao' => $c
       ]);
    }

    return [
        'status' => true,
        'message' => 'Clientes com classificação atualizada'
    ];
});

Route::get('helpers/cobrancasSuperlogica/', function() {
    /**
     * @var \App\Models\Cobrancas[] $cobrancas
     */
    $cobrancas = \App\Models\Cobrancas::whereNotNull('id_superlogica')->
                 whereDate('created_at', '>=', '2020-10-23 00:00:00')->get();
    $invoiceService = new \App\Helpers\API\Superlogica\Invoice();
    $csv = "";

    if(!$cobrancas) {
        return [
            'msg' => 'Nenhuma cobrança encontrada'
        ];
    }

    foreach($cobrancas as $c) {

        $invoice = $invoiceService->get($c->id_superlogica);

        if($invoice) {
            $cliente = $c->cliente;
            $link = $invoice[0]->link_2via;
            $line = "{$cliente->nome_cliente};";
            $line .= "{$cliente->celular};";
            $line .= "{$cliente->email};";
            $line .= "{$c->valor_original};";
            $line .= "$link;\n";
            $csv .= $line;
        }
    }

    echo $csv;
});

Route::get('/checkout-getnet', function() {

    $CLIENT_ID = '28477c3d-d800-4d09-b879-1891fb482f50';
    $SECRET_ID = 'e60402a4-da00-4bb1-b9db-bbf558397a6e';
    $SELLER_ID = '99fc3bd5-7a63-4b9a-9afd-4268a7b5d638';

    $authString = base64_encode("{$CLIENT_ID}:{$SECRET_ID}");

    $http = new \GuzzleHttp\Client([
        'headers' => [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => "Basic $authString"
        ]
    ]);
    try {
        $response = $http->request('POST','https://api-sandbox.getnet.com.br/auth/oauth/v2/token', [
            'form_params' => [
                'scope' => 'oob',
                'grant_type' => 'client_credentials'
            ]
        ]);

        $contents = json_decode($response->getBody()->getContents());
        $token = "Bearer {$contents->access_token}";

        return view('getnet/checkout', [
            'token' => $token,
            'seller_id' => $SELLER_ID,
            'customer_id' => $CLIENT_ID
        ]);
    } catch (\Exception $e) {
        dd($e);
    }
});

Route::get('notificar/rd/cancelamento-automatico', function() {
    ini_set('max_execution_time', 60 * 60);

    $cancelamentos = \App\Models\Cancelamento::where('justificativa', '=', 'Cliente mensal inadimplente a mais de 60 dias.')->where('motivo', \App\Models\Cancelamento::MOTIVO_INADIMPLENCIA)->get();

    foreach($cancelamentos as $c) {
        (new \App\Helpers\API\RDStation\Services\RDCancelamentoAutomaticoService())->process($c);
    }

    return $cancelamentos;
});

Route::get('cancelamentos-reversiveis', function() {
    set_time_limit (0);

    DB::disconnect();
    Config::set('database.default', 'production');
    DB::reconnect();

    /**
     * @var \App\Models\Cancelamento[] $cancelamentos
     */
    $cancelamentos = \App\Models\Cancelamento::where('motivo', \App\Models\Cancelamento::MOTIVO_INADIMPLENCIA)
        ->whereYear('created_at', '>', 2020)->get();

    $rows = '';
    foreach($cancelamentos as $c) {
        if($c->pet->cliente->statusPagamento() ===  Clientes::PAGAMENTO_EM_DIA) {
            $rows .= "<tr>";
            $rows .= "<td>" . $c->id . "</td>";
            $rows .= "<td>" . $c->pet->cliente->nome_cliente . "</td>";
            $rows .= "<td>" . \route('clientes.edit', ['id' => $c->pet->cliente->id]) . "</td>";
            $rows .= "<td>" . $c->pet->nome_pet . "</td>";
            $rows .= "<td>" . $c->created_at->format(\App\Helpers\Utils::BRAZILIAN_DATETIME) . "</td>";
            $rows .= "</tr>";
        }
    }

    echo "<table>
        <thead>
            <tr>
                <th>ID Cancelamento</th>
                <th>Cliente</th>
                <th>Link</th>
                <th>Pet</th>
                <th>Data Cancelamento</th>
            </tr>
        </thead>
        <tbody>
    " . $rows .
        "    </tbody>
    </table>    
    ";
});
Route::get('reverter-cancelamentos', function() {
    set_time_limit (0);

    /**
     * @var \App\Models\Cancelamento[] $cancelamentos
     */
    $cancelamentos = \App\Models\Cancelamento::where('motivo', \App\Models\Cancelamento::MOTIVO_INADIMPLENCIA)
        ->whereYear('created_at', '>', 2020)->get();

    foreach($cancelamentos as $c) {
        if($c->pet->cliente->statusPagamento() ===  Clientes::PAGAMENTO_EM_DIA) {
            $c->pet->ativo = 1;
            $c->pet->update();
            $pp = $c->pet->petsPlanosAtual()->first();
            $pp->data_encerramento_contrato = null;
            $pp->update();

            \App\Models\Notas::registrar('Reversão de cancelamento automático.', $c->pet->cliente, \App\User::find(1));
        }
    }
});

Route::get('correcao-cadastros-duplicados', function(Request $request) {
    $clientes = Clientes::groupBy('cpf')
            ->havingRaw('COUNT(cpf) > ?', [1])
            ->get();

    foreach($clientes as $c) {
        $inativos = Clientes::where('cpf', $c->cpf)->where('ativo', 0)->get();
        foreach($inativos as $i) {
            $i->ignoreCpfMutator = true;
            $i->cpf = '//' . $i->cpf;
            $i->update();

            \App\Models\Notas::registrar('O CPF do cliente foi alterado pois foi encontrada uma duplicidade no sistema.', $i);
        }
    }
});

Route::get('duplicar-plano/{id}/{master}', function(Request $request, $id, $master) {
    if($master != 'l1f3p3t@2021') {
        abort(403, 'Código master incorreto');
        return;
    }

    /**
     * @var \App\Models\Planos $plano
     */
    $plano = \App\Models\Planos::find($id);

    $new = $plano->replicate();
    $new->ativo = 0;
    $new->data_vigencia = Carbon::now()->format('d/m/Y');
    $new->data_inatividade = null;
    $new->nome_plano = $plano->nome_plano . ' ' . Carbon::now()->format('Y/m');
    $new->push();

    //Credenciados
    $credenciados = \App\Models\PlanosCredenciados::where('id_plano', $id)->get();
    foreach($credenciados as $c) {
        $newC = $c->replicate();
        $newC->id_plano = $new->id;
        $newC->push();
    }

    //Grupos
    $grupos = \App\Models\PlanosGrupos::where('plano_id', $id)->get();
    foreach($grupos as $g) {
        $newG = $g->replicate();
        $newG->plano_id = $new->id;
        $newG->push();
    }

    //Procedimentos
    $procedimentos = \App\Models\PlanosProcedimentos::where('id_plano', $id)->get();
    foreach($procedimentos as $p) {
        $newP = $p->replicate();
        $newP->id_plano = $new->id;
        $newP->push();
    }

    return redirect(route('planos.edit', [$new->id]));
});

Route::group(['prefix' => 'telemedicina'], function() {
    Route::get('/home', function() {
        return view('telemedicina.index');
    });
});


Route::group(['prefix' => 'getnet'], function() {
    Route::group(['prefix' => 'pix'], function() {
        Route::get('atendimento/{numeroGuia}/{valor}', 'GetnetController@generateForServiceGuide');
        Route::post('atendimento/{id}/confirm', 'GetnetController@confirmServiceGuide')->name('getnet.pix.guide.confirm');

        Route::get('atendimento/{numeroGuia}', 'GetnetController@servicePaymentStatus')->name('getnet.pix.guide.status');
    });

    Route::get('payments/confirm', 'GetnetController@confirmPayment');
    Route::get('payments/cancel','GetnetController@cancelPayment');
});

Route::get('/batch/cancelamentos/inadimplentes/csv', function() {

    $file =  storage_path("csv/clientes-cancelamento-20210818.csv");

    $clients = \App\Helpers\Utils::csvToArray($file, ",");

    $clientsElegible = [];


    foreach($clients as $client) {
        $c = Clientes::where('nome_cliente', $client['nome'])
                     ->where('email', $client['e-mail'])->first();

        if($c) {
            $clientsElegible[] = $c;
            //echo $c->nome_cliente . "\n";
        };
        $c = null;
    }

    $petsController = new \App\Http\Controllers\PetsController(new \App\Repositories\PetsRepository(app()));

    foreach($clientsElegible as $c) {
        $pets = $c->pets()->where('regime','MENSAL')
            ->where('ativo', 1)->get();

        foreach($pets as $pet) {
            $petsController->cancelarPet($pet->id);

            $cancelamento = (new \App\Models\Cancelamento())->create([
                'motivo' => \App\Models\Cancelamento::MOTIVOS[\App\Models\Cancelamento::MOTIVO_INADIMPLENCIA],
                'justificativa' => 'Cliente mensal inadimplente a mais de 60 dias.',
                'data_cancelamento' => Carbon::now(),
                'id_usuario' => 2,
                'id_pet' => $pet->id,
                'cancelar_externo' => false
            ]);

            (new \App\Helpers\API\RDStation\Services\RDCancelamentoAutomaticoService())->process($cancelamento);

            $mensagemLog = "O pet #{$pet->id} ($pet->nome_pet) foi cancelado automaticamente por motivo de inadimplência.";

            Logger::log(
                \App\Http\Util\LogEvent::WARNING,
                'pets',
                LogMessages::IMPORTANCIA['ALTA'],
                $mensagemLog,
                null,
                'pets',
                $pet->id);
        }

        //Caso todos os pets do cliente sejam do regime MENSAL
        if($c->pets()->where('ativo', 1)->count() == $c->pets()->where('ativo', 1)->where('regime', 'MENSAL')->count()) {
            //Inativa o cliente automaticamente
            $c->ativo = 0;
            $c->update();

            $mensagemLog = "O cliente #{$c->id} ({$c->nome_cliente}) foi cancelado automaticamente por motivo de inadimplência.";

            Logger::log(
                \App\Http\Util\LogEvent::WARNING,
                'clientes',
                LogMessages::IMPORTANCIA['ALTA'],
                $mensagemLog,
                null,
                'clientes',
                $c->id);
        }


        \App\Models\Notas::create([
            'user_id' => 1,
            'cliente_id' => $c->id,
            'corpo' => 'Rotina de cancelamento automático executada por motivo de inadimplência em ' . (Carbon::now()->format('d/m/Y H:i:s'))
        ]);

        echo "Procedimento de CANCELAMENTO executado no cadastro do cliente {$c->nome_cliente} de ID #{$c->id}\n";
    }
});

Route::get('/batch/notas/adicionar/{filename}', function(Request $request, string $filename) {
    ini_set('max_execution_time', 60 * 60);

    $file = storage_path("csv/notas/${filename}.csv");

    if (!$file) {
        abort(400, 'O arquivo mencionado não pôde ser encontrado.');
    }

    $clients = \App\Helpers\Utils::csvToArray($file, ";");

    if (!isset($clients[0]) || !isset($clients[0]['CPF']) || !isset($clients[0]['NOTA'])) {
        abort(400, 'O arquivo mencionado não possui o formato requerido ou não possui os dados necessários.');
    }


    foreach ($clients as $client) {
        $cpf = preg_replace("/[^0-9]/", "", $client['CPF']);
        $nota = $client['NOTA'];

        $c = Clientes::where('cpf', $cpf)->first();

        if ($c) {
            $mensagemLog = "O cliente #{$c->id} ({$c->nome_cliente}) teve uma inclusão automática de nota via envio em massa." . json_encode([
                    'nota' => $nota
                ]);

            Logger::log(
                \App\Http\Util\LogEvent::NOTICE,
                'clientes',
                LogMessages::IMPORTANCIA['BAIXA'],
                $mensagemLog,
                null,
                'clientes',
                $c->id);

            \App\Models\Notas::create([
                'user_id' => 1,
                'cliente_id' => $c->id,
                'corpo' => $nota
            ]);

            echo "Procedimento de inclusão de nota em massa executado no cadastro do cliente {$c->nome_cliente} de ID #{$c->id} <br>";
        } else {
            $mensagemLog = "O cliente com o {$client['CPF']} não foi localizado para inserir nota no cadastro." . json_encode([
                    'Cliente' => $client,
                    'CPF_tratado' => $cpf
                ]);

            Logger::log(
                \App\Http\Util\LogEvent::NOTICE,
                'clientes',
                LogMessages::IMPORTANCIA['BAIXA'],
                $mensagemLog,
                null);
            echo "O cliente com o {$client['CPF']} não foi localizado para inserir nota no cadastro. <br>";
        }
    }
});

Route::get('/batch/notas/corrigir/{filename}', function(Request $request, string $filename) {
    ini_set('max_execution_time', 60 * 60);

    $file =  storage_path("csv/notas/${filename}.csv");

    if(!$file) {
        abort(400, 'O arquivo mencionado não pôde ser encontrado.');
    }

    $clients = \App\Helpers\Utils::csvToArray($file, ";");

    if(!isset($clients[0]) || !isset($clients[0]['CPF']) || !isset($clients[0]['NOTA'])) {
        abort(400, 'O arquivo mencionado não possui o formato requerido ou não possui os dados necessários.');
    }

    $error = false;
    $error_log = [];

    foreach($clients as $client) {
        $cpf = preg_replace("/[^0-9]/", "", $client['CPF']);
        $nota = $client['NOTA'];

        $c = Clientes::where('cpf', $cpf)->first();

        if($c) {
            $mensagemLog = "O cliente #{$c->id} ({$c->nome_cliente}) teve uma correção de notas duplicadas." . json_encode([
                    'nota' => $nota
                ]);
            $duplicate = false;
            $save_log = false;
            $notas = \App\Models\Notas::where([
                ['user_id', '=', 1],
                ['cliente_id', '=', $c->id],
               //[ 'corpo', '=', $nota]
            ])->get();
            if ($notas->count() > 1)
            {

                foreach ($notas as $key => $row)
                {
                    if ($row->corpo === $nota)
                    {
                        if ($duplicate) {
                            $row->delete();
                            echo $row->id.' Duplicado | '.$nota.' | '.$row->corpo.'<br>';
                            $save_log = true;
                        } else {
                            $duplicate = true;
                        }


                    }



                }
                if ($save_log)
                {
                    Logger::log(
                        \App\Http\Util\LogEvent::NOTICE,
                        'clientes',
                        LogMessages::IMPORTANCIA['BAIXA'],
                        $mensagemLog,
                        null,
                        'clientes',
                        $c->id);
                }


            }




            echo "Procedimento de inclusão de nota em massa executado no cadastro do cliente {$c->nome_cliente} de ID #{$c->id} <br>";
        }
    }



});

Route::get('/batch/app/clientes/migracao', function(Request $request) {
    ini_set('max_execution_time', 60 * 30);

    if(Carbon::now()->gte('2021-10-07 23:59:00')) {
        abort(403, 'Data expirada.');
    }

    /**
     * @var \Illuminate\Support\Collection $clientes
     */
//    $clientes = Clientes::where('ativo', 1)->get();
    $clientes = Clientes::whereIn('id', [
        '1012370060'
    ])->get();
    $migrate = \Illuminate\Support\Facades\Cache::remember('all-active-clients__migration-MONICAZUQUI', 60 * 60 * 24, function() use ($clientes) {
        return $clientes->filter(function(Clientes $c) {
            return $c->user !== null && !empty($c->email);
        })->map(function(Clientes $c) {
            $cpf = preg_replace('/[^0-9]/', '', $c->cpf);
            $token = $c->user->token();

            if(!$token) {
                $token = $c->user->createToken('Lifepet APP Cliente', ['*']);
            }

            $nickname = $c->email;
            $nickname = explode('@', $nickname);
            $nickname = $nickname[0];
            $nickname = preg_replace('/-|_/', '.', $nickname);

            return [
                'id' => $c->id,
                'nome' => $c->nome_cliente,
                'email' => $c->email,
                'nickname' => $nickname,
                'password' => substr($cpf, 0, 8),
                'access_token' => $token->accessToken
            ];
        });
    });


    return $migrate;
});

Route::get('/batch/credenciados/rd', function() {

    $clinicas = \Modules\Clinics\Entities\Clinicas::where('ativo', 1)->get();

    foreach($clinicas as $c) {
        try {
            (new \App\Helpers\API\RDStation\Services\RDCredenciadoCadastradoService())->process($c);
            echo "Credenciado {$c->id} enviado para a RD.";
        } catch (Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }
});

Route::get('/mails/plan/signature', function() {
    $plan = \App\Models\Planos::find(70);
    $pets = \App\Models\Pets::first();

    Mail::to('brenogrillo@gmail.com')->send(new \App\Mail\PlanSignature($plan, $pets));
});

Route::get('/batch/superlogica/clientes/boleto', function() {
    ini_set('max_execution_time', 60 * 60);

    $clientes = Clientes::where('forma_pagamento', 'boleto')
                        ->where('ativo', 1)
                        ->whereNull('id_superlogica')
                        ->get();
    $queued = [];
    foreach ($clientes as $cliente) {
        $job = new \App\Jobs\SuperlogicaSyncSignature($cliente);
        dispatch($job);
        $queued[] = $cliente->id . ' - ' . $cliente->nome_cliente;
    }

    foreach ($queued as $q) {
        echo "Client record queued to sync: " . $q . "\n";
    }
});

Route::get('/batch/superlogica/clientes/cartao', function() {
    $clientes = Clientes::whereIn('id', [1020756066,1020756065,1020756064,1020756063,1020756062,1020755548])->get();
    $queued = [];
    foreach ($clientes as $cliente) {
        $job = new \App\Jobs\SuperlogicaSyncSignature($cliente);
        dispatch($job);
        $queued[] = $cliente->id . ' - ' . $cliente->nome_cliente;
    }
    echo "Credit card clientes import just started at " . Carbon::now()->format('Y-m-d H:i:s') . "\n";
    foreach ($queued as $q) {
        echo "Client record queued to sync: " . $q . "\n";
    }
});

Route::get('superlogica/test/sync/{idCliente}', function($idCliente) {
    $cliente = \App\Models\Clientes::find($idCliente);
    if(!$cliente) {
        abort(404, 'Cliente não encontrado');
    }

    $job = new \App\Jobs\SuperlogicaSyncSignature($cliente);
    dispatch($job);

    echo "Job de sincronia despachado com sucesso para o cliente {$cliente->nome_cliente} #{$cliente->id}\n";
});

Route::get('/superlogica/test/getSignatureCliente', function() {
    $cliente = Clientes::find(1020754483);
    $pet = $cliente->pets()->first();

    $recurrenceService = new \App\Helpers\API\Superlogica\V2\Recurrence();

    $activeSignatureId = $recurrenceService->getActiveSignatureId($cliente, $pet->getIdentificadorPlano());
    $pendingSignatureId = $recurrenceService->getPendingPaymentSignatureId($cliente, $pet->getIdentificadorPlano());
    dd([
        'active' => $activeSignatureId,
        'pending' => $pendingSignatureId
    ]);
});

Route::get('batch/superlogica/planos/atualizar', function() {
    $planos = \App\Models\Planos::whereNotNull('id_superlogica')->get();
    foreach($planos as $p) {
        $service = new \App\Helpers\API\Superlogica\V2\Plan();
        $service->setBoleto($p);
        $service->setMultipleSignatures($p);
    }
});

Route::get('batch/superlogica/planos/cadastrar', function() {
    $planos = \App\Models\Planos::whereNull('id_superlogica')->get();

    foreach($planos as $p) {
        $service = new \App\Helpers\API\Superlogica\V2\Plan();
        $service->register($p);
    }
});

Route::get('batch/superlogica/clientes/atualizar', function() {
     $clientes = \App\Models\Clientes::whereNotNull('last_sync')->whereNotNull('id_superlogica')->get();

     foreach($clientes as $c) {
         echo "Updating {$c->nome_cliente}#{$c->id} info!\n";
         dispatch(new \App\Jobs\SyncSuperlogicaClientInfo($c));
     }
});

Route::get('valorProcedimentos/{idPlano}', function(Request $request, $idPlano) {
    $plano = \App\Models\Planos::find($idPlano);

    $procedimentos = $request->get('procedimentos', []);
    if(empty($procedimentos)) {
        return [
            'total' => 0,
            'procedimentos' => []
        ];
    }
    //Calcular map do valor dos procedimentos baseados no plano:
    $procedimentos = collect($procedimentos)->map(function($p) use ($plano) {
        $procedimento = \App\Models\Procedimentos::find($p);
        if(!$procedimento) {
            return [
                'valores' => [
                    'valor_base' => 'Não encontrado',
                    'participacao' => 'Não encontrado'
                ],
                'participacao' => 'Não encontrado'
            ];
        }

        $p = [];
        $p['valores'] = [
            'valor_base' => $procedimento->valor_base,
            'participacao' => $procedimento->valorParticipacao($plano)
        ];
        $p['participacao'] = $p['valores']['participacao'];
        return $p;
    });

    return [
        'total' => $procedimentos->sum('participacao'),
        'procedimentos' => $procedimentos
    ];
});

Route::get('relatorio/superlogica/clientes', function() {
     $clientes = ["ADINEIA ANDRADE LOUREIRO", "ALLAN COSTA FERNANDES","AMANDA LEITE RIBEIRO MATHIAS","Ana Carolina Coelho Da Costa","ANA CAROLINA SEGUI MIRAI","ANTONIO VITO MARSIGLIA JUNIOR","Camila Claudete Toledo Zenetti","Camila Maria Mocbel Bedran","Carlos Eduardo Grillo","CAROLINA CASTELO MARTINS","Cassius Alexandre Cipriano","Cecília Borges da Silva","CHRISTINA DUARTE NUNES MUTZ","CLAUDIA MARIA SCALZER","DANIELLE RODRIGUES DE MIRANDA","DARIA BANHOS TRISTÃO FERNANDES","Deivid Luis Fortuna de Araujo","VITOR TRISTAO ZAMPROGNO DE CASTRO FILHO","EDNEY MARTINS DO VALE","Edson Almeida Freire Junior","Erick Roberto Araújo Silva","FABIANA MODOLO NASCIMENTO","Humberto Frasson Da Silva","IARA ENCARNACAO MACEDO","ILZE AMÉLIA AUGUSTO B REBOUÇAS DE SOUZA","JANINE PAVAN COUTINHO ZORZAL","JUCELIA DA COSTA PAIVA PIVETTA","Karla Bastos da Silveira","Karla do Nascimento Lucas","KARLA RICARDO CHAVES","KAROLINI SOUZA DA CRUZ","KATIA MARIA GIANIZELI RODRIGUES","LEOMAR GUIMARAES RIOS","Luana Blunk da Conceiçao","LUENA GRADI DE LUCENA","MARCIA GUIMARAES ABRAHAO DA COSTA","MARIA LUISA RAMOS GAMA","MARIA MEIRA","MARIZA HELENA NASCIMENTO","NELSON CHIABAI FILHO","NICOLE CRISTINA CALIXTO NUNES","PORCINA ALVES MOREIRA","RAFAEL DE ALVARENGA ROSA","Simone Lucia Romão","SONIA MARIA DE FREITAS","SYOMARA SILVARES ITALA","TAISSA PINHEIRO DE AZEVEDO","THALYTTA FERRACO","Vinicius Souza do Nascimento"];
     $resultado = [];
     foreach ($clientes as $c) {
         $obj = \App\Models\Clientes::where('nome_cliente', 'LIKE', "%" . $c . "%")->first();
         if(!$obj) {
             $resultado[$c] = [
                 'Não encontrado'
             ];
         } else {
             $petsAtivos = $obj->pets()->where('ativo', 1)->count();

             $cancelamentos = Cancelamento::whereIn('id_pet', $obj->pets()->pluck('id'))->count();
             $resultado[$c] = [
                 'Ativo' => $obj->ativo,
                 'Status Pagamento' => $obj->statusPagamento(),
                 'Forma pagamento' => $obj->forma_pagamento,
                 'ID Superlogica' => $obj->id_superlogica,
                 'Última sincronia' => $obj->last_sync ? $obj->last_sync->format('Y-m-d H:i:s') : '-',
                 'Cancelamentos agendados' => $cancelamentos
             ];
         }

     }
     foreach ($resultado as $firstColumn => $row) {
         echo $firstColumn . ";";
         foreach ($row as $column) {
             echo $column . ";";
         }
         echo "\n";
     }
});

Route::get('superlogica/backup/cobrancas', function() {
    $cobrancas = \App\Models\Cobrancas::whereNotNull('id_superlogica')->where('updated_at', "<=", now()->subDay(1))->get();
    foreach($cobrancas as $c) {
        $c->old_superlogica_id = $c->id_superlogica;
        $c->id_superlogica = null;
        $c->update();
    }
});

Route::get('superlogica/assinaturas-corrigidas', function() {
    $clientes = Clientes::whereNotNull('id_superlogica')->where('forma_pagamento', 'boleto')->where('ativo', 1)->get();
    $rows = [
        [
            'id_cliente_lifepet',
            'id_cliente_superlogica',
            'nome_cliente',
            'assinatura',
            'data_inicio'
        ]
    ];

    foreach($clientes as $c) {
        foreach($c->pets()->where('ativo', 1)->get() as $p) {
            if(!$p->petsPlanosAtual()->first()) {
                $rows[] = [
                    $c->id,
                    $c->id_superlogica,
                    $c->nome_cliente,
                    $p->getIdentificadorPlano(),
                    '-',
                    '00/00/0000',
                ];
            } else {
                $rows[] = [
                    $c->id,
                    $c->id_superlogica,
                    $c->nome_cliente,
                    $p->getIdentificadorPlano(),
                    $p->petsPlanosAtual()->first()->id_contrato_superlogica,
                    $p->petsPlanosAtual()->first()->data_inicio_contrato
                ];
            }
        }
    }

    foreach ($rows as $firstColumn => $row) {
        echo $firstColumn . ";";
        foreach ($row as $column) {
            echo $column . ";";
        }
        echo "\n";
    }
});

Route::get('/internal-network-lp', function (Request $request) {

    $planos = \App\Models\Planos::all();

    $query = \Modules\Clinics\Entities\Clinicas::where('ativo', 1)
        ->where('exibir_site', 1)
        ->with([
            'tagsSelecionadas',
            'tagsSelecionadas.tag'
        ]);

    $paramEstado = $request->get('estados', null);
    if($paramEstado) {
        $query->where('estado', $paramEstado);
    }

    $paramCidade = $request->get('cidades', null);
    if($paramCidade) {
        $query->where('cidade', $paramCidade);
    }

    $paramTipo = $request->get('tipos', null);
    if($paramTipo) {
        $query->where('tipo', $paramTipo);
    }

    $clinicas = $query->get();

    $paramPlano = $request->get('planos', null);
    if($paramPlano) {
        $clinicas = $clinicas->filter(function ($clinica) use ($paramPlano) {
            return $clinica->checkPlanoCredenciado($paramPlano);
        });
    }

    $clinicasAll = \Modules\Clinics\Entities\Clinicas::where('ativo', 1)
        ->where('exibir_site', 1)
        ->get();
    $estados = $clinicasAll->pluck('estado')->unique()->filter();
    $cidades = $clinicasAll
        ->pluck('cidade')
        ->unique()
        ->filter();
    $tipos = $clinicasAll->pluck('tipo')->unique()->filter();

    $data = [
        'clinicas' => $clinicas,
        'estados' => $estados,
        'cidades' => $cidades,
        'tipos' => $tipos,
        'planos' => $planos,
        'params' => [
            'estados' => $paramEstado,
            'cidades' => $paramCidade,
            'tipos' => $paramTipo,
            'planos' => $paramPlano
        ]
    ];

    // dd($data);
    return view('rede.rede-credenciada')->with($data);
})->name('internal-network-lp');
