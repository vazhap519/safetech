<?php

namespace App\Filament\Resources\ReviewInvitationResource\Pages;

use App\Filament\Resources\ReviewInvitationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReviewInvitation extends CreateRecord
{
    protected static string $resource = ReviewInvitationResource::class;

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'შეფასების მოწვევა შეიქმნა';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
