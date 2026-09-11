<?php

namespace Tests\Unit;

use App\Events\LeadCreated;
use App\Listeners\ForwardLeadToCrm;
use App\Models\ContactLead;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ForwardLeadToCrmTest extends TestCase
{
    public function test_disabled_crm_does_not_make_an_http_request(): void
    {
        config()->set('leads.crm_webhook_url');
        Http::fake();

        (new ForwardLeadToCrm)->handle(new LeadCreated(new ContactLead));

        Http::assertNothingSent();
    }

    public function test_crm_payload_excludes_private_operational_fields(): void
    {
        config()->set('leads.crm_webhook_url', 'https://crm.example.test/leads');
        config()->set('leads.crm_webhook_token', 'secret-token');
        Http::fake(['https://crm.example.test/*' => Http::response(['ok' => true])]);

        $lead = new ContactLead([
            'name' => 'ქართული კლიენტი',
            'phone' => '+995555123456',
            'source' => 'calculator',
            'submission_key' => 'must-not-leave-the-api',
            'ip_hash' => 'must-not-leave-the-api',
        ]);
        $lead->id = 42;

        (new ForwardLeadToCrm)->handle(new LeadCreated($lead));

        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'https://crm.example.test/leads'
                && $request->hasHeader('Authorization', 'Bearer secret-token')
                && $request['name'] === 'ქართული კლიენტი'
                && ! isset($request['submission_key'])
                && ! isset($request['ip_hash']);
        });
    }
}
