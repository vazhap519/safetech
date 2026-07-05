<?php

namespace App\Events;

use App\Models\ContactLead;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class LeadCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly ContactLead $lead) {}
}
