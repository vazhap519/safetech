<?php

namespace App\Filament\Resources\ServiceConfiguratorResource\Pages;

use App\Filament\Concerns\HasAiContentGenerator;
use App\Filament\Resources\ServiceConfiguratorResource;
use Filament\Resources\Pages\EditRecord;

class EditServiceConfigurator extends EditRecord
{
    use HasAiContentGenerator;

    protected static string $resource = ServiceConfiguratorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->aiContentAction(),
        ];
    }

    protected function aiContentProfile(): string
    {
        return 'service';
    }
}
