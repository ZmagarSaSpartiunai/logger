<?php

namespace Tests\Support\Logs;

use Psr\Log\AbstractLogger;
use Stringable;

final class SpyLogger extends AbstractLogger
{
    /**
     * @var array<int, array{level: string, message: string, context: array<string, mixed>}>
     */
    public array $records = [];

    /**
     * @param mixed $level
     * @param string|Stringable $message
     * @param array<string, mixed> $context
     * @return void
     */
    public function log($level, string|Stringable $message, array $context = []): void
    {
        $this->records[] = [
            'level' => (string) $level,
            'message' => (string) $message,
            'context' => $context,
        ];
    }
}
