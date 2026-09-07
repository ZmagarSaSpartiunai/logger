<?php

namespace App\Http\Resources\Logs;

use App\Support\Logs\LogDeliveryResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read LogDeliveryResult $resource
 */
final class LogDeliveryResource extends JsonResource
{
    /**
     * @var string|null
     */
    public static $wrap = null;

    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'channel' => $this->resource->channel->value,
            'delivered' => $this->resource->delivered,
            $this->mergeWhen($this->resource->failureReason !== null, fn (): array => [
                'failure_reason' => $this->resource->failureReason,
            ]),
        ];
    }
}
