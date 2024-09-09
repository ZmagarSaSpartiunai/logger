<?php

namespace App\Providers;

use App\Interfaces\Services\Logs\LoggerFactoryInterface;
use App\Interfaces\Services\Logs\LoggerInterface;
use App\Services\Logs\LoggerFactory;
use App\Services\Logs\Loggers\BaseLogger;
use Illuminate\Support\ServiceProvider;

class LoggerProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LoggerInterface::class, BaseLogger::class);

        $this->app->singleton(LoggerFactoryInterface::class, function ($app) {
            $factory = $app->make(LoggerFactory::class);
            foreach (config('loggers.list') as $loggerClass => $type) {
                $factory->registerLogger($type, $loggerClass);
            }

            return $factory;
        });

        $this->app->bind(BaseLogger::class, function ($app) {
            $factory = $app->make(LoggerFactoryInterface::class);

            return $factory->createLogger(config('loggers.default'));
        });
    }
}
