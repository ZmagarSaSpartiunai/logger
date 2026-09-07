<?php

namespace App\Services\Logs\Channels;

use App\Contracts\Logs\LogChannelInterface;
use App\Support\Logs\LogMessage;
use Psr\Log\LoggerInterface;

final class FileChannel implements LogChannelInterface
{
    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @param LogMessage $message
     * @return void
     */
    public function write(LogMessage $message): void
    {
        $this->logger->log($message->level->value, 'Log message by File: '.$message->format());
    }
}
