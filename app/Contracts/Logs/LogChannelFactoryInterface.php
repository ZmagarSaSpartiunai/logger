<?php

namespace App\Contracts\Logs;

use App\Enums\Logs\LogChannel;
use App\Exceptions\Logs\UnknownLogChannelException;

interface LogChannelFactoryInterface
{
    /**
     * @param LogChannel $channel
     * @return LogChannelInterface
     * @throws UnknownLogChannelException
     */
    public function make(LogChannel $channel): LogChannelInterface;

    /**
     * @return array<int, LogChannel>
     */
    public function available(): array;
}
