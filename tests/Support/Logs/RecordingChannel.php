<?php

namespace Tests\Support\Logs;

use App\Contracts\Logs\LogChannelInterface;
use App\Support\Logs\LogMessage;

final class RecordingChannel implements LogChannelInterface
{
    /**
     * @var array<int, LogMessage>
     */
    public array $written = [];

    /**
     * @param LogMessage $message
     * @return void
     */
    public function write(LogMessage $message): void
    {
        $this->written[] = $message;
    }
}
