<?php

namespace App\Services\Logs;

use App\Interfaces\Services\Logs\LoggerFactoryInterface;

class LoggerFactory implements LoggerFactoryInterface
{
    protected array $loggers = [];

    /**
     * @param string $type
     * @param string $loggerClass
     * @return void
     */
    public function registerLogger(string $type, string $loggerClass)
    {
        $this->loggers[$type] = $loggerClass;
    }

    /**
     * @param string $type
     * @return Loggers\BaseLogger
     * @throws \Exception
     */
    public function createLogger(string $type): Loggers\BaseLogger
    {
        if (!array_key_exists($type, $this->loggers)) {
            throw new \Exception('Unknown logger type: ' . $type);
        }

        return app($this->loggers[$type]);
    }
}
