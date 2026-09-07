<?php

namespace App\Http\Requests\Logs;

use App\Enums\Logs\LogChannel;
use App\Enums\Logs\LogLevel;
use App\Support\Logs\LogMessage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreLogRequest extends FormRequest
{
    public const ALL_CHANNELS = '*';

    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:2000'],
            'level' => ['sometimes', Rule::enum(LogLevel::class)],
            'channels' => ['sometimes', 'array'],
            'channels.*' => [Rule::in(array_merge(LogChannel::values(), [self::ALL_CHANNELS]))],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'channels.*' => 'channel',
        ];
    }

    /**
     * @return LogMessage
     */
    public function logMessage(): LogMessage
    {
        return LogMessage::create(
            $this->string('message')->toString(),
            $this->enum('level', LogLevel::class) ?? LogLevel::Info,
        );
    }

    /**
     * Null means the caller did not pick any channel and the default applies.
     *
     * @param array<int, LogChannel> $available
     * @return array<int, LogChannel>|null
     */
    public function selectedChannels(array $available): ?array
    {
        $channels = $this->array('channels');

        if ($channels === []) {
            return null;
        }

        if (in_array(self::ALL_CHANNELS, $channels, true)) {
            return $available;
        }

        return array_map(
            static fn (string $name): LogChannel => LogChannel::from($name),
            array_values(array_unique($channels)),
        );
    }
}
