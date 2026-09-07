<?php

namespace App\Contracts\Logs;

use App\Support\Logs\LogMessage;

interface LogChannelInterface
{
    /**
     * @param LogMessage $message
     * @return void
     */
    public function write(LogMessage $message): void;
}
