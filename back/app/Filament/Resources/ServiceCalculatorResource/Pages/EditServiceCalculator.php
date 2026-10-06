<?php

namespace App\Filament\Resources\ServiceCalculatorResource\Pages;

use App\Filament\Resources\ServiceCalculatorResource;
use Filament\Resources\Pages\EditRecord;

class EditServiceCalculator extends EditRecord
{
    protected static string $resource = ServiceCalculatorResource::class;

    public function getTitle(): string
    {
        return 'კალკულატორი — '.($this->record->name ?: $this->record->slug);
    }
}
