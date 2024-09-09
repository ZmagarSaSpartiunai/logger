<?php

namespace App\Providers;

use App\Interfaces\Services\Logs\LoggerInterface;
use App\Interfaces\Services\Logs\LoggerFactoryInterface;
use App\Services\Logs\LoggerFactory;
use App\Services\Logs\Loggers\BaseLogger;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
