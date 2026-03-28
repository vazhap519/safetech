<?php

namespace App\Filament\Resources\HomeTestimonials\Pages;

use App\Filament\Resources\HomeTestimonials\HomeTestimonialResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHomeTestimonial extends EditRecord
{
    protected static string $resource = HomeTestimonialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
