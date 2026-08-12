<?php

namespace App\Filament\Resources\AiKnowledgeCandidateResource\Pages;

use App\Filament\Resources\AiKnowledgeCandidateResource;
use App\Models\AiKnowledgeItem;
use Filament\Resources\Pages\EditRecord;

class EditAiKnowledgeCandidate extends EditRecord
{
    protected static string $resource = AiKnowledgeCandidateResource::class;

    protected function afterSave(): void
    {
        $record = $this->record;

        if ($record->status === 'approved') {
            AiKnowledgeItem::query()->updateOrCreate(
                ['source_reference' => 'candidate:'.$record->id],
                [
                    'title' => mb_substr(trim((string) $record->question), 0, 255),
                    'content' => trim((string) $record->suggested_answer),
                    'category' => 'learned-faq',
                    'locale' => $record->locale,
                    'status' => 'approved',
                    'source_type' => 'learned',
                ],
            );
        }

        $record->forceFill([
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ])->saveQuietly();
    }
}
