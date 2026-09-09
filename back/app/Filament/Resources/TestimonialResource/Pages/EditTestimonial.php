<?php

namespace App\Filament\Resources\TestimonialResource\Pages;

use App\Filament\Concerns\HasAiContentGenerator;
use App\Filament\Resources\TestimonialResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTestimonial extends EditRecord
{
    use HasAiContentGenerator;

    protected static string $resource = TestimonialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->aiContentAction(),
            DeleteAction::make(),
        ];
    }
}
