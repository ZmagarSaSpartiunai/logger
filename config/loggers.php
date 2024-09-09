<?php

use App\Enums\Logs\LogTypes;
use App\Services\Logs\Loggers\DbLogger;
use App\Services\Logs\Loggers\EmailLogger;
use App\Services\Logs\Loggers\FileLogger;

return [
    'default' => LogTypes::LOG_NAME_EMAIL,
    'list' => [
        EmailLogger::class => LogTypes::LOG_NAME_EMAIL,
        FileLogger::class => LogTypes::LOG_NAME_FILE,
        DbLogger::class => LogTypes::LOG_NAME_DB,
    ],
    LogTypes::LOG_NAME_EMAIL => [
        'box' => env('LOGGER_EMAIL'),
    ]
];
