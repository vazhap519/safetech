<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewInvitationResource\Pages;
use App\Filament\Support\NavigationGroup;
use App\Models\ReviewInvitation;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
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

    protected static ?string $navigationLabel = 'შეფასების მოწვევები';

    protected static ?string $modelLabel = 'შეფასების მოწვევა';

    protected static ?string $pluralModelLabel = 'შეფასების მოწვევები';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Sales;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('მოწვევის ინფორმაცია')
                ->description('შექმენით ერთჯერადი პერსონალური ბმული, რომელსაც მომხმარებელს გაუგზავნით შეფასების დასატოვებლად.')
                ->schema([
                    Select::make('project_id')
                        ->label('პროექტი')
                        ->relationship('project', 'name')
                        ->searchable()
                        ->preload()
                        ->helperText('სურვილისამებრ — აირჩიეთ პროექტი, თუ შეფასება კონკრეტულ შესრულებულ სამუშაოს ეხება.'),
                    TextInput::make('recipient_name')
                        ->label('მომხმარებლის სახელი')
                        ->required()
                        ->maxLength(255),
                    DateTimePicker::make('expires_at')
                        ->label('ბმულის ვადა')
                        ->helperText('თუ ცარიელს დატოვებთ, ბმულს ავტომატური ვადა არ ექნება.'),
                    Toggle::make('is_active')
                        ->label('აქტიური')
                        ->default(true),
                ])
                ->columns(2),
            Section::make('საჯარო შეფასების ბმული')
                ->schema([
                    TextInput::make('token')
                        ->label('უსაფრთხო კოდი')
                        ->default(fn (): string => ReviewInvitation::generateToken())
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->readOnly()
                        ->helperText('კოდი გენერირდება ავტომატურად. შენახვის შემდეგ სრული ბმული გამოჩნდება სიის გვერდზე და შეძლებთ მის დაკოპირებას.'),
                ]),
            Section::make('მიღებული შეფასება')
                ->schema([
                    TextInput::make('submitted_at')
                        ->label('გამოგზავნის დრო')
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('testimonial.author')
                        ->label('ავტორი')
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
                    ->label('მომხმარებელი')
                    ->searchable(),
                TextColumn::make('project.name')
                    ->label('პროექტი')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('public_url')
                    ->label('საჯარო შეფასების ბმული')
                    ->getStateUsing(fn (ReviewInvitation $record): string => $record->public_url)
                    ->url(fn (ReviewInvitation $record): string => $record->public_url)
                    ->openUrlInNewTab()
                    ->copyable()
                    ->copyMessage('ბმული დაკოპირდა')
                    ->wrap(),
                IconColumn::make('is_active')
                    ->label('აქტიური')
                    ->boolean(),
                TextColumn::make('expires_at')
                    ->label('ვადა')
                    ->dateTime()
                    ->placeholder('უვადო')
                    ->sortable(),
                TextColumn::make('submitted_at')
                    ->label('შეფასება მიღებულია')
                    ->dateTime()
                    ->placeholder('ჯერ არა')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('openPublicLink')
                    ->label('ბმულის გახსნა')
                    ->url(fn (ReviewInvitation $record): string => $record->public_url)
                    ->openUrlInNewTab(),
                EditAction::make()->label('რედაქტირება'),
                DeleteAction::make()->label('წაშლა'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('არჩეული მოწვევების წაშლა'),
                ]),
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
