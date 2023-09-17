<?php

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

Route::resource('clinicas', 'ClinicasController');
Route::prefix('clinicas')->group(function() {
    Route::get('manualCredenciado', 'ClinicasController@manualCredenciado')->name('clinicas.manualCredenciado');
    Route::get('extrato', 'ClinicasController@extrato')->name('clinicas.extrato');
    Route::get('prestadores', 'ClinicasController@prestadores')->name('clinicas.prestadores');
    Route::get('{id}/perfil', 'ClinicasController@perfil')->name('clinicas.perfil');
    Route::get('{id}/avatar', 'ClinicasController@avatar')->name('clinicas.avatar');

    Route::post('vincularPrestador', 'ClinicasController@vincularPrestador')->name('clinicas.vincularPrestador');
    Route::post('desvincularPrestador', 'ClinicasController@desvincularPrestador')->name('clinicas.desvincularPrestador');
    Route::post('atualizarUsuario', 'ClinicasController@atualizarUsuario')->name('clinicas.atualizarUsuario');
    Route::post('solicitarPrestador', 'ClinicasController@solicitarPrestador')->name('clinicas.solicitarPrestador');
    Route::post('atualizaPlanos', 'ClinicasController@atualizaPlanos')->name('clinicas.atualizaPlanos');
    Route::post('atualizaPrestadores', 'ClinicasController@atualizaPrestadores')->name('clinicas.atualizaPrestadores');
    Route::post('atualizaCategorias', 'ClinicasController@atualizaCategorias')->name('clinicas.atualizaCategorias');
    Route::post('atualizaAcessoUser', 'ClinicasController@atualizaAcessoUser')->name('clinicas.atualizaAcessoUser');
    Route::post('avatarCropUpload', 'ClinicasController@avatarCropUpload')->name('clinicas.avatarCropUpload');
    Route::post('atualizarLimite', 'ClinicasController@atualizaLimites')->name('clinicas.atualizarLimite');
    Route::post('consulta/cpfcnpj', 'ClinicasController@consultaCnpj')->name('clinicas.consultaCNPJ');
    Route::post('consulta/email', 'ClinicasController@consultaEmail')->name('clinicas.consultaEmail');
});

