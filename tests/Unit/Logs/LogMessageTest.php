<?php

namespace Tests\Unit\Logs;

use App\Enums\Logs\LogLevel;
use App\Support\Logs\LogMessage;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class LogMessageTest extends TestCase
{
    public function test_it_defaults_to_the_info_level(): void
    {
        $this->assertSame(LogLevel::Info, LogMessage::create('hello')->level);
    }

    public function test_it_formats_timestamp_level_and_text(): void
    {
        $message = new LogMessage(
            'Disk is full.',
            LogLevel::Error,
            new DateTimeImmutable('2026-09-07 14:30:00'),
        );

        $this->assertSame('[07-09-2026 14:30:00] ERROR: Disk is full.', $message->format());
    }
}
