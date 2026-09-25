<?php

namespace Database\Seeders;

/**
 * Editorial briefs for SafeTech's confirmed service areas.
 *
 * Each city has its own operational questions and property context. These are
 * planning considerations, not claims of completed jobs or guaranteed features.
 */
final class PriorityLocalSeoCopy
{
    public const CITY_ORDER = [
        'tbilisi',
        'bakuriani',
        'surami',
        'borjomi',
        'khashuri',
        'abastumani',
    ];

    /** @return array<string, array<string, mixed>> */
    public static function cities(): array
    {
        return [
            'tbilisi' => [
                'name' => ['ka' => 'თბილისი', 'en' => 'Tbilisi', 'ru' => 'Тбилиси'],
                'in' => ['ka' => 'თბილისში', 'en' => 'in Tbilisi', 'ru' => 'в Тбилиси'],
                'context' => self::t(
                    'თბილისის ობიექტისთვის მნიშვნელოვანია შენობაში დაშვების წესები, სამუშაო საათები, კაბელების არსებული მარშრუტი და მეზობლების ან თანამშრომლების შეუფერხებელი გადაადგილება.',
                    'For a Tbilisi property, confirm building access, working hours, existing cable routes and how to minimise disruption to residents or staff.',
                    'Для объекта в Тбилиси уточните доступ в здание, время работ, существующие кабельные трассы и возможность минимизировать неудобства для жильцов или персонала.',
                ),
                'use' => [
                    'security-access-automation' => self::t('კორპუსის ეზო, ოფისი, მაღაზია ან პარკინგი: წინასწარ დგინდება შესასვლელები, მოძრაობის ნაკადი და უფლებამოსილი პირების დაშვება.', 'Apartment courtyard, office, shop or car park: map entrances, traffic flow and authorised users before selecting the system.', 'Двор жилого дома, офис, магазин или парковка: до выбора системы уточняем входы, поток транспорта и права доступа.'),
                    'network-infrastructure' => self::t('ბინასა და ოფისში ოთახების განლაგება, ქსელის პორტები და საერთო საკომუნიკაციო სივრცე განსაზღვრავს ქსელის გეგმას.', 'In flats and offices, room layout, port counts and shared risers affect the network plan.', 'В квартирах и офисах план сети зависит от комнат, числа портов и общих кабельных шахт.'),
                    'computer-services' => self::t('საოფისე სამუშაო ადგილისა და კერძო კომპიუტერისთვის წინასწარ ვარკვევთ მონაცემების შენარჩუნების, ლიცენზიებისა და ადგილზე მუშაობის პირობებს.', 'For office and home computers, clarify data retention, licensing and whether an onsite visit is necessary.', 'Для офисных и домашних ПК уточняем сохранение данных, лицензии и необходимость выезда.'),
                    'business-it' => self::t('საცალო ობიექტსა და ოფისში გათვალისწინებულია სავაჭრო საათები, სალაროს ან სამუშაო სადგურის გათიშვის გავლენა და ქსელის დამოკიდებულებები.', 'For retail and offices, consider trading hours, the impact of POS/workstation downtime and network dependencies.', 'В магазине и офисе учитываем часы работы, последствия простоя кассы или ПК и зависимость от сети.'),
                    'telecommunications-contractor' => self::t('მრავალოთახიანი ობიექტისთვის განიხილება სუსტი დენების ტრასა, საერთო არხები, საკომუნიკაციო კარადა და სხვა სისტემებთან გადაკვეთები.', 'For multi-room premises, review low-voltage routes, shared conduits, cabinets and intersections with other systems.', 'Для многокомнатного объекта учитываем слаботочные трассы, общие каналы, шкафы и пересечения с другими системами.'),
                ],
            ],
            'bakuriani' => [
                'name' => ['ka' => 'ბაკურიანი', 'en' => 'Bakuriani', 'ru' => 'Бакуриани'],
                'in' => ['ka' => 'ბაკურიანში', 'en' => 'in Bakuriani', 'ru' => 'в Бакуриани'],
                'context' => self::t(
                    'სეზონურ ობიექტზე გაითვალისწინეთ სტუმრების ჩასვლის გრაფიკი, ზამთარში მისადგომობა, გარეთ განთავსებული მოწყობილობების დაცვა და მფლობელის არყოფნისას მართვა.',
                    'For a seasonal property, consider guest check-in times, winter access, outdoor-device exposure and management while the owner is away.',
                    'Для сезонного объекта учитывайте заезды гостей, зимний доступ, размещение оборудования снаружи и управление в отсутствие владельца.',
                ),
                'use' => [
                    'security-access-automation' => self::t('კოტეჯისა და სასტუმროს შესასვლელი, საერთო პარკინგი ან სტუმრების ზონა საჭიროებს სეზონური დატვირთვისა და დისტანციური დაშვების განხილვას.', 'Cottage and hotel entrances, shared parking and guest areas require planning for seasonal traffic and suitable remote access.', 'Въезды коттеджей и гостиниц, общие парковки и гостевые зоны требуют учёта сезонного потока и удалённого доступа.'),
                    'network-infrastructure' => self::t('კოტეჯის რამდენიმე სართული, სქელი კედლები და ცალკეული ნომრები გავლენას ახდენს Wi-Fi-ისა და საკაბელო წერტილების განაწილებაზე.', 'Cottage floors, thick walls and separate guest rooms affect Wi-Fi coverage and cable outlets.', 'Этажи коттеджа, толстые стены и отдельные номера влияют на Wi-Fi и расположение сетевых точек.'),
                    'computer-services' => self::t('სასტუმროს მიღების კომპიუტერსა და სეზონურ სამუშაო ადგილს წინასწარ სჭირდება მონაცემების, საჭირო პროგრამებისა და სტუმრების მიღების გრაფიკის გათვალისწინება.', 'Reception PCs and seasonal workstations require advance planning for data, required applications and guest schedules.', 'Компьютеры ресепшена и сезонные рабочие места требуют планирования данных, программ и графика заезда гостей.'),
                    'business-it' => self::t('განთავსების ობიექტის POS, ადმინისტრაციის კომპიუტერი და სტუმრების ქსელი ერთმანეთისგან გასამიჯნია საჭიროების მიხედვით.', 'Property POS, administration PCs and guest Wi-Fi may need separation according to the actual setup.', 'POS гостиницы, компьютеры администрации и гостевой Wi-Fi при необходимости разделяются по задачам.'),
                    'telecommunications-contractor' => self::t('კოტეჯისა და სასტუმროს კომუნიკაციები დაიგეგმოს სართულების, ნომრებისა და გარე კამერების საკაბელო მარშრუტებით.', 'Plan cottage or hotel low-voltage routes across floors, rooms and any outdoor camera locations.', 'Слаботочные трассы коттеджа или гостиницы планируют между этажами, номерами и наружными камерами.'),
                ],
            ],
            'surami' => [
                'name' => ['ka' => 'სურამი', 'en' => 'Surami', 'ru' => 'Сурами'],
                'in' => ['ka' => 'სურამში', 'en' => 'in Surami', 'ru' => 'в Сурами'],
                'context' => self::t(
                    'კერძო სახლის, აგარაკის ან მცირე ბიზნესის შემთხვევაში მნიშვნელოვანია მფლობელის ადგილზე ყოფნის პერიოდი, კომუნიკაციების მდგომარეობა და დისტანციური მართვის საჭიროება.',
                    'For a home, holiday property or small business, clarify when the owner is onsite, existing utilities and whether remote management is needed.',
                    'Для дома, дачи или малого бизнеса уточняем присутствие владельца, состояние коммуникаций и потребность в удалённом управлении.',
                ),
                'use' => [
                    'security-access-automation' => self::t('სახლის ეზოს, აგარაკის შესასვლელისა და მცირე სასტუმროსთვის განიხილება გარე მოწყობილობები და მფლობელის არყოფნისას დაშვების მართვა.', 'For home gates, holiday homes and small guesthouses, consider outdoor hardware and access while the owner is away.', 'Для ворот дома, дачи и небольшого гостевого дома учитываем наружное оборудование и доступ в отсутствие владельца.'),
                    'network-infrastructure' => self::t('აგარაკის სხვადასხვა ოთახსა და ეზოს ნაწილში Wi-Fi-ის დაგეგმვისას უნდა შევამოწმოთ კედლები, მანძილი და ინტერნეტის რეალური წყარო.', 'For rooms and outdoor areas at a holiday home, check walls, distance and the actual internet connection.', 'Для комнат и двора дачи проверяем стены, расстояния и реальный источник интернета.'),
                    'computer-services' => self::t('სახლში ან მცირე ოფისში კომპიუტერის გამართვამდე ვარკვევთ მონაცემების საჭიროებას, არსებულ პროგრამებსა და სამუშაოს ადგილზე შესრულების პირობებს.', 'For a home or small office PC, confirm required data, installed software and onsite working conditions.', 'Для ПК дома или малого офиса уточняем данные, программы и условия работы на месте.'),
                    'business-it' => self::t('მაღაზიისა და საოჯახო სასტუმროს სისტემებზე სამუშაო შეთანხმდეს მომხმარებლების მიღების საათებთან და არსებულ ინტერნეტთან.', 'Schedule work on shop or guesthouse systems around opening hours and the existing internet connection.', 'Работы с системами магазина и гостевого дома согласуются с часами приёма и имеющимся интернетом.'),
                    'telecommunications-contractor' => self::t('კერძო შენობასა და აგარაკში საკაბელო მარშრუტები უნდა დაიგეგმოს ეზომდე, სართულებსა და საკომუნიკაციო კვანძამდე.', 'For houses and holiday properties, plan routes to the yard, between floors and to the communications point.', 'В доме и на даче кабели планируют до двора, между этажами и к узлу связи.'),
                ],
            ],
            'borjomi' => [
                'name' => ['ka' => 'ბორჯომი', 'en' => 'Borjomi', 'ru' => 'Боржоми'],
                'in' => ['ka' => 'ბორჯომში', 'en' => 'in Borjomi', 'ru' => 'в Боржоми'],
                'context' => self::t(
                    'სასტუმროს, საოჯახო სასტუმროსა და კომერციული ობიექტის შემთხვევაში წინასწარ შეთანხმდეს სამუშაო ფანჯარა, სტუმრების გადაადგილება და არსებული კომუნიკაციების ხელმისაწვდომობა.',
                    'For hotels, guesthouses and businesses, agree a work window, guest circulation and access to existing utilities before a visit.',
                    'Для гостиниц, гостевых домов и бизнеса заранее согласуйте время работ, перемещение гостей и доступ к коммуникациям.',
                ),
                'use' => [
                    'security-access-automation' => self::t('სასტუმროს შესასვლელზე, პარკინგსა და საერთო სივრცეში სისტემის არჩევისას გაითვალისწინეთ სტუმრებისა და პერსონალის სხვადასხვა დაშვება.', 'Hotel entrances, parking and shared areas may need different guest and staff access rules.', 'Входы гостиницы, парковки и общие зоны могут требовать разных правил доступа гостей и персонала.'),
                    'network-infrastructure' => self::t('სასტუმროს ნომრები, საერთო სივრცეები და ადმინისტრაციის ქსელი განიხილება დაფარვისა და საჭიროებისამებრ განცალკევების თვალსაზრისით.', 'Guest rooms, communal areas and administration networks need coverage planning and separation where necessary.', 'Номера, общие помещения и сеть администрации требуют расчёта покрытия и разделения при необходимости.'),
                    'computer-services' => self::t('მიღების კომპიუტერი და საოფისე სამუშაო ადგილი ისე უნდა შემოწმდეს, რომ დაგეგმილი სამუშაო არ დაემთხვეს დატვირთულ მიღების საათებს.', 'Reception and office PC work should be planned outside busy check-in periods where possible.', 'Работы с ПК ресепшена и офиса желательно планировать вне загруженного времени заселения.'),
                    'business-it' => self::t('სასტუმროს POS, პრინტერები და ადმინისტრაციის ქსელი განიხილება სამუშაო პროცესებისა და შესაძლო გათიშვის დროის მიხედვით.', 'Hotel POS, printers and admin networks are scoped around operations and acceptable downtime.', 'POS гостиницы, принтеры и сеть администрации оцениваются с учётом процессов и допустимого простоя.'),
                    'telecommunications-contractor' => self::t('სასტუმროს ნომრებსა და საერთო სივრცეებში კომუნიკაციების დაგეგმვისას უნდა გაითვალისწინოთ დაკავებული ოთახები და სამუშაოების შეზღუდული დრო.', 'In hotel rooms and communal spaces, account for occupied rooms and restricted work windows when planning low-voltage routes.', 'В номерах и общих зонах гостиницы учитывайте занятые помещения и ограниченное время прокладки кабелей.'),
                ],
            ],
            'khashuri' => [
                'name' => ['ka' => 'ხაშური', 'en' => 'Khashuri', 'ru' => 'Хашури'],
                'in' => ['ka' => 'ხაშურში', 'en' => 'in Khashuri', 'ru' => 'в Хашури'],
                'context' => self::t(
                    'სახლში, მაღაზიასა თუ ოფისში პირველ რიგში შეამოწმეთ უკვე გაყვანილი კაბელები და მოწყობილობები: გამართული ნაწილის გამოყენება შესაძლოა უფრო პრაქტიკული იყოს, ვიდრე სრული შეცვლა.',
                    'For homes, shops and offices, first check existing wiring and devices: reusing compatible working components may be preferable to replacing everything.',
                    'Для домов, магазинов и офисов сначала проверяем кабели и устройства: исправные совместимые элементы иногда можно сохранить.',
                ),
                'use' => [
                    'security-access-automation' => self::t('ეზოს შესასვლელი, მაღაზიის კამერები და კორპუსის საერთო კარი შეფასდეს არსებული კვების, კაბელისა და კონტროლერის მიხედვით.', 'Assess home gates, shop cameras and shared entrance doors against existing power, wiring and controllers.', 'Ворота дома, камеры магазина и общие двери оцениваем с учётом питания, кабелей и контроллеров.'),
                    'network-infrastructure' => self::t('მაღაზიის, სახლის ან ოფისის ქსელის გაფართოებამდე შეამოწმეთ ძველი RJ45 წერტილები და მათი რეალური გამტარობა.', 'Before extending a home, shop or office network, test existing RJ45 outlets and actual link performance.', 'Перед расширением сети дома, магазина или офиса проверяем старые RJ45-точки и работу линий.'),
                    'computer-services' => self::t('კერძო და საოფისე კომპიუტერზე სამუშაოს დაწყებამდე განისაზღვროს შესანახი ფაილები და რეალურად შესაცვლელი პროგრამული კომპონენტები.', 'For home and office computers, identify files to preserve and the software components that actually need changes.', 'Для домашних и офисных ПК сначала определяем сохраняемые файлы и нужные изменения ПО.'),
                    'business-it' => self::t('მაღაზიის სალაროსა და საოფისე მოწყობილობების გამართვა დაიწყოს არსებული კონფიგურაციისა და გათიშვის დასაშვები დროის შეფასებით.', 'Start shop POS and office device work by reviewing the existing configuration and acceptable downtime.', 'Работы с кассой магазина и офисными устройствами начинаются с проверки конфигурации и допустимого простоя.'),
                    'telecommunications-contractor' => self::t('არსებულ შენობაში სუსტი დენების დამატებისას განიხილება გამოყენებადი არხები, ძველი საკაბელო ხაზები და ახალი წერტილების მდებარეობა.', 'When adding low-voltage systems to an existing property, inspect usable conduits, existing cables and proposed new outlets.', 'При добавлении слаботочных систем в существующем здании проверяем каналы, старые кабели и новые точки.'),
                ],
            ],
            'abastumani' => [
                'name' => ['ka' => 'აბასთუმანი', 'en' => 'Abastumani', 'ru' => 'Абастумани'],
                'in' => ['ka' => 'აბასთუმანში', 'en' => 'in Abastumani', 'ru' => 'в Абастумани'],
                'context' => self::t(
                    'დასასვენებელ ობიექტზე სასურველია დაზუსტდეს სეზონური დატვირთვა, სამუშაოების მისადგომობა, სტუმრების განრიგი და დისტანციური კონტროლის აუცილებლობა.',
                    'For a holiday property, confirm seasonal demand, site access, guest schedules and whether remote monitoring is needed.',
                    'Для объекта отдыха уточните сезонную нагрузку, доступ к объекту, график гостей и необходимость удалённого контроля.',
                ),
                'use' => [
                    'security-access-automation' => self::t('დასასვენებელი სახლის შესასვლელის, კამერებისა და საერთო პარკინგის დაგეგმვა იწყება სტუმრების დაშვებისა და გარე პირობების შეფასებით.', 'Plan holiday-property entry, cameras or shared parking around guest access and outdoor conditions.', 'Вход, камеры и общую парковку объекта отдыха планируют с учётом доступа гостей и внешних условий.'),
                    'network-infrastructure' => self::t('დასასვენებელი სახლის ნომრები და საერთო ზონები საჭიროებს Wi-Fi-ისა და საკაბელო წერტილების განლაგების შეფასებას.', 'Guest rooms and shared spaces need a coverage and network-outlet plan.', 'Для номеров и общих зон нужно оценить покрытие Wi-Fi и размещение сетевых точек.'),
                    'computer-services' => self::t('მიღების ან ადმინისტრაციის კომპიუტერის გამართვამდე განიხილება საჭირო პროგრამები და ფაილების შენარჩუნება.', 'Before configuring a reception or administration PC, clarify required programs and data preservation.', 'Перед настройкой ПК ресепшена или администрации уточните программы и сохранение файлов.'),
                    'business-it' => self::t('სასტუმროს ადმინისტრაცია და მცირე სავაჭრო ობიექტი საჭიროებს სამუშაო დროისა და ქსელზე დამოკიდებული მოწყობილობების გათვალისწინებას.', 'Hotel administration and small retail operations require planning around working hours and network-dependent devices.', 'Для администрации гостиницы и малого магазина учитывайте часы работы и оборудование, зависящее от сети.'),
                    'telecommunications-contractor' => self::t('დასასვენებელ შენობაში სუსტი დენების ქსელი დაიგეგმოს ოთახებს, სართულებსა და გარე წერტილებს შორის.', 'Plan low-voltage routes between rooms, floors and outdoor points at a holiday property.', 'Слаботочные трассы в объекте отдыха планируют между комнатами, этажами и наружными точками.'),
                ],
            ],
        ];
    }

    /** @return array<string, array{ka:string,en:string,ru:string}> */
    public static function technical(): array
    {
        return [
            'security-access-automation' => self::t(
                'სისტემის შერჩევამდე მოწმდება კვება, კაბელირება, მოწყობილობების თავსებადობა, საჭირო ხედვის ან დაშვების ზონები და უსაფრთხო ექსპლუატაციის პირობები.',
                'Before choosing the system, check power, cabling, device compatibility, required visibility or access zones and safe operation.',
                'Перед выбором системы проверяют питание, кабели, совместимость устройств, зоны обзора или доступа и безопасность эксплуатации.',
            ),
            'network-infrastructure' => self::t(
                'განისაზღვრება პორტების რაოდენობა, კაბელის ტრასა და სიგრძე, მოწყობილობის განთავსება, საჭირო PoE დატვირთვა და დამონტაჟებული ქსელის ტესტირება.',
                'Define port count, cable routes and length, equipment locations, any PoE load and tests for the finished network.',
                'Определяют количество портов, трассы и длину кабеля, размещение оборудования, PoE-нагрузку и тестирование сети.',
            ),
            'computer-services' => self::t(
                'მუშაობამდე ზუსტდება მოწყობილობის მოდელი, პრობლემა, მონაცემების შენარჩუნება, მოქმედი ლიცენზია და საჭირო პროგრამების თავსებადობა.',
                'Before work, identify the device model, fault, data to preserve, valid licensing and compatibility of required software.',
                'Перед работой уточняют модель устройства, неисправность, сохранение данных, действующую лицензию и совместимость ПО.',
            ),
            'business-it' => self::t(
                'საჭიროა არსებული მოწყობილობების, პროგრამების, ქსელისა და თანამშრომლების სამუშაო პროცესის შეფასება, რათა შეთანხმდეს სამუშაოს მოცულობა და გათიშვის დრო.',
                'Review existing devices, software, the network and staff workflow so scope and potential downtime can be agreed.',
                'Проверяют устройства, программы, сеть и работу персонала, чтобы согласовать объём работ и возможный простой.',
            ),
            'telecommunications-contractor' => self::t(
                'მონტაჟამდე მოწმდება საკაბელო გზები, სუსტი და ძალოვანი ხაზების გამიჯვნა, მონიშვნა, საკომუნიკაციო კვანძები და შემდგომი გაფართოების შესაძლებლობა.',
                'Before installation, review cable routes, separation from mains, labelling, communications points and possible expansion.',
                'До монтажа проверяют трассы, разделение со силовыми линиями, маркировку, узлы связи и возможность расширения.',
            ),
        ];
    }

    /** @return array{ka:string,en:string,ru:string} */
    private static function t(string $ka, string $en, string $ru): array
    {
        return compact('ka', 'en', 'ru');
    }
}
