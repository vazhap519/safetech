<?php

namespace App\Filament\Resources\LocalServiceLandingResource\Pages;

use App\Filament\Resources\LocalServiceLandingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLocalServiceLandings extends ListRecords
{
    protected static string $resource = LocalServiceLandingResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Local SEO გვერდის დამატება')];
    }
}
