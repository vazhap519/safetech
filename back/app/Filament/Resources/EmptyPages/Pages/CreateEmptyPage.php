<?php

namespace App\Filament\Resources\EmptyPages\Pages;

use App\Filament\Resources\EmptyPages\EmptyPageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmptyPage extends CreateRecord
{
    protected static string $resource = EmptyPageResource::class;
}
