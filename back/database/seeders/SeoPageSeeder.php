<?php

namespace Database\Seeders;

use App\Models\SeoPage;
use Illuminate\Database\Seeder;

class SeoPageSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->pages() as $page) {
            $this->seedPage($page);
        }
    }

    private function seedPage(array $page): void
    {
        $record = SeoPage::query()->firstOrNew(['key' => $page['key']]);

        if (! $record->exists) {
            $record->fill($page)->save();

            return;
        }

        $legacy = $this->legacyPages()[$page['key']] ?? [];

        foreach (['slug', 'schema_type'] as $field) {
            if (blank($record->getAttribute($field))) {
                $record->setAttribute($field, $page[$field]);
            }
        }

        foreach (['title', 'description', 'og_title', 'og_description'] as $field) {
            $current = trim((string) $record->getAttribute($field));
            $legacyValue = trim((string) ($legacy[$field] ?? ''));

            if ($current === '' || ($legacyValue !== '' && $current === $legacyValue)) {
                $record->setAttribute($field, $page[$field]);
            }
        }

        if (! is_array($record->keywords) || $record->keywords === []) {
            $record->keywords = $page['keywords'];
        }

        if ($record->canonical === null || trim((string) $record->canonical) === '') {
            $record->canonical = $page['canonical'];
        }

        $record->noindex = false;
        $record->translations = $this->mergeTranslations(
            is_array($record->translations) ? $record->translations : [],
            $page['translations'],
            is_array($legacy['translations'] ?? null) ? $legacy['translations'] : [],
        );
        $record->save();
    }

    private function mergeTranslations(array $current, array $defaults, array $legacy): array
    {
        $current['fields'] ??= [];
        $current['keywords'] ??= [];

        foreach ($defaults['fields'] as $field => $locales) {
            $current['fields'][$field] ??= [];

            foreach ($locales as $locale => $value) {
                $configured = trim((string) ($current['fields'][$field][$locale] ?? ''));
                $legacyValue = trim((string) data_get($legacy, "fields.{$field}.{$locale}", ''));

                if ($configured === '' || ($legacyValue !== '' && $configured === $legacyValue)) {
                    $current['fields'][$field][$locale] = $value;
                }
            }
        }

        foreach ($defaults['keywords'] as $locale => $keywords) {
            if (! is_array($current['keywords'][$locale] ?? null) || $current['keywords'][$locale] === []) {
                $current['keywords'][$locale] = $keywords;
            }
        }

        return $current;
    }

    /** @return array<int, array<string, mixed>> */
    private function pages(): array
    {
        return [
            $this->page(
                'home',
                '/',
                self::localized(
                    'IT სერვისები და უსაფრთხოების სისტემები საქართველოში | SafeTech',
                    'IT Services and Security Systems in Georgia | SafeTech',
                    'IT-услуги и системы безопасности в Грузии | SafeTech',
                ),
                self::localized(
                    'SafeTech გეგმავს და ამონტაჟებს კამერებს, დაშვების სისტემებს, ქსელებს, POS სისტემებსა და კომპიუტერულ ინფრასტრუქტურას თბილისში და საქართველოს მასშტაბით.',
                    'SafeTech designs and installs CCTV, access control, networks, POS systems, and computer infrastructure in Tbilisi and across Georgia.',
                    'SafeTech проектирует и устанавливает видеонаблюдение, контроль доступа, сети, POS и компьютерную инфраструктуру в Тбилиси и по Грузии.',
                ),
                [
                    'ka' => ['IT სერვისები', 'კამერების მონტაჟი', 'ქსელის მონტაჟი', 'დაშვების სისტემები', 'SafeTech Georgia'],
                    'en' => ['IT services Georgia', 'CCTV installation', 'network installation', 'access control', 'SafeTech Georgia'],
                    'ru' => ['IT услуги Грузия', 'монтаж видеонаблюдения', 'монтаж сети', 'контроль доступа', 'SafeTech Georgia'],
                ],
                'WebSite',
            ),
            $this->page(
                'about',
                '/about',
                self::localized(
                    'SafeTech Georgia — IT და უსაფრთხოების სისტემების სპეციალისტები',
                    'About SafeTech Georgia — IT and Security Specialists',
                    'О SafeTech Georgia — специалисты по IT и безопасности',
                ),
                self::localized(
                    'გაიცანით SafeTech-ის მიდგომა: ტექნიკური შეფასება, სწორი პროექტირება, პროფესიონალური მონტაჟი, კონფიგურაცია, ტესტირება და შემდგომი მხარდაჭერა.',
                    'Learn how SafeTech assesses, designs, installs, configures, tests, and supports IT and security infrastructure.',
                    'Узнайте, как SafeTech оценивает, проектирует, устанавливает, настраивает, тестирует и обслуживает IT-инфраструктуру и системы безопасности.',
                ),
                [
                    'ka' => ['SafeTech-ის შესახებ', 'IT სპეციალისტები', 'უსაფრთხოების ინჟინერი', 'ქსელის სპეციალისტი'],
                    'en' => ['about SafeTech', 'IT specialists Georgia', 'security systems engineer', 'network specialist'],
                    'ru' => ['о SafeTech', 'IT специалисты Грузия', 'инженер систем безопасности', 'сетевой специалист'],
                ],
                'AboutPage',
            ),
            $this->page(
                'services',
                '/services',
                self::localized(
                    'IT სერვისები, კამერები, ქსელები და დაშვების სისტემები | SafeTech',
                    'IT, CCTV, Network and Access Control Services | SafeTech',
                    'IT, видеонаблюдение, сети и контроль доступа | SafeTech',
                ),
                self::localized(
                    'იხილეთ SafeTech-ის სერვისები და გამოთვალეთ საორიენტაციო ღირებულება: კომპიუტერები, POS, IT მხარდაჭერა, CAT6 ქსელი, რეკი, კამერები, დომოფონი და შლაგბაუმი.',
                    'Explore SafeTech services and estimate pricing for computers, POS, IT support, CAT6 networks, racks, CCTV, intercoms, and barriers.',
                    'Услуги SafeTech и расчет стоимости: компьютеры, POS, IT-поддержка, CAT6, шкафы, камеры, домофоны и шлагбаумы.',
                ),
                [
                    'ka' => ['IT მომსახურება', 'სერვისების კალკულატორი', 'კამერების მონტაჟი', 'CAT6 კაბელი', 'დომოფონის მონტაჟი'],
                    'en' => ['IT services', 'service calculator', 'CCTV installation', 'CAT6 cabling', 'intercom installation'],
                    'ru' => ['IT услуги', 'калькулятор услуг', 'монтаж камер', 'кабель CAT6', 'монтаж домофона'],
                ],
                'CollectionPage',
            ),
            $this->page(
                'projects',
                '/projects',
                self::localized(
                    'განხორციელებული IT და უსაფრთხოების პროექტები | SafeTech',
                    'Completed IT and Security Projects | SafeTech',
                    'Реализованные IT-проекты и системы безопасности | SafeTech',
                ),
                self::localized(
                    'SafeTech-ის მიერ განხორციელებული კამერების, ქსელების, დაშვების სისტემებისა და IT ინფრასტრუქტურის პროექტები.',
                    'CCTV, network, access control, and IT infrastructure projects delivered by SafeTech.',
                    'Проекты видеонаблюдения, сетей, контроля доступа и IT-инфраструктуры, реализованные SafeTech.',
                ),
                [
                    'ka' => ['SafeTech პროექტები', 'კამერების პროექტი', 'ქსელის პროექტი'],
                    'en' => ['SafeTech projects', 'CCTV project', 'network project'],
                    'ru' => ['проекты SafeTech', 'проект видеонаблюдения', 'сетевой проект'],
                ],
                'CollectionPage',
            ),
            $this->page(
                'contact',
                '/contact',
                self::localized(
                    'SafeTech კონტაქტი — IT და უსაფრთხოების კონსულტაცია',
                    'Contact SafeTech — IT and Security Consultation',
                    'Контакты SafeTech — консультация по IT и безопасности',
                ),
                self::localized(
                    'დაუკავშირდით SafeTech-ს ნომრებზე 571 430 169 და 557 316 310. მიიღეთ კონსულტაცია, ობიექტის შეფასება და ტექნიკური შეთავაზება.',
                    'Call SafeTech at 571 430 169 or 557 316 310 for consultation, site assessment, and a technical proposal.',
                    'Позвоните SafeTech по номерам 571 430 169 или 557 316 310 для консультации, оценки объекта и технического предложения.',
                ),
                [
                    'ka' => ['SafeTech კონტაქტი', 'IT კონსულტაცია', 'კამერების მონტაჟის ფასი', 'ტექნიკური დახმარება'],
                    'en' => ['SafeTech contact', 'IT consultation Georgia', 'CCTV installation quote', 'technical support'],
                    'ru' => ['контакты SafeTech', 'IT консультация Грузия', 'стоимость монтажа камер', 'техническая поддержка'],
                ],
                'ContactPage',
            ),
        ];
    }

    /** @return array<string, array<string, mixed>> */
    private function legacyPages(): array
    {
        return [
            'home' => $this->legacy(
                self::localized('IT ინფრასტრუქტურა და უსაფრთხოების სისტემები ბიზნესისთვის', 'IT Infrastructure and Security Systems for Business', 'IT-инфраструктура и системы безопасности для бизнеса'),
                self::localized('SafeTech ქმნის პროფესიონალურ IT და უსაფრთხოების გადაწყვეტილებებს საქართველოში.', 'SafeTech delivers professional IT and security solutions in Georgia.', 'SafeTech внедряет профессиональные IT-решения и системы безопасности в Грузии.'),
            ),
            'about' => $this->legacy(
                self::localized('SafeTech-ის გუნდი და გამოცდილება', 'SafeTech Team and Experience', 'Команда и опыт SafeTech'),
                self::localized('გაიცანით SafeTech-ის გუნდი, გამოცდილება და მუშაობის მიდგომა.', 'Meet the SafeTech team, experience, and approach.', 'Познакомьтесь с командой, опытом и подходом SafeTech.'),
            ),
            'services' => $this->legacy(
                self::localized('IT და უსაფრთხოების სერვისები', 'IT and Security Services', 'IT-услуги и системы безопасности'),
                self::localized('ვიდეოსამეთვალყურეობა, დაშვების კონტროლი, ქსელები, სერვერები და IT მხარდაჭერა.', 'CCTV, access control, networking, servers, and IT support.', 'Видеонаблюдение, контроль доступа, сети, серверы и IT-поддержка.'),
            ),
            'projects' => $this->legacy(
                self::localized('განხორციელებული პროექტები', 'Completed Projects', 'Реализованные проекты'),
                self::localized('SafeTech-ის მიერ განხორციელებული IT და უსაფრთხოების პროექტები.', 'IT and security projects delivered by SafeTech.', 'IT-проекты и проекты безопасности, реализованные SafeTech.'),
            ),
            'contact' => $this->legacy(
                self::localized('კონტაქტი და კონსულტაცია', 'Contact and Consultation', 'Контакты и консультация'),
                self::localized('დაუკავშირდით SafeTech-ს ტექნიკური კონსულტაციისთვის.', 'Contact SafeTech for a technical consultation.', 'Свяжитесь с SafeTech для технической консультации.'),
            ),
        ];
    }

    private function legacy(array $title, array $description): array
    {
        return [
            'title' => $title['ka'],
            'description' => $description['ka'],
            'og_title' => $title['ka'],
            'og_description' => $description['ka'],
            'translations' => [
                'fields' => [
                    'title' => $title,
                    'description' => $description,
                    'og_title' => $title,
                    'og_description' => $description,
                ],
            ],
        ];
    }

    private function page(
        string $key,
        string $slug,
        array $title,
        array $description,
        array $keywords,
        string $schemaType,
    ): array {
        return [
            'key' => $key,
            'slug' => $slug,
            'title' => $title['ka'],
            'description' => $description['ka'],
            'keywords' => $keywords['ka'],
            'og_title' => $title['ka'],
            'og_description' => $description['ka'],
            'canonical' => null,
            'noindex' => false,
            'schema_type' => $schemaType,
            'schema' => null,
            'translations' => [
                'fields' => [
                    'title' => $title,
                    'description' => $description,
                    'og_title' => $title,
                    'og_description' => $description,
                ],
                'keywords' => $keywords,
            ],
        ];
    }

    /** @return array{ka:string,en:string,ru:string} */
    private static function localized(string $ka, string $en, string $ru): array
    {
        return compact('ka', 'en', 'ru');
    }
}
