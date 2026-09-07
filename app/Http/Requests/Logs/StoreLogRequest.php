<?php

namespace App\Http\Requests\Logs;

use App\Enums\Logs\LogChannel;
use Illuminate\Validation\Rule;

final class StoreLogRequest extends LogMessageRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'channel' => ['sometimes', 'nullable', Rule::enum(LogChannel::class)],
        ]);
    }

    /**
     * Null means the caller did not pick a channel and the default applies.
     *
     * @return LogChannel|null
     */
    public function channel(): ?LogChannel
    {
        return $this->enum('channel', LogChannel::class);
    }
}
