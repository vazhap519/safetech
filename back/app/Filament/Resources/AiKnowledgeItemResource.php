<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AiKnowledgeItemResource\Pages;
use App\Filament\Support\NavigationGroup;
use App\Models\AiKnowledgeItem;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AiKnowledgeItemResource extends Resource
{
    protected static ?string $model = AiKnowledgeItem::class;

    protected static ?string $navigationLabel = 'AI ცოდნა';

    protected static ?string $modelLabel = 'AI ცოდნა';

    protected static ?string $pluralModelLabel = 'AI ცოდნა';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Sales;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->label('სათაური')->required()->maxLength(255),
            Select::make('locale')
                ->label('ენა')
                ->options(['ka' => 'ქართული', 'en' => 'English', 'ru' => 'Русский'])
                ->default('ka')
                ->required(),
            TextInput::make('category')->label('კატეგორია')->default('general')->required()->maxLength(80),
            Select::make('status')
                ->label('სტატუსი')
                ->options(['approved' => 'დამტკიცებული', 'draft' => 'Draft', 'disabled' => 'გამორთული'])
                ->default('approved')
                ->required(),
            Select::make('source_type')
                ->label('წყარო')
                ->options(['manual' => 'ხელით დამატებული', 'learned' => 'საუბრიდან დამტკიცებული'])
                ->default('manual')
                ->required(),
            TextInput::make('source_reference')->label('წყაროს მითითება')->maxLength(255),
            Textarea::make('content')->label('დამტკიცებული პასუხი / ცოდნა')->required()->rows(10)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('სათაური')->searchable()->limit(60),
                TextColumn::make('category')->label('კატეგორია')->badge(),
                TextColumn::make('locale')->label('ენა')->badge(),
                TextColumn::make('status')->label('სტატუსი')->badge(),
                TextColumn::make('usage_count')->label('გამოყენება')->numeric()->sortable(),
                TextColumn::make('updated_at')->label('განახლდა')->dateTime()->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options([
                    'approved' => 'დამტკიცებული',
                    'draft' => 'Draft',
                    'disabled' => 'გამორთული',
                ]),
                SelectFilter::make('locale')->options(['ka' => 'ქართული', 'en' => 'English', 'ru' => 'Русский']),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->label('წაშლა')
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('არჩეული ცოდნის ჩანაწერების წაშლა')
                        ->requiresConfirmation(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAiKnowledgeItems::route('/'),
            'create' => Pages\CreateAiKnowledgeItem::route('/create'),
            'edit' => Pages\EditAiKnowledgeItem::route('/{record}/edit'),
        ];
    }
}
