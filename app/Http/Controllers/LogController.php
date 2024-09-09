<?php

namespace App\Http\Controllers;

use App\Interfaces\Services\Logs\LoggerInterface;
use Illuminate\Http\JsonResponse;

class LogController extends Controller
{
    public function __construct(
        private readonly LoggerInterface $logger,
    )
    {
    }

    /**
     * Logs a message to the default logger.
     *
     * @return JsonResponse
     */
    public function log(): JsonResponse
    {
        $this->logger->send(date('H:i:s  d-m-Y ') . ' Sending by default channel.');

        return response()->json(['success' => true]);
    }

    /**
     * Sends a log message to a specific logger type.
     *
     * @param string $type
     * @return JsonResponse
     */
    public function logTo(string $type): JsonResponse
    {
        $this->logger->sendByLogger(date('H:i:s d-m-Y ') . " Sending by type=$type channel.", $type);

        return response()->json(['success' => true]);
    }

    /**
     * Logs a message to all available loggers (email, file, db).
     *
     * @return JsonResponse
     */
    public function logToAll(): JsonResponse
    {
        foreach (config('loggers.list') as $type) {
            $this->logger->sendByLogger(date('H:i:s d-m-Y ') . " Sending by type=$type channel.", $type);
        }

        return response()->json(['success' => true]);
    }
}
