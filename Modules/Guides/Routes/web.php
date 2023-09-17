<?php

Route::group(['prefix' => 'autorizador', 'middleware' => ['auth']], function() {
    Route::get('/', 'AutorizadorController@home')->name('autorizador.home');
    Route::post('/emitirGuia', 'AutorizadorController@emitirGuia')->name('autorizador.emitirGuia');
    Route::get('/guias', 'AutorizadorController@guias')->name('autorizador.verGuias');
    Route::get('/guiasEncaminhamento', 'AutorizadorController@guiasEncaminhamento')->name('autorizador.guiasEncaminhamento');
    Route::get('/buscarEncaminhamento', 'AutorizadorController@buscarEncaminhamento')->name('autorizador.buscarEncaminhamento');
    Route::post('/realizarEncaminhamento', 'AutorizadorController@realizarEncaminhamento')->name('autorizador.realizarEncaminhamento');
    Route::get('/guiasCancelar', 'AutorizadorController@guiasCancelar')->name('autorizador.guiasCancelar');
    Route::get('/guia/{numeroGuia}', 'AutorizadorController@verGuia')->name('autorizador.verGuia');
    Route::post('/guia/autorizar', 'AutorizadorController@autorizar')->name('autorizador.autorizar');
    Route::post('/guia/recusar', 'AutorizadorController@recusar')->name('autorizador.recusar');
    Route::post('/guia/agendar', 'AutorizadorController@agendar')->name('autorizador.agendar');
    Route::post('/guia/adicionarLaudo', 'AutorizadorController@adicionarLaudo')->name('autorizador.adicionarLaudo');
    Route::get('/guia/{numero_guia}/realizar', 'AutorizadorController@formRealizar')->name('autorizador.formRealizar');
    Route::post('/guia/realizar', 'AutorizadorController@realizar')->name('autorizador.realizar');
    Route::post('/guia/solicitarCancelamento', 'AutorizadorController@solicitarCancelamento')->name('autorizador.solicitarCancelamento');
    Route::post('/guia/adicionarAnexo', 'AutorizadorController@adicionarAnexo')->name('autorizador.adicionarAnexo');
    Route::get('/guia/anexosGuia/{file_name}', function($file_name = null)
    {
        $path = storage_path().'/'.'app'.'/anexosGuia/'.$file_name;
        if (file_exists($path)) {
            return Response::download($path);
        }
    });
    Route::post('/checkRegrasPlanosAntigos', 'AutorizadorController@checkRegrasPlanosAntigos')->name('autorizador.checkRegrasPlanosAntigos');

    Route::post('/assinarGuiaCliente', 'AutorizadorController@assinarGuiaCliente')->name('autorizador.assinarGuiaCliente');
    Route::post('/assinarGuiaPrestador', 'AutorizadorController@assinarGuiaPrestador')->name('autorizador.assinarGuiaPrestador');
    Route::get('/assinaturasPendentes', 'AutorizadorController@assinaturasPendentes')->name('autorizador.assinaturasPendentes');

    // Glosas
    Route::get('/glosas', 'AutorizadorController@glosas')->name('autorizador.verGuiasGlosadas');
    Route::post('/defenderGlosa', 'AutorizadorController@defenderGlosa')->name('autorizador.defenderGlosa');
    Route::post('/reverterGlosa', 'AutorizadorController@reverterGlosa')->name('autorizador.reverterGlosa');
    Route::post('/confirmarGlosa', 'AutorizadorController@confirmarGlosa')->name('autorizador.confirmarGlosa');
    Route::post('/guia/glosar', 'AutorizadorController@glosar')->name('autorizador.glosar');

    Route::get('/guia/{numeroGuia}/pagamentoDireto', 'AutorizadorController@pagamentoDireto')->name('autorizador.pagamentoDireto');
    Route::post('/guia/{numeroGuia}/pagamentoDireto', 'AutorizadorController@confirmarRecebimentoPagamentoDireto')->name('autorizador.confirmarPagamentoDireto');
    Route::post('/guia/{numeroGuia}/cancelarPagamentoDireto', 'AutorizadorController@cancelarRecebimentoPagamentoDireto')->name('autorizador.cancelarPagamentoDireto');
});