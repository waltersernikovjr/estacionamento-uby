<?php

namespace App\Providers;

use App\Domain\Contracts\Repositories\CustomerRepositoryInterface;
use App\Domain\Contracts\Repositories\OperatorRepositoryInterface;
use App\Domain\Contracts\Repositories\ParkingSpotRepositoryInterface;
use App\Domain\Contracts\Repositories\PaymentRepositoryInterface;
use App\Domain\Contracts\Repositories\ReservationRepositoryInterface;
use App\Domain\Contracts\Repositories\VehicleRepositoryInterface;
use App\Infrastructure\Repositories\EloquentCustomerRepository;
use App\Infrastructure\Repositories\EloquentOperatorRepository;
use App\Infrastructure\Repositories\EloquentParkingSpotRepository;
use App\Infrastructure\Repositories\EloquentPaymentRepository;
use App\Infrastructure\Repositories\EloquentReservationRepository;
use App\Infrastructure\Repositories\EloquentVehicleRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind repository interfaces to their Eloquent implementations
        $this->app->bind(
            OperatorRepositoryInterface::class,
            EloquentOperatorRepository::class
        );

        $this->app->bind(
            CustomerRepositoryInterface::class,
            EloquentCustomerRepository::class
        );

        $this->app->bind(
            ParkingSpotRepositoryInterface::class,
            EloquentParkingSpotRepository::class
        );

        $this->app->bind(
            ReservationRepositoryInterface::class,
            EloquentReservationRepository::class
        );

        $this->app->bind(
            VehicleRepositoryInterface::class,
            EloquentVehicleRepository::class
        );

        $this->app->bind(
            PaymentRepositoryInterface::class,
            EloquentPaymentRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
