<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\SiteSetting;
use App\Support\MultilingualContent;
use Illuminate\Database\Seeder;

class PageContentSeeder extends Seeder
{
    private const PRIMARY_PHONE = '571 430 169';

    private const SECONDARY_PHONE = '557 316 310';

    public function run(): void
    {
        $this->seedBranding();
        $this->seedContactDetails();
        $this->seedTranslations();
        $this->seedFrequentlyAskedQuestions();
    }

    private function seedBranding(): void
    {
        $setting = SiteSetting::query()->firstOrCreate(
            ['key' => 'branding'],
            [
                'group' => 'general',
                'value' => [],
                'is_public' => true,
            ],
        );
        $value = is_array($setting->value) ? $setting->value : [];

        $value['site_name'] = filled($value['site_name'] ?? null)
            ? trim((string) $value['site_name'])
            : 'SafeTech';
        $value['tagline'] = filled($value['tagline'] ?? null)
            ? trim((string) $value['tagline'])
            : 'IT ინფრასტრუქტურა, უსაფრთხოების სისტემები და ტექნიკური მხარდაჭერა';

        $setting->forceFill([
            'group' => 'general',
            'value' => $value,
            'is_public' => true,
        ])->save();
    }

    private function seedContactDetails(): void
    {
        $setting = SiteSetting::query()->firstOrCreate(
            ['key' => 'contact'],
            [
                'group' => 'general',
                'value' => [],
                'is_public' => true,
            ],
        );
        $value = is_array($setting->value) ? $setting->value : [];
        $existingPhones = collect(is_array($value['phones'] ?? null) ? $value['phones'] : [])
            ->map(fn (mixed $phone): string => is_array($phone)
                ? trim((string) ($phone['value'] ?? ''))
                : trim((string) $phone))
            ->filter()
            ->values()
            ->all();

        $value['phone'] = self::PRIMARY_PHONE;
        $value['phones'] = collect([
            self::PRIMARY_PHONE,
            self::SECONDARY_PHONE,
            ...$existingPhones,
        ])->filter()->unique()->values()->all();
        $value['whatsapp'] = filled($value['whatsapp'] ?? null)
            ? trim((string) $value['whatsapp'])
            : '571430169';
        $value['whatsapp_message'] = filled($value['whatsapp_message'] ?? null)
            ? (string) $value['whatsapp_message']
            : 'გამარჯობა, მსურს SafeTech-ის სერვისზე კონსულტაცია.';
        $value['whatsapp_enabled'] = array_key_exists('whatsapp_enabled', $value)
            ? $value['whatsapp_enabled']
            : true;

        $setting->forceFill([
            'group' => 'general',
            'value' => $value,
            'is_public' => true,
        ])->save();
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

        foreach ($this->translationEntries() as $entry) {
            $key = $entry['key'];
            $map[$key] ??= ['ka' => '', 'en' => '', 'ru' => ''];

            foreach (MultilingualContent::LOCALES as $locale) {
                if (blank($map[$key][$locale] ?? null) && filled($entry[$locale] ?? null)) {
                    $map[$key][$locale] = trim((string) $entry[$locale]);
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

    private function seedFrequentlyAskedQuestions(): void
    {
        foreach ($this->faqDefinitions() as $index => $definition) {
            $record = Faq::query()->firstOrNew([
                'service_id' => null,
                'context' => 'contact',
                'sort_order' => 100 + $index,
            ]);

            if (! $record->exists) {
                $record->fill([
                    'question' => $definition['question']['ka'],
                    'answer' => $definition['answer']['ka'],
                    'is_active' => true,
                    'translations' => [
                        'fields' => [
                            'question' => $definition['question'],
                            'answer' => $definition['answer'],
                        ],
                    ],
                ]);
            } else {
                if (blank($record->question)) {
                    $record->question = $definition['question']['ka'];
                }

                if (blank($record->answer)) {
                    $record->answer = $definition['answer']['ka'];
                }

                $translations = is_array($record->translations) ? $record->translations : [];
                $translations['fields'] ??= [];

                foreach (['question', 'answer'] as $field) {
                    $translations['fields'][$field] ??= [];

                    foreach (MultilingualContent::LOCALES as $locale) {
                        if (blank($translations['fields'][$field][$locale] ?? null)) {
                            $translations['fields'][$field][$locale] = $definition[$field][$locale];
                        }
                    }
                }

                $record->translations = $translations;
            }

            $record->save();
        }
    }

    /** @return array<int, array{key:string,ka:string,en:string,ru:string}> */
    private function translationEntries(): array
    {
        return [
            self::entry('nav.home', 'მთავარი', 'Home', 'Главная'),
            self::entry('nav.about', 'ჩვენ შესახებ', 'About', 'О нас'),
            self::entry('nav.services', 'სერვისები', 'Services', 'Услуги'),
            self::entry('nav.projects', 'პროექტები', 'Projects', 'Проекты'),
            self::entry('nav.contact', 'კონტაქტი', 'Contact', 'Контакты'),
            self::entry('nav.consultation', 'კონსულტაცია', 'Consultation', 'Консультация'),
            self::entry('nav.region', 'მთავარი ნავიგაცია', 'Main navigation', 'Основная навигация'),
            self::entry('nav.mobile.open', 'მენიუს გახსნა', 'Open menu', 'Открыть меню'),

            self::entry('footer.tagline', 'IT ინფრასტრუქტურა, უსაფრთხოების სისტემები და ტექნიკური მხარდაჭერა კერძო და ბიზნეს ობიექტებისთვის.', 'IT infrastructure, security systems, and technical support for homes and businesses.', 'IT-инфраструктура, системы безопасности и техническая поддержка для частных и коммерческих объектов.'),
            self::entry('footer.company.title', 'კომპანია', 'Company', 'Компания'),
            self::entry('footer.services.title', 'სერვისები', 'Services', 'Услуги'),
            self::entry('footer.contact.title', 'კონტაქტი', 'Contact', 'Контакты'),
            self::entry('footer.contact.address', 'საქართველო — ადგილზე მომსახურება შეთანხმებით', 'Georgia — on-site service by appointment', 'Грузия — выездное обслуживание по договоренности'),
            self::entry('footer.copy.rights', 'ყველა უფლება დაცულია.', 'All rights reserved.', 'Все права защищены.'),

            self::entry('forms.choose', 'აირჩიეთ', 'Choose', 'Выберите'),
            self::entry('forms.contactHint', 'მიუთითეთ ტელეფონი ან ელფოსტა, რათა დაგიკავშირდეთ.', 'Provide a phone number or email so we can contact you.', 'Укажите телефон или email, чтобы мы могли связаться с вами.'),
            self::entry('forms.fullName', 'სახელი და გვარი', 'Full name', 'Имя и фамилия'),
            self::entry('forms.firstName', 'სახელი', 'First name', 'Имя'),
            self::entry('forms.lastName', 'გვარი', 'Last name', 'Фамилия'),
            self::entry('forms.phone', 'ტელეფონის ნომერი', 'Phone number', 'Номер телефона'),
            self::entry('forms.details', 'მოთხოვნის დეტალები', 'Project details', 'Детали задачи'),
            self::entry('forms.submitRequest', 'მოთხოვნის გაგზავნა', 'Send request', 'Отправить запрос'),
            self::entry('common.cancel', 'გაუქმება', 'Cancel', 'Отмена'),
            self::entry('common.details', 'დეტალურად', 'View details', 'Подробнее'),
            self::entry('common.readMore', 'ვრცლად', 'Read more', 'Подробнее'),
            self::entry('consultation.form.requiredHint', 'ყველა ველი სავალდებულოა.', 'All fields are required.', 'Все поля обязательны.'),
            self::entry('consultation.form.submit', 'კონსულტაციის მიღება', 'Get consultation', 'Получить консультацию'),
            self::entry('consultation.form.validation', 'შეავსეთ ყველა სავალდებულო ველი და დაეთანხმეთ მონაცემების დამუშავებას.', 'Complete every required field and accept data processing.', 'Заполните все обязательные поля и подтвердите согласие на обработку данных.'),
            self::entry('consultation.form.address', 'ქალაქი / მომსახურების მისამართი', 'City / service address', 'Город / адрес оказания услуги'),
            self::entry('consultation.cta.note', 'შეავსეთ ყველა სავალდებულო ველი და ჩვენ დაგიკავშირდებით.', 'Complete all required fields and we will contact you.', 'Заполните все обязательные поля, и мы свяжемся с вами.'),
            self::entry('forms.company', 'კომპანია ან ობიექტი', 'Company or property', 'Компания или объект'),
            self::entry('forms.phoneNumber', 'ტელეფონის ნომერი', 'Phone number', 'Номер телефона'),
            self::entry('forms.email', 'ელფოსტა', 'Email', 'Электронная почта'),
            self::entry('forms.service', 'სერვისი', 'Service', 'Услуга'),
            self::entry('forms.message', 'აღწერეთ დავალება ან პრობლემა', 'Describe the task or issue', 'Опишите задачу или проблему'),
            self::entry('forms.generalInquiry', 'სერვისი ჯერ არ აგირჩევიათ — მოგვწერეთ ზოგადი მოთხოვნა.', 'No service selected yet — send us a general inquiry.', 'Услуга пока не выбрана — отправьте общий запрос.'),
            self::entry('forms.submitting', 'იგზავნება…', 'Sending…', 'Отправка…'),
            self::entry('forms.send', 'მოთხოვნის გაგზავნა', 'Send request', 'Отправить запрос'),
            self::entry('forms.privacy', 'ვეთანხმები ჩემი საკონტაქტო მონაცემების გამოყენებას მოთხოვნაზე პასუხისთვის.', 'I agree to the use of my contact details to respond to this request.', 'Я согласен на использование контактных данных для ответа на запрос.'),
            self::entry('forms.privacyPolicyLink', 'პოლიტიკის ნახვა', 'View policy', 'Открыть политику'),
            self::entry('forms.validation.contact', 'მიუთითეთ ტელეფონი ან ელფოსტა.', 'Provide a phone number or email.', 'Укажите телефон или email.'),
            self::entry('forms.error.submit', 'მოთხოვნის გაგზავნა ვერ მოხერხდა. სცადეთ თავიდან.', 'The request could not be sent. Please try again.', 'Не удалось отправить запрос. Попробуйте еще раз.'),
            self::entry('forms.error.network', 'ქსელთან კავშირი ვერ დამყარდა. მოგვწერეთ ან დაგვირეკეთ.', 'A network connection could not be established. Message or call us.', 'Не удалось подключиться к сети. Напишите или позвоните нам.'),
            self::entry('forms.chooseService', 'აირჩიეთ სერვისი', 'Select a service', 'Выберите услугу'),
            self::entry('forms.servicesUnavailable', 'სერვისების სია დროებით მიუწვდომელია', 'The service list is temporarily unavailable', 'Список услуг временно недоступен'),
            self::entry('forms.success.submit', 'მოთხოვნა მიღებულია. მალე დაგიკავშირდებით.', 'Your request has been received. We will contact you shortly.', 'Запрос получен. Мы скоро свяжемся с вами.'),
            self::entry('filters.all', 'ყველა', 'All', 'Все'),
            self::entry('filters.aria', 'კონტენტის გაფილტვრა', 'Filter content', 'Фильтр контента'),
            self::entry('projects.completed', 'დასრულებული', 'Completed', 'Завершён'),
            self::entry('projects.video.open', 'ვიდეოს ნახვა', 'Watch video', 'Смотреть видео'),
            self::entry('floating.whatsapp.aria', 'WhatsApp-ში მოგვწერეთ', 'Message us on WhatsApp', 'Напишите нам в WhatsApp'),
            self::entry('floating.whatsapp.tooltip', 'WhatsApp', 'WhatsApp', 'WhatsApp'),
            self::entry('error.retry', 'ხელახლა ცდა', 'Try again', 'Попробовать снова'),
            self::entry('notFound.title', 'გვერდი ვერ მოიძებნა', 'Page not found', 'Страница не найдена'),
            self::entry('notFound.description', 'მითითებული მისამართი არასწორია ან გვერდი გადატანილია.', 'The address may be incorrect or the page may have moved.', 'Возможно, адрес указан неверно или страница была перемещена.'),
            self::entry('notFound.home', 'მთავარ გვერდზე დაბრუნება', 'Return home', 'Вернуться на главную'),
            self::entry('notFound.contact', 'დაგვიკავშირდით', 'Contact us', 'Связаться с нами'),
            self::entry('ai.assistant.greeting', 'გამარჯობა! მე ვარ SafeTech-ის AI კონსულტანტი. მითხარით რა სისტემის ან IT მომსახურების შერჩევაში დაგეხმაროთ.', 'Hello! I’m SafeTech’s AI consultant. Tell me what security system or IT service you need help choosing.', 'Здравствуйте! Я AI-консультант SafeTech. Расскажите, какую систему безопасности или IT-услугу вы хотите подобрать.'),
            self::entry('ai.assistant.title', 'SafeTech AI კონსულტანტი', 'SafeTech AI Consultant', 'AI-консультант SafeTech'),
            self::entry('ai.assistant.subtitle', 'სერვისის შერჩევა • კონსულტაცია • შეთავაზება', 'Service selection • Consultation • Quote', 'Подбор услуги • Консультация • Предложение'),
            self::entry('ai.assistant.placeholder', 'მაგ: მინდა 8 კამერა კერძო სახლში…', 'For example: I need 8 cameras for a house…', 'Например: нужно 8 камер для частного дома…'),
            self::entry('ai.assistant.consent', 'ვეთანხმები ჩატის მონაცემების დამუშავებას კონსულტაციისა და მოთხოვნის დასამუშავებლად.', 'I agree to processing chat data for consultation and handling my request.', 'Я согласен на обработку данных чата для консультации и обработки запроса.'),
            self::entry('ai.assistant.consentRequired', 'ჩატის გასაგზავნად მონიშნეთ მონაცემების დამუშავებაზე თანხმობა.', 'Please accept data processing before sending a chat message.', 'Перед отправкой сообщения подтвердите согласие на обработку данных.'),
            self::entry('ai.assistant.send', 'გაგზავნა', 'Send', 'Отправить'),
            self::entry('ai.assistant.close', 'დახურვა', 'Close', 'Закрыть'),
            self::entry('ai.assistant.open', 'AI კონსულტანტის გახსნა', 'Open AI consultant', 'Открыть AI-консультанта'),
            self::entry('ai.assistant.typing', 'პასუხს ვამზადებ…', 'Preparing an answer…', 'Готовлю ответ…'),
            self::entry('ai.assistant.helpful', 'სასარგებლო პასუხი', 'Helpful answer', 'Полезный ответ'),
            self::entry('ai.assistant.notHelpful', 'არასასარგებლო პასუხი', 'Not helpful', 'Неполезный ответ'),
            self::entry('ai.assistant.error', 'AI კონსულტანტთან დაკავშირება ვერ მოხერხდა.', 'Could not reach the AI consultant.', 'Не удалось связаться с AI-консультантом.'),

            self::entry('consultation.modal.eyebrow', 'ტექნიკური კონსულტაცია', 'Technical consultation', 'Техническая консультация'),
            self::entry('consultation.modal.title', 'მოგვიყევით თქვენი ამოცანის შესახებ', 'Tell us about your project', 'Расскажите о вашей задаче'),
            self::entry('consultation.modal.description', 'მიუთითეთ მოთხოვნა და საკონტაქტო ინფორმაცია. შევაფასებთ დავალებას და შემოგთავაზებთ შესაბამის გადაწყვეტას.', 'Share your requirements and contact details. We will assess the task and propose a suitable solution.', 'Укажите требования и контактные данные. Мы оценим задачу и предложим подходящее решение.'),
            self::entry('consultation.modal.close', 'ფანჯრის დახურვა', 'Close window', 'Закрыть окно'),

            self::entry('home.hero.eyebrow', 'IT და უსაფრთხოების სისტემები საქართველოში', 'IT and security systems in Georgia', 'IT и системы безопасности в Грузии'),
            self::entry('home.hero.titlePrefix', 'ტექნოლოგიური ინფრასტრუქტურა', 'Technology infrastructure', 'Технологическая инфраструктура'),
            self::entry('home.hero.titleAccent', 'თქვენი ობიექტისთვის', 'built for your property', 'для вашего объекта'),
            self::entry('home.hero.description', 'ვგეგმავთ, ვამონტაჟებთ და ვმართავთ კამერებს, დაშვების სისტემებს, ქსელებს, POS სისტემებსა და კომპიუტერულ ინფრასტრუქტურას.', 'We design, install, and support CCTV, access control, networks, POS systems, and computer infrastructure.', 'Проектируем, устанавливаем и обслуживаем видеонаблюдение, контроль доступа, сети, POS и компьютерную инфраструктуру.'),
            self::entry('home.hero.primaryCta', 'უფასო კონსულტაციის მოთხოვნა', 'Request a consultation', 'Запросить консультацию'),
            self::entry('home.hero.secondaryCta', 'სერვისების ნახვა', 'Explore services', 'Посмотреть услуги'),
            self::entry('home.hero.imageAlt', 'SafeTech-ის IT და უსაფრთხოების სისტემები', 'SafeTech IT and security systems', 'IT и системы безопасности SafeTech'),
            self::entry('home.trust.title', 'ტექნოლოგიები და ბრენდები, რომლებთანაც ვმუშაობთ', 'Technologies and brands we work with', 'Технологии и бренды, с которыми мы работаем'),
            self::entry('home.services.eyebrow', 'სრული ტექნიკური მომსახურება', 'Complete technical service', 'Комплексное техническое обслуживание'),
            self::entry('home.services.title', 'ერთი პარტნიორი თქვენი IT და უსაფრთხოებისთვის', 'One partner for IT and security', 'Один партнер для IT и безопасности'),
            self::entry('home.services.description', 'აირჩიეთ საჭირო სერვისი — თითოეული გადაწყვეტა იგეგმება ობიექტის, დატვირთვისა და ბიუჯეტის მიხედვით.', 'Choose the service you need. Every solution is planned around the site, workload, and budget.', 'Выберите нужную услугу. Каждое решение планируется с учетом объекта, нагрузки и бюджета.'),
            self::entry('home.infrastructure.eyebrow', 'სწორად დაგეგმილი სისტემა', 'Properly planned systems', 'Правильно спроектированные системы'),
            self::entry('home.infrastructure.title', 'ინფრასტრუქტურა, რომელიც მზად არის ზრდისთვის', 'Infrastructure ready to grow', 'Инфраструктура, готовая к развитию'),
            self::entry('home.infrastructure.description', 'კაბელიდან და რეკიდან პროგრამულ კონფიგურაციამდე — ყველა კომპონენტი ერთიან, მართვად სისტემად მუშაობს.', 'From cabling and racks to software configuration, every component works as one manageable system.', 'От кабелей и шкафов до программной настройки — все компоненты работают как единая управляемая система.'),
            self::entry('home.infrastructure.imageAlt', 'ქსელური და უსაფრთხოების ინფრასტრუქტურის დაგეგმვა', 'Network and security infrastructure planning', 'Проектирование сетевой инфраструктуры и систем безопасности'),
            self::entry('home.infrastructure.items.0.title', 'წინასწარი პროექტირება', 'Upfront planning', 'Предварительное проектирование'),
            self::entry('home.infrastructure.items.0.description', 'ვადგენთ წერტილებს, მარშრუტებს, დატვირთვას, მოწყობილობებსა და გაფართოების მარაგს.', 'We define points, routes, capacity, equipment, and room for expansion.', 'Определяем точки, трассы, нагрузку, оборудование и запас для расширения.'),
            self::entry('home.infrastructure.items.1.title', 'სტრუქტურირებული ქსელი', 'Structured network', 'Структурированная сеть'),
            self::entry('home.infrastructure.items.1.description', 'CAT6, Patch Panel, როზეტები, სვიჩები, VLAN და Wi-Fi ერთიანი სქემით.', 'CAT6, patch panels, outlets, switches, VLAN, and Wi-Fi under one design.', 'CAT6, патч-панели, розетки, коммутаторы, VLAN и Wi-Fi по единой схеме.'),
            self::entry('home.infrastructure.items.2.title', 'ტესტირება და მონიტორინგი', 'Testing and monitoring', 'Тестирование и мониторинг'),
            self::entry('home.infrastructure.items.2.description', 'ჩაბარებამდე ვამოწმებთ კავშირებს, ჩანაწერს, წვდომებსა და სისტემის სტაბილურობას.', 'Before handover, we verify connectivity, recording, access, and system stability.', 'Перед сдачей проверяем соединения, запись, доступ и стабильность системы.'),
            self::entry('home.projects.eyebrow', 'შესრულებული სამუშაოები', 'Delivered work', 'Выполненные работы'),
            self::entry('home.projects.title', 'გადაწყვეტილებები რეალური ობიექტებისთვის', 'Solutions for real properties', 'Решения для реальных объектов'),
            self::entry('home.projects.description', 'იხილეთ ჩვენ მიერ დაგეგმილი და შესრულებული IT, ქსელური და უსაფრთხოების პროექტები.', 'Explore IT, network, and security projects designed and delivered by our team.', 'Посмотрите IT-, сетевые и охранные проекты, спроектированные и реализованные нашей командой.'),
            self::entry('home.projects.action', 'ყველა პროექტის ნახვა', 'View all projects', 'Посмотреть все проекты'),
            self::entry('home.testimonials.eyebrow', 'კლიენტების გამოცდილება', 'Client feedback', 'Отзывы клиентов'),
            self::entry('home.testimonials.title', 'რეალური შეფასებები შესრულებული სამუშაოს შემდეგ', 'Real feedback after completed work', 'Реальные отзывы после выполненных работ'),
            self::entry('home.testimonials.description', 'ვაქვეყნებთ მხოლოდ იმ შეფასებებს, რომლებსაც კლიენტი თავად გვიზიარებს და ადმინისტრატორი ამოწმებს.', 'We publish only feedback shared by clients and reviewed by our team.', 'Мы публикуем только отзывы, которыми поделились клиенты и которые проверены нашей командой.'),
            self::entry('home.testimonials.googleReviews', 'Google-ზე შეფასებების ნახვა', 'See reviews on Google', 'Смотреть отзывы в Google'),
            self::entry('home.why.eyebrow', 'რატომ SafeTech', 'Why SafeTech', 'Почему SafeTech'),
            self::entry('home.why.title', 'ტექნიკური პასუხისმგებლობა ყველა ეტაპზე', 'Technical ownership at every stage', 'Техническая ответственность на каждом этапе'),
            self::entry('home.why.description', 'ჩვენი მიზანია არა მხოლოდ მოწყობილობის დაყენება, არამედ სტაბილური, უსაფრთხო და მარტივად სამართავი შედეგის ჩაბარება.', 'Our goal is not just installation, but a stable, secure, and manageable result.', 'Наша цель — не просто монтаж, а стабильный, безопасный и удобный в управлении результат.'),
            self::entry('home.why.items.0.title', 'თავსებადი კომპონენტები', 'Compatible components', 'Совместимые компоненты'),
            self::entry('home.why.items.0.description', 'მოწყობილობები შეირჩევა ერთმანეთთან, დატვირთვასა და რეალურ მოთხოვნებთან შესაბამისობით.', 'Equipment is selected for compatibility, capacity, and real requirements.', 'Оборудование подбирается по совместимости, нагрузке и реальным требованиям.'),
            self::entry('home.why.items.1.title', 'უსაფრთხო კონფიგურაცია', 'Secure configuration', 'Безопасная настройка'),
            self::entry('home.why.items.1.description', 'ვცვლით საწყის პაროლებს, ვამართავთ წვდომებს, განახლებებსა და ქსელის დაცვას.', 'We configure credentials, access, updates, and network protection.', 'Настраиваем учетные данные, доступ, обновления и защиту сети.'),
            self::entry('home.why.items.2.title', 'ერთიანი ინფრასტრუქტურა', 'Integrated infrastructure', 'Единая инфраструктура'),
            self::entry('home.why.items.2.description', 'ქსელი, კამერები, დაშვება, UPS და სერვერები მუშაობს შეთანხმებული არქიტექტურით.', 'Networks, CCTV, access, UPS, and servers operate under one architecture.', 'Сеть, камеры, доступ, UPS и серверы работают по единой архитектуре.'),
            self::entry('home.why.items.3.title', 'შემდგომი მხარდაჭერა', 'Ongoing support', 'Дальнейшая поддержка'),
            self::entry('home.why.items.3.description', 'ჩაბარების შემდეგაც ხელმისაწვდომია დიაგნოსტიკა, ცვლილებები და ტექნიკური დახმარება.', 'Diagnostics, changes, and technical support remain available after handover.', 'После сдачи доступны диагностика, изменения и техническая помощь.'),
            self::entry('home.why.items.4.title', 'დოკუმენტირებული მონტაჟი', 'Documented installation', 'Документированный монтаж'),
            self::entry('home.why.items.4.description', 'პორტების, კაბელებისა და მოწყობილობების მარკირება ამარტივებს მომსახურებასა და გაფართოებას.', 'Port, cable, and device labeling simplifies maintenance and expansion.', 'Маркировка портов, кабелей и устройств упрощает обслуживание и расширение.'),
            self::entry('home.why.items.5.title', 'შედეგის შემოწმება', 'Verified results', 'Проверенный результат'),
            self::entry('home.why.items.5.description', 'სისტემა იტესტება დატვირთვაზე და მომხმარებელს გადაეცემა გამოყენების ინსტრუქციით.', 'The system is tested under load and handed over with operating guidance.', 'Система тестируется под нагрузкой и передается с инструкцией по эксплуатации.'),
            self::entry('home.industries.eyebrow', 'ვის ვემსახურებით', 'Who we serve', 'Кому мы помогаем'),
            self::entry('home.industries.title', 'გადაწყვეტილებები სხვადასხვა ტიპის ობიექტისთვის', 'Solutions for every type of property', 'Решения для разных типов объектов'),
            self::entry('home.industries.description', 'კერძო სახლიდან მრავალფილიალიან ბიზნესამდე — სისტემა იგეგმება სივრცისა და პროცესის მიხედვით.', 'From private homes to multi-site businesses, each system is designed around the space and workflow.', 'От частного дома до сети объектов — система проектируется под пространство и процессы.'),
            self::entry('home.industries.items.0', 'ოფისები და ბიზნეს ცენტრები', 'Offices and business centers', 'Офисы и бизнес-центры'),
            self::entry('home.industries.items.1', 'მაღაზიები, კაფეები და სასტუმროები', 'Retail, cafes, and hotels', 'Магазины, кафе и отели'),
            self::entry('home.industries.items.2', 'საწყობები და ლოგისტიკა', 'Warehouses and logistics', 'Склады и логистика'),
            self::entry('home.industries.items.3', 'საწარმოები და კერძო ობიექტები', 'Industrial and private properties', 'Производственные и частные объекты'),
            self::entry('home.cta.eyebrow', 'დაიწყეთ სწორი დაგეგმვით', 'Start with the right plan', 'Начните с правильного плана'),
            self::entry('home.cta.title', 'მიიღეთ თქვენი ამოცანის ტექნიკური შეფასება', 'Get a technical assessment of your project', 'Получите техническую оценку вашей задачи'),
            self::entry('home.cta.description', 'დატოვეთ საკონტაქტო ინფორმაცია და მოკლედ აღწერეთ საჭიროება. დაგიკავშირდებით დეტალების დასაზუსტებლად.', 'Leave your contact details and briefly describe the requirement. We will contact you to clarify the details.', 'Оставьте контакты и кратко опишите задачу. Мы свяжемся с вами для уточнения деталей.'),
            self::entry('home.cta.emailLabel', 'ელფოსტა', 'Email', 'Электронная почта'),
            self::entry('home.cta.emailPlaceholder', 'name@example.com', 'name@example.com', 'name@example.com'),
            self::entry('home.cta.submit', 'კონსულტაციის მოთხოვნა', 'Request consultation', 'Запросить консультацию'),
            self::entry('home.cta.note', 'ზუსტი ღირებულება განისაზღვრება მოთხოვნებისა და ობიექტის შეფასების შემდეგ.', 'Final pricing is determined after reviewing the requirements and site.', 'Точная стоимость определяется после оценки требований и объекта.'),

            self::entry('about.hero.title', 'SafeTech — ტექნიკური პარტნიორი თქვენი ინფრასტრუქტურისთვის', 'SafeTech — your technical infrastructure partner', 'SafeTech — технический партнер вашей инфраструктуры'),
            self::entry('about.hero.description', 'ვაერთიანებთ IT მხარდაჭერას, ქსელურ ინფრასტრუქტურასა და უსაფრთხოების სისტემებს ერთ პასუხისმგებელ მომსახურებაში.', 'We combine IT support, network infrastructure, and security systems under one accountable service.', 'Объединяем IT-поддержку, сетевую инфраструктуру и системы безопасности в одном ответственном сервисе.'),
            self::entry('about.hero.cta.primary', 'დაგვიკავშირდით', 'Contact us', 'Связаться с нами'),
            self::entry('about.hero.cta.secondary', 'პროექტების ნახვა', 'View projects', 'Посмотреть проекты'),
            self::entry('about.story.title', 'როგორ ვმუშაობთ', 'How we work', 'Как мы работаем'),
            self::entry('about.story.paragraph.0', 'SafeTech-ის მიდგომა იწყება მოთხოვნის სწორად გაგებით. ჯერ ვაფასებთ ობიექტს, დატვირთვასა და რისკებს, შემდეგ ვადგენთ თავსებად ტექნიკურ გეგმას.', 'The SafeTech approach starts with understanding the requirement. We assess the site, workload, and risks before defining a compatible technical plan.', 'Подход SafeTech начинается с понимания задачи. Сначала оцениваем объект, нагрузку и риски, затем формируем совместимый технический план.'),
            self::entry('about.story.paragraph.1', 'მონტაჟის შემდეგ ვასრულებთ კონფიგურაციას, ტესტირებასა და მომხმარებლის ინსტრუქტაჟს, რათა სისტემა რეალურად გამოსაყენებელი და მარტივად სამართავი იყოს.', 'After installation, we configure, test, and explain the system so it is practical and easy to manage.', 'После монтажа выполняем настройку, тестирование и обучение, чтобы система была практичной и удобной в управлении.'),
            self::entry('about.story.imageAlt', 'SafeTech-ის ტექნიკური დაგეგმვა და მონტაჟი', 'SafeTech technical planning and installation', 'Техническое проектирование и монтаж SafeTech'),
            self::entry('about.who.title', 'ვინ ვართ', 'Who we are', 'Кто мы'),
            self::entry('about.who.description', 'პრაქტიკულ გამოცდილებაზე დაფუძნებული IT და უსაფრთხოების სერვისი კერძო და ბიზნეს მომხმარებლებისთვის.', 'A practical IT and security service for homes and businesses.', 'Практический IT-сервис и системы безопасности для частных и коммерческих клиентов.'),
            self::entry('about.who.item.0.title', 'უსაფრთხოების სისტემები', 'Security systems', 'Системы безопасности'),
            self::entry('about.who.item.0.description', 'კამერები, ვიდეოდომოფონი, დაშვების კონტროლი და ავტომატური შლაგბაუმები.', 'CCTV, video intercom, access control, and automatic barriers.', 'Видеонаблюдение, домофоны, контроль доступа и автоматические шлагбаумы.'),
            self::entry('about.who.item.1.title', 'ქსელური ინფრასტრუქტურა', 'Network infrastructure', 'Сетевая инфраструктура'),
            self::entry('about.who.item.1.description', 'კაბელირება, როზეტები, Patch Panel, რეკი, როუტერები, Wi-Fi და VLAN.', 'Cabling, outlets, patch panels, racks, routers, Wi-Fi, and VLAN.', 'Кабели, розетки, патч-панели, шкафы, роутеры, Wi-Fi и VLAN.'),
            self::entry('about.who.item.2.title', 'IT მხარდაჭერა', 'IT support', 'IT-поддержка'),
            self::entry('about.who.item.2.description', 'კომპიუტერები, პროგრამები, POS სისტემები, პრინტერები და ბიზნესის ტექნიკური მხარდაჭერა.', 'Computers, software, POS systems, printers, and business IT support.', 'Компьютеры, программы, POS, принтеры и техническая поддержка бизнеса.'),
            self::entry('about.what.item.0.index', '01', '01', '01'),
            self::entry('about.what.item.0.title', 'ვაფასებთ', 'Assess', 'Оцениваем'),
            self::entry('about.what.item.0.description', 'ვადგენთ ამოცანას, არსებულ ინფრასტრუქტურას, რისკებსა და რეალურ საჭიროებებს.', 'We define the task, existing infrastructure, risks, and real needs.', 'Определяем задачу, текущую инфраструктуру, риски и реальные потребности.'),
            self::entry('about.what.item.1.index', '02', '02', '02'),
            self::entry('about.what.item.1.title', 'ვგეგმავთ', 'Plan', 'Планируем'),
            self::entry('about.what.item.1.description', 'ვარჩევთ თავსებად მოწყობილობებს, კაბელის მარშრუტებს, კვებასა და პროგრამულ სქემას.', 'We select compatible equipment, cable routes, power, and software architecture.', 'Подбираем совместимое оборудование, трассы, питание и программную схему.'),
            self::entry('about.what.item.2.index', '03', '03', '03'),
            self::entry('about.what.item.2.title', 'ვაბარებთ', 'Deliver', 'Сдаем'),
            self::entry('about.what.item.2.description', 'ვამონტაჟებთ, ვაკონფიგურირებთ, ვტესტავთ და მომხმარებელს გამართულ სისტემას ვაბარებთ.', 'We install, configure, test, and hand over a fully operational system.', 'Монтируем, настраиваем, тестируем и сдаем полностью рабочую систему.'),
            self::entry('about.how.title', 'სამუშაო პროცესი', 'Our process', 'Процесс работы'),
            self::entry('about.how.item.0.title', 'კონსულტაცია და მოთხოვნის დაზუსტება', 'Consultation and requirements', 'Консультация и уточнение требований'),
            self::entry('about.how.item.0.description', 'ვაგროვებთ ინფორმაციას ობიექტზე, მომხმარებლებსა და სასურველ შედეგზე.', 'We gather information about the site, users, and expected outcome.', 'Собираем информацию об объекте, пользователях и ожидаемом результате.'),
            self::entry('about.how.item.1.title', 'ტექნიკური გეგმა და ფასი', 'Technical plan and pricing', 'Технический план и стоимость'),
            self::entry('about.how.item.1.description', 'ვადგენთ კომპონენტებს, სამუშაოს მოცულობას, ვადებსა და საორიენტაციო ღირებულებას.', 'We define components, scope, schedule, and indicative pricing.', 'Определяем компоненты, объем работ, сроки и ориентировочную стоимость.'),
            self::entry('about.how.item.2.title', 'მონტაჟი და კონფიგურაცია', 'Installation and configuration', 'Монтаж и настройка'),
            self::entry('about.how.item.2.description', 'ვასრულებთ ფიზიკურ მონტაჟს, პროგრამულ გამართვასა და საჭირო ინტეგრაციებს.', 'We complete physical installation, software setup, and required integrations.', 'Выполняем монтаж, программную настройку и необходимые интеграции.'),
            self::entry('about.how.item.3.title', 'ტესტირება და მხარდაჭერა', 'Testing and support', 'Тестирование и поддержка'),
            self::entry('about.how.item.3.description', 'ვამოწმებთ ფუნქციებს, ვუხსნით გამოყენებას და ვაგრძელებთ ტექნიკურ მხარდაჭერას.', 'We verify functionality, explain operation, and continue technical support.', 'Проверяем функции, объясняем использование и продолжаем техническую поддержку.'),
            self::entry('about.numbers.item.0.value', '12+', '12+', '12+'),
            self::entry('about.numbers.item.0.label', 'სპეციალიზებული სერვისი', 'specialized services', 'специализированных услуг'),
            self::entry('about.numbers.item.1.value', '3', '3', '3'),
            self::entry('about.numbers.item.1.label', 'მხარდაჭერილი ენა', 'supported languages', 'поддерживаемых языка'),
            self::entry('about.numbers.item.2.value', '100%', '100%', '100%'),
            self::entry('about.numbers.item.2.label', 'მოთხოვნაზე მორგებული გეგმა', 'requirement-based planning', 'планирование под требования'),
            self::entry('about.numbers.item.3.value', '1', '1', '1'),
            self::entry('about.numbers.item.3.label', 'პასუხისმგებელი ტექნიკური პარტნიორი', 'accountable technical partner', 'ответственный технический партнер'),
            self::entry('about.team.eyebrow', 'ჩვენი გუნდი', 'Our team', 'Наша команда'),
            self::entry('about.team.title', 'სპეციალისტები, რომლებიც პასუხს აგებენ შედეგზე', 'Specialists accountable for the result', 'Специалисты, отвечающие за результат'),
            self::entry('about.team.description', 'გუნდის წევრები და პასუხისმგებლობები იმართება ადმინისტრაციული პანელიდან.', 'Team members and roles are managed from the administration panel.', 'Состав команды и роли управляются из административной панели.'),
            self::entry('about.team.regionLabel', 'SafeTech-ის გუნდი', 'SafeTech team', 'Команда SafeTech'),
            self::entry('about.why.title', 'რატომ გვირჩევენ', 'Why clients choose us', 'Почему выбирают нас'),
            self::entry('about.why.description', 'ტექნიკური გადაწყვეტილება ფასდება არა მხოლოდ მონტაჟის დღეს, არამედ მისი სტაბილურობითა და მომსახურების სიმარტივით.', 'A technical solution is measured not only on installation day, but by stability and ease of maintenance.', 'Техническое решение оценивается не только в день монтажа, но и по стабильности и удобству обслуживания.'),
            self::entry('about.why.item.0.title', 'სწორი არქიტექტურა', 'Correct architecture', 'Правильная архитектура'),
            self::entry('about.why.item.0.description', 'სისტემა იგეგმება დატვირთვის, ქსელის, კვებისა და მომავალი გაფართოების გათვალისწინებით.', 'Systems are planned for capacity, networking, power, and future expansion.', 'Система проектируется с учетом нагрузки, сети, питания и расширения.'),
            self::entry('about.why.item.1.title', 'ხარისხის კონტროლი', 'Quality control', 'Контроль качества'),
            self::entry('about.why.item.1.description', 'კავშირები, პარამეტრები და ძირითადი სცენარები მოწმდება ჩაბარებამდე.', 'Connections, settings, and key scenarios are verified before handover.', 'Соединения, настройки и основные сценарии проверяются до сдачи.'),
            self::entry('about.why.item.2.title', 'გასაგები კომუნიკაცია', 'Clear communication', 'Понятная коммуникация'),
            self::entry('about.why.item.2.description', 'წინასწარ განვმარტავთ სამუშაოს მოცულობას, კომპონენტებსა და ხარჯებს.', 'We explain scope, components, and costs before work begins.', 'Заранее объясняем объем работ, компоненты и расходы.'),
            self::entry('about.why.item.3.title', 'ტექნიკური მხარდაჭერა', 'Technical support', 'Техническая поддержка'),
            self::entry('about.why.item.3.description', 'სისტემის ჩაბარების შემდეგაც ხელმისაწვდომია დახმარება და განახლება.', 'Support and upgrades remain available after handover.', 'После сдачи доступны поддержка и обновления.'),
            self::entry('about.cta.title', 'გაქვთ ტექნიკური ამოცანა?', 'Have a technical project?', 'Есть техническая задача?'),
            self::entry('about.cta.description', 'მოგვწერეთ მოთხოვნა და მიიღეთ თქვენი ობიექტისთვის შესაბამისი გეგმა.', 'Send your requirements and receive a plan tailored to your property.', 'Отправьте требования и получите план, адаптированный под ваш объект.'),
            self::entry('about.cta.button', 'კონსულტაციის მოთხოვნა', 'Request consultation', 'Запросить консультацию'),

            self::entry('services.hero.eyebrow', 'პროფესიონალური IT და უსაფრთხოების სერვისები', 'Professional IT and security services', 'Профессиональные IT-услуги и системы безопасности'),
            self::entry('services.hero.titlePrefix', 'სრული ტექნიკური მომსახურება', 'Complete technical service', 'Комплексное техническое обслуживание'),
            self::entry('services.hero.titleAccent', 'ერთ სივრცეში', 'in one place', 'в одном месте'),
            self::entry('services.hero.titleSuffix', 'დაგეგმვიდან მხარდაჭერამდე', 'from planning to support', 'от проектирования до поддержки'),
            self::entry('services.hero.description', 'აირჩიეთ სერვისი, დააკონფიგურირეთ მოთხოვნები და მიიღეთ თავსებადი კომპონენტებისა და სამუშაოების საორიენტაციო ღირებულება.', 'Choose a service, configure the requirements, and receive indicative pricing for compatible components and labor.', 'Выберите услугу, настройте параметры и получите ориентировочную стоимость совместимых компонентов и работ.'),
            self::entry('services.hero.iso', 'სტანდარტებზე დაფუძნებული სამუშაო', 'Standards-based work', 'Работы по стандартам'),
            self::entry('services.hero.support', 'კონსულტაცია და შემდგომი მხარდაჭერა', 'Consultation and ongoing support', 'Консультация и дальнейшая поддержка'),
            self::entry('services.catalog.title', 'სერვისების კატალოგი', 'Service catalog', 'Каталог услуг'),
            self::entry('services.catalog.description', 'გაეცანით მომსახურებებს ან გამოიყენეთ ქვემოთ მოცემული კალკულატორი კონკრეტული კონფიგურაციისთვის.', 'Explore the services or use the calculator below for a specific configuration.', 'Ознакомьтесь с услугами или используйте калькулятор ниже для конкретной конфигурации.'),
            self::entry('services.catalog.page', 'IT • NETWORK • SECURITY', 'IT • NETWORK • SECURITY', 'IT • NETWORK • SECURITY'),
            self::entry('services.catalog.count', 'სერვისი', 'services', 'услуг'),
            self::entry('services.catalog.helper', 'ფილტრაცია შესაძლებელია კატეგორიის მიხედვით', 'Filter by category', 'Фильтрация по категории'),
            self::entry('services.featured.title', 'ხშირად მოთხოვნილი სერვისები', 'Popular services', 'Популярные услуги'),
            self::entry('services.work.title', 'როგორ იწყება და სრულდება პროექტი', 'How a project is delivered', 'Как выполняется проект'),
            self::entry('services.work.step.0.title', 'მოთხოვნის მიღება', 'Request', 'Запрос'),
            self::entry('services.work.step.0.description', 'ვაზუსტებთ ამოცანას და სასურველ შედეგს.', 'We clarify the task and expected result.', 'Уточняем задачу и ожидаемый результат.'),
            self::entry('services.work.step.1.title', 'შეფასება', 'Assessment', 'Оценка'),
            self::entry('services.work.step.1.description', 'ვამოწმებთ ობიექტს, არსებულ სისტემასა და შეზღუდვებს.', 'We assess the site, current system, and constraints.', 'Оцениваем объект, текущую систему и ограничения.'),
            self::entry('services.work.step.2.title', 'გეგმა', 'Plan', 'План'),
            self::entry('services.work.step.2.description', 'ვადგენთ კომპონენტებს, მარშრუტებსა და არქიტექტურას.', 'We define components, routes, and architecture.', 'Определяем компоненты, трассы и архитектуру.'),
            self::entry('services.work.step.3.title', 'შეთავაზება', 'Quotation', 'Предложение'),
            self::entry('services.work.step.3.description', 'ვუთითებთ სამუშაოს მოცულობას, ვადებსა და ფასს.', 'We specify scope, schedule, and pricing.', 'Указываем объем, сроки и стоимость.'),
            self::entry('services.work.step.4.title', 'შესრულება', 'Delivery', 'Выполнение'),
            self::entry('services.work.step.4.description', 'ვასრულებთ მონტაჟს, პროგრამულ გამართვასა და ინტეგრაციას.', 'We install, configure, and integrate the system.', 'Выполняем монтаж, настройку и интеграцию.'),
            self::entry('services.work.step.5.title', 'ჩაბარება', 'Handover', 'Сдача'),
            self::entry('services.work.step.5.description', 'ვტესტავთ, ვუხსნით გამოყენებას და ვაბარებთ გამართულ სისტემას.', 'We test, explain operation, and hand over the working system.', 'Тестируем, объясняем работу и сдаем исправную систему.'),
            self::entry('services.faq.title', 'ხშირად დასმული კითხვები', 'Frequently asked questions', 'Частые вопросы'),
            self::entry('services.faq.description', 'პასუხები სამუშაოს დაგეგმვის, ფასის, გარანტიისა და მომსახურების შესახებ.', 'Answers about planning, pricing, warranty, and support.', 'Ответы о планировании, стоимости, гарантии и обслуживании.'),
            self::entry('services.faq.contact', 'დამატებითი კითხვის გაგზავნა', 'Ask another question', 'Задать другой вопрос'),
            self::entry('services.cta.title', 'მიიღეთ ზუსტი შეთავაზება თქვენი მოთხოვნისთვის', 'Get an exact proposal for your requirements', 'Получите точное предложение под ваши требования'),
            self::entry('services.cta.description', 'კალკულატორი გაძლევთ საორიენტაციო თანხას, საბოლოო ფასი კი დგინდება დეტალებისა და ობიექტის შეფასების შემდეგ.', 'The calculator provides an estimate. Final pricing follows a review of details and the site.', 'Калькулятор дает ориентировочную сумму. Итоговая стоимость определяется после оценки деталей и объекта.'),
            self::entry('services.cta.quote', 'შეთავაზების მოთხოვნა', 'Request a quotation', 'Запросить предложение'),
            self::entry('services.cta.call', 'დაგვირეკეთ', 'Call us', 'Позвонить'),
            self::entry('service.detail.cta.titlePrefix', 'გჭირდებათ', 'Need', 'Нужна услуга'),
            self::entry('service.detail.cta.description', 'გამოიყენეთ კალკულატორი საორიენტაციო ღირებულებისთვის ან გამოგვიგზავნეთ მოთხოვნა ზუსტი შეთავაზებისთვის.', 'Use the calculator for an estimate or send a request for an exact proposal.', 'Используйте калькулятор для оценки или отправьте запрос для точного предложения.'),
            self::entry('service.detail.cta.consultation', 'კონსულტაცია', 'Consultation', 'Консультация'),
            self::entry('service.detail.cta.call', 'დარეკვა', 'Call', 'Позвонить'),
            self::entry('service.detail.cta.calculator', 'ფასის გამოთვლა', 'Calculate price', 'Рассчитать стоимость'),

            self::entry('contact.hero.title', 'დაგვიკავშირდით ტექნიკური კონსულტაციისთვის', 'Contact us for a technical consultation', 'Свяжитесь с нами для технической консультации'),
            self::entry('contact.hero.description', 'მოგვწერეთ თქვენი ამოცანა ან დაგვირეკეთ: 571 430 169 / 557 316 310.', 'Describe your project or call us: 571 430 169 / 557 316 310.', 'Опишите задачу или позвоните: 571 430 169 / 557 316 310.'),
            self::entry('contact.hero.button', 'მოთხოვნის შევსება', 'Submit a request', 'Заполнить заявку'),
            self::entry('contact.intro.title', 'ერთი შეტყობინებიდან ტექნიკურ გეგმამდე', 'From one message to a technical plan', 'От одного сообщения до технического плана'),
            self::entry('contact.intro.paragraph.0', 'მოკლედ აღწერეთ ობიექტი, პრობლემა ან სასურველი სისტემა. საჭიროების შემთხვევაში დაგისვამთ დამაზუსტებელ კითხვებს.', 'Briefly describe the property, issue, or desired system. We will ask follow-up questions when needed.', 'Кратко опишите объект, проблему или желаемую систему. При необходимости зададим уточняющие вопросы.'),
            self::entry('contact.intro.paragraph.1', 'შეფასების შემდეგ მიიღებთ მომსახურების მოცულობას, შესაბამის კომპონენტებსა და საორიენტაციო ფასს.', 'After assessment, you receive the scope, suitable components, and indicative pricing.', 'После оценки вы получите объем работ, подходящие компоненты и ориентировочную стоимость.'),
            self::entry('contact.intro.badge.0', 'ტექნიკური შეფასება', 'Technical assessment', 'Техническая оценка'),
            self::entry('contact.intro.badge.1', 'მოთხოვნაზე მორგებული გეგმა', 'Tailored plan', 'План под требования'),
            self::entry('contact.intro.imageAlt', 'SafeTech-ის ტექნიკური კონსულტაცია', 'SafeTech technical consultation', 'Техническая консультация SafeTech'),
            self::entry('contact.form.title', 'მოგვწერეთ მოთხოვნა', 'Send your request', 'Отправьте запрос'),
            self::entry('contact.side.title', 'რა მოხდება მოთხოვნის შემდეგ', 'What happens next', 'Что произойдет после запроса'),
            self::entry('contact.side.description', 'გავეცნობით დეტალებს, დაგიკავშირდებით დაზუსტებისთვის და შემოგთავაზებთ ტექნიკურად თავსებად გადაწყვეტას.', 'We review the details, contact you for clarification, and propose a technically compatible solution.', 'Изучим детали, свяжемся для уточнения и предложим технически совместимое решение.'),
            self::entry('contact.info.phone', 'ტელეფონები', 'Phone numbers', 'Телефоны'),
            self::entry('contact.info.email', 'ელფოსტა', 'Email', 'Электронная почта'),
            self::entry('contact.info.address', 'მომსახურების არეალი', 'Service area', 'Зона обслуживания'),
            self::entry('contact.info.hours', 'სამუშაო რეჟიმი', 'Availability', 'Режим работы'),
            self::entry('contact.support.title', 'დისტანციური და ადგილზე მხარდაჭერა', 'Remote and on-site support', 'Удаленная и выездная поддержка'),
            self::entry('contact.support.description', 'პრობლემის ტიპის მიხედვით ვეხმარებით დისტანციურად, ადგილზე ვიზიტით ან სრული პროექტის დაგეგმვით.', 'Depending on the issue, we help remotely, on site, or through complete project planning.', 'В зависимости от задачи помогаем удаленно, с выездом или полным проектированием.'),
            self::entry('contact.support.badge', 'ტექნიკური დახმარება', 'Technical support', 'Техническая поддержка'),
            self::entry('contact.support.imageAlt', 'SafeTech-ის IT და უსაფრთხოების მხარდაჭერა', 'SafeTech IT and security support', 'IT-поддержка и системы безопасности SafeTech'),
            self::entry('contact.support.item.0', 'კომპიუტერებისა და პროგრამების დიაგნოსტიკა', 'Computer and software diagnostics', 'Диагностика компьютеров и программ'),
            self::entry('contact.support.item.1', 'ქსელისა და Wi-Fi-ის პრობლემების მოგვარება', 'Network and Wi-Fi troubleshooting', 'Устранение проблем сети и Wi-Fi'),
            self::entry('contact.support.item.2', 'კამერების, NVR/DVR-ისა და დისტანციური წვდომის გამართვა', 'CCTV, NVR/DVR, and remote access support', 'Настройка камер, NVR/DVR и удаленного доступа'),
            self::entry('contact.support.item.3', 'დომოფონისა და დაშვების სისტემების მომსახურება', 'Intercom and access control support', 'Обслуживание домофонов и контроля доступа'),
            self::entry('contact.support.item.4', 'POS სისტემებისა და პერიფერიის დახმარება', 'POS and peripheral support', 'Поддержка POS и периферии'),
            self::entry('contact.support.item.5', 'ქსელური რეკისა და კაბელირების მოწესრიგება', 'Rack and cabling organization', 'Организация сетевого шкафа и кабелей'),
            self::entry('contact.faq.title', 'კითხვები დაკავშირებამდე', 'Questions before contacting us', 'Вопросы перед обращением'),
            self::entry('contact.final.title', 'მზად ხართ ტექნიკური ამოცანის დასაწყებად?', 'Ready to start your technical project?', 'Готовы начать технический проект?'),
            self::entry('contact.final.button', 'მოთხოვნის გაგზავნა', 'Send request', 'Отправить запрос'),
        ];
    }

    /** @return array<int, array{question:array{ka:string,en:string,ru:string},answer:array{ka:string,en:string,ru:string}}> */
    private function faqDefinitions(): array
    {
        return [
            [
                'question' => self::localized('რომელ ქალაქებში მუშაობთ?', 'Which areas do you serve?', 'В каких регионах вы работаете?'),
                'answer' => self::localized('ვემსახურებით თბილისს, ხაშურს, ბაკურიანს, ბორჯომს და სხვა ქალაქებს წინასწარი შეთანხმებით. გზისა და ლოგისტიკის ხარჯი ითვლება ობიექტის მდებარეობის მიხედვით.', 'We serve Tbilisi, Khashuri, Bakuriani, Borjomi, and other locations by prior arrangement. Travel and logistics are calculated according to the site.', 'Работаем в Тбилиси, Хашури, Бакуриани, Боржоми и других городах по договоренности. Логистика рассчитывается по расположению объекта.'),
            ],
            [
                'question' => self::localized('როგორ განისაზღვრება მომსახურების ფასი?', 'How is the service price determined?', 'Как определяется стоимость?'),
                'answer' => self::localized('ფასი დამოკიდებულია სამუშაოს მოცულობაზე, კაბელისა და მასალების რაოდენობაზე, მოწყობილობებზე, ობიექტის პირობებსა და საჭირო კონფიგურაციაზე. კალკულატორი აჩვენებს საორიენტაციო თანხას.', 'Pricing depends on scope, cabling and materials, equipment, site conditions, and configuration. The calculator provides an indicative amount.', 'Стоимость зависит от объема работ, кабелей и материалов, оборудования, условий объекта и настройки. Калькулятор показывает ориентировочную сумму.'),
            ],
            [
                'question' => self::localized('შესაძლებელია ობიექტის წინასწარი შეფასება?', 'Is a site assessment available?', 'Можно провести предварительную оценку объекта?'),
                'answer' => self::localized('დიახ. მცირე ამოცანა ხშირად ფასდება ფოტოებით, ვიდეოთი ან დისტანციური კონსულტაციით. დიდი პროექტისთვის რეკომენდებულია ადგილზე დათვალიერება და ტექნიკური გეგმის მომზადება.', 'Yes. Small tasks can often be assessed from photos, video, or remotely. Larger projects benefit from an on-site survey and technical plan.', 'Да. Небольшие задачи можно оценить по фото, видео или удаленно. Для крупных проектов рекомендуется выезд и технический план.'),
            ],
            [
                'question' => self::localized('მოწყობილობებს თქვენ არჩევთ თუ მომხმარებელი?', 'Who selects the equipment?', 'Кто подбирает оборудование?'),
                'answer' => self::localized('შესაძლებელია ორივე ვარიანტი. ჩვენ ვარჩევთ თავსებად კომპონენტებს ბიუჯეტისა და მოთხოვნების მიხედვით, ან წინასწარ ვამოწმებთ მომხმარებლის მიერ შერჩეული მოწყობილობების თავსებადობას.', 'Both options are available. We can select compatible components for the budget and requirements or verify equipment chosen by the customer.', 'Возможны оба варианта. Мы подбираем совместимые компоненты под бюджет и требования или проверяем выбранное клиентом оборудование.'),
            ],
            [
                'question' => self::localized('სამუშაოზე გარანტია მოქმედებს?', 'Is the work covered by warranty?', 'Есть ли гарантия на работы?'),
                'answer' => self::localized('გარანტიის პირობები განისაზღვრება შესრულებული სამუშაოს, გამოყენებული მასალებისა და მოწყობილობების მიხედვით და წინასწარ აისახება შეთავაზებაში.', 'Warranty terms depend on the work, materials, and equipment and are specified in the proposal.', 'Условия гарантии зависят от работ, материалов и оборудования и указываются в предложении.'),
            ],
            [
                'question' => self::localized('როგორ დაგიკავშირდეთ სწრაფად?', 'What is the fastest way to contact you?', 'Как связаться быстрее всего?'),
                'answer' => self::localized('დაგვირეკეთ ნომრებზე 571 430 169 ან 557 316 310, ან შეავსეთ მოთხოვნის ფორმა და მიუთითეთ სასურველი სერვისი.', 'Call 571 430 169 or 557 316 310, or submit the request form and select the required service.', 'Позвоните по номерам 571 430 169 или 557 316 310 либо заполните форму и выберите услугу.'),
            ],
        ];
    }

    /** @return array{key:string,ka:string,en:string,ru:string} */
    private static function entry(string $key, string $ka, string $en, string $ru): array
    {
        return compact('key', 'ka', 'en', 'ru');
    }

    /** @return array{ka:string,en:string,ru:string} */
    private static function localized(string $ka, string $en, string $ru): array
    {
        return compact('ka', 'en', 'ru');
    }
}
