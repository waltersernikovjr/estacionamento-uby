<?php

use App\Http\Controllers\Api\OperadorAuthController;
use App\Http\Controllers\Api\ClienteAuthController;
use App\Http\Controllers\Api\VagaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/operador/register', [OperadorAuthController::class, 'register']);
Route::post('/operador/login', [OperadorAuthController::class, 'login']);

Route::post('/cliente/register', [ClienteAuthController::class, 'register']);
Route::post('/cliente/login', [ClienteAuthController::class, 'login']);

Route::group(['middleware' => 'auth:api_operador'], function () {
    Route::post('/operador/logout', [OperadorAuthController::class, 'logout']);
    Route::apiResource('vagas', VagaController::class);
});

Route::group(['middleware' => 'auth:api_cliente'], function () {
    Route::post('/cliente/logout', [ClienteAuthController::class, 'logout']);
});
