<?php

use App\Enums\Logs\LogChannel;
use App\Services\Logs\Channels\DatabaseChannel;
use App\Services\Logs\Channels\EmailChannel;
use App\Services\Logs\Channels\FileChannel;

return [
    'default' => env('LOGGER_DEFAULT_CHANNEL', LogChannel::Email->value),

    'channels' => [
        LogChannel::Email->value => [
            'driver' => EmailChannel::class,
            'box' => env('LOGGER_EMAIL', 'logs@example.com'),
        ],
        LogChannel::File->value => [
            'driver' => FileChannel::class,
        ],
        LogChannel::Database->value => [
            'driver' => DatabaseChannel::class,
        ],
    ],
];
