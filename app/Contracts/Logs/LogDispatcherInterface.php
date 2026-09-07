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
     * @param array<int, LogChannel>|null $channels
     * @return array<int, LogDeliveryResult>
     * @throws UnknownLogChannelException
     */
    public function dispatch(LogMessage $message, ?array $channels = null): array;

    /**
     * @return array<int, LogChannel>
     */
    public function channels(): array;
}
