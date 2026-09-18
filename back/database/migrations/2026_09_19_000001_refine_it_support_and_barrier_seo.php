<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->updateBusinessItCategory();
        $this->updateBusinessItSupport();
        $this->updateTbilisiItLanding();
        $this->updateBarrierService();
    }

    public function down(): void
    {
        // Preserve SEO/editorial changes if this migration is rolled back.
    }

    private function updateBusinessItCategory(): void
    {
        $category = DB::table('category_for_services')->where('slug', 'business-it')->first();

        if (! $category) {
            return;
        }

        $translations = $this->decode($category->translations ?? null);
        $translations['fields'] ??= [];
        $translations['fields']['name'] = [
            'ka' => 'ბიზნეს IT სისტემები',
            'en' => 'Business IT Systems',
            'ru' => 'IT-системы для бизнеса',
        ];
        $translations['fields']['seo_title'] = [
            'ka' => 'ბიზნეს IT სისტემები და IT ინფრასტრუქტურა',
            'en' => 'Business IT Systems and Infrastructure',
            'ru' => 'Бизнес IT-системы и инфраструктура',
        ];
        $translations['fields']['seo_description'] = [
            'ka' => 'ბიზნეს IT სისტემების კატალოგი: POS, კომპიუტერული გარემო, ქსელი, Wi-Fi, სერვერული და სარეზერვო ინფრასტრუქტურა და შესაბამისი ტექნიკური სერვისები. IT მხარდაჭერის სრული მომსახურება წარმოდგენილია ცალკე სპეციალიზებულ გვერდზე.',
            'en' => 'Business IT systems catalog covering POS, workplace computing, networks, Wi-Fi, server and backup infrastructure, with dedicated technical services. Full IT support is presented on its dedicated service page.',
            'ru' => 'Каталог бизнес IT-систем: POS, рабочие компьютеры, сети, Wi-Fi, серверная и резервная инфраструктура и профильные технические услуги. Полная IT-поддержка представлена на отдельной странице услуги.',
        ];
        $translations['fields']['intro_text'] = $translations['fields']['seo_description'];
        $translations['keywords'] = [
            'ka' => ['ბიზნეს IT სისტემები', 'IT ინფრასტრუქტურა', 'POS და ქსელური სისტემები'],
            'en' => ['business IT systems', 'IT infrastructure', 'POS and network systems'],
            'ru' => ['бизнес IT-системы', 'IT-инфраструктура', 'POS и сетевые системы'],
        ];

        DB::table('category_for_services')->where('id', $category->id)->update([
            'name' => 'ბიზნეს IT სისტემები',
            'seo_title' => 'ბიზნეს IT სისტემები და IT ინფრასტრუქტურა',
            'seo_description' => $translations['fields']['seo_description']['ka'],
            'intro_text' => $translations['fields']['intro_text']['ka'],
            'seo_keywords' => $this->json($translations['keywords']['ka']),
            'noindex' => false,
            'translations' => $this->json($translations),
            'updated_at' => now(),
        ]);
    }

    private function updateBusinessItSupport(): void
    {
        $service = DB::table('services')->where('slug', 'business-it-support')->first();

        if (! $service) {
            return;
        }

        $copy = [
            'ka' => [
                'title' => 'IT მხარდაჭერა და ტექნიკური მომსახურება ბიზნესისთვის',
                'description' => 'ერთჯერადი და აბონენტური IT მხარდაჭერა ბიზნესისთვის: Windows და კომპიუტერები, პრინტერები, LAN/Wi-Fi, როუტერები, POS, მომხმარებლები, Microsoft 365, VPN, სერვერები, NAS და სარეზერვო ასლები — დისტანციურად ან ადგილზე, სამუშაოს ტიპის მიხედვით.',
                'seoTitle' => 'IT მხარდაჭერა და IT მომსახურება ბიზნესისთვის | SafeTech',
                'seoDescription' => 'IT მხარდაჭერა და ტექნიკური მომსახურება ბიზნესისთვის — კომპიუტერები, Windows, პრინტერები, ქსელი, Wi-Fi, POS, Microsoft 365, VPN, სერვერები, NAS და Backup. ერთჯერადი ან აბონენტური ფორმატი.',
            ],
            'en' => [
                'title' => 'IT Support and Technical Services for Business',
                'description' => 'One-time and managed IT support for businesses: Windows and computers, printers, LAN/Wi-Fi, routers, POS, users, Microsoft 365, VPN, servers, NAS and backups, delivered remotely or on site depending on the task.',
                'seoTitle' => 'Business IT Support and IT Services | SafeTech',
                'seoDescription' => 'IT support and technical services for business: computers, Windows, printers, networks, Wi-Fi, POS, Microsoft 365, VPN, servers, NAS and backup. One-time or managed support.',
            ],
            'ru' => [
                'title' => 'IT-поддержка и техническое обслуживание бизнеса',
                'description' => 'Разовая и абонентская IT-поддержка бизнеса: Windows и компьютеры, принтеры, LAN/Wi-Fi, роутеры, POS, пользователи, Microsoft 365, VPN, серверы, NAS и резервное копирование — удаленно или с выездом в зависимости от задачи.',
                'seoTitle' => 'IT-поддержка и IT-услуги для бизнеса | SafeTech',
                'seoDescription' => 'IT-поддержка и техническое обслуживание бизнеса: компьютеры, Windows, принтеры, сеть, Wi-Fi, POS, Microsoft 365, VPN, серверы, NAS и резервное копирование. Разовый или абонентский формат.',
            ],
        ];

        $translations = $this->decode($service->translations ?? null);
        $translations['fields'] ??= [];
        foreach (['title', 'description', 'seoTitle', 'seoDescription'] as $field) {
            $translations['fields'][$field] = [
                'ka' => $copy['ka'][$field],
                'en' => $copy['en'][$field],
                'ru' => $copy['ru'][$field],
            ];
        }
        $translations['keywords'] = [
            'ka' => ['IT მხარდაჭერა', 'IT მომსახურება ბიზნესისთვის', 'ტექნიკური მხარდაჭერა', 'აბონენტური IT მომსახურება'],
            'en' => ['business IT support', 'IT services for business', 'managed IT support', 'remote IT support Georgia'],
            'ru' => ['IT-поддержка бизнеса', 'IT-услуги для бизнеса', 'абонентская IT-поддержка', 'удаленная IT-поддержка'],
        ];

        $seo = $this->decode($service->seo ?? null);
        $seo['title'] = $copy['ka']['seoTitle'];
        $seo['description'] = $copy['ka']['seoDescription'];
        $seo['noindex'] = false;
        $seo['schema_type'] = 'Service';

        DB::table('services')->where('id', $service->id)->update([
            'title' => $copy['ka']['title'],
            'description' => $copy['ka']['description'],
            'short_description' => $copy['ka']['description'],
            'long_description' => $copy['ka']['description'],
            'seo_description' => $copy['ka']['seoDescription'],
            'keywords' => $this->json($translations['keywords']['ka']),
            'seo' => $this->json($seo),
            'translations' => $this->json($translations),
            'updated_at' => now(),
        ]);

        $this->upsertFaqs((int) $service->id, 'business-it-support', [
            [
                'key' => 'format',
                'sort' => 1,
                'q' => [
                    'ka' => 'დისტანციური მხარდაჭერა გაქვთ?',
                    'en' => 'Do you provide remote support?',
                    'ru' => 'Вы оказываете удаленную поддержку?',
                ],
                'a' => [
                    'ka' => 'დიახ. Windows-ის, პროგრამების, ანგარიშების, VPN-ის და ზოგი ქსელური პრობლემის მოგვარება ხშირად დისტანციურად შეიძლება; აპარატურული, კაბელირების ან ადგილზე შესამოწმებელი პრობლემა ტექნიკოსის ვიზიტს მოითხოვს.',
                    'en' => 'Yes. Windows, software, account, VPN and some network issues can often be handled remotely; hardware, cabling and site-specific faults require an on-site visit.',
                    'ru' => 'Да. Проблемы Windows, программ, учетных записей, VPN и часть сетевых задач часто решаются удаленно; аппаратные, кабельные и объектовые неисправности требуют выезда.',
                ],
            ],
            [
                'key' => 'subscription',
                'sort' => 2,
                'q' => [
                    'ka' => 'აბონენტური მომსახურება შესაძლებელია?',
                    'en' => 'Is managed monthly support available?',
                    'ru' => 'Доступно абонентское обслуживание?',
                ],
                'a' => [
                    'ka' => 'დიახ. მომსახურების გეგმა ფორმირდება მოწყობილობების, მომხმარებლების, სისტემების, პრიორიტეტებისა და შეთანხმებული რეაგირების დროის მიხედვით.',
                    'en' => 'Yes. The plan is based on devices, users, systems, priorities, and agreed response times.',
                    'ru' => 'Да. План формируется по устройствам, пользователям, системам, приоритетам и согласованному времени реакции.',
                ],
            ],
            [
                'key' => 'scope',
                'sort' => 3,
                'q' => [
                    'ka' => 'რა სისტემებს მოიცავს მხარდაჭერა?',
                    'en' => 'What systems can be supported?',
                    'ru' => 'Какие системы входят в поддержку?',
                ],
                'a' => [
                    'ka' => 'სამუშაოს შეთანხმებული მოცულობის მიხედვით მხარდაჭერა შეიძლება მოიცავდეს კომპიუტერებს, Windows-ს, პროგრამებს, პრინტერებს, LAN/Wi-Fi-ს, როუტერებს, POS-ს, Microsoft 365-ს, VPN-ს, სერვერებს, NAS-სა და Backup-ს.',
                    'en' => 'Depending on the agreed scope, support can cover computers, Windows, software, printers, LAN/Wi-Fi, routers, POS, Microsoft 365, VPN, servers, NAS and backups.',
                    'ru' => 'В согласованный объем могут входить компьютеры, Windows, программы, принтеры, LAN/Wi-Fi, роутеры, POS, Microsoft 365, VPN, серверы, NAS и резервное копирование.',
                ],
            ],
        ]);
    }

    private function updateTbilisiItLanding(): void
    {
        $serviceId = DB::table('services')->where('slug', 'business-it-support')->value('id');

        if (! $serviceId) {
            return;
        }

        $landing = DB::table('local_service_landings')
            ->where('service_id', $serviceId)
            ->where('location_slug', 'tbilisi')
            ->first();

        if (! $landing) {
            return;
        }

        $copy = [
            'ka' => [
                'title' => 'IT მხარდაჭერა თბილისში — ბიზნესისთვის',
                'excerpt' => 'IT მხარდაჭერა თბილისში ოფისებისთვის, მაღაზიებისთვის, სასტუმროებისა და სხვა ბიზნესებისთვის — დისტანციური დახმარება და ადგილზე ტექნიკური ვიზიტი სამუშაოს ტიპის მიხედვით.',
                'content' => "SafeTech უზრუნველყოფს ბიზნეს IT მხარდაჭერას თბილისში: Windows და კომპიუტერები, პრინტერები, LAN/Wi-Fi, როუტერები, POS, მომხმარებლები, Microsoft 365, VPN, სერვერები, NAS და Backup შეთანხმებული სამუშაოს ფარგლებში.\n\nლოკალური გვერდის მიზანია თბილისის ობიექტებისთვის ადგილზე და დისტანციური მხარდაჭერის პირობების აღწერა. სრული IT მომსახურებების ჩამონათვალი და საერთო სერვისის პირობები მოცემულია მთავარ IT მხარდაჭერის გვერდზე.\n\nსამუშაო იწყება პრობლემის ან ინფრასტრუქტურის შეფასებით, შემდეგ განისაზღვრება დისტანციური ჩარევა საკმარისია თუ საჭიროა ტექნიკოსის ადგილზე ვიზიტი.",
                'seoTitle' => 'IT მხარდაჭერა თბილისში — ბიზნესისთვის | SafeTech',
                'seoDescription' => 'IT მხარდაჭერა თბილისში ბიზნესისთვის: Windows, კომპიუტერები, პრინტერები, ქსელი, Wi-Fi, POS, Microsoft 365, VPN, სერვერები და Backup — დისტანციურად ან ადგილზე.',
                'primaryKeyword' => 'IT მხარდაჭერა თბილისში',
            ],
            'en' => [
                'title' => 'IT Support in Tbilisi for Business',
                'excerpt' => 'IT support in Tbilisi for offices, shops, hotels and other businesses, with remote assistance and on-site technical visits depending on the task.',
                'content' => "SafeTech provides business IT support in Tbilisi for Windows and computers, printers, LAN/Wi-Fi, routers, POS, users, Microsoft 365, VPN, servers, NAS and backups within the agreed scope.\n\nThis local page focuses on remote and on-site support for Tbilisi properties. The complete service scope and general support options are described on the main IT Support page.\n\nWork starts with an issue or infrastructure assessment, followed by a decision on whether remote assistance is sufficient or an on-site visit is required.",
                'seoTitle' => 'IT Support in Tbilisi for Business | SafeTech',
                'seoDescription' => 'Business IT support in Tbilisi: Windows, computers, printers, networks, Wi-Fi, POS, Microsoft 365, VPN, servers and backup, remotely or on site.',
                'primaryKeyword' => 'IT support in Tbilisi',
            ],
            'ru' => [
                'title' => 'IT-поддержка бизнеса в Тбилиси',
                'excerpt' => 'IT-поддержка в Тбилиси для офисов, магазинов, отелей и другого бизнеса: удаленная помощь и выезд технического специалиста в зависимости от задачи.',
                'content' => "SafeTech оказывает IT-поддержку бизнеса в Тбилиси: Windows и компьютеры, принтеры, LAN/Wi-Fi, роутеры, POS, пользователи, Microsoft 365, VPN, серверы, NAS и резервное копирование в согласованном объеме.\n\nЭта локальная страница посвящена удаленной и выездной поддержке объектов в Тбилиси. Полный перечень услуг и общие условия представлены на основной странице IT-поддержки.\n\nРабота начинается с оценки проблемы или инфраструктуры, после чего определяется, достаточно ли удаленной помощи или нужен выезд специалиста.",
                'seoTitle' => 'IT-поддержка бизнеса в Тбилиси | SafeTech',
                'seoDescription' => 'IT-поддержка бизнеса в Тбилиси: Windows, компьютеры, принтеры, сеть, Wi-Fi, POS, Microsoft 365, VPN, серверы и Backup — удаленно или с выездом.',
                'primaryKeyword' => 'IT-поддержка в Тбилиси',
            ],
        ];

        $translations = $this->decode($landing->translations ?? null);
        $translations['fields'] ??= [];
        foreach (['title', 'excerpt', 'content', 'seoTitle', 'seoDescription', 'primaryKeyword'] as $field) {
            $translations['fields'][$field] = [
                'ka' => $copy['ka'][$field],
                'en' => $copy['en'][$field],
                'ru' => $copy['ru'][$field],
            ];
        }
        $translations['keywords'] = [
            'ka' => ['IT მხარდაჭერა თბილისში', 'IT მომსახურება თბილისში', 'ბიზნეს IT მხარდაჭერა თბილისში', 'კომპიუტერების ტექნიკური მხარდაჭერა თბილისში'],
            'en' => ['IT support Tbilisi', 'business IT support Tbilisi', 'IT services Tbilisi', 'on-site IT support Tbilisi'],
            'ru' => ['IT-поддержка Тбилиси', 'IT-услуги Тбилиси', 'IT-поддержка бизнеса Тбилиси', 'выездная IT-поддержка Тбилиси'],
        ];

        DB::table('local_service_landings')->where('id', $landing->id)->update([
            'eyebrow' => 'IT მხარდაჭერა ბიზნესისთვის — თბილისი',
            'title' => $copy['ka']['title'],
            'excerpt' => $copy['ka']['excerpt'],
            'content' => $copy['ka']['content'],
            'primary_keyword' => $copy['ka']['primaryKeyword'],
            'keywords' => $this->json($translations['keywords']['ka']),
            'seo_title' => $copy['ka']['seoTitle'],
            'seo_description' => $copy['ka']['seoDescription'],
            'noindex' => false,
            'translations' => $this->json($translations),
            'updated_at' => now(),
        ]);
    }

    private function updateBarrierService(): void
    {
        $service = DB::table('services')->where('slug', 'barrier-gate-installation')->first();

        if (! $service) {
            return;
        }

        $copy = [
            'ka' => [
                'title' => 'შლაგბაუმის მონტაჟი და ავტომატური მართვის სისტემები',
                'description' => 'ავტომატური შლაგბაუმის დაგეგმვა, მონტაჟი და კონფიგურაცია პულტით, GSM-ით, RFID/Access Control-ით ან თავსებადი LPR ნომრის ამოცნობით. უსაფრთხოების სქემა შეიძლება მოიცავდეს Photocell-სა და Loop Detector-ს; შესაძლებლობები დამოკიდებულია შერჩეულ კონტროლერსა და მოდელზე.',
                'seoTitle' => 'შლაგბაუმის მონტაჟი — LPR, GSM და ავტომატური მართვა | SafeTech',
                'seoDescription' => 'შლაგბაუმის მონტაჟი და გამართვა: პულტი, GSM, RFID/Access Control, თავსებადი LPR კამერა, Photocell, Loop Detector, ავტომატური დახურვა და უსაფრთხოების ტესტირება.',
            ],
            'en' => [
                'title' => 'Barrier Gate Installation and Automated Access Control',
                'description' => 'Planning, installation and configuration of automatic barrier gates with remote control, GSM, RFID/access control or compatible LPR plate recognition. Safety can include photocells and loop detectors; available functions depend on the selected controller and model.',
                'seoTitle' => 'Barrier Gate Installation — LPR, GSM and Automated Access | SafeTech',
                'seoDescription' => 'Barrier installation and setup with remote, GSM, RFID/access control, compatible LPR camera, photocell, loop detector, automatic closing and safety testing.',
            ],
            'ru' => [
                'title' => 'Монтаж шлагбаума и автоматическое управление доступом',
                'description' => 'Проектирование, монтаж и настройка автоматических шлагбаумов с пультом, GSM, RFID/контролем доступа или совместимым LPR-распознаванием номеров. Безопасность может включать фотоэлементы и Loop Detector; функции зависят от выбранного контроллера и модели.',
                'seoTitle' => 'Монтаж шлагбаума — LPR, GSM и автоматический доступ | SafeTech',
                'seoDescription' => 'Монтаж и настройка шлагбаума: пульт, GSM, RFID/контроль доступа, совместимая LPR-камера, фотоэлементы, Loop Detector, автозакрытие и проверка безопасности.',
            ],
        ];

        $translations = $this->decode($service->translations ?? null);
        $translations['fields'] ??= [];
        foreach (['title', 'description', 'seoTitle', 'seoDescription'] as $field) {
            $translations['fields'][$field] = [
                'ka' => $copy['ka'][$field],
                'en' => $copy['en'][$field],
                'ru' => $copy['ru'][$field],
            ];
        }
        $translations['keywords'] = [
            'ka' => ['შლაგბაუმის მონტაჟი', 'LPR შლაგბაუმი', 'GSM შლაგბაუმი', 'Loop Detector', 'Photocell'],
            'en' => ['barrier gate installation', 'LPR barrier', 'GSM barrier gate', 'loop detector', 'photocell'],
            'ru' => ['монтаж шлагбаума', 'LPR шлагбаум', 'GSM шлагбаум', 'Loop Detector', 'фотоэлемент'],
        ];

        $seo = $this->decode($service->seo ?? null);
        $seo['title'] = $copy['ka']['seoTitle'];
        $seo['description'] = $copy['ka']['seoDescription'];
        $seo['noindex'] = false;
        $seo['schema_type'] = 'Service';

        DB::table('services')->where('id', $service->id)->update([
            'title' => $copy['ka']['title'],
            'description' => $copy['ka']['description'],
            'short_description' => $copy['ka']['description'],
            'long_description' => $copy['ka']['description'],
            'seo_description' => $copy['ka']['seoDescription'],
            'keywords' => $this->json($translations['keywords']['ka']),
            'seo' => $this->json($seo),
            'translations' => $this->json($translations),
            'updated_at' => now(),
        ]);

        DB::table('faqs')
            ->where('service_id', $service->id)
            ->where('context', 'service:barrier-gate-installation:safety')
            ->delete();

        $this->upsertFaqs((int) $service->id, 'barrier-gate-installation', [
            [
                'key' => 'selection',
                'sort' => 1,
                'q' => [
                    'ka' => 'რომელი სიგრძის შლაგბაუმი მჭირდება?',
                    'en' => 'What boom length do I need?',
                    'ru' => 'Какая длина стрелы нужна?',
                ],
                'a' => [
                    'ka' => 'სიგრძე და ძრავის კლასი შეირჩევა გასასვლელის სიგანის, გამოყენების ინტენსივობისა და გახსნის სიჩქარის მიხედვით.',
                    'en' => 'Boom length and motor class depend on lane width, usage frequency, and required opening speed.',
                    'ru' => 'Длина стрелы и класс привода выбираются по ширине проезда, интенсивности и скорости открытия.',
                ],
            ],
            [
                'key' => 'price',
                'sort' => 2,
                'q' => [
                    'ka' => 'რა ღირს შლაგბაუმის მონტაჟი?',
                    'en' => 'How much does barrier gate installation cost?',
                    'ru' => 'Сколько стоит монтаж шлагбаума?',
                ],
                'a' => [
                    'ka' => 'საბოლოო ფასი დამოკიდებულია გასასვლელის სიგანეზე, შლაგბაუმის მოდელზე, ფუნდამენტსა და კვებაზე, უსაფრთხოების სენსორებზე და მართვის მეთოდზე. ზუსტი შეთავაზება მზადდება ობიექტის მოთხოვნების დაზუსტების შემდეგ.',
                    'en' => 'Final cost depends on lane width, barrier model, foundation and power, safety sensors, and the control method. An accurate quote is prepared after the site requirements are confirmed.',
                    'ru' => 'Итоговая стоимость зависит от ширины проезда, модели шлагбаума, основания и питания, датчиков безопасности и способа управления. Точное предложение формируется после уточнения требований объекта.',
                ],
            ],
            [
                'key' => 'lpr',
                'sort' => 3,
                'q' => [
                    'ka' => 'როგორ მუშაობს ნომრის ამომცნობი LPR კამერა?',
                    'en' => 'How does an LPR plate-recognition camera work?',
                    'ru' => 'Как работает LPR-камера распознавания номеров?',
                ],
                'a' => [
                    'ka' => 'თავსებადი LPR კამერა კითხულობს სანომრე ნიშნებს და კონტროლერის სცენარის მიხედვით დაშვებულ ნომერზე შეიძლება გაიცეს გახსნის სიგნალი. შედეგი დამოკიდებულია კამერის მოდელზე, კუთხეზე, განათებასა და კონფიგურაციაზე.',
                    'en' => 'A compatible LPR camera reads license plates and can trigger opening for authorized plates according to the controller logic. Results depend on the camera model, angle, lighting, and configuration.',
                    'ru' => 'Совместимая LPR-камера считывает номера и по логике контроллера может подавать сигнал открытия для разрешенных номеров. Результат зависит от модели камеры, угла, освещения и настройки.',
                ],
            ],
            [
                'key' => 'control',
                'sort' => 4,
                'q' => [
                    'ka' => 'შესაძლებელია შლაგბაუმის ტელეფონით გაღება?',
                    'en' => 'Can the barrier be opened by phone?',
                    'ru' => 'Можно открывать шлагбаум с телефона?',
                ],
                'a' => [
                    'ka' => 'დიახ, თუ არჩეული სისტემა მხარს უჭერს GSM კონტროლერს, აპლიკაციას ან შესაბამის Access Control ინტეგრაციას.',
                    'en' => 'Yes, when the selected system supports a GSM controller, app, or suitable access-control integration.',
                    'ru' => 'Да, если выбранная система поддерживает GSM-контроллер, приложение или соответствующую интеграцию контроля доступа.',
                ],
            ],
            [
                'key' => 'loop-photocell',
                'sort' => 5,
                'q' => [
                    'ka' => 'რა განსხვავებაა Loop Detector-სა და Photocell-ს შორის?',
                    'en' => 'What is the difference between a loop detector and a photocell?',
                    'ru' => 'В чем разница между Loop Detector и фотоэлементом?',
                ],
                'a' => [
                    'ka' => 'Loop Detector გზის საფარში მოთავსებული ინდუქციური მარყუჟით ავტომობილს აფიქსირებს, Photocell კი სხივის გადაკვეთას აკონტროლებს. კონკრეტულ ობიექტზე შეიძლება გამოიყენებოდეს ერთი ან ორივე უსაფრთხოების სქემის მიხედვით.',
                    'en' => 'A loop detector senses a vehicle through an inductive loop in the roadway, while a photocell monitors interruption of an optical beam. One or both may be used depending on the safety design.',
                    'ru' => 'Loop Detector определяет автомобиль индукционной петлей в покрытии, а фотоэлемент контролирует пересечение светового луча. В зависимости от схемы безопасности может применяться один или оба элемента.',
                ],
            ],
            [
                'key' => 'dual-control',
                'sort' => 6,
                'q' => [
                    'ka' => 'შეიძლება პულტით და GSM-ით ერთდროულად მართვა?',
                    'en' => 'Can remote control and GSM be used together?',
                    'ru' => 'Можно одновременно использовать пульт и GSM?',
                ],
                'a' => [
                    'ka' => 'ხშირად შესაძლებელია, თუ კონტროლერს აქვს შესაბამისი შესასვლელები და არჩეული მოდულები თავსებადია. საბოლოო სქემა კონკრეტული კონტროლერის მიხედვით მოწმდება.',
                    'en' => 'Often yes, if the controller has the required inputs and the selected modules are compatible. The final wiring and logic must be verified for the specific controller.',
                    'ru' => 'Часто да, если у контроллера есть нужные входы и выбранные модули совместимы. Итоговая схема проверяется по конкретному контроллеру.',
                ],
            ],
            [
                'key' => 'upgrade',
                'sort' => 7,
                'q' => [
                    'ka' => 'შესაძლებელია უკვე დამონტაჟებული შლაგბაუმის განახლება?',
                    'en' => 'Can an existing barrier gate be upgraded?',
                    'ru' => 'Можно модернизировать уже установленный шлагбаум?',
                ],
                'a' => [
                    'ka' => 'შესაძლებელია დიაგნოსტიკა და, თავსებადობის შემთხვევაში, მართვის მოდულის, უსაფრთხოების სენსორების, GSM-ის, LPR-ის ან Access Control ინტეგრაციის დამატება. წინასწარ მოწმდება არსებული კონტროლერი და მექანიკა.',
                    'en' => 'We can diagnose an existing system and, where compatible, add control modules, safety sensors, GSM, LPR, or access-control integration. The existing controller and mechanics are checked first.',
                    'ru' => 'Возможна диагностика и, при совместимости, добавление модулей управления, датчиков безопасности, GSM, LPR или интеграции контроля доступа. Сначала проверяются существующий контроллер и механика.',
                ],
            ],
        ]);
    }

    /**
     * @param array<int, array{key:string,sort:int,q:array<string,string>,a:array<string,string>}> $items
     */
    private function upsertFaqs(int $serviceId, string $slug, array $items): void
    {
        foreach ($items as $item) {
            DB::table('faqs')->updateOrInsert(
                [
                    'service_id' => $serviceId,
                    'context' => "service:{$slug}:{$item['key']}",
                ],
                [
                    'question' => $item['q']['ka'],
                    'answer' => $item['a']['ka'],
                    'is_active' => true,
                    'sort_order' => $item['sort'],
                    'translations' => $this->json([
                        'fields' => [
                            'question' => $item['q'],
                            'answer' => $item['a'],
                        ],
                    ]),
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }
    }

    /** @return array<string, mixed> */
    private function decode(mixed $value): array
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        return is_array($value) ? $value : [];
    }

    private function json(array $value): string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
};
