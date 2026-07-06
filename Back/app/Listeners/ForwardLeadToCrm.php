<?php

namespace App\Listeners;

use App\Events\LeadCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;

final class ForwardLeadToCrm implements ShouldQueue
{
    public function handle(LeadCreated $event): void
    {
        $url = config('leads.crm_webhook_url');

        if (! $url) {
            return;
        }

        $request = Http::acceptJson()->timeout(10)->retry(2, 250);

        if ($token = config('leads.crm_webhook_token')) {
            $request = $request->withToken($token);
        }

        $request->post($url, $event->lead->only([
            'id',
            'name',
            'first_name',
            'last_name',
            'company',
            'phone',
            'email',
            'service',
            'service_slug',
            'project_size',
            'property_type',
            'details',
            'message',
            'source',
            'created_at',
        ]))->throw();
    }
}
