<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AiConversationResource\Pages;
use App\Filament\Support\NavigationGroup;
use App\Models\AiConversation;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AiConversationResource extends Resource
{
    protected static ?string $model = AiConversation::class;

    protected static ?string $navigationLabel = 'AI საუბრები';

    protected static ?string $modelLabel = 'AI საუბარი';

    protected static ?string $pluralModelLabel = 'AI საუბრები';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Sales;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('public_id')->label('Conversation ID')->disabled(),
            Select::make('locale')
                ->label('ენა')
                ->options(['ka' => 'ქართული', 'en' => 'English', 'ru' => 'Русский'])
                ->disabled(),
            TextInput::make('lead_score')->label('Lead score')->numeric()->disabled(),
            Select::make('status')
                ->label('სტატუსი')
                ->options([
                    'active' => 'აქტიური',
                    'converted' => 'ლიდად გარდაქმნილი',
                    'closed' => 'დახურული',
                ]),
            TextInput::make('contactLead.phone')->label('ლიდის ტელეფონი')->disabled(),
            Textarea::make('transcript')
                ->label('საუბრის სრული ისტორია')
                ->helperText('👍/👎 ნიშნები აჩვენებს მომხმარებლის შეფასებას AI პასუხზე.')
                ->rows(18)
                ->disabled()
                ->dehydrated(false)
                ->columnSpanFull(),
            Textarea::make('metadata')
                ->label('Metadata')
                ->formatStateUsing(fn ($state): string => json_encode($state ?? [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT))
                ->disabled()
                ->dehydrated(false)
                ->rows(5),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('last_message_at')->label('ბოლო შეტყობინება')->dateTime()->sortable(),
                TextColumn::make('locale')->label('ენა')->badge(),
                TextColumn::make('lead_score')->label('Lead score')->numeric()->sortable(),
                TextColumn::make('status')->label('სტატუსი')->badge()->sortable(),
                TextColumn::make('messages_count')->counts('messages')->label('შეტყობინებები')->numeric(),
                TextColumn::make('contactLead.phone')->label('ლიდი')->searchable(),
                TextColumn::make('public_id')->label('ID')->copyable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('last_message_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options([
                    'active' => 'აქტიური',
                    'converted' => 'ლიდად გარდაქმნილი',
                    'closed' => 'დახურული',
                ]),
            ])
            ->recordActions([
                EditAction::make()->label('ნახვა'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAiConversations::route('/'),
            'edit' => Pages\EditAiConversation::route('/{record}/edit'),
        ];
    }
}
