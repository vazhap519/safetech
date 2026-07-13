<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactLeadResource\Pages;
use App\Models\ContactLead;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ContactLeadResource extends Resource
{
    protected static ?string $model = ContactLead::class;

    protected static ?string $navigationLabel = 'მოთხოვნები';

    protected static ?string $modelLabel = 'მოთხოვნა';

    /** @return array<string, string> */
    private static function statusOptions(): array
    {
        return [
            'new' => 'ახალი',
            'contacted' => 'დაკავშირებული',
            'qualified' => 'კვალიფიცირებული',
            'won' => 'წარმატებული',
            'lost' => 'დაკარგული',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('სრული სახელი')->disabled(),
            TextInput::make('first_name')->label('სახელი')->disabled(),
            TextInput::make('last_name')->label('გვარი')->disabled(),
            TextInput::make('company')->label('კომპანია')->disabled(),
            TextInput::make('phone')->label('ტელეფონი')->disabled(),
            TextInput::make('email')->label('ელფოსტა')->disabled(),
            TextInput::make('service')->label('სერვისი')->disabled(),
            TextInput::make('service_slug')->label('სერვისის კოდი')->disabled(),
            TextInput::make('project_size')->label('პროექტის ზომა')->disabled(),
            TextInput::make('property_type')->label('ობიექტის ტიპი')->disabled(),
            TextInput::make('source')->label('წყარო')->disabled(),
            Textarea::make('details')
                ->label('დინამიური ველები')
                ->formatStateUsing(function ($state): string {
                    $lines = collect($state ?? [])
                        ->filter(fn ($detail): bool => is_array($detail))
                        ->map(function (array $detail): string {
                            $label = trim((string) ($detail['label'] ?? $detail['key'] ?? 'ველი'));
                            $value = trim((string) ($detail['value'] ?? ''));

                            return $value === '' ? '' : $label.': '.$value;
                        })
                        ->filter()
                        ->values()
                        ->all();

                    return $lines ? implode(PHP_EOL, $lines) : '—';
                })
                ->disabled()
                ->dehydrated(false)
                ->rows(6),
            Textarea::make('message')->label('შეტყობინება')->disabled()->rows(8),
            Select::make('status')
                ->label('სტატუსი')
                ->options(self::statusOptions())
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('თარიღი')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('სახელი')
                    ->formatStateUsing(
                        fn ($record) => $record->name ?: trim($record->first_name.' '.$record->last_name),
                    )
                    ->searchable(['name', 'first_name', 'last_name']),
                TextColumn::make('phone')->label('ტელეფონი')->searchable(),
                TextColumn::make('email')->label('ელფოსტა')->searchable(),
                TextColumn::make('service')->label('სერვისი'),
                TextColumn::make('status')
                    ->label('სტატუსი')
                    ->formatStateUsing(fn (string $state): string => self::statusOptions()[$state] ?? $state)
                    ->badge(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options(self::statusOptions()),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markContacted')
                        ->label('დაკავშირებულად მონიშვნა')
                        ->action(
                            fn (Collection $records) => $records->each->update([
                                'status' => 'contacted',
                            ]),
                        ),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactLeads::route('/'),
            'edit' => Pages\EditContactLead::route('/{record}/edit'),
        ];
    }
}
