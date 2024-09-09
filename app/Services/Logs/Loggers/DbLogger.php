<?php

namespace App\Services\Logs\Loggers;

use App\Enums\Logs\LogTypes;

class DbLogger extends BaseLogger
{
    public readonly string $type;

    public function __construct()
    {
        parent::__construct();
        $this->type = LogTypes::LOG_NAME_DB;
    }

    public function sendLogMessage(string $message): void
    {
        echo 'Log message by Db: ' . $message . PHP_EOL;
    }
}
