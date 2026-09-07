<?php

namespace App\Enums\Logs;

enum LogChannel: string
{
    case Email = 'email';
    case File = 'file';
    case Database = 'database';
}
