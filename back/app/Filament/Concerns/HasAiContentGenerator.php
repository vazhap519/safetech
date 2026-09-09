<?php

namespace App\Filament\Concerns;

use App\Application\Ai\CmsContentGenerator;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Throwable;

trait HasAiContentGenerator
{
    protected function aiContentAction(): Action
    {
        return Action::make('generateAiContent')
            ->label('AI — სრული შევსება KA / EN / RU')
            ->icon('heroicon-o-sparkles')
            ->color('primary')
            ->modalHeading('AI კონტენტის გენერირება')
            ->modalDescription('ჩაწერე მხოლოდ რეალური ფაქტები. AI შეავსებს მარკეტინგულ, SEO და თარგმანის ველებს სამ ენაზე. ფასებს, ტექნიკურ მონაცემებს, კონტაქტებსა და სხვა ფაქტებს არ გამოიგონებს.')
            ->schema([
                Textarea::make('facts')
                    ->label('საწყისი ინფორმაცია / ფაქტები')
                    ->placeholder('მაგ.: ბაკურიანი, 2 კოტეჯი. თითოეულზე 3 TVT 4MP Full Color კამერა. 24/7 ჩანაწერი, მობილურიდან დისტანციური კონტროლი...')
                    ->helperText('რაც უფრო ზუსტ ფაქტებს მიუთითებ, მით უკეთესი იქნება შედეგი. არ არის საჭირო გამართული ტექსტი — საკმარისია მოკლე ჩამონათვალი.')
                    ->rows(8)
                    ->required(),
                Select::make('mode')
                    ->label('შევსების რეჟიმი')
                    ->options([
                        'empty' => 'შეავსე მხოლოდ ცარიელი ველები',
                        'rewrite' => 'გააუმჯობესე არსებული ტექსტებიც',
                    ])
                    ->default('empty')
                    ->required(),
            ])
            ->action(function (array $data): void {
                try {
                    $current = is_array($this->data ?? null) ? $this->data : [];
                    $generator = app(CmsContentGenerator::class);
                    $updates = $generator->generate(
                        $this->aiContentProfile(),
                        (string) ($data['facts'] ?? ''),
                        $current,
                        ($data['mode'] ?? 'empty') === 'rewrite',
                    );

                    if ($updates === []) {
                        Notification::make()
                            ->title('შესავსები უსაფრთხო ველები ვერ მოიძებნა')
                            ->body('შეამოწმე საწყისი ფაქტები ან აირჩიე „გააუმჯობესე არსებული ტექსტებიც“ რეჟიმი.')
                            ->warning()
                            ->send();
                        return;
                    }

                    $this->data = $generator->mergeIntoState($current, $updates);

                    Notification::make()
                        ->title('AI კონტენტი მომზადდა')
                        ->body('KA / EN / RU ველები განახლდა ფორმაში. გადაამოწმე შედეგი და შემდეგ დააჭირე Save-ს — ავტომატურად ბაზაში არ ინახება.')
                        ->success()
                        ->send();
                } catch (Throwable $e) {
                    report($e);

                    Notification::make()
                        ->title('AI გენერაცია ვერ შესრულდა')
                        ->body(Str::limit($e->getMessage(), 220))
                        ->danger()
                        ->send();
                }
            });
    }

    protected function aiContentProfile(): string
    {
        $resource = strtolower((string) static::$resource);

        return match (true) {
            str_contains($resource, 'projectresource') => 'project',
            str_contains($resource, 'serviceresource') => 'service',
            str_contains($resource, 'aboutpageresource') => 'about',
            str_contains($resource, 'faqresource') => 'faq',
            str_contains($resource, 'seopageresource') => 'seo',
            str_contains($resource, 'pagesetting'), str_contains($resource, 'sitesettingresource') => 'settings',
            str_contains($resource, 'pageresource') => 'page',
            default => 'generic',
        };
    }
}
