<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Leads\Contracts\LeadRepository;
use App\Domain\Leads\Data\LeadData;
use App\Models\ContactLead;

final class EloquentLeadRepository implements LeadRepository
{
    public function create(LeadData $data): ContactLead
    {
        return ContactLead::query()->create($data->toArray());
    }
}
