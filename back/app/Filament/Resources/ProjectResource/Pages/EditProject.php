<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Filament\Support\RelatedProjectDefaults;
use App\Models\LocalServiceLanding;
use App\Models\Project;
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
            Action::make('manageRelatedProjects')
                ->label('Related Projects')
                ->icon('heroicon-o-link')
                ->color('gray')
                ->modalHeading('Related Projects მართვა')
                ->modalDescription('აქედან შეგიძლია პირდაპირ მონიშნო ან მოხსნა დაკავშირებული პროექტები. ცვლილება ინახება პირდაპირ Project-ში და არ არის დამოკიდებული repeater-ის წაშლის ღილაკზე.')
                ->schema([
                    Select::make('related_slugs')
                        ->label('დაკავშირებული პროექტები')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->options(fn (): array => Project::query()
                            ->whereKeyNot($this->record->getKey())
                            ->orderBy('name')
                            ->pluck('name', 'slug')
                            ->all())
                        ->helperText('ყველას მოსახსნელად დატოვე სია ცარიელი და დააჭირე Save-ს.'),
                ])
                ->fillForm(fn (): array => [
                    'related_slugs' => collect($this->record->related ?? [])
                        ->pluck('slug')
                        ->filter()
                        ->values()
                        ->all(),
                ])
                ->action(function (array $data): void {
                    $slugs = collect($data['related_slugs'] ?? [])
                        ->map(fn ($slug): string => trim((string) $slug))
                        ->filter()
                        ->unique()
                        ->values();

                    $allowedSlugs = Project::query()
                        ->whereKeyNot($this->record->getKey())
                        ->whereIn('slug', $slugs)
                        ->pluck('slug')
                        ->all();

                    $related = collect($allowedSlugs)
                        ->map(function (string $slug): ?array {
                            $defaults = RelatedProjectDefaults::forSlug($slug);

                            if ($defaults === null) {
                                return null;
                            }

                            $item = ['slug' => $slug];

                            foreach ($defaults as $path => $value) {
                                data_set($item, $path, $value);
                            }

                            return $item;
                        })
                        ->filter()
                        ->values()
                        ->all();

                    $this->record->forceFill(['related' => $related])->save();
                    $this->fillForm();

                    Notification::make()
                        ->title('Related Projects განახლდა')
                        ->body($related ? 'არჩეული პროექტები შეინახა.' : 'ყველა Related Project მოიხსნა.')
                        ->success()
                        ->send();
                }),
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
