<?php

namespace App\Jobs;

use App\Models\Webhook;
use App\Models\WebhookDelivery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Throwable;

class DispatchWebhookJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Webhook $webhook,
        public string $event,
        public array $payload,
        public int $attempt = 1,
    ) {}

    public function handle(): void
    {
        $delivery = WebhookDelivery::create([
            'webhook_id' => $this->webhook->id,
            'event' => $this->event,
            'payload' => $this->payload,
            'status' => 'pending',
            'attempt' => $this->attempt,
        ]);

        $body = json_encode($this->payload);
        $signature = $this->signPayload($body);

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Webhook-Event' => $this->event,
                    'X-Webhook-Signature' => $signature,
                ])
                ->withBody($body, 'application/json')
                ->post($this->webhook->target_url);

            $delivery->update([
                'response_status' => $response->status(),
                'response_body' => mb_substr($response->body(), 0, 65000),
                'status' => $response->successful() ? 'success' : 'failed',
            ]);
        } catch (Throwable $e) {
            $delivery->update([
                'response_status' => null,
                'response_body' => $e->getMessage(),
                'status' => 'failed',
            ]);
        }
    }

    protected function signPayload(string $body): string
    {
        $secret = $this->webhook->secret ?? config('app.key');

        return hash_hmac('sha256', $body, $secret);
    }
}
