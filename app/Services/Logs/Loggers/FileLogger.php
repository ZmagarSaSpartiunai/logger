<?php

namespace App\Services\Logs\Loggers;

use App\Enums\Logs\LogTypes;

class FileLogger extends BaseLogger
{
    public readonly string $type;

    public function __construct()
    {
        parent::__construct();
        $this->type = LogTypes::LOG_NAME_FILE;
    }

    public function sendLogMessage(string $message): void
    {
        echo 'Log message by File: ' . $message . PHP_EOL;
    }
}
