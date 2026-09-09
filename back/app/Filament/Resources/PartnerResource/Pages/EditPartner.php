<?php

namespace App\Filament\Resources\PartnerResource\Pages;

use App\Filament\Concerns\HasAiContentGenerator;
use App\Filament\Resources\PartnerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPartner extends EditRecord
{
    use HasAiContentGenerator;

    protected static string $resource = PartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->aiContentAction(),
            DeleteAction::make(),
        ];
    }
}
