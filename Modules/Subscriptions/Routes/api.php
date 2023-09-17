<?php

use Modules\Subscriptions\Http\Controllers\Api\SubscriptionController;

Route::apiResource('subscription', 'SubscriptionController');
Route::apiResource('plan', 'PlanController');
Route::apiResource('customer', 'CustomerController');
Route::apiResource('pet', 'PetController');
Route::apiResource('pet-plan', 'PetPlanController');