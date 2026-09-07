<?php

namespace App\Contracts\Logs;

use App\Enums\Logs\LogChannel;
use App\Exceptions\Logs\UnknownLogChannelException;
use App\Support\Logs\LogDeliveryResult;
use App\Support\Logs\LogMessage;

interface LogDispatcherInterface
{
    /**
     * @param LogMessage $message
     * @param LogChannel|null $channel
     * @return LogDeliveryResult
     * @throws UnknownLogChannelException
     */
    public function dispatch(LogMessage $message, ?LogChannel $channel = null): LogDeliveryResult;

    /**
     * @param LogMessage $message
     * @return array<int, LogDeliveryResult>
     * @throws UnknownLogChannelException
     */
    public function broadcast(LogMessage $message): array;
}
