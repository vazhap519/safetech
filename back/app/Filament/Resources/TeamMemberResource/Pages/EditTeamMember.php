<?php

namespace App\Filament\Resources\TeamMemberResource\Pages;

use App\Filament\Concerns\HasAiContentGenerator;
use App\Filament\Resources\TeamMemberResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTeamMember extends EditRecord
{
    use HasAiContentGenerator;

    protected static string $resource = TeamMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->aiContentAction(),
            DeleteAction::make(),
        ];
    }
}
