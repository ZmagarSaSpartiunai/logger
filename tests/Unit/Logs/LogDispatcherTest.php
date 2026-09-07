<?php

namespace Tests\Unit\Logs;

use App\Contracts\Logs\LogChannelInterface;
use App\Enums\Logs\LogChannel;
use App\Services\Logs\LogDispatcher;
use App\Support\Logs\LogDeliveryResult;
use App\Support\Logs\LogMessage;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Tests\Support\Logs\FailingChannel;
use Tests\Support\Logs\FakeChannelFactory;
use Tests\Support\Logs\RecordingChannel;

final class LogDispatcherTest extends TestCase
{
    public function test_it_falls_back_to_the_default_channel(): void
    {
        $email = new RecordingChannel;
        $file = new RecordingChannel;
        $dispatcher = $this->dispatcher([
            LogChannel::Email->value => $email,
            LogChannel::File->value => $file,
        ]);

        $delivery = $dispatcher->dispatch(LogMessage::create('hello'));

        $this->assertSame(LogChannel::Email, $delivery->channel);
        $this->assertTrue($delivery->delivered);
        $this->assertCount(1, $email->written);
        $this->assertCount(0, $file->written);
    }

    public function test_it_honours_an_explicit_channel(): void
    {
        $email = new RecordingChannel;
        $file = new RecordingChannel;
        $dispatcher = $this->dispatcher([
            LogChannel::Email->value => $email,
            LogChannel::File->value => $file,
        ]);

        $delivery = $dispatcher->dispatch(LogMessage::create('hello'), LogChannel::File);

        $this->assertSame(LogChannel::File, $delivery->channel);
        $this->assertTrue($delivery->delivered);
        $this->assertCount(0, $email->written);
        $this->assertCount(1, $file->written);
    }

    public function test_it_reports_a_failing_single_channel_instead_of_throwing(): void
    {
        $dispatcher = $this->dispatcher([LogChannel::Email->value => new FailingChannel]);

        $delivery = $dispatcher->dispatch(LogMessage::create('hello'));

        $this->assertFalse($delivery->delivered);
        $this->assertSame('Transport is down.', $delivery->failureReason);
    }

    public function test_it_broadcasts_to_every_configured_channel(): void
    {
        $email = new RecordingChannel;
        $file = new RecordingChannel;
        $dispatcher = $this->dispatcher([
            LogChannel::Email->value => $email,
            LogChannel::File->value => $file,
        ]);

        $deliveries = $dispatcher->broadcast(LogMessage::create('hello'));

        $this->assertSame(
            [LogChannel::Email, LogChannel::File],
            array_map(static fn (LogDeliveryResult $d): LogChannel => $d->channel, $deliveries),
        );
        $this->assertTrue($deliveries[0]->delivered);
        $this->assertTrue($deliveries[1]->delivered);
    }

    public function test_a_failing_channel_does_not_stop_the_broadcast(): void
    {
        $file = new RecordingChannel;
        $deliveries = $this->dispatcher([
            LogChannel::Email->value => new FailingChannel,
            LogChannel::File->value => $file,
        ])->broadcast(LogMessage::create('hello'));

        $this->assertFalse($deliveries[0]->delivered);
        $this->assertSame('Transport is down.', $deliveries[0]->failureReason);
        $this->assertTrue($deliveries[1]->delivered);
        $this->assertCount(1, $file->written);
    }

    /**
     * @param  array<string, LogChannelInterface>  $channels
     */
    private function dispatcher(array $channels): LogDispatcher
    {
        return new LogDispatcher(
            new FakeChannelFactory($channels),
            LogChannel::Email,
            new NullLogger,
        );
    }
}
