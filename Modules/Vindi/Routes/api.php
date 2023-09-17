<?php

Route::group([
    'middleware' => ['auth:api'],
    'prefix' => 'vindi'
], function () {
    Route::get('payment-profiles', "PaymentProfileController@index");
});

Route::prefix('vindi')->group(function() {
    Route::post('webhook', "WebhookController@index");
    Route::post('cards/add', "PaymentProfileController@appPaymentProfile");
});