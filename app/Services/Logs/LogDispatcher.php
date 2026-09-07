<?php

namespace App\Services\Logs;

use App\Contracts\Logs\LogChannelFactoryInterface;
use App\Contracts\Logs\LogChannelInterface;
use App\Contracts\Logs\LogDispatcherInterface;
use App\Enums\Logs\LogChannel;
use App\Exceptions\Logs\UnknownLogChannelException;
use App\Support\Logs\LogDeliveryResult;
use App\Support\Logs\LogMessage;
use Psr\Log\LoggerInterface;
use Throwable;

final class LogDispatcher implements LogDispatcherInterface
{
    /**
     * @param LogChannelFactoryInterface $factory
     * @param LogChannel $defaultChannel
     * @param LoggerInterface $fallbackLogger
     */
    public function __construct(
        private readonly LogChannelFactoryInterface $factory,
        private readonly LogChannel $defaultChannel,
        private readonly LoggerInterface $fallbackLogger,
    ) {
    }

    /**
     * @param LogMessage $message
     * @param LogChannel|null $channel
     * @return LogDeliveryResult
     * @throws UnknownLogChannelException
     */
    public function dispatch(LogMessage $message, ?LogChannel $channel = null): LogDeliveryResult
    {
        $target = $channel ?? $this->defaultChannel;

        return $this->writeTo($this->factory->make($target), $target, $message);
    }

    /**
     * @param LogMessage $message
     * @return array<int, LogDeliveryResult>
     * @throws UnknownLogChannelException
     */
    public function broadcast(LogMessage $message): array
    {
        $deliveries = [];

        foreach ($this->factory->available() as $channel) {
            $deliveries[] = $this->writeTo($this->factory->make($channel), $channel, $message);
        }

        return $deliveries;
    }

    /**
     * @param LogChannelInterface $target
     * @param LogChannel $channel
     * @param LogMessage $message
     * @return LogDeliveryResult
     */
    private function writeTo(
        LogChannelInterface $target,
        LogChannel $channel,
        LogMessage $message,
    ): LogDeliveryResult {
        try {
            $target->write($message);

            return LogDeliveryResult::delivered($channel);
        } catch (Throwable $failure) {
            $this->fallbackLogger->error('Log channel failed.', [
                'channel' => $channel->value,
                'exception' => $failure,
            ]);

            return LogDeliveryResult::failed($channel, $failure);
        }
    }
}
