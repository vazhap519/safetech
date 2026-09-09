<?php

namespace App\Filament\Resources\PartnerResource\Pages;

use App\Filament\Concerns\HasAiContentGenerator;
use App\Filament\Resources\PartnerResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePartner extends CreateRecord
{
    use HasAiContentGenerator;

    protected static string $resource = PartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->aiContentAction(),
        ];
    }
}
