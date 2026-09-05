<?php

use App\Models\Faq;
use App\Models\Service;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        foreach ($this->services() as $slug => $data) {
            $service = Service::query()->where('slug', $slug)->first();

            if (! $service) {
                continue;
            }

            $service->forceFill([
                'name' => $data['name']['ka'],
                'eyebrow' => $data['eyebrow']['ka'],
                'title' => $data['title']['ka'],
                'description' => $data['description']['ka'],
                'short_description' => $data['description']['ka'],
                'long_description' => $data['long_description']['ka'],
                'seo_description' => $data['seo_description']['ka'],
                'keywords' => $data['keywords']['ka'],
                'overview' => [
                    'title' => $data['overview_title']['ka'],
                    'paragraphs' => [
                        $data['description']['ka'],
                        $data['long_description']['ka'],
                    ],
                    'stats' => [],
                ],
                'highlights' => $data['highlights']['ka'],
                'features' => $data['highlights']['ka'],
                'benefits' => array_map(
                    fn (string $item): array => ['title' => $item, 'description' => ''],
                    $data['highlights']['ka'],
                ),
                'solutions' => array_map(
                    fn (string $item): array => ['title' => $item, 'description' => ''],
                    $data['solutions']['ka'],
                ),
                'seo' => [
                    'title' => $data['seo_title']['ka'],
                    'description' => $data['seo_description']['ka'],
                    'noindex' => false,
                ],
                'button_text' => $data['button_text']['ka'],
                'cta_title' => $data['cta_title']['ka'],
                'cta_description' => $data['cta_description']['ka'],
                'translations' => $this->translations($service, $data),
            ])->save();

            foreach ($data['faqs'] as $index => $faq) {
                $context = "service:{$slug}:organic-growth-{$faq['key']}";
                $record = Faq::query()->firstOrNew([
                    'service_id' => $service->getKey(),
                    'context' => $context,
                ]);

                $record->forceFill([
                    'question' => $faq['question']['ka'],
                    'answer' => $faq['answer']['ka'],
                    'is_active' => true,
                    'sort_order' => 40 + $index,
                    'translations' => [
                        'fields' => [
                            'question' => $faq['question'],
                            'answer' => $faq['answer'],
                        ],
                    ],
                ])->save();
            }
        }
    }

    public function down(): void
    {
        Faq::query()
            ->where('context', 'like', 'service:%:organic-growth-%')
            ->delete();
    }

    private function translations(Service $service, array $data): array
    {
        $translations = is_array($service->translations) ? $service->translations : [];
        $translations['fields'] ??= [];

        foreach ([
            'name' => 'name',
            'eyebrow' => 'eyebrow',
            'title' => 'title',
            'description' => 'description',
            'seoTitle' => 'seo_title',
            'seoDescription' => 'seo_description',
        ] as $field => $source) {
            $translations['fields'][$field] = $data[$source];
        }

        return $translations;
    }

    private function services(): array
    {
        return [
            'security-camera-installation' => $this->service(
                ['უსაფრთხოების კამერების მონტაჟი', 'Security Camera Installation', 'Монтаж камер видеонаблюдения'],
                ['ვიდეომეთვალყურეობა სახლისა და ბიზნესისთვის', 'CCTV for homes and businesses', 'Видеонаблюдение для дома и бизнеса'],
                ['უსაფრთხოების კამერების მონტაჟი — IP, PoE, NVR/DVR და ტელეფონიდან ნახვა', 'Security Camera Installation — IP, PoE, NVR/DVR and Mobile Viewing', 'Монтаж камер — IP, PoE, NVR/DVR и просмотр с телефона'],
                ['ვგეგმავთ კამერების სწორ განლაგებას, ვაწყობთ CAT6/PoE ინფრასტრუქტურას, ვამონტაჟებთ IP ან ანალოგურ კამერებს, NVR/DVR-ს, HDD-ს და ვამართავთ უსაფრთხო დისტანციურ წვდომას.', 'We plan camera placement, build CAT6/PoE infrastructure, install IP or analog cameras, NVR/DVR and storage, and configure secure remote access.', 'Планируем размещение камер, строим CAT6/PoE инфраструктуру, устанавливаем IP или аналоговые камеры, NVR/DVR и накопитель, настраиваем безопасный удаленный доступ.'],
                ['სისტემა იგეგმება ობიექტის რეალური რისკების მიხედვით: შესასვლელები, ეზო, პერიმეტრი, სალარო, საწყობი და სხვა კრიტიკული ზონები. წინასწარ ვითვლით ჩანაწერის საჭირო პერიოდს, დისკის მოცულობას და ქსელის დატვირთვას, რათა სისტემა სტაბილურად იმუშაოს 24/7.', 'The system is designed around real site risks: entrances, yard, perimeter, checkout, storage and other critical zones. We calculate retention, storage and network load before installation for reliable 24/7 operation.', 'Система проектируется по реальным рискам объекта: входы, двор, периметр, касса, склад и другие критические зоны. Заранее рассчитываем архив, объем диска и нагрузку сети для стабильной работы 24/7.'],
                ['კამერების მონტაჟი თბილისში და საქართველოში | SafeTech', 'CCTV Installation in Tbilisi and Georgia | SafeTech', 'Монтаж видеонаблюдения в Тбилиси и Грузии | SafeTech'],
                ['უსაფრთხოების კამერების პროფესიონალური მონტაჟი თბილისში და რეგიონებში: IP/PoE კამერები, NVR/DVR, 24/7 ჩანაწერი, HDD-ის გამოთვლა, Full Color ღამის ხედვა და ტელეფონიდან ნახვა.', 'Professional CCTV installation in Tbilisi and across Georgia: IP/PoE cameras, NVR/DVR, 24/7 recording, storage sizing, full-color night vision and mobile viewing.', 'Профессиональный монтаж видеонаблюдения в Тбилиси и регионах: IP/PoE камеры, NVR/DVR, запись 24/7, расчет диска, цветное ночное видение и просмотр с телефона.'],
                ['კამერების მონტაჟი', 'ვიდეოკამერების მონტაჟი', 'უსაფრთხოების კამერები თბილისი', 'IP კამერები', 'PoE კამერები', 'NVR მონტაჟი', 'Full Color კამერა'],
                ['სწორი ხედვის კუთხეები და მკვდარი ზონების შემცირება', '24/7 ჩანაწერისა და HDD-ის სწორი გამოთვლა', 'ტელეფონიდან უსაფრთხო დისტანციური ნახვა'],
                ['ობიექტის შეფასება და კამერების წერტილების დაგეგმვა', 'CAT6/PoE კაბელირება და კამერების მონტაჟი', 'NVR/DVR, ჩანაწერი, დეტექცია და აპლიკაციის გამართვა'],
                ['როგორ იგეგმება კამერების სისტემა', 'How a CCTV system is planned', 'Как проектируется система видеонаблюдения'],
                ['მიიღეთ კამერების სისტემის შეთავაზება', 'Get a CCTV system quote', 'Получить расчет системы видеонаблюдения'],
                ['მოგვწერეთ რამდენი კამერა გჭირდებათ და სად მდებარეობს ობიექტი — დაგეხმარებით კამერების რაოდენობის, ტიპისა და ჩანაწერის მოცულობის შერჩევაში.', 'Tell us how many cameras you need and where the property is located — we will help select camera count, type and storage.', 'Сообщите, сколько камер нужно и где находится объект — поможем подобрать количество, тип и объем архива.'],
                [
                    $this->faq('price', 'რა განსაზღვრავს კამერების მონტაჟის ფასს?', 'What determines CCTV installation cost?', 'От чего зависит стоимость монтажа камер?', 'ფასი დამოკიდებულია კამერების რაოდენობაზე, გარჩევადობაზე, კაბელის სიგრძეზე, NVR/DVR-ზე, HDD-ის მოცულობაზე, UPS-ზე და მონტაჟის სირთულეზე. ზუსტი შეთავაზება მზადდება ობიექტის მოთხოვნების მიხედვით.', 'Cost depends on camera count, resolution, cable length, NVR/DVR, storage size, UPS and installation complexity. A precise quote is prepared for the site requirements.', 'Цена зависит от количества и разрешения камер, длины кабеля, NVR/DVR, объема диска, UPS и сложности монтажа. Точный расчет делается под объект.'),
                    $this->faq('internet', 'ინტერნეტის გათიშვისას კამერები ჩაიწერს?', 'Will cameras record if the internet goes down?', 'Будут ли камеры записывать без интернета?', 'თუ კამერები და NVR/DVR ლოკალურ ქსელში მუშაობს და კვება აქვს, ჩანაწერი გაგრძელდება ინტერნეტის გარეშეც. ინტერნეტი ძირითადად დისტანციური ნახვისთვისაა საჭირო.', 'If cameras and the recorder work on the local network and have power, recording continues without internet. Internet is mainly required for remote viewing.', 'Если камеры и регистратор работают в локальной сети и имеют питание, запись продолжится без интернета. Интернет нужен в основном для удаленного просмотра.'),
                ],
            ),
            'network-cable-installation' => $this->service(
                ['ქსელის კაბელის გაყვანა', 'Network Cable Installation', 'Прокладка сетевого кабеля'],
                ['სტრუქტურირებული CAT6 ქსელი სახლისა და ბიზნესისთვის', 'Structured CAT6 networks for homes and businesses', 'Структурированная CAT6 сеть для дома и бизнеса'],
                ['ქსელის გაყვანა CAT6 — კაბელირება, RJ45, Patch Panel და ტესტირება', 'CAT6 Network Cabling — RJ45, Patch Panel and Testing', 'Прокладка CAT6 — RJ45, Patch Panel и тестирование'],
                ['ვგეგმავთ ქსელის მარშრუტებს და ვაკეთებთ CAT5e/CAT6 კაბელირებას ოფისებში, სახლებში, სასტუმროებში, მაღაზიებსა და საწარმოებში — მარკირებით, RJ45/Keystone დაბოლოებით და ტესტირებით.', 'We design routes and install CAT5e/CAT6 cabling in offices, homes, hotels, shops and industrial sites, with labeling, RJ45/keystone termination and testing.', 'Проектируем трассы и прокладываем CAT5e/CAT6 в офисах, домах, отелях, магазинах и на производстве с маркировкой, оконцовкой RJ45/Keystone и тестированием.'],
                ['ქსელი იგეგმება ისე, რომ მომავალში მოწყობილობების დამატება და პრობლემის დიაგნოსტიკა მარტივი იყოს. საჭიროებისას ვაწყობთ Patch Panel-ს, Rack-ს, Switch-ს, Access Point-ებს და ცალკე VLAN-ებს კამერებისთვის, ოფისისთვის, სტუმრებისთვის ან POS სისტემებისთვის.', 'The network is designed for easy expansion and troubleshooting. When needed we install patch panels, racks, switches, access points and separate VLANs for CCTV, office, guests or POS.', 'Сеть проектируется с учетом расширения и удобной диагностики. При необходимости устанавливаем Patch Panel, Rack, Switch, точки доступа и отдельные VLAN для камер, офиса, гостей или POS.'],
                ['ქსელის გაყვანა CAT6 თბილისში და საქართველოში | SafeTech', 'CAT6 Network Cabling in Tbilisi and Georgia | SafeTech', 'Прокладка CAT6 в Тбилиси и Грузии | SafeTech'],
                ['CAT6 ქსელის პროფესიონალური გაყვანა თბილისში და რეგიონებში: კაბელის მარშრუტი, RJ45/Keystone, Patch Panel, Rack, მარკირება და ხაზების ტესტირება.', 'Professional CAT6 cabling in Tbilisi and across Georgia: routing, RJ45/keystone, patch panels, racks, labeling and line testing.', 'Профессиональная прокладка CAT6 в Тбилиси и регионах: трасса, RJ45/Keystone, Patch Panel, Rack, маркировка и тестирование линий.'],
                ['ქსელის გაყვანა', 'CAT6 კაბელის გაყვანა', 'LAN ქსელი', 'RJ45 მონტაჟი', 'Patch Panel მონტაჟი', 'ქსელის მონტაჟი თბილისი'],
                ['სწორი მარშრუტი და ძალოვანი კაბელებისგან დაშორება', 'ორივე ბოლოს მარკირება და დოკუმენტირება', 'ყველა ხაზის ტესტირება ჩაბარებამდე'],
                ['ქსელის წერტილებისა და მარშრუტების დაგეგმვა', 'CAT6, Keystone, Patch Panel და Rack მონტაჟი', 'Switch-ის მიერთება, ტესტირება და დოკუმენტირება'],
                ['პროფესიონალურად დაგეგმილი ქსელი', 'A professionally planned network', 'Профессионально спроектированная сеть'],
                ['მიიღეთ ქსელის გაყვანის შეთავაზება', 'Get a network cabling quote', 'Получить расчет сетевой прокладки'],
                ['მოგვწერეთ რამდენი ქსელის წერტილი გჭირდებათ და რა ტიპის ობიექტია — დაგეხმარებით კაბელის, Rack-ისა და Switch-ის სწორად დაგეგმვაში.', 'Tell us how many network points you need and the property type — we will help plan cabling, rack and switching.', 'Сообщите количество сетевых точек и тип объекта — поможем правильно спланировать кабель, Rack и Switch.'],
                [
                    $this->faq('meter', 'ქსელის კაბელი მეტრობით როგორ ითვლება?', 'How is network cable length calculated?', 'Как рассчитывается длина сетевого кабеля?', 'ითვლება რეალური მარშრუტი Rack/Switch-იდან თითოეულ ქსელის წერტილამდე და ემატება ტექნიკური მარაგი დაბოლოებისა და მომსახურებისთვის.', 'We calculate the real route from rack/switch to every network point and add service slack for termination and maintenance.', 'Считается реальный маршрут от Rack/Switch до каждой точки плюс технический запас на оконцовку и обслуживание.'),
                    $this->faq('test', 'მონტაჟის შემდეგ ხაზებს ამოწმებთ?', 'Do you test lines after installation?', 'Вы тестируете линии после монтажа?', 'დიახ. ხაზები მოწმდება დაბოლოებაზე, წყვილების თანმიმდევრობაზე და კავშირის გამართულობაზე; პროექტის მოთხოვნის მიხედვით შესაძლებელია უფრო სრულყოფილი ტესტირებაც.', 'Yes. Lines are checked for termination, pair order and connectivity; more advanced testing can be included where required.', 'Да. Проверяем оконцовку, порядок пар и связь; при необходимости выполняется более полное тестирование.'),
                ],
            ),
            'router-wifi-configuration' => $this->service(
                ['Wi‑Fi და როუტერის კონფიგურაცია', 'Wi-Fi and Router Configuration', 'Настройка Wi-Fi и роутера'],
                ['სტაბილური Wi‑Fi და უსაფრთხო ქსელი', 'Stable Wi-Fi and a secure network', 'Стабильный Wi-Fi и защищенная сеть'],
                ['Wi‑Fi და როუტერის გამართვა — MikroTik, Mesh, VLAN, VPN და Firewall', 'Wi-Fi and Router Setup — MikroTik, Mesh, VLAN, VPN and Firewall', 'Настройка Wi-Fi и роутера — MikroTik, Mesh, VLAN, VPN и Firewall'],
                ['ვასწორებთ სუსტ Wi‑Fi დაფარვას, როუტერის პარამეტრებს, MikroTik-ს, Access Point/გადამცემებს, Mesh-ს, DHCP/NAT-ს, VLAN-ს, VPN-სა და Firewall-ს სახლისა და ბიზნესის ქსელებისთვის.', 'We fix weak Wi-Fi coverage and configure routers, MikroTik, access points, mesh, DHCP/NAT, VLAN, VPN and firewalls for home and business networks.', 'Устраняем слабое покрытие Wi-Fi и настраиваем роутеры, MikroTik, точки доступа, Mesh, DHCP/NAT, VLAN, VPN и Firewall для дома и бизнеса.'],
                ['Wi‑Fi-ის პრობლემა ხშირად მხოლოდ უფრო ძლიერი როუტერით არ გვარდება. ვაფასებთ ფართობს, კედლებს, სართულებს, კლიენტების რაოდენობასა და რეალურ დატვირთვას და ამის მიხედვით ვირჩევთ Access Point-ების ან Mesh-ის რაოდენობასა და განლაგებას.', 'Wi-Fi problems are often not solved by a stronger router alone. We assess area, walls, floors, client count and actual load to determine access point or mesh quantity and placement.', 'Проблема Wi-Fi часто не решается только более мощным роутером. Оцениваем площадь, стены, этажи, число клиентов и нагрузку, затем подбираем количество и размещение точек доступа или Mesh.'],
                ['Wi‑Fi და MikroTik-ის გამართვა თბილისში | SafeTech', 'Wi-Fi and MikroTik Setup in Tbilisi | SafeTech', 'Настройка Wi-Fi и MikroTik в Тбилиси | SafeTech'],
                ['Wi‑Fi, MikroTik და როუტერის პროფესიონალური გამართვა: დაფარვის გაუმჯობესება, Mesh/Access Point, DHCP, NAT, VLAN, VPN, Firewall და სტუმრის ქსელი.', 'Professional Wi-Fi, MikroTik and router setup: coverage improvement, mesh/access points, DHCP, NAT, VLAN, VPN, firewall and guest networks.', 'Профессиональная настройка Wi-Fi, MikroTik и роутеров: улучшение покрытия, Mesh/Access Point, DHCP, NAT, VLAN, VPN, Firewall и гостевая сеть.'],
                ['WiFi გამართვა', 'როუტერის კონფიგურაცია', 'MikroTik გამართვა', 'WiFi თბილისი', 'Mesh WiFi', 'VLAN', 'VPN'],
                ['სტაბილური Wi‑Fi დაფარვა რეალური გაზომვების მიხედვით', 'MikroTik, VLAN, VPN და Firewall-ის სწორი კონფიგურაცია', 'სტუმრისა და სამუშაო ქსელების უსაფრთხო განცალკევება'],
                ['დაფარვისა და დატვირთვის შეფასება', 'Router, MikroTik, Mesh ან Access Point-ების კონფიგურაცია', 'სიჩქარის, roaming-ისა და უსაფრთხოების ტესტირება'],
                ['სტაბილური Wi‑Fi იწყება სწორი დაგეგმვით', 'Stable Wi-Fi starts with correct planning', 'Стабильный Wi-Fi начинается с правильного планирования'],
                ['მიიღეთ Wi‑Fi/ქსელის კონსულტაცია', 'Get a Wi-Fi/network consultation', 'Получить консультацию по Wi-Fi/сети'],
                ['აღწერეთ სად არის სუსტი სიგნალი, რამდენი სართულია და რამდენი მოწყობილობა ერთდება — შევარჩევთ შესაბამის Router/Mesh/Access Point გადაწყვეტას.', 'Tell us where signal is weak, the number of floors and connected devices — we will recommend the right router/mesh/access point solution.', 'Опишите, где слабый сигнал, сколько этажей и устройств — подберем подходящий Router/Mesh/Access Point.'],
                [
                    $this->faq('weak', 'რატომ არის Wi‑Fi სუსტი სხვა ოთახში ან სართულზე?', 'Why is Wi-Fi weak in another room or floor?', 'Почему Wi-Fi слабый в другой комнате или на этаже?', 'სიგნალს ასუსტებს მანძილი, ბეტონის კედლები, გადახურვა, ჩარევა და Access Point-ის არასწორი მდებარეობა. სწორი გადაწყვეტა ხშირად რამდენიმე სწორად განთავსებული Access Point-ია.', 'Distance, concrete walls, floors, interference and poor AP placement reduce signal. The right solution is often multiple correctly positioned access points.', 'Сигнал ослабляют расстояние, бетонные стены, перекрытия, помехи и неправильное размещение точки доступа. Часто решение — несколько правильно расположенных точек.'),
                    $this->faq('isp', 'ინტერნეტპროვაიდერის შეცვლის შემდეგ ქსელს გამართავთ?', 'Can you reconfigure a network after changing ISP?', 'Настроите сеть после смены провайдера?', 'დიახ. ვამართავთ WAN/PPPoE/DHCP პარამეტრებს, LAN-ს, სტატიკურ IP-ებს, Port Forwarding-ს, VPN-ს და საჭირო ბიზნეს მოწყობილობებს ახალ ინტერნეტზე.', 'Yes. We configure WAN/PPPoE/DHCP, LAN, static IPs, port forwarding, VPN and required business devices for the new connection.', 'Да. Настраиваем WAN/PPPoE/DHCP, LAN, статические IP, Port Forwarding, VPN и нужные бизнес-устройства под нового провайдера.'),
                ],
            ),
            'business-it-support' => $this->service(
                ['IT მომსახურება და მხარდაჭერა', 'IT Services and Support', 'IT-услуги и поддержка'],
                ['ერთჯერადი და აბონენტური IT მხარდაჭერა ბიზნესისთვის', 'One-time and managed IT support for business', 'Разовая и абонентская IT-поддержка бизнеса'],
                ['IT მომსახურება ბიზნესისთვის — კომპიუტერები, ქსელი, პრინტერები, POS და სერვერები', 'Business IT Support — Computers, Networks, Printers, POS and Servers', 'IT-поддержка бизнеса — компьютеры, сеть, принтеры, POS и серверы'],
                ['ვუზრუნველყოფთ დისტანციურ და ადგილზე IT მხარდაჭერას ოფისებისთვის, მაღაზიებისთვის, სასტუმროებისთვის და სხვა ბიზნესებისთვის: Windows, კომპიუტერები, პრინტერები, ქსელი, Wi‑Fi, POS, მომხმარებლები და სერვერული სერვისები.', 'We provide remote and on-site IT support for offices, shops, hotels and other businesses: Windows, computers, printers, networks, Wi-Fi, POS, users and server services.', 'Оказываем удаленную и выездную IT-поддержку офисов, магазинов, отелей и другого бизнеса: Windows, компьютеры, принтеры, сеть, Wi-Fi, POS, пользователи и серверные сервисы.'],
                ['ერთჯერადი პრობლემის მოგვარების გარდა შესაძლებელია აბონენტური მხარდაჭერა, სადაც წინასწარ განისაზღვრება მოწყობილობების მოცულობა, პრიორიტეტები, რეაგირების წესი, პროფილაქტიკა და საჭირო დოკუმენტაცია.', 'In addition to one-time troubleshooting, managed support is available with defined device scope, priorities, response process, preventive maintenance and documentation.', 'Кроме разового устранения проблем доступна абонентская поддержка с определенным объемом устройств, приоритетами, порядком реакции, профилактикой и документацией.'],
                ['IT მომსახურება თბილისში და საქართველოში | SafeTech', 'Business IT Support in Tbilisi and Georgia | SafeTech', 'IT-поддержка бизнеса в Тбилиси и Грузии | SafeTech'],
                ['IT მომსახურება და ტექნიკური მხარდაჭერა ოფისებისთვის, მაღაზიებისთვის და სხვა ბიზნესებისთვის: კომპიუტერები, Windows, ქსელი, Wi‑Fi, პრინტერები, POS და სერვერები.', 'IT services and technical support for offices, shops and other businesses: computers, Windows, networks, Wi-Fi, printers, POS and servers.', 'IT-услуги и техническая поддержка офисов, магазинов и другого бизнеса: компьютеры, Windows, сеть, Wi-Fi, принтеры, POS и серверы.'],
                ['IT მომსახურება', 'IT support თბილისი', 'კომპიუტერული მომსახურება', 'ქსელის გამართვა', 'პრინტერის გამართვა', 'POS მხარდაჭერა', 'აბონენტური IT მომსახურება'],
                ['დისტანციური და ადგილზე ტექნიკური მხარდაჭერა', 'ქსელის, კომპიუტერებისა და ბიზნეს სისტემების ერთიანი მართვა', 'ერთჯერადი ან აბონენტური მომსახურების ფორმატი'],
                ['ინფრასტრუქტურის და პრობლემის შეფასება', 'დიაგნოსტიკა, შეკეთება და კონფიგურაცია', 'პროფილაქტიკა, დოკუმენტირება და შემდგომი მხარდაჭერა'],
                ['ბიზნესისთვის საიმედო IT მხარდაჭერა', 'Reliable IT support for business', 'Надежная IT-поддержка бизнеса'],
                ['მიიღეთ IT მომსახურების შეთავაზება', 'Get an IT support quote', 'Получить предложение по IT-поддержке'],
                ['მოგვწერეთ რა პრობლემა გაქვთ ან რამდენი სამუშაო ადგილი/მოწყობილობა გჭირდებათ მხარდაჭერაში — შემოგთავაზებთ ერთჯერად ან აბონენტურ ფორმატს.', 'Tell us the problem or how many workstations/devices need support — we will recommend one-time or managed support.', 'Опишите проблему или количество рабочих мест/устройств — предложим разовый или абонентский формат.'],
                [
                    $this->faq('subscription', 'რა განსხვავებაა ერთჯერად და აბონენტურ IT მომსახურებას შორის?', 'What is the difference between one-time and managed IT support?', 'Чем отличается разовая и абонентская IT-поддержка?', 'ერთჯერადი მომსახურება კონკრეტულ პრობლემას აგვარებს. აბონენტური მხარდაჭერა მოიცავს შეთანხმებულ ინფრასტრუქტურაზე რეგულარულ რეაგირებას, პროფილაქტიკასა და ტექნიკურ კონტროლს.', 'One-time service resolves a specific issue. Managed support covers agreed infrastructure with ongoing response, preventive work and technical oversight.', 'Разовая услуга решает конкретную проблему. Абонентская поддержка включает регулярное реагирование, профилактику и технический контроль согласованной инфраструктуры.'),
                    $this->faq('remote', 'რომელი პრობლემების მოგვარება შეიძლება დისტანციურად?', 'What issues can be solved remotely?', 'Какие проблемы можно решить удаленно?', 'პროგრამული პარამეტრების, Windows-ის, ანგარიშების, VPN-ის, ზოგი ქსელური და პროგრამული პრობლემის მოგვარება ხშირად შესაძლებელია დისტანციურად; აპარატურული ან კაბელირების პრობლემა ადგილზე ვიზიტს მოითხოვს.', 'Software settings, Windows, accounts, VPN and some network/software issues can often be solved remotely; hardware and cabling faults require an on-site visit.', 'Настройки ПО, Windows, учетные записи, VPN и часть сетевых проблем часто решаются удаленно; аппаратные и кабельные неисправности требуют выезда.'),
                ],
            ),
            'barrier-gate-installation' => $this->service(
                ['ჭკვიანი შლაგბაუმის მონტაჟი', 'Smart Barrier Gate Installation', 'Монтаж умного шлагбаума'],
                ['ავტომატური დაშვება პულტით, GSM-ით, ბარათით ან ნომრის ამოცნობით', 'Automatic access by remote, GSM, card or plate recognition', 'Автоматический доступ по пульту, GSM, карте или номеру'],
                ['ჭკვიანი შლაგბაუმის მონტაჟი — LPR კამერა, GSM, პულტი და უსაფრთხოების სენსორები', 'Smart Barrier Installation — LPR Camera, GSM, Remote and Safety Sensors', 'Монтаж умного шлагбаума — LPR-камера, GSM, пульт и датчики безопасности'],
                ['ვგეგმავთ და ვამონტაჟებთ ავტომატურ შლაგბაუმებს ეზოებისთვის, პარკინგებისთვის, კორპუსებისთვის და ბიზნეს ობიექტებისთვის — ფოტოსენსორით, Loop Detector-ით, პულტით, GSM-ით, ბარათით ან LPR ნომრის ამოცნობით.', 'We design and install automatic barriers for yards, parking, residential and business sites with photocells, loop detectors, remotes, GSM, cards or LPR plate recognition.', 'Проектируем и устанавливаем автоматические шлагбаумы для дворов, парковок, жилых и коммерческих объектов с фотоэлементами, Loop Detector, пультами, GSM, картами или LPR распознаванием номеров.'],
                ['უსაფრთხო სისტემა მოიცავს არა მხოლოდ ძრავსა და ბუმს, არამედ დაბრკოლების დეტექციასაც. მანქანის უსაფრთხო გავლისთვის სწორად ვარჩევთ ფოტოსენსორისა და ინდუქციური მარყუჟის პოზიციებს, ხოლო LPR სცენარში ვამოწმებთ კამერის კუთხეს, განათებასა და ნომრის ამოცნობის პირობებს.', 'A safe system includes more than the motor and boom: obstacle detection is essential. We position photocells and induction loops correctly and, for LPR, verify camera angle, lighting and plate recognition conditions.', 'Безопасная система включает не только привод и стрелу, но и обнаружение препятствий. Правильно размещаем фотоэлементы и индукционную петлю, а для LPR проверяем угол камеры, освещение и условия распознавания номера.'],
                ['შლაგბაუმის და LPR კამერის მონტაჟი | SafeTech', 'Barrier Gate and LPR Camera Installation | SafeTech', 'Монтаж шлагбаума и LPR-камеры | SafeTech'],
                ['ავტომატური და ჭკვიანი შლაგბაუმის მონტაჟი: ფუნდამენტი, ბუმი, Photocell, Loop Detector, პულტი, GSM, ბარათი და LPR ნომრის ამოცნობა.', 'Automatic smart barrier installation: foundation, boom, photocells, loop detector, remotes, GSM, cards and LPR plate recognition.', 'Монтаж автоматического умного шлагбаума: основание, стрела, Photocell, Loop Detector, пульт, GSM, карта и LPR распознавание номера.'],
                ['შლაგბაუმის მონტაჟი', 'ჭკვიანი შლაგბაუმი', 'LPR კამერა', 'ნომრის ამოცნობა', 'Loop Detector', 'Photocell', 'GSM შლაგბაუმი'],
                ['მანქანის უსაფრთხოების Photocell/Loop Detector სქემა', 'პულტი, GSM, ბარათი ან LPR ავტომატური დაშვება', 'დომოფონთან და Access Control-თან ინტეგრაცია'],
                ['გასასვლელის, სიგანისა და ინტენსივობის შეფასება', 'ფუნდამენტი, ბუმი, კვება და უსაფრთხოების სენსორები', 'LPR/GSM/პულტის კონფიგურაცია და სრული უსაფრთხოების ტესტი'],
                ['უსაფრთხო და ავტომატიზებული ავტომობილის დაშვება', 'Safe and automated vehicle access', 'Безопасный автоматизированный въезд'],
                ['მიიღეთ შლაგბაუმის სისტემის შეთავაზება', 'Get a barrier system quote', 'Получить расчет системы шлагбаума'],
                ['მოგვწერეთ გასასვლელის სიგანე და როგორ გინდათ გახსნა — პულტით, GSM-ით, ბარათით თუ ავტომატურად ნომრის ამოცნობით.', 'Tell us the lane width and how you want it opened — remote, GSM, card or automatic plate recognition.', 'Сообщите ширину проезда и способ открытия — пульт, GSM, карта или автоматическое распознавание номера.'],
                [
                    $this->faq('lpr', 'როგორ ხსნის შლაგბაუმს LPR კამერა ნომრის ამოცნობით?', 'How does an LPR camera open a barrier by plate recognition?', 'Как LPR-камера открывает шлагбаум по номеру?', 'კამერა კითხულობს ავტომობილის ნომერს, ადარებს ნებადართულ სიას და თავსებადი კონტროლერის/რელეს საშუალებით აძლევს გახსნის ბრძანებას. საჭიროა სწორი კუთხე, განათება და უსაფრთხოების სენსორები.', 'The camera reads the plate, checks it against an allowed list and triggers the compatible controller/relay. Correct angle, lighting and safety sensors are required.', 'Камера считывает номер, сверяет его со списком доступа и через совместимый контроллер/реле дает команду открытия. Нужны правильный угол, освещение и датчики безопасности.'),
                    $this->faq('loop', 'Loop Detector და Photocell რისთვის არის საჭირო?', 'Why are a loop detector and photocell needed?', 'Зачем нужны Loop Detector и Photocell?', 'ისინი უსაფრთხოებისთვის გამოიყენება: აფიქსირებენ მანქანას ან დაბრკოლებას და ხელს უშლიან ბუმის არასასურველ დაშვებას. კონკრეტული სქემა ობიექტის მოძრაობის ლოგიკის მიხედვით იგეგმება.', 'They are safety devices that detect a vehicle or obstacle and help prevent unwanted boom lowering. The exact layout depends on traffic flow.', 'Это устройства безопасности: обнаруживают автомобиль или препятствие и помогают предотвратить нежелательное опускание стрелы. Схема зависит от движения на объекте.'),
                ],
            ),
            'intercom-access-control-installation' => $this->service(
                ['დომოფონი და დაშვების კონტროლი', 'Intercom and Access Control', 'Домофон и контроль доступа'],
                ['კარისა და ჭიშკრის უსაფრთხო მართვა', 'Secure door and gate access', 'Безопасное управление дверью и воротами'],
                ['დაშვების კონტროლის მონტაჟი — დომოფონი, ელექტრო საკეტი, ბარათი, PIN და ბიომეტრია', 'Access Control Installation — Intercom, Electric Lock, Card, PIN and Biometrics', 'Монтаж контроля доступа — домофон, электрозамок, карта, PIN и биометрия'],
                ['ვაწყობთ ვიდეოდომოფონისა და Access Control სისტემებს კერძო სახლებისთვის, ოფისებისთვის, კორპუსებისთვის და ბიზნესისთვის: გარე პანელი, მონიტორი, კონტროლერი, ელექტრო საკეტი, Exit ღილაკი, ბარათი, PIN, ბიომეტრია და სარეზერვო კვება.', 'We install video intercom and access control systems for homes, offices, residential buildings and businesses: outdoor station, monitor, controller, electric lock, exit button, card, PIN, biometrics and backup power.', 'Устанавливаем видеодомофоны и системы контроля доступа для домов, офисов, жилых зданий и бизнеса: вызывная панель, монитор, контроллер, электрозамок, кнопка выхода, карта, PIN, биометрия и резервное питание.'],
                ['სისტემა იგეგმება კარის/ჭიშკრის ტიპის, უსაფრთხოების მოთხოვნისა და მომხმარებლების რაოდენობის მიხედვით. სწორად ვარჩევთ NO/NC ლოგიკას, კვების ბლოკს, საკეტის ტიპს და ავარიულ გახსნის/სარეზერვო კვების სცენარს.', 'The system is designed around door/gate type, security requirements and user count. We select NO/NC logic, power supply, lock type and emergency/backup scenarios correctly.', 'Система проектируется по типу двери/ворот, требованиям безопасности и числу пользователей. Правильно подбираем NO/NC логику, питание, тип замка и аварийные/резервные сценарии.'],
                ['დაშვების კონტროლის და დომოფონის მონტაჟი | SafeTech', 'Access Control and Intercom Installation | SafeTech', 'Монтаж контроля доступа и домофона | SafeTech'],
                ['დომოფონისა და დაშვების კონტროლის პროფესიონალური მონტაჟი: ელექტრო საკეტი, Exit ღილაკი, ბარათი, PIN, ბიომეტრია, მობილური აპლიკაცია და სარეზერვო კვება.', 'Professional intercom and access control installation: electric locks, exit buttons, card, PIN, biometrics, mobile app and backup power.', 'Профессиональный монтаж домофона и контроля доступа: электрозамок, кнопка выхода, карта, PIN, биометрия, мобильное приложение и резервное питание.'],
                ['დაშვების კონტროლი', 'დომოფონის მონტაჟი', 'ელექტრო საკეტი', 'Access Control', 'ბარათით კარი', 'ბიომეტრიული დაშვება'],
                ['საკეტისა და NO/NC ლოგიკის სწორი შერჩევა', 'ბარათი, PIN, ბიომეტრია ან მობილური აპლიკაცია', 'სარეზერვო კვება და ავარიული სცენარის გათვალისწინება'],
                ['კარის/ჭიშკრის და მოთხოვნების შეფასება', 'კაბელირება, კონტროლერი, საკეტი და წამკითხველი', 'მომხმარებლების დამატება და უსაფრთხოების სრული ტესტი'],
                ['სწორად დაგეგმილი დაშვების კონტროლი', 'Correctly designed access control', 'Правильно спроектированный контроль доступа'],
                ['მიიღეთ დაშვების სისტემის შეთავაზება', 'Get an access control quote', 'Получить расчет контроля доступа'],
                ['მოგვწერეთ კარის ან ჭიშკრის ტიპი და როგორ გინდათ დაშვება — დომოფონით, ბარათით, PIN-ით, ბიომეტრიით თუ მობილურით.', 'Tell us the door or gate type and desired access method — intercom, card, PIN, biometrics or mobile.', 'Сообщите тип двери или ворот и способ доступа — домофон, карта, PIN, биометрия или телефон.'],
                [
                    $this->faq('fail', 'დენის გათიშვისას კარი როგორ იმუშავებს?', 'How will the door work during a power outage?', 'Как будет работать дверь при отключении электричества?', 'ეს დამოკიდებულია საკეტის ტიპზე და უსაფრთხოების მოთხოვნაზე. საჭიროა სწორად შეირჩეს fail-safe/fail-secure ლოგიკა და საჭიროებისას დაემატოს UPS ან აკუმულატორული სარეზერვო კვება.', 'It depends on lock type and security requirements. Fail-safe/fail-secure behavior must be selected correctly and UPS/battery backup added when required.', 'Это зависит от типа замка и требований безопасности. Нужно правильно выбрать fail-safe/fail-secure логику и при необходимости добавить UPS/аккумулятор.'),
                    $this->faq('mobile', 'დომოფონიდან კარის გახსნა ტელეფონით შეიძლება?', 'Can I open the door from my phone?', 'Можно открыть дверь с телефона через домофон?', 'თავსებადი IP დომოფონისა და ქსელის შემთხვევაში შესაძლებელია აპლიკაციით ზარის მიღება, ვიდეოს ნახვა და კარის გახსნა. შესაძლებლობები კონკრეტულ მოწყობილობაზეა დამოკიდებული.', 'With a compatible IP intercom and network, the app can receive calls, show video and open the door. Capabilities depend on the selected equipment.', 'При совместимом IP-домофоне и сети приложение может принимать звонки, показывать видео и открывать дверь. Возможности зависят от оборудования.'),
                ],
            ),
        ];
    }

    private function service(array $name, array $eyebrow, array $title, array $description, array $longDescription, array $seoTitle, array $seoDescription, array $keywordsKa, array $highlightsKa, array $solutionsKa, array $overviewTitle, array $ctaTitle, array $ctaDescription, array $faqs): array
    {
        return [
            'name' => $this->localize($name),
            'eyebrow' => $this->localize($eyebrow),
            'title' => $this->localize($title),
            'description' => $this->localize($description),
            'long_description' => $this->localize($longDescription),
            'seo_title' => $this->localize($seoTitle),
            'seo_description' => $this->localize($seoDescription),
            'keywords' => [
                'ka' => $keywordsKa,
                'en' => [],
                'ru' => [],
            ],
            'highlights' => [
                'ka' => $highlightsKa,
                'en' => $highlightsKa,
                'ru' => $highlightsKa,
            ],
            'solutions' => [
                'ka' => $solutionsKa,
                'en' => $solutionsKa,
                'ru' => $solutionsKa,
            ],
            'overview_title' => $this->localize($overviewTitle),
            'button_text' => $this->localize(['შეთავაზების მოთხოვნა', 'Request a quote', 'Запросить расчет']),
            'cta_title' => $this->localize($ctaTitle),
            'cta_description' => $this->localize($ctaDescription),
            'faqs' => $faqs,
        ];
    }

    private function faq(string $key, string $qKa, string $qEn, string $qRu, string $aKa, string $aEn, string $aRu): array
    {
        return [
            'key' => $key,
            'question' => ['ka' => $qKa, 'en' => $qEn, 'ru' => $qRu],
            'answer' => ['ka' => $aKa, 'en' => $aEn, 'ru' => $aRu],
        ];
    }

    private function localize(array $values): array
    {
        return ['ka' => $values[0], 'en' => $values[1], 'ru' => $values[2]];
    }
};
