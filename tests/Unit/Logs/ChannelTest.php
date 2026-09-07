<?php

namespace Tests\Unit\Logs;

use App\Enums\Logs\LogLevel;
use App\Services\Logs\Channels\DatabaseChannel;
use App\Services\Logs\Channels\EmailChannel;
use App\Services\Logs\Channels\FileChannel;
use App\Support\Logs\LogMessage;
use PHPUnit\Framework\TestCase;
use Tests\Support\Logs\SpyLogger;

final class ChannelTest extends TestCase
{
    public function test_file_channel_writes_through_the_logger(): void
    {
        $logger = new SpyLogger;

        (new FileChannel($logger))->write(LogMessage::create('Disk is full.', LogLevel::Error));

        $this->assertCount(1, $logger->records);
        $this->assertSame('error', $logger->records[0]['level']);
        $this->assertStringContainsString('Log message by File:', $logger->records[0]['message']);
        $this->assertStringContainsString('Disk is full.', $logger->records[0]['message']);
    }

    public function test_database_channel_writes_through_the_logger(): void
    {
        $logger = new SpyLogger;

        (new DatabaseChannel($logger))->write(LogMessage::create('Row added.'));

        $this->assertSame('info', $logger->records[0]['level']);
        $this->assertStringContainsString('Log message by Db:', $logger->records[0]['message']);
    }

    public function test_email_channel_reports_its_mailbox(): void
    {
        $logger = new SpyLogger;

        (new EmailChannel($logger, 'ops@example.com'))->write(LogMessage::create('Mail sent.'));

        $this->assertStringContainsString('email=ops@example.com', $logger->records[0]['message']);
    }

    /**
     * A channel must never write to the output buffer: doing so corrupts the
     * HTTP response body before the controller can send its own.
     */
    public function test_channels_produce_no_output(): void
    {
        ob_start();
        (new FileChannel(new SpyLogger))->write(LogMessage::create('quiet'));
        (new DatabaseChannel(new SpyLogger))->write(LogMessage::create('quiet'));
        (new EmailChannel(new SpyLogger, 'ops@example.com'))->write(LogMessage::create('quiet'));

        $this->assertSame('', (string) ob_get_clean());
    }
}
