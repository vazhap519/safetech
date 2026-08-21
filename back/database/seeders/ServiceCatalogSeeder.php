<?php

namespace Database\Seeders;

use App\Models\CategoryForService;
use App\Models\Faq;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Support\Calculators\DefaultCalculatorProfiles;
use App\Support\MultilingualContent;
use Illuminate\Database\Seeder;

class ServiceCatalogSeeder extends Seeder
{
    private const LOCALES = ['ka', 'en', 'ru'];

    public function run(): void
    {
        $categories = $this->seedCategories();
        $translationEntries = [];

        foreach ($this->services() as $index => $definition) {
            $service = $this->seedService(
                $definition,
                $categories[$definition['category']]->getKey(),
                $index + 1,
            );

            $this->seedFaqs($service, $definition['faqs']);
            $translationEntries = array_merge(
                $translationEntries,
                $this->serviceTranslationEntries($definition),
            );
        }

        $this->mergePublicTranslations($translationEntries);
    }

    /** @return array<string, CategoryForService> */
    private function seedCategories(): array
    {
        $records = [];

        foreach ($this->categories() as $definition) {
            $payload = [
                'name' => $definition['name']['ka'],
                'slug' => $definition['slug'],
                'seo_title' => $definition['seo_title']['ka'],
                'seo_description' => $definition['seo_description']['ka'],
                'seo_keywords' => array_column($definition['keywords'], 'ka'),
                'intro_text' => $definition['description']['ka'],
                'faq' => $this->localizedFaqList($definition['faqs'], 'ka'),
                'schema' => null,
                'noindex' => false,
                'translations' => [
                    'fields' => [
                        'name' => $definition['name'],
                        'seo_title' => $definition['seo_title'],
                        'seo_description' => $definition['seo_description'],
                        'intro_text' => $definition['description'],
                    ],
                    'keywords' => [
                        'ka' => array_column($definition['keywords'], 'ka'),
                        'en' => array_column($definition['keywords'], 'en'),
                        'ru' => array_column($definition['keywords'], 'ru'),
                    ],
                    'faq' => [
                        'ka' => $this->localizedFaqList($definition['faqs'], 'ka'),
                        'en' => $this->localizedFaqList($definition['faqs'], 'en'),
                        'ru' => $this->localizedFaqList($definition['faqs'], 'ru'),
                    ],
                ],
            ];

            $record = CategoryForService::query()->firstOrNew([
                'slug' => $definition['slug'],
            ]);
            $this->fillMissing($record, $payload);
            $record->save();
            $records[$definition['slug']] = $record;
        }

        return $records;
    }

    private function seedService(array $definition, int $categoryId, int $sortOrder): Service
    {
        $highlights = array_column($definition['highlights'], 'ka');
        $benefits = $this->localizedCards($definition['highlights'], 'ka');
        $solutions = $this->localizedCards($definition['scope'], 'ka', true);
        $process = $this->localizedProcess('ka');
        $industries = array_column($this->industrySet($definition['category']), 'ka');
        $description = $definition['description']['ka'];

        $payload = [
            'category_for_service_id' => $categoryId,
            'slug' => $definition['slug'],
            'name' => $definition['name']['ka'],
            'eyebrow' => $definition['eyebrow']['ka'],
            'icon' => $definition['icon'],
            'title' => $definition['title']['ka'],
            'description' => $description,
            'short_description' => $description,
            'long_description' => $description,
            'seo_description' => $definition['seo_description']['ka'],
            'keywords' => array_column($definition['keywords'], 'ka'),
            'highlights' => $highlights,
            'overview' => [
                'title' => $definition['overview_title']['ka'],
                'paragraphs' => [
                    $description,
                    $definition['overview_text']['ka'],
                ],
                'stats' => [],
            ],
            'benefits' => $benefits,
            'solutions' => $solutions,
            'industries' => $industries,
            'process' => $process,
            'brands' => [],
            'features' => $highlights,
            'faq' => $this->localizedFaqList($definition['faqs'], 'ka'),
            'seo' => [
                'title' => $definition['seo_title']['ka'],
                'description' => $definition['seo_description']['ka'],
                'noindex' => false,
            ],
            'lead_form' => DefaultCalculatorProfiles::for($definition['calculator_profile']),
            'button_text' => 'შეთავაზების მოთხოვნა',
            'cta_title' => 'მიიღეთ ზუსტი ტექნიკური შეთავაზება',
            'cta_description' => 'დაგვიკავშირდით კონსულტაციისა და ობიექტის მოთხოვნებზე მორგებული ფასის მისაღებად.',
            'warranty' => 'სამუშაოს გარანტიის პირობები განისაზღვრება შესრულებული მომსახურებისა და გამოყენებული კომპონენტების მიხედვით.',
            'sla' => 'რეაგირების დრო და მხარდაჭერის პირობები შეთანხმდება მომსახურების მოცულობის მიხედვით.',
            'is_published' => true,
            'sort_order' => $sortOrder,
            'translations' => [
                'fields' => [
                    'name' => $definition['name'],
                    'eyebrow' => $definition['eyebrow'],
                    'title' => $definition['title'],
                    'description' => $definition['description'],
                    'seoTitle' => $definition['seo_title'],
                    'seoDescription' => $definition['seo_description'],
                ],
            ],
        ];

        $record = Service::query()->firstOrNew(['slug' => $definition['slug']]);
        $this->fillMissing($record, $payload);
        $record->save();

        return $record;
    }

    /** @param array<int, array<string, mixed>> $faqs */
    private function seedFaqs(Service $service, array $faqs): void
    {
        foreach ($faqs as $index => $faq) {
            $context = "service:{$service->slug}:{$faq['key']}";
            $record = Faq::query()->firstOrNew([
                'service_id' => $service->getKey(),
                'context' => $context,
            ]);

            $this->fillMissing($record, [
                'question' => $faq['question']['ka'],
                'answer' => $faq['answer']['ka'],
                'is_active' => true,
                'sort_order' => $index + 1,
                'translations' => [
                    'fields' => [
                        'question' => $faq['question'],
                        'answer' => $faq['answer'],
                    ],
                ],
            ]);
            $record->save();
        }
    }

    private function fillMissing($model, array $defaults): void
    {
        if (! $model->exists) {
            $model->fill($defaults);

            return;
        }

        foreach ($defaults as $field => $default) {
            $current = $model->getAttribute($field);

            if (is_array($default)) {
                $currentArray = is_array($current) ? $current : [];
                $merged = $this->mergeMissingArrayValues($currentArray, $default);

                if ($merged !== $currentArray) {
                    $model->setAttribute($field, $merged);
                }

                continue;
            }

            if (blank($current) && filled($default)) {
                $model->setAttribute($field, $default);
            }
        }
    }

    private function mergeMissingArrayValues(array $current, array $defaults): array
    {
        if (array_is_list($defaults)) {
            return $current === [] ? $defaults : $current;
        }

        foreach ($defaults as $key => $default) {
            if (! array_key_exists($key, $current)) {
                $current[$key] = $default;

                continue;
            }

            if (is_array($default) && is_array($current[$key])) {
                $current[$key] = $this->mergeMissingArrayValues($current[$key], $default);

                continue;
            }

            if (blank($current[$key]) && filled($default)) {
                $current[$key] = $default;
            }
        }

        return $current;
    }

    /** @param array<int, array<string, mixed>> $entries */
    private function mergePublicTranslations(array $entries): void
    {
        $setting = SiteSetting::query()->firstOrCreate(
            ['key' => 'translations'],
            [
                'group' => 'general',
                'value' => ['entries' => []],
                'is_public' => true,
            ],
        );
        $value = is_array($setting->value) ? $setting->value : [];
        $map = MultilingualContent::mapFrom($value);

        foreach ($entries as $entry) {
            $key = trim((string) ($entry['key'] ?? ''));

            if ($key === '') {
                continue;
            }

            $map[$key] ??= ['ka' => '', 'en' => '', 'ru' => ''];

            foreach (self::LOCALES as $locale) {
                $configured = trim((string) ($map[$key][$locale] ?? ''));
                $replacement = trim((string) ($entry[$locale] ?? ''));
                $georgianSource = trim((string) ($entry['ka'] ?? ''));

                // Older releases copied Georgian list items into EN/RU. Keep
                // editorial overrides, but replace this known placeholder when
                // the seed now has a distinct localized value.
                if ($replacement !== '' && ($configured === '' || (
                    $locale !== 'ka'
                    && $configured === $georgianSource
                    && $replacement !== $georgianSource
                ))) {
                    $map[$key][$locale] = $replacement;
                }
            }
        }

        $value['entries'] = MultilingualContent::entriesFromMap($map);
        $setting->forceFill([
            'group' => 'general',
            'value' => $value,
            'is_public' => true,
        ])->save();
    }

    /** @return array<int, array<string, string>> */
    private function serviceTranslationEntries(array $definition): array
    {
        $prefix = "service.{$definition['slug']}";
        $entries = [];
        $this->appendEntry($entries, "{$prefix}.name", $definition['name']);
        $this->appendEntry($entries, "{$prefix}.card.title", $definition['name']);
        $this->appendEntry($entries, "{$prefix}.card.description", $definition['description']);
        $this->appendEntry($entries, "{$prefix}.eyebrow", $definition['eyebrow']);
        $this->appendEntry($entries, "{$prefix}.title", $definition['title']);
        $this->appendEntry($entries, "{$prefix}.description", $definition['description']);
        $this->appendEntry($entries, "{$prefix}.seoTitle", $definition['seo_title']);
        $this->appendEntry($entries, "{$prefix}.seoDescription", $definition['seo_description']);
        $this->appendEntry($entries, "{$prefix}.overview.title", $definition['overview_title']);
        $this->appendEntry($entries, "{$prefix}.overview.paragraph.0", $definition['description']);
        $this->appendEntry($entries, "{$prefix}.overview.paragraph.1", $definition['overview_text']);

        foreach ($definition['keywords'] as $index => $value) {
            $this->appendEntry($entries, "{$prefix}.keyword.{$index}", $value);
        }

        foreach ($definition['highlights'] as $index => $value) {
            $this->appendEntry($entries, "{$prefix}.highlight.{$index}", $value);
            $this->appendEntry($entries, "{$prefix}.benefit.{$index}.title", $value);
            $this->appendEntry(
                $entries,
                "{$prefix}.benefit.{$index}.description",
                $this->benefitDescription($index),
            );
        }

        foreach ($definition['scope'] as $index => $value) {
            $this->appendEntry($entries, "{$prefix}.solution.{$index}.title", $value);
            $this->appendEntry(
                $entries,
                "{$prefix}.solution.{$index}.description",
                $this->solutionDescription($index),
            );
        }

        foreach ($this->industrySet($definition['category']) as $index => $value) {
            $this->appendEntry($entries, "{$prefix}.industry.{$index}", $value);
        }

        foreach ($this->processDefinitions() as $index => $step) {
            $this->appendEntry($entries, "{$prefix}.process.{$index}.title", $step['title']);
            $this->appendEntry($entries, "{$prefix}.process.{$index}.description", $step['description']);
        }

        return $entries;
    }

    private function appendEntry(array &$entries, string $key, array $values): void
    {
        $entries[] = [
            'key' => $key,
            'ka' => $values['ka'],
            'en' => $values['en'],
            'ru' => $values['ru'],
        ];
    }

    private function localizedFaqList(array $faqs, string $locale): array
    {
        return array_map(fn (array $faq): array => [
            'question' => $faq['question'][$locale],
            'answer' => $faq['answer'][$locale],
        ], $faqs);
    }

    private function localizedCards(array $items, string $locale, bool $featured = false): array
    {
        $icons = ['verified', 'engineering', 'support_agent'];

        return array_map(fn (array $item, int $index): array => array_filter([
            'icon' => $icons[$index % count($icons)],
            'title' => $item[$locale],
            'description' => ($featured
                ? $this->solutionDescription($index)
                : $this->benefitDescription($index))[$locale],
            'featured' => $featured && $index === 0 ? true : null,
        ], fn ($value) => $value !== null), $items, array_keys($items));
    }

    private function localizedProcess(string $locale): array
    {
        return array_map(fn (array $step): array => [
            'title' => $step['title'][$locale],
            'description' => $step['description'][$locale],
        ], $this->processDefinitions());
    }

    private function benefitDescription(int $index): array
    {
        return [
            self::t('სწორი დაგეგმვა ამცირებს ზედმეტ ხარჯს და შემდგომ ტექნიკურ პრობლემებს.', 'Correct planning reduces unnecessary cost and future technical issues.', 'Правильное планирование снижает лишние расходы и будущие технические проблемы.'),
            self::t('სამუშაო სრულდება უსაფრთხო, სტანდარტებზე დაფუძნებული მეთოდით.', 'Work is completed with a safe, standards-based approach.', 'Работы выполняются безопасно и в соответствии со стандартами.'),
            self::t('სისტემა მოწმდება, დოკუმენტირდება და მომხმარებელს სრულად ბარდება.', 'The system is tested, documented, and handed over to the customer.', 'Система тестируется, документируется и передается заказчику.'),
        ][$index % 3];
    }

    private function solutionDescription(int $index): array
    {
        return [
            self::t('კონფიგურაცია შეირჩევა ობიექტის, დატვირთვისა და ბიუჯეტის მიხედვით.', 'The configuration is selected for the site, workload, and budget.', 'Конфигурация подбирается с учетом объекта, нагрузки и бюджета.'),
            self::t('გამოიყენება თავსებადი კომპონენტები და წინასწარ განსაზღვრული მონტაჟის გეგმა.', 'Compatible components and a defined installation plan are used.', 'Используются совместимые компоненты и заранее определенный план монтажа.'),
            self::t('ჩაბარებამდე მოწმდება ფუნქციონალი, უსაფრთხოება და გამოყენების სიმარტივე.', 'Functionality, security, and ease of use are verified before handover.', 'Перед сдачей проверяются функциональность, безопасность и удобство использования.'),
        ][$index % 3];
    }

    private function processDefinitions(): array
    {
        return [
            [
                'title' => self::t('მოთხოვნის შეფასება', 'Requirement assessment', 'Оценка требований'),
                'description' => self::t('ვაზუსტებთ ამოცანას, გარემოს, მოწყობილობებსა და სასურველ შედეგს.', 'We clarify the task, environment, equipment, and expected result.', 'Уточняем задачу, условия, оборудование и ожидаемый результат.'),
            ],
            [
                'title' => self::t('გეგმა და შეთავაზება', 'Plan and quotation', 'План и предложение'),
                'description' => self::t('ვადგენთ სამუშაოს მოცულობას, კომპონენტებს, ვადებსა და საორიენტაციო ფასს.', 'We define scope, components, schedule, and indicative pricing.', 'Определяем объем работ, компоненты, сроки и ориентировочную стоимость.'),
            ],
            [
                'title' => self::t('ინსტალაცია და კონფიგურაცია', 'Installation and configuration', 'Установка и настройка'),
                'description' => self::t('ვასრულებთ მონტაჟს, პროგრამულ გამართვასა და საჭირო ინტეგრაციებს.', 'We complete installation, software configuration, and required integrations.', 'Выполняем монтаж, программную настройку и необходимые интеграции.'),
            ],
            [
                'title' => self::t('ტესტირება და ჩაბარება', 'Testing and handover', 'Тестирование и сдача'),
                'description' => self::t('ვამოწმებთ სისტემას, ვუხსნით გამოყენებას და ვაბარებთ გამართულ მდგომარეობაში.', 'We test the system, explain its use, and hand it over fully operational.', 'Тестируем систему, объясняем использование и сдаем в рабочем состоянии.'),
            ],
        ];
    }

    private function industrySet(string $category): array
    {
        return match ($category) {
            'computer-services' => [
                self::t('კერძო მომხმარებლები', 'Home users', 'Частные пользователи'),
                self::t('ოფისები', 'Offices', 'Офисы'),
                self::t('სასწავლო სივრცეები', 'Education', 'Образование'),
            ],
            'business-it' => [
                self::t('მაღაზიები', 'Retail', 'Розничная торговля'),
                self::t('რესტორნები და კაფეები', 'Restaurants and cafes', 'Рестораны и кафе'),
                self::t('ოფისები და მომსახურების ობიექტები', 'Offices and service businesses', 'Офисы и сервисные предприятия'),
            ],
            'network-infrastructure' => [
                self::t('ოფისები', 'Offices', 'Офисы'),
                self::t('სასტუმროები და კოტეჯები', 'Hotels and cottages', 'Отели и коттеджи'),
                self::t('საწყობები და საწარმოები', 'Warehouses and industrial sites', 'Склады и производства'),
            ],
            default => [
                self::t('კერძო სახლები', 'Private homes', 'Частные дома'),
                self::t('კომერციული ობიექტები', 'Commercial properties', 'Коммерческие объекты'),
                self::t('სასტუმროები და საწარმოები', 'Hospitality and industrial sites', 'Гостиничные и промышленные объекты'),
            ],
        };
    }

    /** @return array<int, array<string, mixed>> */
    private function categories(): array
    {
        return [
            $this->category(
                'computer-services',
                self::t('კომპიუტერული სერვისები', 'Computer Services', 'Компьютерные услуги'),
                self::t('კომპიუტერის შეკეთება, აწყობა და პროგრამული მომსახურება', 'Computer Repair, Assembly and Software Services', 'Ремонт, сборка и программное обслуживание компьютеров'),
                self::t('Windows-ისა და სხვა ოპერაციული სისტემების ინსტალაცია, კომპიუტერის აწყობა, პროფილაქტიკური გაწმენდა და ტექნიკური გამართვა თბილისში და საქართველოს მასშტაბით.', 'Operating system installation, custom PC assembly, preventive cleaning, and computer setup across Georgia.', 'Установка операционных систем, сборка компьютеров, профилактическая чистка и настройка по всей Грузии.'),
                [
                    self::t('კომპიუტერის სერვისი', 'computer service', 'компьютерный сервис'),
                    self::t('Windows-ის ინსტალაცია', 'Windows installation', 'установка Windows'),
                    self::t('კომპიუტერის აწყობა', 'custom PC build', 'сборка компьютера'),
                ],
            ),
            $this->category(
                'business-it',
                self::t('ბიზნეს IT სისტემები', 'Business IT Systems', 'IT-системы для бизнеса'),
                self::t('POS სისტემები და ბიზნესის IT მხარდაჭერა', 'POS Systems and Business IT Support', 'POS-системы и IT-поддержка бизнеса'),
                self::t('POS სისტემების ინსტალაცია, სალარო მოწყობილობების ინტეგრაცია და ყოველდღიური IT მხარდაჭერა მაღაზიებისთვის, კვების ობიექტებისა და ოფისებისთვის.', 'POS deployment, retail hardware integration, and day-to-day IT support for shops, hospitality, and offices.', 'Внедрение POS, интеграция торгового оборудования и ежедневная IT-поддержка магазинов, заведений и офисов.'),
                [
                    self::t('POS სისტემა', 'POS system', 'POS-система'),
                    self::t('IT მხარდაჭერა', 'IT support', 'IT-поддержка'),
                    self::t('სალარო პროგრამა', 'retail software', 'кассовая программа'),
                ],
            ),
            $this->category(
                'network-infrastructure',
                self::t('ქსელური ინფრასტრუქტურა', 'Network Infrastructure', 'Сетевая инфраструктура'),
                self::t('ქსელის მოწყობა, კაბელირება და რეკის ორგანიზება', 'Network Setup, Cabling and Rack Organization', 'Монтаж сети, кабельной системы и организация шкафа'),
                self::t('როუტერების, Wi-Fi ქსელების, CAT6 კაბელის, Patch Panel-ის, RJ45 როზეტებისა და საკომუნიკაციო რეკების პროფესიონალური მონტაჟი და კონფიგურაცია.', 'Professional installation and configuration of routers, Wi-Fi, CAT6 cabling, patch panels, RJ45 outlets, and communication racks.', 'Профессиональный монтаж и настройка роутеров, Wi-Fi, кабеля CAT6, патч-панелей, розеток RJ45 и телекоммуникационных шкафов.'),
                [
                    self::t('ქსელის მონტაჟი', 'network installation', 'монтаж сети'),
                    self::t('CAT6 კაბელის გაყვანა', 'CAT6 cabling', 'прокладка CAT6'),
                    self::t('Patch Panel მონტაჟი', 'patch panel installation', 'монтаж патч-панели'),
                ],
            ),
            $this->category(
                'security-access-automation',
                self::t('უსაფრთხოება და დაშვების ავტომატიკა', 'Security and Access Automation', 'Безопасность и автоматизация доступа'),
                self::t('კამერები, დომოფონები, დაშვების კონტროლი და შლაგბაუმები', 'CCTV, Intercom, Access Control and Barriers', 'Видеонаблюдение, домофоны, контроль доступа и шлагбаумы'),
                self::t('ვიდეოსამეთვალყურეობის, ვიდეოდომოფონის, დაშვების კონტროლისა და ავტომატური შლაგბაუმების დაგეგმვა, მონტაჟი, პროგრამული გამართვა და დისტანციური მართვა.', 'Design, installation, configuration, and remote management of CCTV, video intercom, access control, and automatic barriers.', 'Проектирование, монтаж, настройка и удаленное управление видеонаблюдением, домофонами, контролем доступа и автоматическими шлагбаумами.'),
                [
                    self::t('კამერების მონტაჟი', 'CCTV installation', 'монтаж видеонаблюдения'),
                    self::t('დომოფონის მონტაჟი', 'intercom installation', 'монтаж домофона'),
                    self::t('შლაგბაუმის მონტაჟი', 'barrier gate installation', 'монтаж шлагбаума'),
                ],
            ),
        ];
    }

    private function category(string $slug, array $name, array $seoTitle, array $description, array $keywords): array
    {
        return [
            'slug' => $slug,
            'name' => $name,
            'seo_title' => $seoTitle,
            'seo_description' => $description,
            'description' => $description,
            'keywords' => $keywords,
            'faqs' => [
                $this->faq('assessment',
                    self::t('შესაძლებელია წინასწარი კონსულტაცია?', 'Is a preliminary consultation available?', 'Доступна ли предварительная консультация?'),
                    self::t('დიახ. მოთხოვნის მიხედვით ვაზუსტებთ ამოცანას, ვაფასებთ სამუშაოს მოცულობას და ვადგენთ შესაბამის გადაწყვეტას.', 'Yes. We clarify the requirements, assess the scope, and propose a suitable solution.', 'Да. Мы уточняем требования, оцениваем объем работ и предлагаем подходящее решение.'),
                ),
                $this->faq('price',
                    self::t('როგორ განისაზღვრება საბოლოო ფასი?', 'How is the final price determined?', 'Как определяется итоговая стоимость?'),
                    self::t('ფასი დამოკიდებულია სამუშაოს მოცულობაზე, ობიექტის პირობებზე, არჩეულ მოწყობილობებსა და საჭირო მასალებზე.', 'Pricing depends on scope, site conditions, selected equipment, and required materials.', 'Стоимость зависит от объема, условий объекта, выбранного оборудования и необходимых материалов.'),
                ),
            ],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function services(): array
    {
        return [
            $this->service('operating-system-installation', 'computer-services', 'desktop_windows', 'it-support',
                self::t('ოპერაციული სისტემების ინსტალაცია', 'Operating System Installation', 'Установка операционных систем'),
                self::t('Windows-ის და ოპერაციული სისტემების ინსტალაცია და გამართვა', 'Windows and Operating System Installation and Setup', 'Установка и настройка Windows и операционных систем'),
                self::t('ლიცენზირებული Windows-ის ინსტალაცია, დრაივერების გამართვა, განახლებები, ძირითადი პროგრამები, მონაცემების გადატანა და სისტემის ოპტიმიზაცია.', 'Licensed Windows installation, drivers, updates, essential software, data migration, and system optimization.', 'Установка лицензионной Windows, драйверов, обновлений, основных программ, перенос данных и оптимизация системы.'),
                self::t('Windows-ის ინსტალაცია და გამართვა | SafeTech', 'Windows Installation and Setup in Georgia | SafeTech', 'Установка и настройка Windows в Грузии | SafeTech'),
                self::t('Windows 10/11-ის პროფესიონალური ინსტალაცია, დრაივერები, პროგრამები, მონაცემების გადატანა და კომპიუტერის ოპტიმიზაცია თბილისში და რეგიონებში.', 'Professional Windows 10/11 installation, drivers, software, data migration, and PC optimization across Georgia.', 'Профессиональная установка Windows 10/11, драйверов, программ, перенос данных и оптимизация компьютера по Грузии.'),
                ['Windows 10/11', 'დრაივერები და განახლებები', 'მონაცემების უსაფრთხო გადატანა'],
                ['სისტემის სუფთა ინსტალაცია', 'დრაივერებისა და პროგრამების გამართვა', 'ოპტიმიზაცია და ტესტირება'],
                ['Windows installation', 'ოპერაციული სისტემის დაყენება', 'კომპიუტერის პროგრამული გამართვა'],
                [
                    $this->faq('license', self::t('ლიცენზიის გააქტიურებაც შედის?', 'Is license activation included?', 'Входит ли активация лицензии?'), self::t('აქტივაცია სრულდება მომხმარებლის მოქმედი ლიცენზიით ან ცალკე შეთანხმებული ლიცენზიის შეძენის შემდეგ.', 'Activation is completed with the customer’s valid license or after purchasing an agreed license.', 'Активация выполняется действующей лицензией клиента или после согласованной покупки лицензии.')),
                    $this->faq('data', self::t('ფაილები შემენახება?', 'Will my files be preserved?', 'Сохранятся ли мои файлы?'), self::t('სამუშაოს დაწყებამდე ვამოწმებთ მონაცემებს და შეთანხმების შემთხვევაში ვაკეთებთ სარეზერვო ასლსა და გადატანას.', 'Before work begins, we review the data and, when agreed, create a backup and migrate it.', 'Перед началом работ мы проверяем данные и по согласованию создаем резервную копию и переносим их.')),
                    $this->faq('time', self::t('რამდენ ხანს გრძელდება ინსტალაცია?', 'How long does installation take?', 'Сколько длится установка?'), self::t('ჩვეულებრივ რამდენიმე საათი, თუმცა დრო დამოკიდებულია კომპიუტერის მდგომარეობაზე, განახლებებსა და გადასატან მონაცემებზე.', 'Usually a few hours, depending on the computer, updates, and amount of data to migrate.', 'Обычно несколько часов — в зависимости от компьютера, обновлений и объема переносимых данных.')),
                ]),
            $this->service('custom-computer-build', 'computer-services', 'memory', 'it-support',
                self::t('კომპიუტერების აწყობა', 'Custom Computer Assembly', 'Сборка компьютеров'),
                self::t('კომპიუტერის პროფესიონალური აწყობა და კომპონენტების შერჩევა', 'Professional Custom PC Assembly and Component Selection', 'Профессиональная сборка компьютера и подбор компонентов'),
                self::t('საოფისე, სამუშაო, დიზაინერული და სათამაშო კომპიუტერების დაგეგმვა, თავსებადი კომპონენტების შერჩევა, აწყობა, BIOS-ის გამართვა და დატვირთვის ტესტირება.', 'Planning, compatible component selection, assembly, BIOS setup, and stress testing for office, workstation, creative, and gaming PCs.', 'Планирование, подбор совместимых компонентов, сборка, настройка BIOS и нагрузочное тестирование офисных, рабочих и игровых ПК.'),
                self::t('კომპიუტერის აწყობა შეკვეთით | SafeTech Georgia', 'Custom PC Assembly in Georgia | SafeTech', 'Сборка компьютера на заказ в Грузии | SafeTech'),
                self::t('კომპიუტერის აწყობა შეკვეთით: კომპონენტების შერჩევა, თავსებადობის შემოწმება, cable management, BIOS, Windows და სტრეს-ტესტი.', 'Custom PC assembly with component selection, compatibility checks, cable management, BIOS, Windows, and stress testing.', 'Сборка ПК на заказ: подбор компонентов, проверка совместимости, cable management, BIOS, Windows и стресс-тест.'),
                ['თავსებადი კომპონენტები', 'სუფთა cable management', 'სტრეს-ტესტი და ტემპერატურები'],
                ['კონფიგურაციის შერჩევა', 'პროფესიონალური აწყობა', 'BIOS და სისტემის ტესტირება'],
                ['custom PC build', 'კომპიუტერის აწყობა', 'კომპონენტების შერჩევა'],
                [
                    $this->faq('parts', self::t('კომპონენტებსაც არჩევთ?', 'Do you also select the components?', 'Вы также подбираете комплектующие?'), self::t('დიახ. ბიუჯეტისა და დანიშნულების მიხედვით ვარჩევთ სრულად თავსებად კომპონენტებს.', 'Yes. We select fully compatible components according to budget and intended use.', 'Да. Подбираем полностью совместимые комплектующие с учетом бюджета и назначения.')),
                    $this->faq('existing', self::t('ჩემი შეძენილი ნაწილებით აწყობა შეიძლება?', 'Can you build using parts I already bought?', 'Можно собрать из уже купленных деталей?'), self::t('დიახ, წინასწარ შევამოწმებთ თავსებადობასა და კომპონენტების მდგომარეობას.', 'Yes, after checking compatibility and the condition of the components.', 'Да, после проверки совместимости и состояния комплектующих.')),
                    $this->faq('testing', self::t('აწყობის შემდეგ იტესტება?', 'Is the PC tested after assembly?', 'Тестируется ли ПК после сборки?'), self::t('დიახ. მოწმდება ჩართვა, BIOS, მეხსიერება, დისკები, ტემპერატურები და სისტემის სტაბილურობა.', 'Yes. Power-on, BIOS, memory, storage, temperatures, and overall stability are tested.', 'Да. Проверяются запуск, BIOS, память, накопители, температуры и стабильность системы.')),
                ]),
            $this->service('computer-cleaning-maintenance', 'computer-services', 'cleaning_services', 'it-support',
                self::t('კომპიუტერების გაწმენდა და პროფილაქტიკა', 'Computer Cleaning and Preventive Maintenance', 'Чистка и профилактика компьютеров'),
                self::t('კომპიუტერის გაწმენდა, თერმოპასტის შეცვლა და გაგრილების პროფილაქტიკა', 'Computer Cleaning, Thermal Paste Replacement and Cooling Maintenance', 'Чистка компьютера, замена термопасты и обслуживание охлаждения'),
                self::t('დესკტოპისა და ლეპტოპის მტვრისგან გაწმენდა, ქულერების შემოწმება, თერმოპასტისა და საჭირო თერმობალიშების შეცვლა, ტემპერატურების კონტროლი.', 'Desktop and laptop dust cleaning, fan inspection, thermal paste and required thermal pad replacement, and temperature verification.', 'Чистка настольных ПК и ноутбуков от пыли, проверка вентиляторов, замена термопасты и термопрокладок, контроль температур.'),
                self::t('კომპიუტერის გაწმენდა და თერმოპასტა | SafeTech', 'Computer Cleaning and Thermal Paste Service | SafeTech', 'Чистка компьютера и замена термопасты | SafeTech'),
                self::t('ლეპტოპისა და კომპიუტერის პროფესიონალური გაწმენდა, თერმოპასტის შეცვლა, გაგრილების შემოწმება და ტემპერატურის ტესტირება.', 'Professional laptop and desktop cleaning, thermal paste replacement, cooling inspection, and temperature testing.', 'Профессиональная чистка ноутбуков и ПК, замена термопасты, проверка охлаждения и температур.'),
                ['მტვრის სრული გაწმენდა', 'თერმოპასტის შეცვლა', 'ტემპერატურის ტესტირება'],
                ['დიაგნოსტიკა და დაშლა', 'გაგრილების სისტემის მომსახურება', 'აწყობა და დატვირთვის ტესტი'],
                ['კომპიუტერის გაწმენდა', 'თერმოპასტის შეცვლა', 'ლეპტოპის პროფილაქტიკა'],
                [
                    $this->faq('frequency', self::t('რამდენად ხშირად არის გაწმენდა საჭირო?', 'How often is cleaning needed?', 'Как часто нужна чистка?'), self::t('საშუალოდ წელიწადში ერთხელ, ხოლო მტვრიან გარემოში ან მაღალი დატვირთვისას — უფრო ხშირად.', 'Typically once a year, and more often in dusty environments or under heavy use.', 'Обычно раз в год, а в пыльных условиях или при высокой нагрузке — чаще.')),
                    $this->faq('pads', self::t('თერმობალიშებიც იცვლება?', 'Are thermal pads replaced too?', 'Меняются ли термопрокладки?'), self::t('მხოლოდ საჭიროების შემთხვევაში და შესაბამისი სისქის ზუსტად შერჩევის შემდეგ.', 'Only when necessary and after selecting the exact required thickness.', 'Только при необходимости и после точного подбора требуемой толщины.')),
                    $this->faq('temperature', self::t('შედეგი როგორ მოწმდება?', 'How is the result verified?', 'Как проверяется результат?'), self::t('აწყობის შემდეგ მოწმდება ქულერების მუშაობა, ტემპერატურები და დატვირთვისას სტაბილურობა.', 'After reassembly, fan operation, temperatures, and stability under load are checked.', 'После сборки проверяются вентиляторы, температуры и стабильность под нагрузкой.')),
                ]),
            $this->service('rack-assembly-cable-management', 'network-infrastructure', 'dns', 'server-infrastructure',
                self::t('რეკების აწყობა და კაბელ მენეჯმენტი', 'Rack Assembly and Cable Management', 'Сборка шкафов и кабель-менеджмент'),
                self::t('საკომუნიკაციო რეკის აწყობა, მოწყობილობების განლაგება და კაბელ მენეჯმენტი', 'Communication Rack Assembly, Equipment Layout and Cable Management', 'Сборка телекоммуникационного шкафа, размещение оборудования и кабель-менеджмент'),
                self::t('კედლისა და იატაკის რეკების მონტაჟი, Patch Panel-ის, სვიჩის, NVR-ის, UPS-ისა და PDU-ის სწორად განლაგება, მარკირება და მოწესრიგებული კაბელ მენეჯმენტი.', 'Wall and floor rack installation, correct placement of patch panels, switches, NVRs, UPS and PDU units, labeling, and organized cable management.', 'Монтаж настенных и напольных шкафов, правильное размещение патч-панелей, коммутаторов, NVR, UPS и PDU, маркировка и аккуратный кабель-менеджмент.'),
                self::t('რეკის აწყობა და კაბელ მენეჯმენტი | SafeTech', 'Network Rack Assembly and Cable Management | SafeTech', 'Сборка сетевого шкафа и кабель-менеджмент | SafeTech'),
                self::t('ქსელური და CCTV რეკის პროფესიონალური აწყობა, Patch Panel, სვიჩი, NVR, UPS, PDU, მარკირება და კაბელების მოწესრიგება.', 'Professional network and CCTV rack assembly with patch panel, switch, NVR, UPS, PDU, labeling, and cable organization.', 'Профессиональная сборка сетевого и CCTV-шкафа: патч-панель, коммутатор, NVR, UPS, PDU, маркировка и организация кабелей.'),
                ['სწორი U განაწილება', 'მარკირებული კაბელები', 'გაგრილებისა და მომსახურების სივრცე'],
                ['რეკის ზომისა და დატვირთვის დაგეგმვა', 'მოწყობილობების მონტაჟი', 'კაბელების ორგანიზება და დოკუმენტაცია'],
                ['რეკის აწყობა', 'cable management', 'ქსელური კარადა'],
                [
                    $this->faq('size', self::t('რამდენ U-იანი რეკი მჭირდება?', 'What rack size do I need?', 'Какой размер шкафа в U мне нужен?'), self::t('ზომა ითვლება მოწყობილობების რაოდენობის, მომავალი გაფართოებისა და გაგრილების საჭიროების მიხედვით.', 'Size is calculated from equipment count, future expansion, and cooling requirements.', 'Размер рассчитывается по количеству оборудования, запасу на расширение и требованиям охлаждения.')),
                    $this->faq('existing', self::t('არსებული არეული რეკის მოწესრიგებაც შეიძლება?', 'Can you reorganize an existing messy rack?', 'Можно упорядочить существующий шкаф?'), self::t('დიახ. ვაკეთებთ ინვენტარიზაციას, მარკირებას, გადალაგებასა და კაბელების უსაფრთხო ორგანიზებას.', 'Yes. We inventory, label, rearrange, and safely organize the cabling.', 'Да. Выполняем инвентаризацию, маркировку, перестановку и безопасную организацию кабелей.')),
                    $this->faq('documentation', self::t('პორტების მარკირებას აკეთებთ?', 'Do you label the ports?', 'Вы маркируете порты?'), self::t('დიახ. Patch Panel-ისა და სვიჩის პორტები შეიძლება დაინომროს და შესაბამისი სქემა მომზადდეს.', 'Yes. Patch panel and switch ports can be numbered and documented.', 'Да. Порты патч-панели и коммутатора могут быть пронумерованы и задокументированы.')),
                ]),
            $this->service('pos-system-installation', 'business-it', 'point_of_sale', 'it-support',
                self::t('POS სისტემის აწყობა და კონფიგურაცია', 'POS System Installation and Configuration', 'Установка и настройка POS-системы'),
                self::t('POS სისტემის, სალარო მოწყობილობებისა და პროგრამის სრულად გამართვა', 'Complete POS Hardware and Software Setup', 'Полная настройка POS-оборудования и программного обеспечения'),
                self::t('POS ტერმინალის, კომპიუტერის, ჩეკის პრინტერის, ბარკოდ სკანერის, ფულის უჯრისა და სალარო პროგრამის მიერთება, კონფიგურაცია, ტესტირება და პერსონალის ინსტრუქტაჟი.', 'Connection, configuration, testing, and staff instruction for POS terminals, computers, receipt printers, barcode scanners, cash drawers, and retail software.', 'Подключение, настройка, тестирование и обучение персонала работе с POS-терминалом, компьютером, чековым принтером, сканером, денежным ящиком и кассовой программой.'),
                self::t('POS სისტემის ინსტალაცია და გამართვა | SafeTech', 'POS System Installation and Configuration | SafeTech', 'Установка и настройка POS-системы | SafeTech'),
                self::t('POS სისტემის სრულად აწყობა: ტერმინალი, ჩეკის პრინტერი, ბარკოდ სკანერი, ფულის უჯრა, სალარო პროგრამა, ქსელი და მომხმარებლის სწავლება.', 'Complete POS deployment: terminal, receipt printer, barcode scanner, cash drawer, retail software, network, and user training.', 'Полное внедрение POS: терминал, чековый принтер, сканер, денежный ящик, кассовая программа, сеть и обучение.'),
                ['მოწყობილობების სრული ინტეგრაცია', 'სალარო პროგრამის გამართვა', 'ტესტირება და პერსონალის სწავლება'],
                ['საჭიროებებისა და პროგრამის შერჩევა', 'POS მოწყობილობების მიერთება', 'ტესტირება და სამუშაო პროცესის ჩაბარება'],
                ['POS system', 'სალარო სისტემის მონტაჟი', 'ჩეკის პრინტერის გამართვა'],
                [
                    $this->faq('software', self::t('სალარო პროგრამასაც აყენებთ?', 'Do you install the retail software too?', 'Вы устанавливаете кассовую программу?'), self::t('დიახ, შერჩეული პროგრამის ინსტალაცია და მოწყობილობებთან ინტეგრაცია შედის შეთანხმებულ სამუშაოში.', 'Yes. Installation of the selected software and integration with the hardware can be included.', 'Да. Установка выбранной программы и интеграция с оборудованием включаются в согласованный объем.')),
                    $this->faq('hardware', self::t('არსებულ მოწყობილობებს გამოიყენებთ?', 'Can existing hardware be used?', 'Можно использовать имеющееся оборудование?'), self::t('შესაძლებელია, თუ მოწყობილობები ტექნიკურად გამართული და არჩეულ პროგრამასთან თავსებადია.', 'Yes, provided the hardware is functional and compatible with the selected software.', 'Да, если оборудование исправно и совместимо с выбранной программой.')),
                    $this->faq('training', self::t('თანამშრომლების სწავლება შედის?', 'Is staff training included?', 'Входит ли обучение сотрудников?'), self::t('სისტემის ჩაბარებისას ვუხსნით ძირითად ოპერაციებს, მოწყობილობების გამოყენებასა და ყოველდღიურ პროცედურებს.', 'At handover, we explain core operations, hardware use, and daily procedures.', 'При сдаче объясняем основные операции, использование оборудования и ежедневные процедуры.')),
                ]),
            $this->service('business-it-support', 'business-it', 'support_agent', 'it-support',
                self::t('IT მხარდაჭერა', 'IT Support', 'IT-поддержка'),
                self::t('დისტანციური და ადგილზე IT მხარდაჭერა ბიზნესისთვის', 'Remote and On-Site IT Support for Business', 'Удаленная и выездная IT-поддержка бизнеса'),
                self::t('კომპიუტერების, პრინტერების, ქსელის, პროგრამების, მომხმარებლებისა და ბიზნესისთვის კრიტიკული IT სისტემების ყოველდღიური მხარდაჭერა, დიაგნოსტიკა და პროფილაქტიკა.', 'Day-to-day support, diagnostics, and preventive maintenance for computers, printers, networks, software, users, and business-critical IT systems.', 'Ежедневная поддержка, диагностика и профилактика компьютеров, принтеров, сетей, программ, пользователей и критичных IT-систем бизнеса.'),
                self::t('IT მხარდაჭერა ბიზნესისთვის | SafeTech Georgia', 'Business IT Support in Georgia | SafeTech', 'IT-поддержка бизнеса в Грузии | SafeTech'),
                self::t('დისტანციური და ადგილზე IT მხარდაჭერა ოფისებისთვის, მაღაზიებისთვის, სასტუმროებისა და სხვა ბიზნესებისთვის — ერთჯერადი ან აბონენტური მომსახურება.', 'Remote and on-site IT support for offices, retail, hotels, and other businesses, available as one-time or managed service.', 'Удаленная и выездная IT-поддержка офисов, магазинов, отелей и другого бизнеса — разово или по абонентскому договору.'),
                ['დისტანციური დახმარება', 'ადგილზე ტექნიკური ვიზიტი', 'პროფილაქტიკა და დოკუმენტაცია'],
                ['ინფრასტრუქტურის შეფასება', 'ინციდენტებისა და მოთხოვნების მართვა', 'პერიოდული მოვლა და გაუმჯობესება'],
                ['IT support Georgia', 'კომპიუტერული დახმარება ბიზნესისთვის', 'აბონენტური IT მომსახურება'],
                [
                    $this->faq('format', self::t('დისტანციური მხარდაჭერა გაქვთ?', 'Do you provide remote support?', 'Вы оказываете удаленную поддержку?'), self::t('დიახ. პრობლემის ტიპის მიხედვით დახმარება შესაძლებელია დისტანციურად ან ადგილზე ვიზიტით.', 'Yes. Depending on the issue, support is provided remotely or on site.', 'Да. В зависимости от проблемы поддержка оказывается удаленно или с выездом.')),
                    $this->faq('subscription', self::t('აბონენტური მომსახურება შესაძლებელია?', 'Is managed monthly support available?', 'Доступно абонентское обслуживание?'), self::t('დიახ. მომსახურების გეგმა ფორმირდება მოწყობილობების, მომხმარებლებისა და საჭირო რეაგირების დროის მიხედვით.', 'Yes. The plan is based on device count, users, and required response times.', 'Да. План формируется по количеству устройств, пользователей и требуемому времени реакции.')),
                    $this->faq('scope', self::t('რა სისტემებს მოიცავს მხარდაჭერა?', 'What systems can be supported?', 'Какие системы входят в поддержку?'), self::t('კომპიუტერები, Windows, პროგრამები, პრინტერები, ქსელი, Wi-Fi, POS და შეთანხმებული ბიზნეს სისტემები.', 'Computers, Windows, software, printers, networks, Wi-Fi, POS, and agreed business systems.', 'Компьютеры, Windows, программы, принтеры, сеть, Wi-Fi, POS и согласованные бизнес-системы.')),
                ]),
            $this->service('security-camera-installation', 'security-access-automation', 'videocam', 'cctv',
                self::t('უსაფრთხოების კამერების მონტაჟი და გამართვა', 'Security Camera Installation and Setup', 'Монтаж и настройка камер видеонаблюдения'),
                self::t('IP და ანალოგური კამერების მონტაჟი, NVR/DVR-ის გამართვა და დისტანციური წვდომა', 'IP and Analog CCTV Installation, NVR/DVR Setup and Remote Access', 'Монтаж IP и аналоговых камер, настройка NVR/DVR и удаленного доступа'),
                self::t('ობიექტის შეფასება, კამერების წერტილების დაგეგმვა, კაბელირება, PoE ქსელი, კამერების მონტაჟი, NVR/DVR, ჩანაწერის არქივი, მოძრაობისა და ადამიანის დეტექცია და ტელეფონიდან ნახვა.', 'Site assessment, camera placement, cabling, PoE networking, camera installation, NVR/DVR, recording retention, motion and human detection, and mobile viewing.', 'Оценка объекта, размещение камер, кабельная сеть, PoE, монтаж камер, NVR/DVR, архив, детекция движения и людей, просмотр с телефона.'),
                self::t('კამერების მონტაჟი და გამართვა | SafeTech Georgia', 'CCTV Camera Installation in Georgia | SafeTech', 'Монтаж камер видеонаблюдения в Грузии | SafeTech'),
                self::t('IP და ანალოგური კამერების პროფესიონალური მონტაჟი, NVR/DVR, PoE, 24/7 ჩანაწერი, ტელეფონიდან ნახვა და უსაფრთხოების სისტემის სრული გამართვა.', 'Professional IP and analog CCTV installation, NVR/DVR, PoE, 24/7 recording, mobile viewing, and complete system setup.', 'Профессиональный монтаж IP и аналоговых камер, NVR/DVR, PoE, запись 24/7, просмотр с телефона и полная настройка.'),
                ['კამერების სწორი განლაგება', '24/7 ჩანაწერის გამართვა', 'ტელეფონიდან უსაფრთხო წვდომა'],
                ['ობიექტის შეფასება და პროექტირება', 'კაბელირება და მოწყობილობების მონტაჟი', 'NVR/DVR, დეტექცია და დისტანციური ნახვა'],
                ['კამერების მონტაჟი', 'CCTV installation Georgia', 'NVR DVR configuration'],
                [
                    $this->faq('storage', self::t('რამდენი დღის ჩანაწერი შემენახება?', 'How many days of recording will be stored?', 'Сколько дней будет храниться запись?'), self::t('არქივის ხანგრძლივობა ითვლება კამერების რაოდენობის, გარჩევადობის, ბიტრეიტისა და დისკის მოცულობის მიხედვით.', 'Retention is calculated from camera count, resolution, bitrate, and disk capacity.', 'Срок хранения рассчитывается по количеству камер, разрешению, битрейту и объему диска.')),
                    $this->faq('mobile', self::t('ტელეფონიდან ნახვა შეიძლება?', 'Can I view cameras from my phone?', 'Можно смотреть камеры с телефона?'), self::t('დიახ. თავსებადი სისტემისთვის ვამართავთ უსაფრთხო დისტანციურ წვდომას და აპლიკაციას.', 'Yes. We configure secure remote access and the compatible mobile application.', 'Да. Настраиваем безопасный удаленный доступ и совместимое приложение.')),
                    $this->faq('existing', self::t('არსებული კამერების შეკეთებაც შეგიძლიათ?', 'Can you repair an existing CCTV system?', 'Вы ремонтируете существующие системы?'), self::t('დიახ. ვაკეთებთ კაბელის, კონექტორების, კვების, PoE-ის, კამერების, ჩამწერისა და ქსელის დიაგნოსტიკას.', 'Yes. We diagnose cabling, connectors, power, PoE, cameras, recorders, and network issues.', 'Да. Диагностируем кабели, разъемы, питание, PoE, камеры, регистраторы и сеть.')),
                ]),
            $this->service('intercom-access-control-installation', 'security-access-automation', 'fingerprint', 'access-control',
                self::t('დომოფონებისა და დაშვების სისტემების მონტაჟი', 'Intercom and Access Control Installation', 'Монтаж домофонов и систем контроля доступа'),
                self::t('ვიდეოდომოფონის, ელექტრო საკეტის და დაშვების კონტროლის ინსტალაცია და გამართვა', 'Video Intercom, Electric Lock and Access Control Installation', 'Установка видеодомофона, электрозамка и контроля доступа'),
                self::t('ვიდეოდომოფონი, გარე პანელი, მონიტორი, ელექტრო საკეტი, Exit ღილაკი, ბარათი, PIN, ბიომეტრია, კარის კონტაქტი, სარეზერვო კვება და მობილური აპლიკაციის კონფიგურაცია.', 'Video intercom, outdoor station, monitor, electric lock, exit button, card, PIN, biometrics, door contact, backup power, and mobile application configuration.', 'Видеодомофон, вызывная панель, монитор, электрозамок, кнопка выхода, карта, PIN, биометрия, датчик двери, резервное питание и мобильное приложение.'),
                self::t('დომოფონი და დაშვების კონტროლი | SafeTech Georgia', 'Intercom and Access Control Installation | SafeTech', 'Домофоны и контроль доступа в Грузии | SafeTech'),
                self::t('ვიდეოდომოფონის, ელექტრო საკეტის, Exit ღილაკის, ბარათის/PIN/ბიომეტრიისა და დაშვების სისტემის პროფესიონალური მონტაჟი და გამართვა.', 'Professional installation and setup of video intercoms, electric locks, exit buttons, card, PIN, biometric, and access control systems.', 'Профессиональный монтаж и настройка видеодомофонов, электрозамков, кнопок выхода, карт, PIN, биометрии и контроля доступа.'),
                ['უსაფრთხო საკეტის სქემა', 'ბარათი, PIN და ბიომეტრია', 'სარეზერვო კვება და აპლიკაცია'],
                ['კარისა და ჭიშკრის შეფასება', 'დომოფონისა და საკეტის მონტაჟი', 'წვდომების, აპლიკაციისა და უსაფრთხოების ტესტი'],
                ['დომოფონის მონტაჟი', 'access control installation', 'ელექტრო საკეტის დაყენება'],
                [
                    $this->faq('lock', self::t('რომელი ელექტრო საკეტი მჭირდება?', 'Which electric lock do I need?', 'Какой электрозамок нужен?'), self::t('საკეტი შეირჩევა კარის ან ჭიშკრის ტიპის, გახსნის მიმართულების, უსაფრთხოებისა და კვების მოთხოვნის მიხედვით.', 'The lock is selected according to the door or gate type, opening direction, security, and power requirements.', 'Замок выбирается по типу двери или ворот, направлению открывания, требованиям безопасности и питания.')),
                    $this->faq('backup', self::t('დენის გათიშვისას იმუშავებს?', 'Will it work during a power outage?', 'Будет ли работать при отключении электричества?'), self::t('სარეზერვო კვების დამატების შემთხვევაში სისტემა გააგრძელებს მუშაობას განსაზღვრული დროით.', 'With backup power, the system can continue operating for a calculated period.', 'При наличии резервного питания система продолжит работу в течение рассчитанного времени.')),
                    $this->faq('phone', self::t('ზარის მიღება ტელეფონზე შეიძლება?', 'Can intercom calls be received on a phone?', 'Можно принимать вызовы на телефон?'), self::t('თავსებადი IP ვიდეოდომოფონის შემთხვევაში შესაძლებელია აპლიკაციით ზარის მიღება და კარის გახსნა.', 'With a compatible IP video intercom, calls and door opening can be handled in the mobile app.', 'При совместимом IP-видеодомофоне можно принимать вызовы и открывать дверь через приложение.')),
                ]),
            $this->service('router-wifi-configuration', 'network-infrastructure', 'router', 'networking',
                self::t('როუტერების ინსტალაცია და კონფიგურაცია', 'Router Installation and Configuration', 'Установка и настройка роутеров'),
                self::t('როუტერის, Wi-Fi ქსელის, MikroTik-ისა და უსაფრთხო ინტერნეტის გამართვა', 'Router, Wi-Fi, MikroTik and Secure Internet Setup', 'Настройка роутера, Wi-Fi, MikroTik и безопасного интернета'),
                self::t('როუტერის ინსტალაცია, ISP პარამეტრები, Wi-Fi დაფარვა, სტუმრის ქსელი, DHCP, სტატიკური IP, Port Forwarding, VPN, VLAN, Firewall და MikroTik-ის კონფიგურაცია.', 'Router installation, ISP settings, Wi-Fi coverage, guest networks, DHCP, static IP, port forwarding, VPN, VLAN, firewall, and MikroTik configuration.', 'Установка роутера, параметры провайдера, Wi-Fi, гостевая сеть, DHCP, статический IP, проброс портов, VPN, VLAN, firewall и MikroTik.'),
                self::t('როუტერის დაყენება და Wi-Fi გამართვა | SafeTech', 'Router and Wi-Fi Configuration in Georgia | SafeTech', 'Настройка роутера и Wi-Fi в Грузии | SafeTech'),
                self::t('როუტერისა და MikroTik-ის პროფესიონალური კონფიგურაცია: Wi-Fi, DHCP, VLAN, VPN, Firewall, Port Forwarding და ქსელის უსაფრთხოება.', 'Professional router and MikroTik configuration: Wi-Fi, DHCP, VLAN, VPN, firewall, port forwarding, and network security.', 'Профессиональная настройка роутеров и MikroTik: Wi-Fi, DHCP, VLAN, VPN, firewall, проброс портов и безопасность сети.'),
                ['სტაბილური Wi-Fi დაფარვა', 'დაცული ქსელის პარამეტრები', 'VLAN, VPN და Firewall'],
                ['დაფარვისა და მოთხოვნების შეფასება', 'როუტერისა და ქსელის კონფიგურაცია', 'სიჩქარის, როუმინგისა და უსაფრთხოების ტესტი'],
                ['როუტერის კონფიგურაცია', 'MikroTik setup', 'Wi-Fi installation'],
                [
                    $this->faq('coverage', self::t('Wi-Fi ყველა ოთახში დაიჭერს?', 'Will Wi-Fi cover every room?', 'Будет ли Wi-Fi во всех комнатах?'), self::t('დაფარვა დამოკიდებულია ფართობზე, კედლებზე, სართულებსა და მოწყობილობების მდებარეობაზე; საჭიროებისას ემატება Access Point ან Mesh.', 'Coverage depends on area, walls, floors, and device placement; access points or mesh can be added when needed.', 'Покрытие зависит от площади, стен, этажей и размещения; при необходимости добавляются точки доступа или Mesh.')),
                    $this->faq('mikrotik', self::t('MikroTik-ის გამართვას აკეთებთ?', 'Do you configure MikroTik?', 'Вы настраиваете MikroTik?'), self::t('დიახ. ვამართავთ ინტერნეტს, DHCP-ს, NAT-ს, VLAN-ს, VPN-ს, Firewall-სა და სხვა საჭირო ფუნქციებს.', 'Yes. We configure internet, DHCP, NAT, VLAN, VPN, firewall, and other required functions.', 'Да. Настраиваем интернет, DHCP, NAT, VLAN, VPN, firewall и другие функции.')),
                    $this->faq('security', self::t('ქსელის უსაფრთხოებაც მოწმდება?', 'Is network security reviewed?', 'Проверяется ли безопасность сети?'), self::t('დიახ. იცვლება ადმინისტრატორის წვდომა, Wi-Fi დაცვა, firmware და საჭირო Firewall წესები.', 'Yes. Administrator access, Wi-Fi security, firmware, and required firewall rules are reviewed.', 'Да. Проверяются доступ администратора, защита Wi-Fi, прошивка и необходимые правила firewall.')),
                ]),
            $this->service('network-cable-installation', 'network-infrastructure', 'cable', 'networking',
                self::t('ქსელის კაბელის გაყვანა', 'Network Cable Installation', 'Прокладка сетевого кабеля'),
                self::t('CAT5e, CAT6 და ოპტიკური ქსელის კაბელის პროფესიონალური გაყვანა', 'Professional CAT5e, CAT6 and Fiber Network Cabling', 'Профессиональная прокладка CAT5e, CAT6 и оптоволокна'),
                self::t('ქსელის მარშრუტის დაგეგმვა, სპილენძის CAT5e/CAT6 კაბელის ან ოპტიკის გაყვანა, გოფრა/არხი, დაშორებების დაცვა, ორივე ბოლოს მარკირება და ხაზის ტესტირება.', 'Network route planning, CAT5e/CAT6 copper or fiber installation, conduit or trunking, separation compliance, endpoint labeling, and line testing.', 'Планирование трассы, прокладка медного CAT5e/CAT6 или оптики, гофра/канал, соблюдение расстояний, маркировка и тестирование линии.'),
                self::t('ქსელის კაბელის გაყვანა CAT6 | SafeTech Georgia', 'CAT6 Network Cable Installation in Georgia | SafeTech', 'Прокладка сетевого кабеля CAT6 в Грузии | SafeTech'),
                self::t('CAT5e/CAT6 ქსელის კაბელის გაყვანა ოფისში, სახლში, სასტუმროსა და საწარმოში — მარშრუტი, გოფრა, მარკირება, RJ45 და ტესტირება.', 'CAT5e/CAT6 cabling for offices, homes, hotels, and industrial sites, including routing, conduit, labeling, RJ45, and testing.', 'Прокладка CAT5e/CAT6 в офисах, домах, отелях и на производстве: трасса, гофра, маркировка, RJ45 и тестирование.'),
                ['სწორი მარშრუტი და დაშორებები', 'ორივე ბოლოს მარკირება', 'ქსელის ტესტირება'],
                ['გეგმისა და მეტრაჟის დათვლა', 'კაბელის უსაფრთხო გაყვანა', 'დასრულება, მარკირება და ტესტი'],
                ['CAT6 კაბელის გაყვანა', 'structured cabling Georgia', 'ქსელის მონტაჟი'],
                [
                    $this->faq('type', self::t('CAT5e ჯობს თუ CAT6?', 'Should I choose CAT5e or CAT6?', 'Что выбрать: CAT5e или CAT6?'), self::t('ახალი მონტაჟისთვის უმეტეს შემთხვევაში რეკომენდებულია ხარისხიანი 100% სპილენძის CAT6, თუმცა არჩევანი დამოკიდებულია მანძილსა და მოთხოვნებზე.', 'For most new installations, quality 100% copper CAT6 is recommended, depending on distance and requirements.', 'Для большинства новых монтажей рекомендуется качественный медный CAT6, с учетом длины и требований.')),
                    $this->faq('power', self::t('დენის კაბელთან ერთად შეიძლება გაყვანა?', 'Can it run next to power cables?', 'Можно прокладывать рядом с силовым кабелем?'), self::t('სასურველია შესაბამისი დაშორების დაცვა ან ცალკე არხის გამოყენება; გადაკვეთა კეთდება 90 გრადუსით.', 'Required separation or a separate pathway should be used; crossings are made at 90 degrees.', 'Следует соблюдать расстояние или использовать отдельный канал; пересечения выполняются под 90 градусов.')),
                    $this->faq('test', self::t('ხაზები იტესტება?', 'Are the cable runs tested?', 'Линии тестируются?'), self::t('დიახ. დასრულების შემდეგ მოწმდება გამტარობა, წყვილების თანმიმდევრობა და მარკირება.', 'Yes. Continuity, pair order, and labeling are checked after termination.', 'Да. После оконцевания проверяются целостность, порядок пар и маркировка.')),
                ]),
            $this->service('patch-panel-network-outlet-installation', 'network-infrastructure', 'settings_input_component', 'networking',
                self::t('Patch Panel-ის და ქსელური როზეტების მონტაჟი', 'Patch Panel and Network Outlet Installation', 'Монтаж патч-панелей и сетевых розеток'),
                self::t('Patch Panel, Keystone, RJ45 როზეტი და ქსელის წერტილების დასრულება', 'Patch Panel, Keystone, RJ45 Outlet and Network Point Termination', 'Оконцевание патч-панелей, Keystone, розеток RJ45 и сетевых точек'),
                self::t('Patch Panel-ის აწყობა, Keystone მოდულების ჩასმა, RJ45 ქსელური როზეტების მონტაჟი, T568A/T568B სტანდარტით დაპრესვა, პორტების მარკირება და თითოეული ხაზის ტესტირება.', 'Patch panel termination, keystone installation, RJ45 outlet mounting, T568A/T568B termination, port labeling, and testing of every line.', 'Оконцевание патч-панелей, установка Keystone и розеток RJ45, обжим по T568A/T568B, маркировка портов и тест каждой линии.'),
                self::t('Patch Panel და RJ45 როზეტების მონტაჟი | SafeTech', 'Patch Panel and RJ45 Outlet Installation | SafeTech', 'Монтаж патч-панелей и розеток RJ45 | SafeTech'),
                self::t('Patch Panel-ის, Keystone-ისა და RJ45 ქსელური როზეტების პროფესიონალური მონტაჟი, T568B დაპრესვა, პორტების მარკირება და ტესტირება.', 'Professional patch panel, keystone, and RJ45 outlet installation with T568B termination, labeling, and testing.', 'Профессиональный монтаж патч-панелей, Keystone и розеток RJ45, обжим T568B, маркировка и тестирование.'),
                ['სტანდარტული T568A/T568B დასრულება', 'პორტებისა და როზეტების მარკირება', 'ყველა ხაზის ტესტირება'],
                ['პორტების გეგმა', 'Patch Panel და როზეტების დასრულება', 'მარკირება და ტესტის ანგარიში'],
                ['Patch Panel მონტაჟი', 'RJ45 როზეტის მონტაჟი', 'Keystone termination'],
                [
                    $this->faq('standard', self::t('T568A თუ T568B გამოიყენება?', 'Do you use T568A or T568B?', 'Используется T568A или T568B?'), self::t('ობიექტზე გამოიყენება ერთი შეთანხმებული სტანდარტი; პრაქტიკაში ხშირად T568B, ორივე ბოლოზე იდენტურად.', 'One consistent standard is used across the site, commonly T568B, identically at both ends.', 'На объекте используется единый стандарт, чаще T568B, одинаково на обоих концах.')),
                    $this->faq('panel', self::t('რამდენპორტიანი Patch Panel მჭირდება?', 'What patch panel size do I need?', 'На сколько портов нужна патч-панель?'), self::t('ირჩევა მოქმედი წერტილების რაოდენობისა და მომავალი გაფართოების მარაგის მიხედვით.', 'It is selected based on active points plus capacity for future expansion.', 'Выбирается по количеству действующих точек с запасом на расширение.')),
                    $this->faq('certification', self::t('როზეტები და პორტები მოწმდება?', 'Are outlets and ports tested?', 'Розетки и порты проверяются?'), self::t('დიახ. თითოეული ხაზი მოწმდება და მარკირება თავსდება Patch Panel-სა და საბოლოო როზეტზე.', 'Yes. Every line is tested and labeled at both the patch panel and final outlet.', 'Да. Каждая линия тестируется и маркируется на патч-панели и конечной розетке.')),
                ]),
            $this->service('barrier-gate-installation', 'security-access-automation', 'toll', 'access-control',
                self::t('შლაგბაუმების მონტაჟი', 'Barrier Gate Installation', 'Монтаж шлагбаумов'),
                self::t('ავტომატური შლაგბაუმის მონტაჟი, დაშვების ინტეგრაცია და დისტანციური მართვა', 'Automatic Barrier Gate Installation, Access Integration and Remote Control', 'Монтаж автоматического шлагбаума, интеграция доступа и удаленное управление'),
                self::t('შლაგბაუმის შერჩევა და ფუნდამენტი, ბუმის მონტაჟი, ფოტოსენსორები, ინდუქციური მარყუჟი, პულტი, GSM, ბარათი, ნომრის ამოცნობა და დაშვების სისტემასთან ინტეგრაცია.', 'Barrier selection and foundation, boom installation, photocells, inductive loop, remote controls, GSM, cards, license plate recognition, and access system integration.', 'Подбор и основание шлагбаума, монтаж стрелы, фотоэлементы, индукционная петля, пульты, GSM, карты, распознавание номеров и интеграция доступа.'),
                self::t('შლაგბაუმის მონტაჟი და ავტომატიკა | SafeTech', 'Automatic Barrier Gate Installation | SafeTech', 'Монтаж автоматического шлагбаума | SafeTech'),
                self::t('ავტომატური შლაგბაუმის პროფესიონალური მონტაჟი: ფუნდამენტი, უსაფრთხოების სენსორები, პულტი, GSM, ბარათი, ნომრის ამოცნობა და დაშვების ინტეგრაცია.', 'Professional automatic barrier installation with foundation, safety sensors, remote, GSM, cards, plate recognition, and access integration.', 'Профессиональный монтаж автоматического шлагбаума: основание, датчики безопасности, пульты, GSM, карты, распознавание номеров и интеграция.'),
                ['უსაფრთხოების ფოტოსენსორები', 'პულტი, GSM ან ბარათი', 'დომოფონთან და დაშვებასთან ინტეგრაცია'],
                ['გასასვლელისა და ინტენსივობის შეფასება', 'ფუნდამენტი, კვება და მექანიკის მონტაჟი', 'სენსორები, მართვა და უსაფრთხოების ტესტი'],
                ['შლაგბაუმის მონტაჟი', 'automatic barrier Georgia', 'GSM gate access'],
                [
                    $this->faq('selection', self::t('რომელი სიგრძის შლაგბაუმი მჭირდება?', 'What boom length do I need?', 'Какая длина стрелы нужна?'), self::t('სიგრძე და ძრავის კლასი შეირჩევა გასასვლელის სიგანის, გამოყენების ინტენსივობისა და გახსნის სიჩქარის მიხედვით.', 'Boom length and motor class depend on lane width, usage frequency, and required opening speed.', 'Длина стрелы и класс привода выбираются по ширине проезда, интенсивности и скорости открытия.')),
                    $this->faq('safety', self::t('მანქანაზე ხომ არ ჩამოეშვება?', 'Can it lower onto a vehicle?', 'Может ли стрела опуститься на автомобиль?'), self::t('სწორი უსაფრთხოების სქემა მოიცავს ფოტოსენსორებს ან ინდუქციურ მარყუჟს, რომლებიც აფიქსირებს დაბრკოლებას.', 'A proper safety design uses photocells or an inductive loop to detect obstacles.', 'Правильная схема безопасности включает фотоэлементы или индукционную петлю для обнаружения препятствий.')),
                    $this->faq('control', self::t('ტელეფონით გახსნა შეიძლება?', 'Can it be opened by phone?', 'Можно открывать с телефона?'), self::t('დიახ. შესაძლებელია GSM კონტროლერი, აპლიკაცია ან დაშვების სისტემასთან ინტეგრაცია.', 'Yes. A GSM controller, mobile app, or access control integration can be used.', 'Да. Можно использовать GSM-контроллер, приложение или интеграцию с контролем доступа.')),
                ]),
        ];
    }

    private function service(
        string $slug,
        string $category,
        string $icon,
        string $calculatorProfile,
        array $name,
        array $title,
        array $description,
        array $seoTitle,
        array $seoDescription,
        array $highlights,
        array $scope,
        array $keywords,
        array $faqs,
    ): array {
        return [
            'slug' => $slug,
            'category' => $category,
            'icon' => $icon,
            'calculator_profile' => $calculatorProfile,
            'name' => $name,
            'eyebrow' => match ($category) {
                'computer-services' => self::t('კომპიუტერული სერვისები', 'Computer services', 'Компьютерные услуги'),
                'business-it' => self::t('ბიზნეს IT სისტემები', 'Business IT systems', 'IT-системы для бизнеса'),
                'network-infrastructure' => self::t('ქსელური ინფრასტრუქტურა', 'Network infrastructure', 'Сетевая инфраструктура'),
                default => self::t('უსაფრთხოება და ავტომატიკა', 'Security and automation', 'Безопасность и автоматизация'),
            },
            'title' => $title,
            'description' => $description,
            'seo_title' => $seoTitle,
            'seo_description' => $seoDescription,
            'overview_title' => $title,
            'overview_text' => self::t(
                'ვმუშაობთ წინასწარ შეთანხმებული გეგმით, თავსებადი მოწყობილობებით და ჩაბარებამდე სრულად ვამოწმებთ შედეგს.',
                'We work to an agreed plan, use compatible equipment, and fully verify the result before handover.',
                'Работаем по согласованному плану, используем совместимое оборудование и полностью проверяем результат перед сдачей.',
            ),
            'highlights' => $this->localizedServiceItems($slug, 'highlights', $highlights),
            'scope' => $this->localizedServiceItems($slug, 'scope', $scope),
            'keywords' => array_map(fn (string $value): array => self::t($value, $value, $value), $keywords),
            'faqs' => $faqs,
        ];
    }

    /**
     * Highlights and scope items are visible service copy, not just internal
     * tags.  They must therefore carry their own KA/EN/RU values instead of
     * duplicating the Georgian fallback into every locale.
     */
    private function localizedServiceItems(string $slug, string $field, array $values): array
    {
        $translations = self::SERVICE_ITEM_TRANSLATIONS[$slug][$field] ?? [];

        return array_map(function (string $value, int $index) use ($translations): array {
            $translation = $translations[$index] ?? [];

            return self::t(
                $value,
                $translation['en'] ?? $value,
                $translation['ru'] ?? $value,
            );
        }, $values, array_keys($values));
    }

    /** @var array<string, array<string, array<int, array{en: string, ru: string}>>> */
    private const SERVICE_ITEM_TRANSLATIONS = [
        'operating-system-installation' => [
            'highlights' => [
                ['en' => 'Windows 10/11', 'ru' => 'Windows 10/11'],
                ['en' => 'Drivers and updates', 'ru' => 'Драйверы и обновления'],
                ['en' => 'Safe data migration', 'ru' => 'Безопасный перенос данных'],
            ],
            'scope' => [
                ['en' => 'Clean system installation', 'ru' => 'Чистая установка системы'],
                ['en' => 'Driver and software setup', 'ru' => 'Настройка драйверов и программ'],
                ['en' => 'Optimization and testing', 'ru' => 'Оптимизация и тестирование'],
            ],
        ],
        'custom-computer-build' => [
            'highlights' => [
                ['en' => 'Compatible components', 'ru' => 'Совместимые компоненты'],
                ['en' => 'Clean cable management', 'ru' => 'Аккуратный кабель-менеджмент'],
                ['en' => 'Stress test and temperatures', 'ru' => 'Стресс-тест и температуры'],
            ],
            'scope' => [
                ['en' => 'Configuration selection', 'ru' => 'Подбор конфигурации'],
                ['en' => 'Professional assembly', 'ru' => 'Профессиональная сборка'],
                ['en' => 'BIOS and system testing', 'ru' => 'Тестирование BIOS и системы'],
            ],
        ],
        'computer-cleaning-maintenance' => [
            'highlights' => [
                ['en' => 'Full dust cleaning', 'ru' => 'Полная очистка от пыли'],
                ['en' => 'Thermal paste replacement', 'ru' => 'Замена термопасты'],
                ['en' => 'Temperature testing', 'ru' => 'Тестирование температур'],
            ],
            'scope' => [
                ['en' => 'Diagnostics and disassembly', 'ru' => 'Диагностика и разборка'],
                ['en' => 'Cooling system maintenance', 'ru' => 'Обслуживание системы охлаждения'],
                ['en' => 'Reassembly and load testing', 'ru' => 'Сборка и нагрузочный тест'],
            ],
        ],
        'rack-assembly-cable-management' => [
            'highlights' => [
                ['en' => 'Correct U-space allocation', 'ru' => 'Правильное распределение U-мест'],
                ['en' => 'Labeled cables', 'ru' => 'Маркированные кабели'],
                ['en' => 'Space for cooling and service', 'ru' => 'Пространство для охлаждения и обслуживания'],
            ],
            'scope' => [
                ['en' => 'Rack size and load planning', 'ru' => 'Планирование размера и нагрузки стойки'],
                ['en' => 'Equipment installation', 'ru' => 'Монтаж оборудования'],
                ['en' => 'Cable organization and documentation', 'ru' => 'Организация кабелей и документация'],
            ],
        ],
        'pos-system-installation' => [
            'highlights' => [
                ['en' => 'Full equipment integration', 'ru' => 'Полная интеграция оборудования'],
                ['en' => 'POS software setup', 'ru' => 'Настройка кассовой программы'],
                ['en' => 'Testing and staff training', 'ru' => 'Тестирование и обучение персонала'],
            ],
            'scope' => [
                ['en' => 'Requirements and software selection', 'ru' => 'Выбор требований и программы'],
                ['en' => 'POS equipment connection', 'ru' => 'Подключение POS-оборудования'],
                ['en' => 'Testing and workflow handover', 'ru' => 'Тестирование и передача рабочего процесса'],
            ],
        ],
        'business-it-support' => [
            'highlights' => [
                ['en' => 'Remote assistance', 'ru' => 'Удаленная помощь'],
                ['en' => 'On-site technical visits', 'ru' => 'Выезд технического специалиста'],
                ['en' => 'Maintenance and documentation', 'ru' => 'Профилактика и документация'],
            ],
            'scope' => [
                ['en' => 'Infrastructure assessment', 'ru' => 'Оценка инфраструктуры'],
                ['en' => 'Incident and request management', 'ru' => 'Управление инцидентами и запросами'],
                ['en' => 'Scheduled maintenance and improvement', 'ru' => 'Периодическое обслуживание и улучшение'],
            ],
        ],
        'security-camera-installation' => [
            'highlights' => [
                ['en' => 'Correct camera placement', 'ru' => 'Правильное размещение камер'],
                ['en' => '24/7 recording setup', 'ru' => 'Настройка записи 24/7'],
                ['en' => 'Secure phone access', 'ru' => 'Безопасный доступ с телефона'],
            ],
            'scope' => [
                ['en' => 'Site assessment and design', 'ru' => 'Оценка объекта и проектирование'],
                ['en' => 'Cabling and equipment installation', 'ru' => 'Кабелирование и монтаж оборудования'],
                ['en' => 'NVR/DVR, detection, and remote viewing', 'ru' => 'NVR/DVR, детекция и удаленный просмотр'],
            ],
        ],
        'intercom-access-control-installation' => [
            'highlights' => [
                ['en' => 'Secure lock configuration', 'ru' => 'Безопасная схема замка'],
                ['en' => 'Card, PIN, and biometrics', 'ru' => 'Карта, PIN и биометрия'],
                ['en' => 'Backup power and mobile app', 'ru' => 'Резервное питание и приложение'],
            ],
            'scope' => [
                ['en' => 'Door and gate assessment', 'ru' => 'Оценка двери и ворот'],
                ['en' => 'Intercom and lock installation', 'ru' => 'Монтаж домофона и замка'],
                ['en' => 'Access, app, and security testing', 'ru' => 'Тестирование доступа, приложения и безопасности'],
            ],
        ],
        'router-wifi-configuration' => [
            'highlights' => [
                ['en' => 'Stable Wi-Fi coverage', 'ru' => 'Стабильное покрытие Wi-Fi'],
                ['en' => 'Secure network settings', 'ru' => 'Защищенные настройки сети'],
                ['en' => 'VLAN, VPN and firewall', 'ru' => 'VLAN, VPN и межсетевой экран'],
            ],
            'scope' => [
                ['en' => 'Coverage and requirements assessment', 'ru' => 'Оценка покрытия и требований'],
                ['en' => 'Router and network configuration', 'ru' => 'Настройка роутера и сети'],
                ['en' => 'Speed, roaming, and security testing', 'ru' => 'Тест скорости, роуминга и безопасности'],
            ],
        ],
        'network-cable-installation' => [
            'highlights' => [
                ['en' => 'Correct routes and separation', 'ru' => 'Правильные маршруты и расстояния'],
                ['en' => 'Labels at both ends', 'ru' => 'Маркировка с обеих сторон'],
                ['en' => 'Network testing', 'ru' => 'Тестирование сети'],
            ],
            'scope' => [
                ['en' => 'Plan and length calculation', 'ru' => 'Расчет плана и метража'],
                ['en' => 'Safe cable routing', 'ru' => 'Безопасная прокладка кабеля'],
                ['en' => 'Termination, labeling, and testing', 'ru' => 'Оконцевание, маркировка и тестирование'],
            ],
        ],
        'patch-panel-network-outlet-installation' => [
            'highlights' => [
                ['en' => 'Standard T568A/T568B termination', 'ru' => 'Стандартная разделка T568A/T568B'],
                ['en' => 'Port and outlet labeling', 'ru' => 'Маркировка портов и розеток'],
                ['en' => 'Testing every line', 'ru' => 'Тестирование всех линий'],
            ],
            'scope' => [
                ['en' => 'Port layout plan', 'ru' => 'План портов'],
                ['en' => 'Patch panel and outlet termination', 'ru' => 'Оконцевание патч-панели и розеток'],
                ['en' => 'Labeling and test report', 'ru' => 'Маркировка и отчет по тестированию'],
            ],
        ],
        'barrier-gate-installation' => [
            'highlights' => [
                ['en' => 'Safety photo sensors', 'ru' => 'Фотоэлементы безопасности'],
                ['en' => 'Remote, GSM, or card access', 'ru' => 'Пульт, GSM или карта'],
                ['en' => 'Intercom and access integration', 'ru' => 'Интеграция с домофоном и доступом'],
            ],
            'scope' => [
                ['en' => 'Entrance and traffic assessment', 'ru' => 'Оценка проезда и интенсивности'],
                ['en' => 'Foundation, power, and mechanism installation', 'ru' => 'Фундамент, питание и монтаж механики'],
                ['en' => 'Sensors, control, and safety testing', 'ru' => 'Датчики, управление и тест безопасности'],
            ],
        ],
    ];

    private function faq(string $key, array $question, array $answer): array
    {
        return compact('key', 'question', 'answer');
    }

    private static function t(string $ka, string $en, string $ru): array
    {
        return compact('ka', 'en', 'ru');
    }
}
