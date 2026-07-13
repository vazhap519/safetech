<?php

namespace App\Domain\Leads\Contracts;

use App\Domain\Leads\Data\LeadData;
use App\Models\ContactLead;

interface LeadRepository
{
    public function create(LeadData $data): ContactLead;
}
