<?php

namespace Database\Seeders;

use App\Models\SeoPage;
use App\Models\SiteSetting;
use App\Support\MultilingualContent;
use Illuminate\Database\Seeder;

class PrivacyPageSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSeo();
        $this->seedTranslations();
    }

    private function seedSeo(): void
    {
        $record = SeoPage::query()->firstOrNew(['key' => 'privacy']);
        $defaults = [
            'slug' => '/privacy',
            'title' => 'კონფიდენციალურობის პოლიტიკა | SafeTech',
            'description' => 'SafeTech-ის კონფიდენციალურობის პოლიტიკა განმარტავს, როგორ ვაგროვებთ, ვიყენებთ და ვიცავთ მომხმარებლის მონაცემებს.',
            'keywords' => ['კონფიდენციალურობის პოლიტიკა', 'SafeTech privacy', 'პერსონალური მონაცემები'],
            'og_title' => 'კონფიდენციალურობის პოლიტიკა | SafeTech',
            'og_description' => 'ინფორმაცია SafeTech-ის მიერ პერსონალური მონაცემების დამუშავებისა და დაცვის შესახებ.',
            'noindex' => true,
            'schema_type' => 'WebPage',
            'translations' => [
                'fields' => [
                    'title' => [
                        'ka' => 'კონფიდენციალურობის პოლიტიკა | SafeTech',
                        'en' => 'Privacy Policy | SafeTech',
                        'ru' => 'Политика конфиденциальности | SafeTech',
                    ],
                    'description' => [
                        'ka' => 'SafeTech-ის კონფიდენციალურობის პოლიტიკა განმარტავს, როგორ ვაგროვებთ, ვიყენებთ და ვიცავთ მომხმარებლის მონაცემებს.',
                        'en' => 'The SafeTech privacy policy explains how we collect, use, and protect customer information.',
                        'ru' => 'Политика конфиденциальности SafeTech объясняет, как мы собираем, используем и защищаем данные клиентов.',
                    ],
                    'og_title' => [
                        'ka' => 'კონფიდენციალურობის პოლიტიკა | SafeTech',
                        'en' => 'Privacy Policy | SafeTech',
                        'ru' => 'Политика конфиденциальности | SafeTech',
                    ],
                    'og_description' => [
                        'ka' => 'ინფორმაცია SafeTech-ის მიერ პერსონალური მონაცემების დამუშავებისა და დაცვის შესახებ.',
                        'en' => 'Information about personal data processing and protection at SafeTech.',
                        'ru' => 'Информация об обработке и защите персональных данных в SafeTech.',
                    ],
                ],
                'keywords' => [
                    'ka' => ['კონფიდენციალურობის პოლიტიკა', 'SafeTech privacy', 'პერსონალური მონაცემები'],
                    'en' => ['SafeTech privacy policy', 'personal data Georgia', 'website privacy'],
                    'ru' => ['политика конфиденциальности SafeTech', 'персональные данные', 'конфиденциальность сайта'],
                ],
            ],
        ];

        if ($record->exists === false) {
            $record->fill($defaults)->save();

            return;
        }

        foreach ($defaults as $field => $default) {
            if ($field === 'noindex') {
                $record->noindex = true;
                continue;
            }

            $current = $record->getAttribute($field);

            if (is_array($default)) {
                if (is_array($current) === false || $current === []) {
                    $record->setAttribute($field, $default);
                }
                continue;
            }

            if (blank($current)) {
                $record->setAttribute($field, $default);
            }
        }

        $record->save();
    }

    private function seedTranslations(): void
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

        foreach ($this->entries() as $entry) {
            $key = $entry['key'];
            $map[$key] ??= ['ka' => '', 'en' => '', 'ru' => ''];

            foreach (MultilingualContent::LOCALES as $locale) {
                if (blank($map[$key][$locale] ?? null)) {
                    $map[$key][$locale] = $entry[$locale];
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

    /** @return array<int, array{key:string,ka:string,en:string,ru:string}> */
    private function entries(): array
    {
        return [
            $this->entry('privacy.eyebrow', 'სამართლებრივი ინფორმაცია', 'Legal information', 'Правовая информация'),
            $this->entry('privacy.title', 'კონფიდენციალურობის პოლიტიკა', 'Privacy Policy', 'Политика конфиденциальности'),
            $this->entry('privacy.intro', 'ეს პოლიტიკა განმარტავს, რა ინფორმაციას ვიღებთ საკონტაქტო ფორმებიდან და ანალიტიკური ტექნოლოგიებიდან, რატომ ვიყენებთ მას და როგორ შეგიძლიათ თქვენი უფლებების გამოყენება.', 'This policy explains what information we receive through contact forms and analytics technologies, why we use it, and how you can exercise your rights.', 'Эта политика объясняет, какие данные мы получаем через контактные формы и аналитические технологии, зачем используем их и как вы можете реализовать свои права.'),
            $this->entry('privacy.updated', 'ბოლო განახლება: 2 აგვისტო, 2026', 'Last updated: August 2, 2026', 'Последнее обновление: 2 августа 2026 г.'),
            $this->entry('privacy.collection.title', 'რა მონაცემებს ვაგროვებთ', 'Information we collect', 'Какие данные мы собираем'),
            $this->entry('privacy.collection.body', 'შესაძლოა მივიღოთ თქვენი სახელი, ტელეფონის ნომერი, ელფოსტა, შეტყობინების ტექსტი, შერჩეული სერვისი და ტექნიკური მოთხოვნის დეტალები. საიტის უსაფრთხოებისა და ანალიტიკისთვის შეიძლება დამუშავდეს მოწყობილობის, ბრაუზერისა და გვერდის გამოყენების ტექნიკური მონაცემებიც.', 'We may receive your name, phone number, email, message, selected service, and technical request details. Device, browser, and page usage data may also be processed for security and analytics.', 'Мы можем получать ваше имя, телефон, email, текст сообщения, выбранную услугу и детали технического запроса. Для безопасности и аналитики также могут обрабатываться технические данные устройства, браузера и использования страниц.'),
            $this->entry('privacy.use.title', 'როგორ ვიყენებთ მონაცემებს', 'How we use information', 'Как мы используем данные'),
            $this->entry('privacy.use.body', 'მონაცემებს ვიყენებთ მოთხოვნაზე პასუხისთვის, კონსულტაციისა და შეთავაზების მოსამზადებლად, მომსახურების გასაწევად, ხარისხისა და უსაფრთხოების გასაუმჯობესებლად და კანონით გათვალისწინებული ვალდებულებების შესასრულებლად.', 'We use information to answer requests, prepare consultations and quotations, deliver services, improve quality and security, and meet legal obligations.', 'Мы используем данные для ответа на запросы, подготовки консультаций и предложений, оказания услуг, повышения качества и безопасности и выполнения законных обязанностей.'),
            $this->entry('privacy.cookies.title', 'Cookies, ანალიტიკა და რეკლამა', 'Cookies, analytics, and advertising', 'Cookies, аналитика и реклама'),
            $this->entry('privacy.cookies.body', 'ანალიტიკური და სარეკლამო ტექნოლოგიები იტვირთება მხოლოდ შესაბამისი თანხმობის შემდეგ. არჩევანის შეცვლა შეგიძლიათ ბრაუზერის მონაცემების გასუფთავებით ან საიტზე ხელახლა არჩევით.', 'Analytics and advertising technologies are loaded only after the relevant consent. You can change your choice by clearing browser data or selecting again on the website.', 'Аналитические и рекламные технологии загружаются только после соответствующего согласия. Изменить выбор можно, очистив данные браузера или выбрав заново на сайте.'),
            $this->entry('privacy.sharing.title', 'მონაცემების გაზიარება', 'Information sharing', 'Передача данных'),
            $this->entry('privacy.sharing.body', 'პერსონალურ მონაცემებს არ ვყიდით. საჭიროების შემთხვევაში მონაცემები შეიძლება დამუშავდეს სანდო ტექნიკურ მომწოდებლებთან მხოლოდ მომსახურების, ჰოსტინგის, ელფოსტის, ანალიტიკის ან სამართლებრივი მოთხოვნის შესრულების მიზნით.', 'We do not sell personal data. Information may be processed by trusted technical providers only for service delivery, hosting, email, analytics, or legal compliance.', 'Мы не продаем персональные данные. Информация может обрабатываться надежными техническими поставщиками только для оказания услуг, хостинга, email, аналитики или выполнения требований закона.'),
            $this->entry('privacy.retention.title', 'შენახვა და უსაფრთხოება', 'Retention and security', 'Хранение и безопасность'),
            $this->entry('privacy.retention.body', 'მონაცემებს ვინახავთ მხოლოდ იმ ვადით, რაც საჭიროა მოთხოვნისა და მომსახურების სამართავად ან კანონით გათვალისწინებული ვალდებულებისთვის. ვიყენებთ გონივრულ ტექნიკურ და ორგანიზაციულ უსაფრთხოების ზომებს.', 'We retain information only as long as needed to manage the request and service or meet legal duties, and use reasonable technical and organizational safeguards.', 'Мы храним данные только столько, сколько необходимо для обработки запроса и услуги или выполнения законных обязанностей, и применяем разумные технические и организационные меры защиты.'),
            $this->entry('privacy.rights.title', 'თქვენი უფლებები', 'Your rights', 'Ваши права'),
            $this->entry('privacy.rights.body', 'შეგიძლიათ მოითხოვოთ თქვენი მონაცემების შესახებ ინფორმაცია, გასწორება ან წაშლა, დამუშავების შეზღუდვა და თანხმობის გაუქმება, თუ სხვა სამართლებრივი საფუძველი არ არსებობს.', 'You may request access, correction or deletion, restriction of processing, and withdrawal of consent where no other legal basis applies.', 'Вы можете запросить доступ, исправление или удаление данных, ограничение обработки и отзыв согласия, если нет другого законного основания.'),
            $this->entry('privacy.contact.title', 'კონტაქტი კონფიდენციალურობის საკითხებზე', 'Privacy contact', 'Контакты по вопросам конфиденциальности'),
            $this->entry('privacy.contact.body', 'კონფიდენციალურობის საკითხზე დაგვიკავშირდით საიტზე მითითებული ტელეფონით ან ელფოსტით.', 'For privacy questions, contact us using the phone number or email shown on the website.', 'По вопросам конфиденциальности свяжитесь с нами по телефону или email, указанным на сайте.'),
        ];
    }

    /** @return array{key:string,ka:string,en:string,ru:string} */
    private function entry(string $key, string $ka, string $en, string $ru): array
    {
        return compact('key', 'ka', 'en', 'ru');
    }
}
