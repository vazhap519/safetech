<?php

namespace App\Support\Leads;

use App\Models\ContactLead;

final class LeadScorer
{
    /** @return array{score: int, priority: string} */
    public function score(ContactLead $lead): array
    {
        $score = 20;

        if (filled($lead->company)) {
            $score += 25;
        }
        if (in_array($lead->source, ['service-calculator', 'calculator'], true)) {
            $score += 20;
        }

        // The AI assistant creates a lead only after the visitor explicitly
        // supplies contact details and asks to be contacted.
        if ($lead->source === 'ai-assistant') {
            $score += 50;
        }
        if (mb_strlen((string) $lead->message) >= 120) {
            $score += 10;
        }

        $businessContext = mb_strtolower(implode(' ', array_filter([
            $lead->company, $lead->property_type, $lead->project_size, $lead->message,
        ])));

        if (preg_match('/(?:company|business|office|warehouse|hotel|factory|კომპანია|ბიზნეს|ოფისი|საწყობ|სასტუმრო|ქარხანა|компан|бизнес|офис|склад|отел|завод)/u', $businessContext)) {
            $score += 15;
        }

        if (count($lead->details ?? []) >= 4) {
            $score += 10;
        }
        $score = min(100, $score);

        return [
            'score' => $score,
            'priority' => $score >= 70 ? 'high' : ($score >= 40 ? 'medium' : 'normal'),
        ];
    }
}
