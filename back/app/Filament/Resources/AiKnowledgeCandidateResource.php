<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AiKnowledgeCandidateResource\Pages;
use App\Filament\Support\NavigationGroup;
use App\Models\AiKnowledgeCandidate;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AiKnowledgeCandidateResource extends Resource
{
    protected static ?string $model = AiKnowledgeCandidate::class;

    protected static ?string $navigationLabel = 'AI სასწავლო რიგი';

    protected static ?string $modelLabel = 'AI ცოდნის კანდიდატი';

    protected static ?string $pluralModelLabel = 'AI სასწავლო რიგი';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Sales;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Textarea::make('question')->label('მომხმარებლის კითხვა')->disabled()->dehydrated(false)->rows(5),
            Textarea::make('suggested_answer')->label('დამტკიცებული პასუხი')->required()->rows(10),
            Select::make('locale')
                ->label('ენა')
                ->options(['ka' => 'ქართული', 'en' => 'English', 'ru' => 'Русский'])
                ->required(),
            TextInput::make('occurrences')->label('გამეორებები')->numeric()->disabled()->dehydrated(false),
            Select::make('status')
                ->label('გადაწყვეტილება')
                ->options([
                    'pending' => 'განხილვის მოლოდინში',
                    'approved' => 'დამტკიცება',
                    'rejected' => 'უარყოფა',
                ])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('question')->label('კითხვა')->searchable()->limit(80),
                TextColumn::make('locale')->label('ენა')->badge(),
                TextColumn::make('occurrences')->label('გამეორებები')->numeric()->sortable(),
                TextColumn::make('status')->label('სტატუსი')->badge()->sortable(),
                TextColumn::make('created_at')->label('შექმნილია')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options([
                    'pending' => 'განხილვის მოლოდინში',
                    'approved' => 'დამტკიცებული',
                    'rejected' => 'უარყოფილი',
                ]),
            ])
            ->recordActions([
                EditAction::make()->label('განხილვა'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAiKnowledgeCandidates::route('/'),
            'edit' => Pages\EditAiKnowledgeCandidate::route('/{record}/edit'),
        ];
    }
}
