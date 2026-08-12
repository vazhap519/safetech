<?php

namespace App\Filament\Resources\AiKnowledgeItemResource\Pages;

use App\Filament\Resources\AiKnowledgeItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAiKnowledgeItems extends ListRecords
{
    protected static string $resource = AiKnowledgeItemResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('ცოდნის დამატება')];
    }
}
