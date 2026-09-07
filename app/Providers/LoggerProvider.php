<?php

namespace App\Providers;

use App\Contracts\Logs\LogChannelFactoryInterface;
use App\Contracts\Logs\LogChannelInterface;
use App\Contracts\Logs\LogDispatcherInterface;
use App\Enums\Logs\LogChannel;
use App\Exceptions\Logs\UnknownLogChannelException;
use App\Services\Logs\Channels\EmailChannel;
use App\Services\Logs\LogChannelFactory;
use App\Services\Logs\LogDispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

class LoggerProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(
            LogChannelFactoryInterface::class,
            fn (Application $app): LogChannelFactory => new LogChannelFactory($app, $this->drivers()),
        );

        $this->app->singleton(
            LogDispatcherInterface::class,
            fn (Application $app): LogDispatcher => new LogDispatcher(
                $app->make(LogChannelFactoryInterface::class),
                $this->defaultChannel(),
                $app->make(LoggerInterface::class),
            ),
        );

        $this->app->when(EmailChannel::class)
            ->needs('$mailbox')
            ->give(fn (): string => (string) config('loggers.channels.'.LogChannel::Email->value.'.box'));
    }

    /**
     * @return array<string, class-string<LogChannelInterface>>
     * @throws UnknownLogChannelException
     */
    private function drivers(): array
    {
        $drivers = [];

        foreach ((array) config('loggers.channels', []) as $name => $options) {
            $channel = LogChannel::tryFrom((string) $name);

            if ($channel === null) {
                throw UnknownLogChannelException::forName((string) $name, $this->channelNames());
            }

            $drivers[$channel->value] = $options['driver'];
        }

        return $drivers;
    }

    /**
     * @return LogChannel
     * @throws UnknownLogChannelException
     */
    private function defaultChannel(): LogChannel
    {
        $name = (string) config('loggers.default');
        $channel = LogChannel::tryFrom($name);

        if ($channel === null || ! array_key_exists($name, $this->drivers())) {
            throw UnknownLogChannelException::forName($name, array_keys($this->drivers()));
        }

        return $channel;
    }

    /**
     * @return array<int, string>
     */
    private function channelNames(): array
    {
        return array_column(LogChannel::cases(), 'value');
    }
}
