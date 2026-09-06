<?php

namespace App\Filament\Resources\SiteSettingResource\Pages;

use App\Filament\Resources\SiteSettingResource;
use App\Support\AdminCacheManager;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListSiteSettings extends ListRecords
{
    protected static string $resource = SiteSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clearCaches')
                ->label('ქეშის გაწმენდა')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('ქეშის გაწმენდა')
                ->modalDescription('გაიწმინდება Laravel-ის config/route/view/application cache და განახლდება frontend-ის CMS cache. მონაცემები არ წაიშლება.')
                ->action(function (): void {
                    AdminCacheManager::clear();

                    Notification::make()
                        ->title('ქეში წარმატებით გაიწმინდა')
                        ->body('Laravel და frontend CMS cache განახლებულია.')
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
