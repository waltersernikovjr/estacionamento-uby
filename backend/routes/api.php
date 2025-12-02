<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\Auth\CustomerAuthController;
use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\Auth\OperatorAuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\OperatorController;
use App\Http\Controllers\Api\ParkingSpotController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\VehicleController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('health', function () {
        return response()->json(['status' => 'ok', 'timestamp' => now()]);
    });

    Route::post('operators/register', [OperatorAuthController::class, 'register']);
    Route::post('operators/login', [OperatorAuthController::class, 'login']);

    Route::post('customers/register', [CustomerAuthController::class, 'register']);
    Route::post('customers/login', [CustomerAuthController::class, 'login']);

    Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->name('verification.verify');
    Route::post('email/resend', [EmailVerificationController::class, 'resend'])
        ->name('verification.resend');

    Route::get('address/{zipCode}', [AddressController::class, 'getByZipCode']);

    Route::get('parking-spots-available', [ParkingSpotController::class, 'available'])
        ->name('parking-spots.available');
});

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post('operators/logout', [OperatorAuthController::class, 'logout']);
    Route::get('operators/me', [OperatorAuthController::class, 'me']);
    Route::get('operators/stats', [OperatorController::class, 'stats']);
    Route::post('customers/logout', [CustomerAuthController::class, 'logout']);
    Route::get('customers/me', [CustomerAuthController::class, 'me']);

    Route::apiResource('operators', OperatorController::class);
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('parking-spots', ParkingSpotController::class);

    Route::get('reservations/search', [ReservationController::class, 'searchByPlate'])
        ->name('reservations.search-by-plate');
    Route::get('reservations/active-by-spot/{spotId}', [ReservationController::class, 'getActiveBySpot'])
        ->name('reservations.active-by-spot');
    Route::post('reservations/{id}/complete', [ReservationController::class, 'complete'])
        ->name('reservations.complete');
    Route::post('reservations/{id}/cancel', [ReservationController::class, 'cancel'])
        ->name('reservations.cancel');
    Route::post('reservations/{id}/operator-finalize', [ReservationController::class, 'operatorFinalize'])
        ->name('reservations.operator-finalize');
    Route::apiResource('reservations', ReservationController::class)->except(['update']);

    Route::apiResource('vehicles', VehicleController::class);

    Route::apiResource('payments', PaymentController::class)->except(['index']);
    Route::post('payments/{id}/mark-as-paid', [PaymentController::class, 'markAsPaid'])
        ->name('payments.mark-as-paid');
});
