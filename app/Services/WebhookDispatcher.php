<?php

namespace App\Services;

use App\Jobs\DispatchWebhookJob;
use App\Models\Organization;
use App\Models\Webhook;
use Illuminate\Support\Facades\Http;

class WebhookDispatcher
{
    public function dispatch(Organization $org, string $event, array $payload): void
    {
        $webhooks = Webhook::query()
            ->withoutGlobalScopes()
            ->where('organization_id', $org->id)
            ->where('is_active', true)
            ->get()
            ->filter(fn (Webhook $webhook) => $this->subscribedTo($webhook, $event));

        foreach ($webhooks as $webhook) {
            if (class_exists(DispatchWebhookJob::class)) {
                DispatchWebhookJob::dispatch($webhook, $event, $payload);
            } else {
                $this->deliverSync($webhook, $event, $payload);
            }
        }
    }

    protected function subscribedTo(Webhook $webhook, string $event): bool
    {
        $events = $webhook->events ?? [];

        return in_array($event, $events, true) || in_array('*', $events, true);
    }

    protected function deliverSync(Webhook $webhook, string $event, array $payload): void
    {
        $body = json_encode($payload);
        $secret = $webhook->secret ?? config('app.key');
        $signature = hash_hmac('sha256', $body, $secret);

        Http::timeout(30)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'X-Webhook-Event' => $event,
                'X-Webhook-Signature' => $signature,
            ])
            ->withBody($body, 'application/json')
            ->post($webhook->target_url);
    }
}
