<?php

namespace App\Http\Requests\Logs;

use App\Enums\Logs\LogLevel;
use App\Support\Logs\LogMessage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class LogMessageRequest extends FormRequest
{
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
}
