<?php

namespace App\Filament\Resources\HomeWhyUs\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;

class HomeWhyUsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('why_us_title')->label('სათაური'),
                 TextInput::make('why_us_description')
                 ->label('აღწერა'),
                Repeater::make('why_us_items')
                    ->schema([
                        TextInput::make('title')->label('სათაური')->required(),
                        TextInput::make('description')->label('აღწერა')->required(),




                        Select::make('icon')
                            ->label('აირჩიე იკონი')
                            ->options([

                                // ⚡ Performance / Speed
                                'zap' => '⚡ სწრაფი მომსახურება',
                                'rocket' => '🚀 ინოვაცია / სწრაფი გაშვება',
                                'clock' => '⏱ დროის დაზოგვა',
                                'speed' => '🏎 მაღალი სიჩქარე',

                                // 🛠 Expertise / Work
                                'tools' => '🛠 გამოცდილება',
                                'settings' => '⚙ ტექნიკური მართვა',
                                'code' => '💻 პროგრამირება',
                                'build' => '🏗 სისტემის აწყობა',

                                // 🛡 Trust / Security
                                'shield' => '🛡 უსაფრთხოება',
                                'lock' => '🔒 მონაცემთა დაცვა',
                                'verified' => '✔ სანდოობა',
                                'compliance' => '📜 სტანდარტების დაცვა',

                                // ⭐ Quality
                                'star' => '⭐ მაღალი ხარისხი',
                                'trophy' => '🏆 საუკეთესო შედეგი',
                                'medal' => '🥇 პროფესიონალიზმი',
                                'like' => '👍 კმაყოფილი მომხმარებლები',

                                // 🎧 Support
                                'support' => '🎧 მხარდაჭერა',
                                'chat' => '💬 კომუნიკაცია',
                                'team' => '👥 გუნდი',
                                'help' => '🙋 დახმარება',

                                // 📍 Location / Presence
                                'map' => '📍 ლოკაცია',
                                'global' => '🌍 გლობალური მომსახურება',
                                'office' => '🏢 ოფისი',

                                // 📊 Business / Growth
                                'chart' => '📈 ზრდა',
                                'analytics' => '📊 ანალიტიკა',
                                'target' => '🎯 მიზნის მიღწევა',
                                'trend' => '📉 ოპტიმიზაცია',

                                // 🔄 Process / Workflow
                                'process' => '🔄 პროცესის ავტომატიზაცია',
                                'workflow' => '🧩 workflow',
                                'integration' => '🔗 ინტეგრაცია',

                                // 💡 Innovation
                                'idea' => '💡 ინოვაციური იდეები',
                                'ai' => '🤖 AI გადაწყვეტილებები',
                                'cloud' => '☁ cloud ტექნოლოგია',
                                'data' => '🗄 მონაცემთა მართვა',

                                // 🔧 Reliability
                                'fix' => '🔧 პრობლემის გადაჭრა',
                                'guarantee' => '🛡 გარანტია',
                                'stable' => '📦 სტაბილურობა',

                            ])
                            ->searchable()
                            ->required(),
                    ]),

            ]);
    }
}
