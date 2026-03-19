<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
 Forms\Components\TextInput::make('site_name')->label('საიტის სახელი'),
                /*
                |--------------------------------------------------------------------------
                | HomePage Titles
                |--------------------------------------------------------------------------
                */
                Section::make('Homepage Titles')
                    ->schema([
                        Forms\Components\TextInput::make('our_services')->required(),
                        Forms\Components\TextInput::make('recent_projets'),
                        Forms\Components\TextInput::make('client_reviews'),
                        Forms\Components\TextInput::make('latest_articles'),
                        Forms\Components\TextInput::make('get_consultation'),
                    ])->columns(2),

                /*
                |--------------------------------------------------------------------------
                | Top Bar
                |--------------------------------------------------------------------------
                */
                Section::make('Top Bar')
                    ->schema([
                        Forms\Components\TextInput::make('top_bar_consultation_text'),
                        Forms\Components\TextInput::make('top_bar_number'),
                    ])->columns(2),

                /*
                |--------------------------------------------------------------------------
                | Navigation
                |--------------------------------------------------------------------------
                */
                Section::make('Navigation')
                    ->schema([
                        Forms\Components\TextInput::make('navigation_services'),
                        Forms\Components\TextInput::make('navigation_projects'),
                        Forms\Components\TextInput::make('navigation_about'),
                        Forms\Components\TextInput::make('navigation_blog'),
                        Forms\Components\TextInput::make('navigation_contact'),
                    ])->columns(2),

                /*
                |--------------------------------------------------------------------------
                | Footer
                |--------------------------------------------------------------------------
                */
                Section::make('Footer')
                    ->schema([
                        Forms\Components\TextInput::make('footer_services'),
                        Forms\Components\TextInput::make('footer_contact'),
                        Forms\Components\TextInput::make('footer_company'),
                        Forms\Components\Textarea::make('footer_description')->rows(3),
                    ])->columns(2),

                /*
                |--------------------------------------------------------------------------
                | Media (Spatie 🔥)
                |--------------------------------------------------------------------------
                */
          Section::make('Media')
    ->schema([
        SpatieMediaLibraryFileUpload::make('logo')
            ->collection('logo')
            ->image()
            ->label('Logo'),

        SpatieMediaLibraryFileUpload::make('favicon')
            ->collection('favicon')
            ->image()
            ->label('Favicon'),
    ])->columns(2),
                /*
                |--------------------------------------------------------------------------
                | Styles (JSON)
                |--------------------------------------------------------------------------
                */
                Section::make('Styles')
                    ->schema([
                        Forms\Components\KeyValue::make('styles')
                            ->keyLabel('Property')
                            ->valueLabel('Value')
                            ->addButtonLabel('Add Style')
                            ->columnSpanFull(),
                    ]),

            ]);
    }
}