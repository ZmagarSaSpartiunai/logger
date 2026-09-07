<?php

namespace Tests\Support\Logs;

use App\Contracts\Logs\LogChannelFactoryInterface;
use App\Contracts\Logs\LogChannelInterface;
use App\Enums\Logs\LogChannel;
use App\Exceptions\Logs\UnknownLogChannelException;

final class FakeChannelFactory implements LogChannelFactoryInterface
{
    /**
     * @param array<string, LogChannelInterface> $channels
     */
    public function __construct(
        private readonly array $channels,
    ) {
    }

    /**
     * @param LogChannel $channel
     * @return LogChannelInterface
     * @throws UnknownLogChannelException
     */
    public function make(LogChannel $channel): LogChannelInterface
    {
        return $this->channels[$channel->value]
            ?? throw UnknownLogChannelException::forChannel($channel, array_keys($this->channels));
    }

    /**
     * @return array<int, LogChannel>
     */
    public function available(): array
    {
        return array_map(
            static fn (string $name): LogChannel => LogChannel::from($name),
            array_keys($this->channels),
        );
    }
}
