<?php

namespace App\Filament\Resources\AiKnowledgeItemResource\Pages;

use App\Filament\Resources\AiKnowledgeItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAiKnowledgeItem extends EditRecord
{
    protected static string $resource = AiKnowledgeItemResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
