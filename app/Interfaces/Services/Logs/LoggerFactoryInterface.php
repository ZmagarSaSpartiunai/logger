<?php

namespace App\Interfaces\Services\Logs;

use App\Services\Logs\Loggers\BaseLogger;

interface LoggerFactoryInterface
{
    public function createLogger(string $type): BaseLogger;
}
