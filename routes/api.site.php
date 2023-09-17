<?php

Route::post('/register', 'SiteAPIController@register');
Route::post('/login', 'SiteAPIController@login');
Route::get('/check-user-exist', 'SiteAPIController@checkUserExist');