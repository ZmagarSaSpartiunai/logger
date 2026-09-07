<?php

namespace Tests\Feature\Logs;

use App\Contracts\Logs\LogChannelFactoryInterface;
use App\Contracts\Logs\LogChannelInterface;
use App\Contracts\Logs\LogDispatcherInterface;
use App\Enums\Logs\LogChannel;
use Tests\Support\Logs\FailingChannel;
use Tests\Support\Logs\FakeChannelFactory;
use Tests\Support\Logs\RecordingChannel;
use Tests\TestCase;

final class LogApiTest extends TestCase
{
    private RecordingChannel $email;

    private RecordingChannel $file;

    private RecordingChannel $database;

    protected function setUp(): void
    {
        parent::setUp();

        $this->email = new RecordingChannel;
        $this->file = new RecordingChannel;
        $this->database = new RecordingChannel;

        $this->swapChannels([
            LogChannel::Email->value => $this->email,
            LogChannel::File->value => $this->file,
            LogChannel::Database->value => $this->database,
        ]);
    }

    public function test_it_logs_to_the_default_channel(): void
    {
        $response = $this->postJson(route('logs.store'), ['message' => 'hello']);

        $response->assertAccepted()
            ->assertJsonPath('data.deliveries', [
                ['channel' => 'email', 'delivered' => true],
            ])
            ->assertJsonPath('data.level', 'info');

        $this->assertCount(1, $this->email->written);
        $this->assertSame('hello', $this->email->written[0]->text);
    }

    public function test_it_logs_to_an_explicit_channel(): void
    {
        $response = $this->postJson(route('logs.store'), [
            'message' => 'hello',
            'channel' => LogChannel::File->value,
            'level' => 'warning',
        ]);

        $response->assertAccepted()
            ->assertJsonPath('data.deliveries', [
                ['channel' => 'file', 'delivered' => true],
            ])
            ->assertJsonPath('data.level', 'warning');

        $this->assertCount(1, $this->file->written);
        $this->assertCount(0, $this->email->written);
    }

    public function test_it_broadcasts_to_every_channel(): void
    {
        $response = $this->postJson(route('logs.broadcast'), ['message' => 'hello']);

        $response->assertAccepted()->assertJsonPath('data.deliveries', [
            ['channel' => 'email', 'delivered' => true],
            ['channel' => 'file', 'delivered' => true],
            ['channel' => 'database', 'delivered' => true],
        ]);
    }

    public function test_a_partially_failing_broadcast_reports_multi_status(): void
    {
        $this->swapChannels([
            LogChannel::Email->value => new FailingChannel,
            LogChannel::File->value => $this->file,
        ]);

        $response = $this->postJson(route('logs.broadcast'), ['message' => 'hello']);

        $response->assertStatus(207)->assertJsonPath('data.deliveries', [
            [
                'channel' => 'email',
                'delivered' => false,
                'failure_reason' => 'Transport is down.',
            ],
            ['channel' => 'file', 'delivered' => true],
        ]);

        $this->assertCount(1, $this->file->written);
    }

    public function test_a_fully_failing_delivery_reports_bad_gateway(): void
    {
        $this->swapChannels([LogChannel::Email->value => new FailingChannel]);

        $this->postJson(route('logs.store'), ['message' => 'hello'])
            ->assertStatus(502)
            ->assertJsonPath('data.deliveries.0.delivered', false)
            ->assertJsonPath('data.deliveries.0.failure_reason', 'Transport is down.');
    }

    public function test_it_requires_a_message(): void
    {
        $this->postJson(route('logs.store'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('message');
    }

    public function test_it_rejects_an_unknown_channel(): void
    {
        $this->postJson(route('logs.store'), ['message' => 'hello', 'channel' => 'carrier-pigeon'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('channel');

        $this->assertCount(0, $this->email->written);
    }

    public function test_it_rejects_an_unknown_level(): void
    {
        $this->postJson(route('logs.store'), ['message' => 'hello', 'level' => 'catastrophic'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('level');
    }

    /**
     * @param  array<string, LogChannelInterface>  $channels
     */
    private function swapChannels(array $channels): void
    {
        $this->app->instance(LogChannelFactoryInterface::class, new FakeChannelFactory($channels));
        $this->app->forgetInstance(LogDispatcherInterface::class);
    }
}
