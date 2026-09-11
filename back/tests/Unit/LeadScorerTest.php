<?php

namespace Tests\Unit;

use App\Models\ContactLead;
use App\Support\Leads\LeadScorer;
use PHPUnit\Framework\TestCase;

class LeadScorerTest extends TestCase
{
    public function test_detailed_business_calculator_lead_is_high_priority(): void
    {
        $lead = new ContactLead([
            'company' => 'Safe Warehouse LLC',
            'property_type' => 'warehouse',
            'source' => 'service-calculator',
            'message' => str_repeat('Detailed requirement ', 10),
            'details' => array_fill(0, 4, ['value' => '1']),
        ]);

        $result = (new LeadScorer)->score($lead);

        $this->assertSame(100, $result['score']);
        $this->assertSame('high', $result['priority']);
    }

    public function test_small_generic_request_remains_normal_priority(): void
    {
        $result = (new LeadScorer)->score(new ContactLead([
            'source' => 'consultation-popup',
            'message' => 'Need help',
        ]));

        $this->assertSame(['score' => 20, 'priority' => 'normal'], $result);
    }

    public function test_explicit_ai_conversion_is_high_priority(): void
    {
        $result = (new LeadScorer)->score(new ContactLead([
            'source' => 'ai-assistant',
            'message' => 'Please contact me.',
        ]));

        $this->assertSame(['score' => 70, 'priority' => 'high'], $result);
    }
}
