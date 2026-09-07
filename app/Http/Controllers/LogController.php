<?php

namespace App\Http\Controllers;

use App\Contracts\Logs\LogDispatcherInterface;
use App\Exceptions\Logs\UnknownLogChannelException;
use App\Http\Requests\Logs\StoreLogRequest;
use App\Http\Resources\Logs\LogDeliveryResource;
use App\Support\Logs\LogDeliveryResult;
use App\Support\Logs\LogMessage;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class LogController extends Controller
{
    /**
     * @param LogDispatcherInterface $dispatcher
     */
    public function __construct(
        private readonly LogDispatcherInterface $dispatcher,
    ) {
    }

    /**
     * @param StoreLogRequest $request
     * @return JsonResponse
     * @throws UnknownLogChannelException
     */
    public function store(StoreLogRequest $request): JsonResponse
    {
        $message = $request->logMessage();

        return $this->respond($message, $this->dispatcher->dispatch(
            $message,
            $request->selectedChannels($this->dispatcher->channels()),
        ));
    }

    /**
     * @param LogMessage $message
     * @param array<int, LogDeliveryResult> $deliveries
     * @return JsonResponse
     */
    private function respond(LogMessage $message, array $deliveries): JsonResponse
    {
        return response()->json([
            'data' => [
                'deliveries' => LogDeliveryResource::collection($deliveries)->resolve(),
                'level' => $message->level->value,
                'logged_at' => $message->occurredAt->format(DATE_ATOM),
            ],
        ], $this->statusFor($deliveries));
    }

    /**
     * @param array<int, LogDeliveryResult> $deliveries
     * @return int
     */
    private function statusFor(array $deliveries): int
    {
        $delivered = array_filter(
            $deliveries,
            static fn (LogDeliveryResult $delivery): bool => $delivery->delivered,
        );

        if ($delivered === []) {
            return Response::HTTP_BAD_GATEWAY;
        }

        return count($delivered) === count($deliveries)
            ? Response::HTTP_ACCEPTED
            : Response::HTTP_MULTI_STATUS;
    }
}
