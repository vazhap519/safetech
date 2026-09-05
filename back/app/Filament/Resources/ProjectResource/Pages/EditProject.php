<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\LocalServiceLanding;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manageLocalServiceLandings')
                ->label('Service / City კავშირები')
                ->icon('heroicon-o-map-pin')
                ->color('primary')
                ->modalHeading('Project ↔ Service / City Landing')
                ->modalDescription('მონიშნე მხოლოდ ის სერვისისა და ქალაქის გვერდები, რომლებსაც ეს რეალური პროექტი ეკუთვნის. ეს კავშირები გამოიყენება internal linking-სა და Project structured data-ში.')
                ->schema([
                    Select::make('landing_ids')
                        ->label('დაკავშირებული Service / City გვერდები')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->options(fn (): array => LocalServiceLanding::query()
                            ->with('service')
                            ->where('is_published', true)
                            ->where('noindex', false)
                            ->orderBy('location_name')
                            ->get()
                            ->mapWithKeys(fn (LocalServiceLanding $landing): array => [
                                $landing->id => sprintf(
                                    '%s → %s',
                                    $landing->service?->name ?: $landing->service?->title ?: 'Service',
                                    $landing->location_name,
                                ),
                            ])
                            ->all())
                        ->helperText('მაგ.: კამერების მონტაჟი → ბაკურიანი. შეგიძლია ერთ პროექტს რამდენიმე შესაბამისი landing დაუკავშირო.'),
                ])
                ->fillForm(fn (): array => [
                    'landing_ids' => $this->record->localServiceLandings()->pluck('local_service_landings.id')->all(),
                ])
                ->action(function (array $data): void {
                    $landingIds = collect($data['landing_ids'] ?? [])
                        ->map(fn ($id): int => (int) $id)
                        ->filter()
                        ->unique()
                        ->values()
                        ->all();

                    $allowedIds = LocalServiceLanding::query()
                        ->whereIn('id', $landingIds)
                        ->where('is_published', true)
                        ->where('noindex', false)
                        ->pluck('id')
                        ->all();

                    $this->record->localServiceLandings()->sync($allowedIds);
                    $this->record->touch();

                    Notification::make()
                        ->title('Service / City კავშირები განახლდა')
                        ->body('Project internal links და structured data გამოიყენებს შენ მიერ არჩეულ რეალურ landing-ებს.')
                        ->success()
                        ->send();
                }),
            DeleteAction::make(),
        ];
    }
}
