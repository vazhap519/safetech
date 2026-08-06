<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewInvitationResource\Pages;
use App\Filament\Support\NavigationGroup;
use App\Models\ReviewInvitation;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReviewInvitationResource extends Resource
{
    protected static ?string $model = ReviewInvitation::class;

    protected static ?string $navigationLabel = 'Review invitations';

    protected static ?string $modelLabel = 'Review invitation';

    protected static ?string $pluralModelLabel = 'Review invitations';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Sales;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Invitation')
                ->schema([
                    Select::make('project_id')
                        ->label('Project')
                        ->relationship('project', 'name')
                        ->searchable()
                        ->preload(),
                    TextInput::make('recipient_name')
                        ->label('Recipient name')
                        ->maxLength(255),
                    DateTimePicker::make('expires_at')
                        ->label('Expires at')
                        ->helperText('Leave blank when the link should not expire automatically.'),
                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ])
                ->columns(2),
            Section::make('Public review link')
                ->schema([
                    TextInput::make('token')
                        ->label('Token')
                        ->default(fn (): string => ReviewInvitation::generateToken())
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->readOnly()
                        ->helperText('Generated automatically and kept stable so a sent link cannot change.'),
                ]),
            Section::make('Submission')
                ->schema([
                    TextInput::make('submitted_at')
                        ->label('Submitted at')
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('testimonial.author')
                        ->label('Submitted by')
                        ->disabled()
                        ->dehydrated(false),
                ])
                ->columns(2)
                ->visible(fn (?ReviewInvitation $record): bool => $record?->submitted_at !== null),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('recipient_name')
                    ->label('Recipient')
                    ->searchable(),
                TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable(),
                TextColumn::make('public_url')
                    ->label('Public review link')
                    ->getStateUsing(fn (ReviewInvitation $record): string => $record->public_url)
                    ->url(fn (ReviewInvitation $record): string => $record->public_url)
                    ->openUrlInNewTab()
                    ->copyable()
                    ->copyMessage('Public review link copied')
                    ->wrap(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime()
                    ->placeholder('Never')
                    ->sortable(),
                TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->placeholder('Not yet')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviewInvitations::route('/'),
            'create' => Pages\CreateReviewInvitation::route('/create'),
            'edit' => Pages\EditReviewInvitation::route('/{record}/edit'),
        ];
    }
}
