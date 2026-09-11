<?php

namespace App\Application\Leads\Actions;

use App\Domain\Leads\Contracts\LeadRepository;
use App\Domain\Leads\Data\LeadData;
use App\Events\LeadCreated;
use App\Models\ContactLead;
use App\Support\Leads\LeadScorer;
use Illuminate\Support\Facades\DB;

final readonly class CreateLead
{
    public function __construct(private LeadRepository $repository, private LeadScorer $scorer) {}

    public function execute(LeadData $data): ContactLead
    {
        $lead = DB::transaction(function () use ($data): ContactLead {
            $lead = $this->repository->create($data);
            $result = $this->scorer->score($lead);
            $lead->forceFill([
                'lead_score' => $result['score'],
                'priority' => $result['priority'],
            ])->saveQuietly();

            return $lead;
        });
        LeadCreated::dispatch($lead);

        return $lead;
    }
}
