<?php

namespace App\Filament\Resources\PageImageResource\Pages;

use App\Filament\Resources\PageImageResource;
use Filament\Resources\Pages\EditRecord;

class EditPageImages extends EditRecord
{
    protected static string $resource = PageImageResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
