<?php

namespace App\Filament\Resources\ReviewInvitationResource\Pages;

use App\Filament\Resources\ReviewInvitationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReviewInvitations extends ListRecords
{
    protected static string $resource = ReviewInvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('მოწვევის შექმნა'),
        ];
    }
}
