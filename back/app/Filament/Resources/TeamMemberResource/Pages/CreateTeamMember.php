<?php

namespace App\Filament\Resources\TeamMemberResource\Pages;

use App\Filament\Concerns\HasAiContentGenerator;
use App\Filament\Resources\TeamMemberResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTeamMember extends CreateRecord
{
    use HasAiContentGenerator;

    protected static string $resource = TeamMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->aiContentAction(),
        ];
    }
}
