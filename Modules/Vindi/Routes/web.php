<?php

Route::get('/vindi/index', 'IndexController@index')->name('vindi.index');
Route::get('/vindi/customer', 'IndexController@customer')->name('vindi.customer');
Route::get('/vindi/create-customer', 'IndexController@createCustomer')->name('vindi.create-customer');