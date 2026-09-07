<?php

namespace Tests\Unit\Logs;

use App\Enums\Logs\LogChannel;
use App\Exceptions\Logs\UnknownLogChannelException;
use App\Services\Logs\Channels\FileChannel;
use App\Services\Logs\LogChannelFactory;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class LogChannelFactoryTest extends TestCase
{
    public function test_it_resolves_the_configured_driver(): void
    {
        $this->assertInstanceOf(FileChannel::class, $this->factory()->make(LogChannel::File));
    }

    public function test_it_reuses_an_already_resolved_channel(): void
    {
        $factory = $this->factory();

        $this->assertSame($factory->make(LogChannel::File), $factory->make(LogChannel::File));
    }

    public function test_it_rejects_a_channel_without_a_driver(): void
    {
        $this->expectException(UnknownLogChannelException::class);

        $this->factory()->make(LogChannel::Database);
    }

    public function test_it_lists_only_the_configured_channels(): void
    {
        $this->assertSame([LogChannel::File], $this->factory()->available());
    }

    private function factory(): LogChannelFactory
    {
        $container = new Container;
        $container->instance(LoggerInterface::class, new NullLogger);

        return new LogChannelFactory($container, [
            LogChannel::File->value => FileChannel::class,
        ]);
    }
}
