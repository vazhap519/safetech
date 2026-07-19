<?php

namespace Database\Seeders;

use App\Models\SeoPage;
use Illuminate\Database\Seeder;

class SeoPageSeeder extends Seeder
{
    public function run(): void
    {
        SeoPage::query()
            ->whereIn('key', ['settings', 'contact-page'])
            ->delete();

        foreach ($this->pages() as $page) {
            $record = SeoPage::query()->firstOrCreate(
                ['key' => $page['key']],
                $page,
            );

            if (! is_array($record->translations) || $record->translations === []) {
                $record->forceFill($page)->save();
            }
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function pages(): array
    {
        return [
            $this->page(
                key: 'home',
                slug: '/',
                titles: [
                    'IT ინფრასტრუქტურა და უსაფრთხოების სისტემები ბიზნესისთვის',
                    'IT Infrastructure and Security Systems for Business',
                    'IT-инфраструктура и системы безопасности для бизнеса',
                ],
                descriptions: [
                    'SafeTech ქმნის ვიდეოსამეთვალყურეობის, დაშვების კონტროლის, ქსელური და სერვერული ინფრასტრუქტურის პროფესიონალურ გადაწყვეტილებებს საქართველოში.',
                    'SafeTech delivers professional CCTV, access control, networking, and server infrastructure solutions for businesses in Georgia.',
                    'SafeTech внедряет видеонаблюдение, контроль доступа, сетевую и серверную инфраструктуру для бизнеса в Грузии.',
                ],
                keywords: [
                    ['IT ინფრასტრუქტურა', 'უსაფრთხოების სისტემები', 'CCTV', 'ქსელები'],
                    ['IT infrastructure', 'security systems', 'CCTV', 'networking'],
                    ['IT-инфраструктура', 'системы безопасности', 'CCTV', 'сети'],
                ],
                schemaType: 'WebSite',
            ),
            $this->page(
                key: 'services',
                slug: '/services',
                titles: ['IT და უსაფრთხოების სერვისები', 'IT and Security Services', 'IT-услуги и системы безопасности'],
                descriptions: [
                    'ვიდეოსამეთვალყურეობა, დაშვების კონტროლი, ქსელები, სერვერები და მართვადი IT მხარდაჭერა ბიზნესისთვის საქართველოში.',
                    'CCTV, access control, networking, server infrastructure, and managed IT support for businesses in Georgia.',
                    'Видеонаблюдение, контроль доступа, сети, серверная инфраструктура и IT-поддержка для бизнеса в Грузии.',
                ],
                keywords: [
                    ['CCTV მონტაჟი', 'დაშვების კონტროლი', 'ქსელის მოწყობა', 'IT მხარდაჭერა'],
                    ['CCTV installation', 'access control', 'network setup', 'IT support'],
                    ['монтаж CCTV', 'контроль доступа', 'монтаж сети', 'IT-поддержка'],
                ],
            ),
            $this->page(
                key: 'service-calculator',
                slug: '/service-calculator',
                titles: ['IT და უსაფრთხოების სერვისების კალკულატორი', 'IT and Security Service Calculator', 'Калькулятор IT-услуг и систем безопасности'],
                descriptions: [
                    'გამოთვალეთ CCTV-ის, ქსელის, დაშვების კონტროლის, სერვერული ინფრასტრუქტურისა და IT მხარდაჭერის საორიენტაციო ბიუჯეტი.',
                    'Estimate the budget for CCTV, networking, access control, server infrastructure, and managed IT support.',
                    'Рассчитайте бюджет видеонаблюдения, сети, контроля доступа, серверной инфраструктуры и IT-поддержки.',
                ],
                keywords: [
                    ['სერვისის კალკულატორი', 'CCTV ფასი', 'ქსელის ფასი', 'IT მხარდაჭერის ფასი'],
                    ['service calculator', 'CCTV price', 'network estimate', 'IT support price'],
                    ['калькулятор услуг', 'цена CCTV', 'расчет сети', 'стоимость IT-поддержки'],
                ],
            ),
            $this->page(
                key: 'projects',
                slug: '/projects',
                titles: ['განხორციელებული IT და უსაფრთხოების პროექტები', 'Completed IT and Security Projects', 'Реализованные IT-проекты и системы безопасности'],
                descriptions: [
                    'ნახეთ SafeTech-ის მიერ განხორციელებული ვიდეოსამეთვალყურეობის, ქსელური და სერვერული ინფრასტრუქტურის პროექტები.',
                    'Explore SafeTech CCTV, networking, and server infrastructure projects delivered for businesses.',
                    'Проекты SafeTech по видеонаблюдению, сетевой и серверной инфраструктуре для бизнеса.',
                ],
                keywords: [
                    ['IT პროექტები', 'CCTV პროექტები', 'ქსელური ინფრასტრუქტურა'],
                    ['IT projects', 'CCTV projects', 'network infrastructure'],
                    ['IT-проекты', 'проекты CCTV', 'сетевая инфраструктура'],
                ],
            ),
            $this->page(
                key: 'about',
                slug: '/about',
                titles: ['SafeTech-ის გუნდი და გამოცდილება', 'SafeTech Team and Experience', 'Команда и опыт SafeTech'],
                descriptions: [
                    'გაიცანით SafeTech-ის გუნდი, გამოცდილება და მიდგომა უსაფრთხოებისა და IT ინფრასტრუქტურის პროექტებისადმი.',
                    'Meet the SafeTech team and learn how we deliver security and IT infrastructure projects.',
                    'Познакомьтесь с командой SafeTech и нашим подходом к проектам безопасности и IT-инфраструктуры.',
                ],
                keywords: [
                    ['SafeTech გუნდი', 'IT კომპანია საქართველო', 'სისტემური ინტეგრატორი'],
                    ['SafeTech team', 'IT company Georgia', 'systems integrator'],
                    ['команда SafeTech', 'IT-компания Грузия', 'системный интегратор'],
                ],
            ),
            $this->page(
                key: 'contact',
                slug: '/contact',
                titles: ['კონტაქტი და ტექნიკური კონსულტაცია', 'Contact and Technical Consultation', 'Контакты и техническая консультация'],
                descriptions: [
                    'დაუკავშირდით SafeTech-ს IT ინფრასტრუქტურისა და უსაფრთხოების სისტემების კონსულტაციისა და მორგებული შეთავაზებისთვის.',
                    'Contact SafeTech for an IT infrastructure or security systems consultation and a tailored proposal.',
                    'Свяжитесь с SafeTech для консультации и предложения по IT-инфраструктуре и системам безопасности.',
                ],
                keywords: [
                    ['SafeTech კონტაქტი', 'IT კონსულტაცია', 'უსაფრთხოების სისტემების შეთავაზება'],
                    ['SafeTech contact', 'IT consultation', 'security systems proposal'],
                    ['контакты SafeTech', 'IT-консультация', 'расчет систем безопасности'],
                ],
                schemaType: 'LocalBusiness',
            ),
            $this->page(
                key: 'blog',
                slug: '/blog',
                titles: ['IT და უსაფრთხოების ბლოგი', 'IT and Security Blog', 'Блог об IT и безопасности'],
                descriptions: [
                    'პრაქტიკული სტატიები ვიდეოსამეთვალყურეობის, ქსელების, სერვერების, კიბერუსაფრთხოებისა და ბიზნეს IT-ის შესახებ.',
                    'Practical articles about CCTV, networking, servers, cybersecurity, and business IT.',
                    'Практические статьи о видеонаблюдении, сетях, серверах, кибербезопасности и IT для бизнеса.',
                ],
                keywords: [
                    ['IT ბლოგი', 'უსაფრთხოების რჩევები', 'ქსელური ინფრასტრუქტურა'],
                    ['IT blog', 'security advice', 'network infrastructure'],
                    ['IT-блог', 'советы по безопасности', 'сетевая инфраструктура'],
                ],
            ),
            $this->page(
                key: 'privacy',
                slug: '/privacy',
                titles: ['კონფიდენციალურობის პოლიტიკა', 'Privacy Policy', 'Политика конфиденциальности'],
                descriptions: [
                    'გაეცანით, როგორ აგროვებს, იყენებს და იცავს SafeTech ვებსაიტის მომხმარებელთა და საკონტაქტო ფორმების მონაცემებს.',
                    'Learn how SafeTech collects, uses, and protects website visitor and contact-form data.',
                    'Узнайте, как SafeTech собирает, использует и защищает данные посетителей сайта и контактных форм.',
                ],
                keywords: [
                    ['კონფიდენციალურობის პოლიტიკა', 'პერსონალური მონაცემები'],
                    ['privacy policy', 'personal data'],
                    ['политика конфиденциальности', 'персональные данные'],
                ],
            ),
        ];
    }

    /** @param array<int, string> $titles
     *  @param array<int, string> $descriptions
     *  @param array<int, array<int, string>> $keywords
     *  @return array<string, mixed>
     */
    private function page(
        string $key,
        string $slug,
        array $titles,
        array $descriptions,
        array $keywords,
        string $schemaType = 'WebPage',
    ): array {
        return [
            'key' => $key,
            'slug' => $slug,
            'title' => $titles[0],
            'description' => $descriptions[0],
            'keywords' => array_map(fn (string $value): array => ['value' => $value], $keywords[0]),
            'og_title' => $titles[0],
            'og_description' => $descriptions[0],
            'schema_type' => $schemaType,
            'noindex' => false,
            'translations' => [
                'fields' => [
                    'title' => ['ka' => $titles[0], 'en' => $titles[1], 'ru' => $titles[2]],
                    'description' => ['ka' => $descriptions[0], 'en' => $descriptions[1], 'ru' => $descriptions[2]],
                    'og_title' => ['ka' => $titles[0], 'en' => $titles[1], 'ru' => $titles[2]],
                    'og_description' => ['ka' => $descriptions[0], 'en' => $descriptions[1], 'ru' => $descriptions[2]],
                ],
                'keywords' => [
                    'ka' => $keywords[0],
                    'en' => $keywords[1],
                    'ru' => $keywords[2],
                ],
            ],
        ];
    }
}
