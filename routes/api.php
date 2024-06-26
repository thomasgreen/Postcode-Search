<?php

use App\Http\Controllers\StoresController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/stores', [StoresController::class, 'store']);

    Route::get('/stores/near/{postcode}', [StoresController::class, 'storesNearPostcode']);
});


