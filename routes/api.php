<?php

use App\Http\Controllers\StoresController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/stores', [StoresController::class, 'store']);

    Route::get('/stores/near/{postcode}', [StoresController::class, 'storesNearPostcode']);
    Route::get('/stores/can-deliver/{postcode}', [StoresController::class, 'storesCanDeliverToPostcode']);
});
