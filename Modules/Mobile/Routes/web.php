<?php

Route::prefix('mobile')->group(function() {
    Route::group(['prefix' => 'pusher'], function () {
        Route::get('/', 'PusherController@index')->name('mobile.pusher.index');
        Route::get('/new', 'PusherController@create')->name('mobile.pusher.create');
        Route::post('/check', 'PusherController@check')->name('mobile.pusher.check');
        Route::post('/send', 'PusherController@send')->name('mobile.pusher.send');
    });

    Route::group(['prefix' => 'push', 'middleware' => ['auth']], function () {
        Route::get('/notifications', 'PushNotificationController@create')->name('mobile.push.notifications.create');
        Route::post('/notifications/send', 'PushNotificationController@send')->name('mobile.push.notifications.send');
        Route::post('/notifications/send_test', 'PushNotificationController@sendTest')->name('mobile.push.notifications.send_test');
        Route::post('/notifications/search', 'PushNotificationController@search')->name('mobile.push.notifications.search');
    });
});
