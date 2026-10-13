<?php

use App\Http\Controllers\Webhook\PayOSWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post(
    '/webhooks/payos',
    [PayOSWebhookController::class, 'handle']
)->name('webhooks.payos');