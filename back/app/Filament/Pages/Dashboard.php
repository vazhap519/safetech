<?php

namespace App\Filament\Pages;

use App\Support\AdminCacheManager;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('clearCache')
                ->label('ქეშის გაწმენდა')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('ქეშის გაწმენდა')
                ->modalDescription('გასუფთავდება Laravel-ის cache/config/routes/views და განახლდება public/Next.js CMS cache. გამოიყენე როცა ადმინში ცვლილება ან frontend-ის განახლება არ აისახება.')
                ->action(function (): void {
                    AdminCacheManager::clear();

                    Notification::make()
                        ->title('ქეში გასუფთავდა')
                        ->body('Laravel და frontend CMS cache განახლებულია.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
