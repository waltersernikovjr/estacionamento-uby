<?php

use App\Http\Controllers\Api\OperadorAuthController;
use App\Http\Controllers\Api\ClienteAuthController;
use App\Http\Controllers\Api\VagasController;
use Illuminate\Support\Facades\Route;

Route::post('/operador/register', [OperadorAuthController::class, 'register']);
Route::post('/operador/login', [OperadorAuthController::class, 'login']);
Route::get('/operador', [OperadorAuthController::class, 'index']);

Route::post('/cliente/register', [ClienteAuthController::class, 'register']);
Route::post('/cliente/login', [ClienteAuthController::class, 'login']);

Route::group(['middleware' => 'auth:api_operador'], function () {
    Route::post('/operador/logout', [OperadorAuthController::class, 'logout']);
    Route::post('/vagas', [VagasController::class, 'store']);
});

Route::get('/vagas', [VagasController::class, 'index']);

Route::group(['middleware' => 'auth:api_cliente'], function () {
    Route::patch('/vagas/{vaga}/ocupar', [VagasController::class, 'ocupar']);
    Route::post('/cliente/logout', [ClienteAuthController::class, 'logout']);
});
