<?php

namespace App\Filament\Widgets;

use App\Models\AiConversation;
use App\Models\AiFeedback;
use App\Models\AiKnowledgeCandidate;
use App\Models\AiKnowledgeItem;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AiAssistantStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('AI საუბრები — 30 დღე', AiConversation::query()->where('created_at', '>=', now()->subDays(30))->count())
                ->color('primary'),
            Stat::make('AI-დან შექმნილი ლიდები', AiConversation::query()->whereNotNull('contact_lead_id')->count())
                ->color('success'),
            Stat::make('Knowledge review რიგი', AiKnowledgeCandidate::query()->where('status', 'pending')->count())
                ->color('warning'),
            Stat::make('დამტკიცებული AI ცოდნა', AiKnowledgeItem::query()->where('status', 'approved')->count())
                ->color('info'),
            Stat::make('უარყოფითი შეფასებები', AiFeedback::query()->where('rating', -1)->count())
                ->description('პასუხები, რომლებიც საჭიროებს გაუმჯობესებას')
                ->color('danger'),
        ];
    }
}
