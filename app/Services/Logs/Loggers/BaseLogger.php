<?php

namespace App\Services\Logs\Loggers;

use App\Interfaces\Services\Logs\LoggerFactoryInterface;
use App\Interfaces\Services\Logs\LoggerInterface;

abstract class BaseLogger implements LoggerInterface
{
    private string $type;
    private readonly LoggerFactoryInterface $loggerFactory;

    public function __construct()
    {
        $this->loggerFactory = app(LoggerFactoryInterface::class);
        $this->type = config('loggers.default');
    }

    public function send(string $message): void
    {
        $this->sendByLogger($message, $this->type);
    }

    public function sendByLogger(string $message, string $loggerType): void
    {
        $logger = $this->loggerFactory->createLogger($loggerType);
        $logger->sendLogMessage($message);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    abstract function sendLogMessage(string $message): void;
}
