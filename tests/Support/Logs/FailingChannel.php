<?php

namespace Tests\Support\Logs;

use App\Contracts\Logs\LogChannelInterface;
use App\Support\Logs\LogMessage;
use RuntimeException;

final class FailingChannel implements LogChannelInterface
{
    /**
     * @param LogMessage $message
     * @return void
     */
    public function write(LogMessage $message): void
    {
        throw new RuntimeException('Transport is down.');
    }
}
