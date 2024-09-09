<?php

namespace App\Services\Logs\Loggers;

use App\Enums\Logs\LogTypes;

class EmailLogger extends BaseLogger
{
    public readonly string $type;
    public string $email;

    public function __construct()
    {
        parent::__construct();
        $this->email = config('loggers.' . LogTypes::LOG_NAME_EMAIL . '.box');
        $this->type = LogTypes::LOG_NAME_EMAIL;
    }

    public function sendLogMessage(string $message): void
    {
        echo "Log message by $this->type=$this->email: $message" . PHP_EOL;
    }
}
