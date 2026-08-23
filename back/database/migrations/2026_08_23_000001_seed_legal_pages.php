<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ($this->pages() as $slug => $page) {
            $existing = DB::table('pages')->where('slug', $slug)->first();

            DB::table('pages')->updateOrInsert(
                ['slug' => $slug],
                [
                    'title' => $page['title']['ka'],
                    'excerpt' => $page['excerpt']['ka'],
                    'content' => $page['content']['ka'],
                    'seo_title' => $page['seo_title']['ka'],
                    'seo_description' => $page['seo_description']['ka'],
                    'keywords' => json_encode($page['keywords'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'translations' => json_encode([
                        'fields' => [
                            'title' => $page['title'],
                            'excerpt' => $page['excerpt'],
                            'content' => $page['content'],
                            'seoTitle' => $page['seo_title'],
                            'seoDescription' => $page['seo_description'],
                        ],
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'is_published' => true,
                    'noindex' => false,
                    'sort_order' => $page['sort_order'],
                    'published_at' => $existing?->published_at ?: now(),
                    'created_at' => $existing?->created_at ?: now(),
                    'updated_at' => now(),
                ],
            );
        }
    }

    public function down(): void
    {
        // Legal pages may be edited by an administrator after deployment.
        // Preserve that production content during rollbacks.
    }

    /** @return array<string, array<string, mixed>> */
    private function pages(): array
    {
        return [
            'privacy' => [
                'title' => [
                    'ka' => 'კონფიდენციალურობის პოლიტიკა',
                    'en' => 'Privacy Policy',
                    'ru' => 'Политика конфиденциальности',
                ],
                'excerpt' => [
                    'ka' => 'როგორ აგროვებს, იყენებს და იცავს SafeTech ვებსაიტის ვიზიტორებისა და მომხმარებლების პერსონალურ მონაცემებს.',
                    'en' => 'How SafeTech collects, uses, and protects personal data from website visitors and customers.',
                    'ru' => 'Как SafeTech собирает, использует и защищает персональные данные посетителей сайта и клиентов.',
                ],
                'seo_title' => [
                    'ka' => 'კონფიდენციალურობის პოლიტიკა | SafeTech',
                    'en' => 'Privacy Policy | SafeTech',
                    'ru' => 'Политика конфиденциальности | SafeTech',
                ],
                'seo_description' => [
                    'ka' => 'SafeTech-ის კონფიდენციალურობის პოლიტიკა — პერსონალური მონაცემები, საკონტაქტო ფორმები, AI კონსულტანტი, ანალიტიკა, cookies და მომხმარებლის უფლებები.',
                    'en' => 'SafeTech privacy policy covering personal data, contact forms, the AI consultant, analytics, cookies, and user rights.',
                    'ru' => 'Политика конфиденциальности SafeTech: персональные данные, формы связи, AI-консультант, аналитика, cookies и права пользователей.',
                ],
                'keywords' => ['SafeTech privacy', 'კონფიდენციალურობის პოლიტიკა', 'privacy policy Georgia', 'პერსონალური მონაცემები'],
                'sort_order' => 900,
                'content' => [
                    'ka' => <<<'TEXT'
## 1. ზოგადი ინფორმაცია

ეს კონფიდენციალურობის პოლიტიკა აღწერს, როგორ ამუშავებს SafeTech ინფორმაციას, რომელსაც ვიღებთ safetech.ge-ის გამოყენებისას, საკონტაქტო ფორმების შევსებისას, AI კონსულტანტთან ურთიერთობისას ან მომსახურების შესახებ ჩვენთან დაკავშირებისას.

პოლიტიკა ვრცელდება ვებსაიტზე მიღებულ მონაცემებზე. კონკრეტული მომსახურების გაწევისას დამატებითი ინფორმაცია ან შეთანხმება შეიძლება გახდეს საჭირო.

## 2. რა მონაცემებს შეიძლება ვაგროვებდეთ

- სახელი და გვარი, თუ თავად მიუთითებთ.
- ტელეფონის ნომერი, ელფოსტა და სხვა საკონტაქტო მონაცემები.
- კომპანიის, ობიექტის, მისამართის ან მომსახურების მოთხოვნის შესახებ ინფორმაცია, რომელსაც თავად გვაწვდით.
- საკონტაქტო ფორმაში, AI კონსულტანტთან ან სხვა კომუნიკაციაში გამოგზავნილი ტექსტი.
- ტექნიკური ინფორმაცია, როგორიცაა IP მისამართისგან წარმოებული დაცული იდენტიფიკატორი, მოწყობილობის/ბრაუზერის ტიპი, მოთხოვნის დრო და უსაფრთხოებისთვის საჭირო ტექნიკური ლოგები.
- თანხმობისა და ანალიტიკის პარამეტრები, როდესაც შესაბამის ფუნქციებს იყენებთ.

## 3. რისთვის ვიყენებთ მონაცემებს

- თქვენს მოთხოვნაზე პასუხის გასაცემად და კონსულტაციისთვის.
- მომსახურების, მონტაჟის, შეფასების ან კომერციული შეთავაზების მოსამზადებლად.
- ვებსაიტის, ფორმების და AI კონსულტანტის მუშაობისა და უსაფრთხოების უზრუნველსაყოფად.
- სპამის, ბოროტად გამოყენებისა და ტექნიკური ინციდენტების გამოსავლენად.
- ვებსაიტის ხარისხისა და მომხმარებლის გამოცდილების გასაუმჯობესებლად.
- ანალიტიკისა და მარკეტინგული ინტეგრაციების გამოსაყენებლად მხოლოდ მაშინ, როდესაც ისინი ჩართულია და შესაბამისი თანხმობა არსებობს.

## 4. AI კონსულტანტი

AI კონსულტანტში შეყვანილი შეტყობინებები შეიძლება დამუშავდეს ავტომატური პასუხის მოსამზადებლად. თუ მომხმარებელი თავად მიუთითებს ტელეფონის ნომერს და ითხოვს დაკავშირებას, შესაბამისი მოთხოვნა შეიძლება გადაიქცეს SafeTech-ის გაყიდვების/კონსულტაციის ლიდად.

AI პასუხი შეიძლება შეიცავდეს ავტომატურად გენერირებულ ინფორმაციას და არ ცვლის სპეციალისტის საბოლოო ტექნიკურ შეფასებას, ობიექტის დათვალიერებას ან ოფიციალურ კომერციულ შეთავაზებას.

## 5. Cookies, ანალიტიკა და მესამე მხარის სერვისები

ვებსაიტმა შეიძლება გამოიყენოს აუცილებელი ტექნიკური cookies. დამატებითი ანალიტიკური ან მარკეტინგული ტექნოლოგიები გამოიყენება მხოლოდ შესაბამისი პარამეტრებისა და თანხმობის ფარგლებში.

საიტზე შეიძლება იყოს ინტეგრირებული მესამე მხარის სერვისები, მაგალითად ანალიტიკა, სოციალური ქსელები, რუკები ან AI მომსახურება. ასეთი პროვაიდერები მონაცემებს საკუთარი პირობებისა და კონფიდენციალურობის წესების შესაბამისად ამუშავებენ.

## 6. ვის შეიძლება გადაეცეს ინფორმაცია

პერსონალური ინფორმაცია არ იყიდება. მონაცემები შეიძლება გაეზიაროს მხოლოდ იმ ტექნიკურ ან მომსახურების პროვაიდერებს, რომლებიც აუცილებელია ვებსაიტის, კომუნიკაციის, ჰოსტინგის, ანალიტიკის ან მოთხოვნის დამუშავებისთვის, ან როდესაც ამას კანონი მოითხოვს.

## 7. მონაცემების შენახვა და დაცვა

მონაცემებს ვინახავთ მხოლოდ იმ ვადით, რაც საჭიროა მოთხოვნის დამუშავებისთვის, მომსახურების ურთიერთობისთვის, უსაფრთხოებისთვის, სამართლებრივი ვალდებულებების შესასრულებლად ან გონივრული ბიზნეს ჩანაწერებისთვის.

ვიყენებთ გონივრულ ტექნიკურ და ორგანიზაციულ ზომებს მონაცემების არასანქცირებული წვდომის, შეცვლის, დაკარგვის ან გამჟღავნების რისკის შესამცირებლად. ინტერნეტით მონაცემთა გადაცემის აბსოლუტური უსაფრთხოება გარანტირებული ვერ იქნება.

## 8. თქვენი უფლებები და არჩევანი

თქვენ შეგიძლიათ მოგვმართოთ თქვენი მონაცემების შესახებ ინფორმაციის, გასწორების ან წაშლის მოთხოვნით, როდესაც ეს მოქმედი კანონმდებლობით არის შესაძლებელი. ასევე შეგიძლიათ შეცვალოთ ვებსაიტის consent/cookie პარამეტრები შესაბამისი მართვის ღილაკიდან.

## 9. ბავშვების მონაცემები

ვებსაიტი და SafeTech-ის მომსახურებები განკუთვნილი არ არის ბავშვებისგან პერსონალური მონაცემების მიზანმიმართული შეგროვებისთვის. თუ ფიქრობთ, რომ არასრულწლოვნის მონაცემები უნებლიედ მივიღეთ, დაგვიკავშირდით.

## 10. ცვლილებები და კონტაქტი

პოლიტიკა შეიძლება პერიოდულად განახლდეს. მოქმედი ვერსია ყოველთვის გამოქვეყნდება ამ გვერდზე და გვერდზე გამოჩნდება ბოლო განახლების თარიღი.

კონფიდენციალურობასთან დაკავშირებული საკითხებისთვის დაგვიკავშირდით safetech.ge-ის საკონტაქტო არხების საშუალებით.
TEXT,
                    'en' => <<<'TEXT'
## 1. General information

This Privacy Policy explains how SafeTech processes information received when you use safetech.ge, submit a contact form, interact with the AI consultant, or contact us about a service.

This policy applies to data received through the website. Additional information or agreements may be required when a specific service is provided.

## 2. Data we may collect

- Your name when you choose to provide it.
- Phone number, email address, and other contact information.
- Information about your company, property, location, or service request that you provide voluntarily.
- Text submitted through contact forms, the AI consultant, or other communications.
- Technical information such as a protected identifier derived from an IP address, device/browser type, request time, and security-related logs.
- Consent and analytics preferences when you use those features.

## 3. How we use data

- To respond to inquiries and provide consultations.
- To prepare service, installation, assessment, or commercial proposals.
- To operate and secure the website, forms, and AI consultant.
- To detect spam, abuse, and technical incidents.
- To improve website quality and user experience.
- To use analytics or marketing integrations only when they are enabled and the appropriate consent exists.

## 4. AI consultant

Messages entered into the AI consultant may be processed to generate an automated response. If a user voluntarily provides a phone number and requests contact, the request may be converted into a SafeTech sales or consultation lead.

AI responses may contain automatically generated information and do not replace a specialist's final technical assessment, site inspection, or formal commercial proposal.

## 5. Cookies, analytics, and third-party services

The website may use technically necessary cookies. Additional analytics or marketing technologies are used only in accordance with the applicable settings and consent.

The website may integrate third-party services such as analytics, social networks, maps, or AI services. Those providers process data under their own terms and privacy policies.

## 6. Sharing information

We do not sell personal information. Data may be shared only with technical or service providers required for website operation, communications, hosting, analytics, or request processing, or where disclosure is required by law.

## 7. Retention and security

We keep data only for as long as reasonably necessary to handle a request, maintain a service relationship, protect security, comply with legal obligations, or keep appropriate business records.

We use reasonable technical and organizational measures to reduce the risk of unauthorized access, alteration, loss, or disclosure. Absolute security of data transmitted over the internet cannot be guaranteed.

## 8. Your rights and choices

You may contact us to request information about, correction of, or deletion of your personal data where available under applicable law. You may also change website consent/cookie preferences through the relevant settings control.

## 9. Children's data

The website and SafeTech services are not intended to intentionally collect personal information from children. If you believe we have unintentionally received a minor's information, please contact us.

## 10. Changes and contact

This policy may be updated periodically. The current version will always be published on this page and the page will display its latest update date.

For privacy questions, contact us through the contact channels published on safetech.ge.
TEXT,
                    'ru' => <<<'TEXT'
## 1. Общая информация

Настоящая Политика конфиденциальности описывает, как SafeTech обрабатывает информацию, полученную при использовании safetech.ge, отправке формы связи, взаимодействии с AI-консультантом или обращении по поводу услуг.

Политика применяется к данным, полученным через сайт. При оказании конкретной услуги может потребоваться дополнительная информация или соглашение.

## 2. Какие данные мы можем собирать

- Имя и фамилию, если вы указываете их добровольно.
- Номер телефона, email и другие контактные данные.
- Информацию о компании, объекте, местоположении или запросе на услугу, которую вы предоставляете.
- Текст, отправленный через формы, AI-консультанта или другие каналы связи.
- Техническую информацию: защищенный идентификатор, сформированный на основе IP-адреса, тип устройства/браузера, время запроса и журналы, необходимые для безопасности.
- Настройки согласия и аналитики при использовании соответствующих функций.

## 3. Для чего используются данные

- Для ответа на запросы и консультаций.
- Для подготовки услуги, монтажа, оценки или коммерческого предложения.
- Для работы и защиты сайта, форм и AI-консультанта.
- Для выявления спама, злоупотреблений и технических инцидентов.
- Для улучшения качества сайта и пользовательского опыта.
- Для аналитики и маркетинговых интеграций только при их включении и наличии соответствующего согласия.

## 4. AI-консультант

Сообщения, введенные в AI-консультанте, могут обрабатываться для подготовки автоматического ответа. Если пользователь добровольно указывает номер телефона и просит связаться с ним, запрос может быть преобразован в лид SafeTech для продажи или консультации.

Ответы AI могут содержать автоматически созданную информацию и не заменяют окончательную техническую оценку специалиста, осмотр объекта или официальное коммерческое предложение.

## 5. Cookies, аналитика и сторонние сервисы

Сайт может использовать технически необходимые cookies. Дополнительные аналитические или маркетинговые технологии используются только в рамках соответствующих настроек и согласия.

На сайте могут использоваться сторонние сервисы, например аналитика, социальные сети, карты или AI-сервисы. Такие поставщики обрабатывают данные согласно собственным условиям и политикам конфиденциальности.

## 6. Передача информации

Мы не продаем персональные данные. Информация может передаваться только техническим или сервисным поставщикам, необходимым для работы сайта, связи, хостинга, аналитики или обработки запроса, либо когда этого требует закон.

## 7. Хранение и безопасность

Данные хранятся только столько, сколько разумно необходимо для обработки запроса, обслуживания клиента, безопасности, выполнения юридических обязательств или ведения необходимых деловых записей.

Мы применяем разумные технические и организационные меры для снижения риска несанкционированного доступа, изменения, потери или раскрытия данных. Абсолютная безопасность передачи данных через интернет не может быть гарантирована.

## 8. Ваши права и выбор

Вы можете обратиться к нам с запросом информации о ваших данных, их исправления или удаления в случаях, предусмотренных применимым законодательством. Настройки согласия/cookies также можно изменить через соответствующий элемент управления на сайте.

## 9. Данные детей

Сайт и услуги SafeTech не предназначены для целенаправленного сбора персональных данных детей. Если вы считаете, что данные несовершеннолетнего были получены непреднамеренно, свяжитесь с нами.

## 10. Изменения и контакты

Политика может периодически обновляться. Актуальная версия всегда публикуется на этой странице вместе с датой последнего обновления.

По вопросам конфиденциальности свяжитесь с нами через контактные каналы, указанные на safetech.ge.
TEXT,
                ],
            ],
            'terms' => [
                'title' => [
                    'ka' => 'მომსახურების პირობები',
                    'en' => 'Terms of Service',
                    'ru' => 'Условия использования',
                ],
                'excerpt' => [
                    'ka' => 'safetech.ge-ის გამოყენებისა და SafeTech-ის მომსახურებების შესახებ მოთხოვნის ძირითადი პირობები.',
                    'en' => 'The main terms governing use of safetech.ge and requests for SafeTech services.',
                    'ru' => 'Основные условия использования safetech.ge и обращения за услугами SafeTech.',
                ],
                'seo_title' => [
                    'ka' => 'მომსახურების პირობები | SafeTech',
                    'en' => 'Terms of Service | SafeTech',
                    'ru' => 'Условия использования | SafeTech',
                ],
                'seo_description' => [
                    'ka' => 'SafeTech-ის ვებსაიტისა და მომსახურებების გამოყენების პირობები — კონსულტაცია, შეთავაზებები, მონტაჟი, AI კონსულტანტი, პასუხისმგებლობა და ინტელექტუალური საკუთრება.',
                    'en' => 'SafeTech website and service terms covering consultations, proposals, installation, the AI consultant, liability, and intellectual property.',
                    'ru' => 'Условия SafeTech: консультации, предложения, монтаж, AI-консультант, ответственность и интеллектуальная собственность.',
                ],
                'keywords' => ['SafeTech terms', 'მომსახურების პირობები', 'terms of service Georgia', 'CCTV installation terms'],
                'sort_order' => 901,
                'content' => [
                    'ka' => <<<'TEXT'
## 1. პირობების მიღება

safetech.ge-ის გამოყენებით თქვენ ეთანხმებით ამ პირობებს. თუ რომელიმე პირობას არ ეთანხმებით, შეგიძლიათ შეწყვიტოთ ვებსაიტის გამოყენება. მომსახურების კონკრეტულ შეკვეთაზე შეიძლება გავრცელდეს დამატებითი წერილობითი შეთავაზება, ანგარიშ-ფაქტურა, ტექნიკური დავალება ან შეთანხმება.

## 2. ვებსაიტის დანიშნულება

ვებსაიტი წარმოადგენს SafeTech-ის მომსახურებების, პროექტებისა და საკონტაქტო შესაძლებლობების საინფორმაციო პლატფორმას. საიტზე მოცემული ინფორმაცია არ წარმოადგენს ავტომატურად სავალდებულო კომერციულ შეთავაზებას, თუ კონკრეტულად ასე არ არის მითითებული.

## 3. მოთხოვნები, ფასები და შეთავაზებები

ვებსაიტიდან გამოგზავნილი მოთხოვნა ან AI კონსულტანტთან მიმოწერა თავისთავად არ ქმნის მომსახურების ხელშეკრულებას. საბოლოო მოცულობა, მოწყობილობები, ფასი, ვადები, ტრანსპორტირების/გზის ხარჯები და სხვა პირობები შეიძლება დაზუსტდეს ობიექტის, ტექნიკური მოთხოვნების და რეალური სამუშაოს შეფასების შემდეგ.

საიტზე ან ავტომატურ პასუხში ნაჩვენები სავარაუდო ფასი, გაანგარიშება ან ტექნიკური რეკომენდაცია საინფორმაციო ხასიათისაა, თუ წერილობით შეთავაზებაში სხვა რამ არ არის დადასტურებული.

## 4. მონტაჟი და ტექნიკური მომსახურება

მომხმარებელი ვალდებულია უზრუნველყოს ობიექტზე უსაფრთხო და შეთანხმებული წვდომა, საჭირო ნებართვები და იმ გარემოებების შესახებ ინფორმაციის მიწოდება, რომლებიც სამუშაოს უსაფრთხოებას ან შესრულებას შეიძლება შეეხოს.

სამუშაოს მოცულობის ცვლილებამ, დამატებითმა კაბელმა, მოწყობილობამ, სამშენებლო სამუშაომ, რთულმა წვდომამ ან შეთანხმებული პირობების ცვლილებამ შეიძლება გავლენა მოახდინოს ფასსა და ვადებზე.

## 5. მოწყობილობები და მესამე მხარის პროდუქტები

SafeTech შეიძლება მუშაობდეს სხვადასხვა მწარმოებლის მოწყობილობებთან და პროგრამულ სერვისებთან. მესამე მხარის პროდუქტების ფუნქციები, cloud/P2P სერვისები, აპლიკაციები, firmware, გარანტია და ხელმისაწვდომობა შეიძლება შეიცვალოს მწარმოებლის ან მომწოდებლის მიერ და იმართება მათი პირობებით.

## 6. AI კონსულტანტი და ავტომატური ინფორმაცია

AI კონსულტანტი განკუთვნილია პირველადი ინფორმაციისა და მოთხოვნის სწრაფად დასამუშავებლად. მისი პასუხი შეიძლება იყოს არასრული ან საჭიროებდეს სპეციალისტის გადამოწმებას.

უსაფრთხოების სისტემის საბოლოო პროექტირება, ელექტრო/ქსელური გადაწყვეტილება, შენახვის მოცულობა, მოწყობილობის თავსებადობა და მონტაჟის ღირებულება უნდა დადასტურდეს კონკრეტული მოთხოვნების მიხედვით.

## 7. აკრძალული გამოყენება

ვებსაიტის გამოყენება დაუშვებელია უკანონო საქმიანობისთვის, სისტემაზე არასანქცირებული წვდომის მცდელობისთვის, მავნე კოდის გასავრცელებლად, სპამისთვის, მომსახურების შეფერხებისთვის ან სხვა მომხმარებლებისა და SafeTech-ის უფლებების დასარღვევად.

## 8. ინტელექტუალური საკუთრება

ვებსაიტის ტექსტები, დიზაინი, ბრენდინგი, ფოტოები, ვიდეოები, პროექტის აღწერები და სხვა მასალები დაცულია შესაბამისი უფლებებით, გარდა იმ მასალისა, რომელიც მესამე მხარეს ეკუთვნის. მათი კომერციული გამოყენება ან მასობრივი კოპირება ნებართვის გარეშე დაუშვებელია კანონით დაშვებული შემთხვევების გარდა.

## 9. პასუხისმგებლობის შეზღუდვა

ვცდილობთ საიტზე ზუსტი და აქტუალური ინფორმაცია გამოვაქვეყნოთ, თუმცა ვერ ვიძლევით გარანტიას, რომ ყველა ინფორმაცია ყოველთვის შეცდომისგან თავისუფალი ან მუდმივად ხელმისაწვდომი იქნება.

კანონით დაშვებულ ფარგლებში SafeTech არ არის პასუხისმგებელი იმ არაპირდაპირ ზიანზე, რომელიც გამოწვეულია მხოლოდ ვებსაიტზე არსებული ზოგადი ინფორმაციის, მესამე მხარის სერვისის ან ავტომატური AI პასუხის გამოყენებით კონკრეტული ტექნიკური შეფასების გარეშე.

## 10. კონფიდენციალურობა

პერსონალური მონაცემების დამუშავების შესახებ ინფორმაცია მოცემულია SafeTech-ის კონფიდენციალურობის პოლიტიკაში: /privacy.

## 11. პირობების ცვლილება და კონტაქტი

ეს პირობები შეიძლება პერიოდულად განახლდეს. მოქმედი ვერსია ყოველთვის გამოქვეყნდება ამ გვერდზე.

პირობებთან ან მომსახურებასთან დაკავშირებული კითხვებისთვის დაგვიკავშირდით safetech.ge-ის საკონტაქტო არხების საშუალებით.
TEXT,
                    'en' => <<<'TEXT'
## 1. Acceptance of terms

By using safetech.ge, you agree to these terms. If you do not agree with any part, you may stop using the website. A specific service order may also be governed by a written proposal, invoice, technical scope, or separate agreement.

## 2. Purpose of the website

The website is an information platform for SafeTech services, projects, and contact options. Information displayed on the site does not automatically constitute a binding commercial offer unless explicitly stated otherwise.

## 3. Requests, prices, and proposals

Submitting a request through the website or communicating with the AI consultant does not by itself create a service contract. Final scope, equipment, price, timing, travel costs, and other terms may be confirmed after the property, technical requirements, and actual work are assessed.

Any estimated price, calculation, or technical recommendation shown on the site or in an automated response is informational unless confirmed in a written proposal.

## 4. Installation and technical service

The customer must provide safe and agreed access to the site, required permissions, and information about circumstances that may affect safe or proper performance of the work.

Changes in scope, extra cabling, equipment, construction work, difficult access, or changes to agreed conditions may affect pricing and timing.

## 5. Equipment and third-party products

SafeTech may work with equipment and software services from multiple manufacturers. Third-party product features, cloud/P2P services, apps, firmware, warranties, and availability may change at the manufacturer or supplier level and are governed by their terms.

## 6. AI consultant and automated information

The AI consultant is intended for initial information and rapid request handling. Its responses may be incomplete or require verification by a specialist.

Final security-system design, electrical/network decisions, storage capacity, equipment compatibility, and installation pricing must be confirmed for the specific requirements.

## 7. Prohibited use

You may not use the website for unlawful activity, unauthorized access attempts, distributing malicious code, spam, disrupting services, or violating the rights of SafeTech or other users.

## 8. Intellectual property

Website text, design, branding, photos, videos, project descriptions, and other materials are protected by applicable rights except where material belongs to a third party. Commercial reuse or bulk copying without permission is prohibited except where permitted by law.

## 9. Limitation of liability

We aim to keep website information accurate and current, but cannot guarantee that all information will always be error-free or continuously available.

To the extent permitted by law, SafeTech is not responsible for indirect losses caused solely by reliance on general website information, a third-party service, or an automated AI response without a specific technical assessment.

## 10. Privacy

Information about processing personal data is provided in the SafeTech Privacy Policy at /privacy.

## 11. Changes and contact

These terms may be updated periodically. The current version will always be published on this page.

For questions about these terms or our services, contact us through the contact channels published on safetech.ge.
TEXT,
                    'ru' => <<<'TEXT'
## 1. Принятие условий

Используя safetech.ge, вы соглашаетесь с настоящими условиями. Если вы не согласны с какой-либо частью, вы можете прекратить использование сайта. Конкретный заказ также может регулироваться письменным предложением, счетом, техническим заданием или отдельным соглашением.

## 2. Назначение сайта

Сайт является информационной платформой об услугах, проектах и способах связи с SafeTech. Информация на сайте не является автоматически обязательным коммерческим предложением, если прямо не указано иное.

## 3. Запросы, цены и предложения

Отправка запроса через сайт или переписка с AI-консультантом сама по себе не создает договор на оказание услуг. Окончательный объем, оборудование, цена, сроки, транспортные расходы и другие условия могут быть подтверждены после оценки объекта, технических требований и фактического объема работ.

Любая ориентировочная цена, расчет или техническая рекомендация на сайте или в автоматическом ответе носит информационный характер, если иное не подтверждено письменным предложением.

## 4. Монтаж и техническое обслуживание

Клиент должен обеспечить безопасный и согласованный доступ к объекту, необходимые разрешения и информацию об обстоятельствах, которые могут повлиять на безопасность или выполнение работ.

Изменение объема, дополнительный кабель, оборудование, строительные работы, сложный доступ или изменение согласованных условий могут повлиять на цену и сроки.

## 5. Оборудование и сторонние продукты

SafeTech может работать с оборудованием и программными сервисами разных производителей. Функции сторонних продуктов, cloud/P2P-сервисы, приложения, firmware, гарантии и доступность могут изменяться производителем или поставщиком и регулируются их условиями.

## 6. AI-консультант и автоматическая информация

AI-консультант предназначен для первичной информации и быстрой обработки запроса. Его ответы могут быть неполными и требовать проверки специалистом.

Окончательное проектирование системы безопасности, электрические/сетевые решения, объем хранения, совместимость оборудования и стоимость монтажа должны подтверждаться с учетом конкретных требований.

## 7. Запрещенное использование

Запрещено использовать сайт для незаконной деятельности, попыток несанкционированного доступа, распространения вредоносного кода, спама, нарушения работы сервисов или прав SafeTech и других пользователей.

## 8. Интеллектуальная собственность

Тексты, дизайн, брендинг, фотографии, видео, описания проектов и другие материалы сайта защищены соответствующими правами, кроме материалов, принадлежащих третьим лицам. Коммерческое использование или массовое копирование без разрешения запрещено, кроме случаев, разрешенных законом.

## 9. Ограничение ответственности

Мы стремимся публиковать точную и актуальную информацию, но не гарантируем, что вся информация всегда будет безошибочной или постоянно доступной.

В пределах, разрешенных законом, SafeTech не несет ответственности за косвенные убытки, возникшие исключительно из-за использования общей информации сайта, стороннего сервиса или автоматического ответа AI без конкретной технической оценки.

## 10. Конфиденциальность

Информация об обработке персональных данных приведена в Политике конфиденциальности SafeTech по адресу /privacy.

## 11. Изменения и контакты

Условия могут периодически обновляться. Актуальная версия всегда публикуется на этой странице.

По вопросам условий или услуг свяжитесь с нами через контактные каналы, опубликованные на safetech.ge.
TEXT,
                ],
            ],
        ];
    }
};
