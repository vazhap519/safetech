<?php

namespace App\Filament\Resources\TestimonialResource\Pages;

use App\Filament\Concerns\HasAiContentGenerator;
use App\Filament\Resources\TestimonialResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTestimonial extends CreateRecord
{
    use HasAiContentGenerator;

    protected static string $resource = TestimonialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->aiContentAction(),
        ];
    }
}
