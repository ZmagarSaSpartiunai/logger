<?php

namespace App\Services\Logs\Channels;

use App\Contracts\Logs\LogChannelInterface;
use App\Support\Logs\LogMessage;
use Psr\Log\LoggerInterface;

final class EmailChannel implements LogChannelInterface
{
    /**
     * @param LoggerInterface $logger
     * @param string $mailbox
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly string $mailbox,
    ) {
    }

    /**
     * @param LogMessage $message
     * @return void
     */
    public function write(LogMessage $message): void
    {
        $this->logger->log(
            $message->level->value,
            "Log message by email={$this->mailbox}: ".$message->format(),
        );
    }
}
