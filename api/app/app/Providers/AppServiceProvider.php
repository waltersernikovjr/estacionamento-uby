<?php

namespace App\Providers;

use App\Repositories\ClienteRepository;
use App\Repositories\DatabaseClienteRepository;
use App\Repositories\DatabaseVeiculoRepository;
use App\Repositories\VeiculoRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //public function register()
        $this->app->register(\PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider::class);

        $this->app->bind(
            \App\Repositories\ClienteRepository::class,
            \App\Repositories\DatabaseClienteRepository::class
        );

        $this->app->bind(
            \App\Repositories\VeiculoRepository::class,
            \App\Repositories\DatabaseVeiculoRepository::class
        );

        $this->app->bind(
            \App\Repositories\VagaRepository::class,
            \App\Repositories\DatabaseVagaRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
