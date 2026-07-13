<?php

namespace App\Application\Leads\Actions;

use App\Domain\Leads\Contracts\LeadRepository;
use App\Domain\Leads\Data\LeadData;
use App\Events\LeadCreated;
use App\Models\ContactLead;
use Illuminate\Support\Facades\DB;

final readonly class CreateLead
{
    public function __construct(private LeadRepository $repository) {}

    public function execute(LeadData $data): ContactLead
    {
        $lead = DB::transaction(fn (): ContactLead => $this->repository->create($data));
        LeadCreated::dispatch($lead);

        return $lead;
    }
}
