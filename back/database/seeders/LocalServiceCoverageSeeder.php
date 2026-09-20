<?php

namespace Database\Seeders;

use App\Models\LocalServiceLanding;
use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * Complete canonical service coverage with six editorially distinct Tbilisi
 * pages. No fictional projects, addresses, reviews, testimonials or rankings.
 * Does not republish/overwrite administrator-managed existing pages.
 */
final class LocalServiceCoverageSeeder extends Seeder
{
    private const LOCATION = ['ka' => 'თბილისი', 'en' => 'Tbilisi', 'ru' => 'Тбилиси'];

    public function run(): void
    {
        foreach ($this->definitions() as $slug => $copy) {
            $service = Service::query()->where('slug', $slug)->publiclyVisible()->first();

            if (! $service) {
                continue;
            }

            $landing = LocalServiceLanding::query()->firstOrNew([
                'service_id' => $service->getKey(),
                'location_slug' => 'tbilisi',
            ]);

            // Preserve intentional edits and unpublished/noindex decisions.
            if ($landing->exists) {
                continue;
            }

            $titles = $copy['title'];
            $contents = [];
            $excerpts = [];
            $seoTitles = [];
            $seoDescriptions = [];
            $ctaTitles = [];
            $ctaTexts = [];
            $keywords = [];
            $primary = [];

            foreach (['ka', 'en', 'ru'] as $locale) {
                $contents[$locale] = implode("\n\n", [
                    $copy['intro'][$locale],
                    $copy['technical'][$locale],
                    $copy['scope'][$locale],
                ]);
                $excerpts[$locale] = $copy['intro'][$locale];
                $seoTitles[$locale] = $titles[$locale].' | SafeTech';
                $seoDescriptions[$locale] = $copy['seo'][$locale];
                $ctaTitles[$locale] = $copy['cta'][$locale];
                $ctaTexts[$locale] = $copy['request'][$locale];
                $primary[$locale] = $titles[$locale];
                $keywords[$locale] = [
                    $titles[$locale],
                    $copy['name'][$locale].' '.self::LOCATION[$locale],
                ];
            }

            $benefits = array_map(fn (array $value): array => [
                'title' => $value['title']['ka'],
                'description' => $value['description']['ka'],
                'translations' => [
                    'en' => [
                        'title' => $value['title']['en'],
                        'description' => $value['description']['en'],
                    ],
                    'ru' => [
                        'title' => $value['title']['ru'],
                        'description' => $value['description']['ru'],
                    ],
                ],
            ], $copy['benefits']);
            $faqs = array_map(fn (array $value): array => [
                'question' => $value['question']['ka'],
                'answer' => $value['answer']['ka'],
                'translations' => [
                    'en' => [
                        'question' => $value['question']['en'],
                        'answer' => $value['answer']['en'],
                    ],
                    'ru' => [
                        'question' => $value['question']['ru'],
                        'answer' => $value['answer']['ru'],
                    ],
                ],
            ], $copy['faqs']);

            $landing->fill([
                'location_name' => self::LOCATION['ka'],
                'eyebrow' => $copy['name']['ka'].' · თბილისი',
                'title' => $titles['ka'],
                'excerpt' => $excerpts['ka'],
                'content' => $contents['ka'],
                'benefits' => $benefits,
                'faq' => $faqs,
                'cta_title' => $ctaTitles['ka'],
                'cta_text' => $ctaTexts['ka'],
                'primary_keyword' => $primary['ka'],
                'keywords' => $keywords['ka'],
                'seo_title' => $seoTitles['ka'],
                'seo_description' => $seoDescriptions['ka'],
                'translations' => [
                    'fields' => [
                        'locationName' => self::LOCATION,
                        'eyebrow' => [
                            'ka' => $copy['name']['ka'].' · თბილისი',
                            'en' => $copy['name']['en'].' · Tbilisi',
                            'ru' => $copy['name']['ru'].' · Тбилиси',
                        ],
                        'title' => $titles,
                        'excerpt' => $excerpts,
                        'content' => $contents,
                        'ctaTitle' => $ctaTitles,
                        'ctaText' => $ctaTexts,
                        'primaryKeyword' => $primary,
                        'seoTitle' => $seoTitles,
                        'seoDescription' => $seoDescriptions,
                        'ogTitle' => $seoTitles,
                        'ogDescription' => $seoDescriptions,
                    ],
                    'keywords' => $keywords,
                    'seo' => ['schema_type' => 'Service'],
                ],
                'is_published' => true,
                'noindex' => false,
                'published_at' => now(),
                'sort_order' => 101,
            ])->save();

            // A project link requires an actual related completed project and
            // editorial verification of both city and service. Never attach by
            // city substring alone merely to improve a dashboard counter.
        }
    }

    private function definitions(): array
    {
        return [
            'operating-system-installation' => [
                'name' => ['ka' => 'Windows-ის ინსტალაცია', 'en' => 'Windows installation', 'ru' => 'Установка Windows'],
                'title' => ['ka' => 'Windows-ის ინსტალაცია თბილისში', 'en' => 'Windows installation in Tbilisi', 'ru' => 'Установка Windows в Тбилиси'],
                'intro' => [
                    'ka' => 'თბილისში Windows-ის ინსტალაცია და კომპიუტერის პროგრამული გამართვა კერძო მომხმარებლებისა და ოფისებისთვის. დაწყებამდე განვიხილავთ მოწყობილობის მდგომარეობას, შესანახ მონაცემებსა და მოქმედ ლიცენზიას.',
                    'en' => 'Windows installation and workstation setup in Tbilisi for homes and offices. Before reinstalling the system, we check the device, important files and the available Windows licence.',
                    'ru' => 'Установка Windows и настройка компьютеров в Тбилиси для дома и офиса. Перед переустановкой проверяем устройство, важные файлы и наличие действующей лицензии.',
                ],
                'technical' => [
                    'ka' => 'ვაზუსტებთ SSD/HDD-ის ჯანმრთელობას, BIOS/UEFI და TPM მოთხოვნებს, ვადგენთ თავსებად Windows-ის რედაქციას, ვაყენებთ საჭირო დრაივერებსა და განახლებებს. მონაცემების გადატანა და სარეზერვო ასლის გაკეთება თანხმობის შემდეგ ხდება; საჭიროებისას ცალკე მოწმდება BitLocker-ის აღდგენის გასაღები.',
                    'en' => 'We assess SSD/HDD health, BIOS/UEFI and TPM requirements, choose a compatible Windows edition and configure drivers and updates. Data migration or backup takes place only after agreement; a BitLocker recovery key may be required.',
                    'ru' => 'Проверяем состояние SSD/HDD, требования BIOS/UEFI и TPM, выбираем совместимую редакцию Windows, настраиваем драйверы и обновления. Перенос данных выполняется по согласованию; при BitLocker может понадобиться ключ восстановления.',
                ],
                'scope' => [
                    'ka' => 'საჭიროა კომპიუტერის მოდელი, მიმდინარე შეცდომის აღწერა, შესანახი ფაილების მოცულობა და სამუშაოს შესრულების მისამართი თბილისში. პროგრამების თავსებადობა, ოფისის დომენში ჩართვა ან სპეციალური პროგრამები წინასწარ ზუსტდება.',
                    'en' => 'For a quote, share the computer model, current fault, approximate amount of data to preserve and the Tbilisi service location. Domain joining and specialist software must be agreed separately.',
                    'ru' => 'Для расчёта укажите модель ПК, проблему, объём сохраняемых файлов и адрес в Тбилиси. Подключение к домену и специализированные программы согласуются отдельно.',
                ],
                'seo' => [
                    'ka' => 'Windows 10/11-ის ინსტალაცია თბილისში: დრაივერები, განახლებები, მონაცემების გადატანა შეთანხმებით და კომპიუტერის შემოწმება. SafeTech.',
                    'en' => 'Windows 10/11 installation in Tbilisi: drivers, updates, agreed data backup and workstation checks. Request a SafeTech quote.',
                    'ru' => 'Установка Windows 10/11 в Тбилиси: драйверы, обновления, перенос данных по согласованию и проверка ПК. SafeTech.',
                ],
                'cta' => ['ka' => 'Windows-ის გამართვის მოთხოვნა', 'en' => 'Request Windows setup', 'ru' => 'Заказать настройку Windows'],
                'request' => [
                    'ka' => 'მოგვწერეთ კომპიუტერის მოდელი, პრობლემა და ფაილების შენარჩუნების საჭიროება. მომსახურების მოცულობასა და დროს შეთანხმებით დავაზუსტებთ.',
                    'en' => 'Send the PC model, the fault and whether files must be preserved. We will confirm the scope and timing.',
                    'ru' => 'Укажите модель ПК, неисправность и необходимость сохранить файлы. Согласуем объём и время работ.',
                ],
                'benefits' => $this->steps(
                    ['ka' => 'მონაცემების დაცვა', 'en' => 'Data-first approach', 'ru' => 'Сохранность данных'],
                    ['ka' => 'განვიხილავთ სარეზერვო ასლს, დისკის მდგომარეობასა და დაშიფვრას ინსტალაციამდე.', 'en' => 'We discuss backup, disk health and encryption before reinstalling.', 'ru' => 'До установки обсуждаем резервную копию, состояние диска и шифрование.'],
                    ['ka' => 'თავსებადობის შემოწმება', 'en' => 'Compatibility review', 'ru' => 'Проверка совместимости'],
                    ['ka' => 'UEFI/TPM, მოწყობილობის დრაივერები და საჭირო პროგრამები მოწმდება.', 'en' => 'UEFI/TPM, drivers and essential applications are checked.', 'ru' => 'Проверяем UEFI/TPM, драйверы и необходимые приложения.'],
                    ['ka' => 'ჩაბარების ტესტი', 'en' => 'Handover checks', 'ru' => 'Контроль после установки'],
                    ['ka' => 'ვამოწმებთ ქსელს, განახლებებსა და საბაზისო მოწყობილობებს.', 'en' => 'We test networking, updates and core peripherals.', 'ru' => 'Проверяем сеть, обновления и основные устройства.'],
                ),
                'faqs' => $this->basicFaq(
                    ['ka' => 'ფაილები ხომ არ წაიშლება?', 'en' => 'Will my files be erased?', 'ru' => 'Удалятся ли мои файлы?'],
                    ['ka' => 'სისტემის გადაყენებამდე ვაზუსტებთ რომელ ფაილებს ვინახავთ და საჭიროებისას ვგეგმავთ სარეზერვო ასლს. მონაცემთა აღდგენა გარანტირებული არ არის დაზიანებული დისკის შემთხვევაში.', 'en' => 'We agree which files need preserving and plan a backup if needed. Recovery from a damaged drive cannot be guaranteed.', 'ru' => 'Заранее согласуем сохранение файлов и резервную копию; восстановление с повреждённого диска не гарантируется.'],
                    ['ka' => 'ლიცენზია შედის?', 'en' => 'Is a Windows licence included?', 'ru' => 'Лицензия Windows входит в стоимость?'],
                    ['ka' => 'მოქმედი ლიცენზია ან ახალი ლიცენზიის საჭიროება წინასწარ მოწმდება და ცალკე შეთანხმდება.', 'en' => 'We check existing activation and quote any required new licence separately.', 'ru' => 'Проверяем текущую активацию; новую лицензию при необходимости согласуем отдельно.'],
                ),
            ],
            'custom-computer-build' => [
                'name' => ['ka' => 'კომპიუტერის აწყობა', 'en' => 'Custom PC assembly', 'ru' => 'Сборка компьютера'],
                'title' => ['ka' => 'კომპიუტერის აწყობა თბილისში', 'en' => 'Custom PC assembly in Tbilisi', 'ru' => 'Сборка компьютера в Тбилиси'],
                'intro' => [
                    'ka' => 'თბილისში პერსონალური კომპიუტერის კონფიგურაციის შერჩევა და აწყობა ოფისისთვის, პროგრამირებისთვის, დიზაინისთვის ან თამაშებისთვის. კომპონენტებს ვარჩევთ რეალური დატვირთვის, განახლების გეგმებისა და ბიუჯეტის მიხედვით.',
                    'en' => 'Custom PC planning and assembly in Tbilisi for office work, development, design and gaming. Component choice follows real workloads, upgrade plans and budget.',
                    'ru' => 'Подбор комплектующих и сборка ПК в Тбилиси для офиса, разработки, дизайна и игр. Конфигурацию определяют задачи, бюджет и планы обновления.',
                ],
                'technical' => [
                    'ka' => 'ვამოწმებთ CPU-სა და დედაპლატის სოკეტს, BIOS-ის მხარდაჭერას, RAM-ის პროფილს, SSD-ის ინტერფეისს, GPU-ის გაბარიტებს, კვების ბლოკის რესურსსა და გაგრილებას. აწყობის შემდეგ ტარდება ტემპერატურის, მეხსიერების, დისკისა და დატვირთვის ტესტები.',
                    'en' => 'We check CPU socket and BIOS support, RAM profiles, SSD interface, GPU clearance, PSU headroom and case airflow. Handover includes temperatures, memory, storage and load checks.',
                    'ru' => 'Проверяем сокет и BIOS, профили ОЗУ, интерфейс SSD, габариты видеокарты, запас мощности БП и вентиляцию корпуса. После сборки тестируем температуры, память, диск и нагрузку.',
                ],
                'scope' => [
                    'ka' => 'გამოგვიგზავნეთ ძირითადი პროგრამების ან თამაშების სია, ეკრანის გარჩევადობა, ბიუჯეტი და უკვე შეძენილი ნაწილები. თუ გაქვთ ნაწილები, წინასწარ მოწმდება მათი თავსებადობა და გარანტიის პირობები.',
                    'en' => 'Share the main applications or games, monitor resolution, target budget and already owned parts. We check compatibility and component warranty terms before assembly.',
                    'ru' => 'Укажите программы или игры, разрешение монитора, бюджет и уже купленные детали. Совместимость и гарантийные условия комплектующих проверяются заранее.',
                ],
                'seo' => [
                    'ka' => 'კომპიუტერის აწყობა თბილისში: კომპონენტების თავსებადობა, გაგრილება, BIOS, დატვირთვის ტესტი. მიიღეთ მოთხოვნებზე მორგებული კონფიგურაცია.',
                    'en' => 'Custom PC assembly in Tbilisi: compatible parts, cooling, BIOS setup and load testing. Plan an office, developer or gaming workstation.',
                    'ru' => 'Сборка ПК в Тбилиси: совместимые детали, охлаждение, настройка BIOS и тест под нагрузкой. Подбор под задачи и бюджет.',
                ],
                'cta' => ['ka' => 'მოითხოვეთ კომპიუტერის კონფიგურაცია', 'en' => 'Request a PC build plan', 'ru' => 'Заказать подбор конфигурации'],
                'request' => [
                    'ka' => 'მოგვწერეთ მიზანი, ბიუჯეტი და უკვე არსებული ნაწილები; შეგიდგენთ თავსებადობისა და აწყობის სამუშაოს შეთავაზებას.',
                    'en' => 'Share your workload, budget and owned components for a compatibility and assembly proposal.',
                    'ru' => 'Сообщите задачи, бюджет и имеющиеся детали для расчёта сборки.',
                ],
                'benefits' => $this->steps(
                    ['ka' => 'თავსებადი ნაწილები', 'en' => 'Compatible components', 'ru' => 'Совместимые детали'],
                    ['ka' => 'სოკეტი, BIOS, RAM და კორპუსის სივრცე მოწმდება შეკვეთამდე.', 'en' => 'Socket, BIOS, RAM and case clearance are checked ahead.', 'ru' => 'Сокет, BIOS, ОЗУ и место в корпусе проверяются заранее.'],
                    ['ka' => 'გაგრილება და კვება', 'en' => 'Cooling and power', 'ru' => 'Охлаждение и питание'],
                    ['ka' => 'ვითვალისწინებთ დატვირთვისას სიმძლავრისა და ტემპერატურის რეზერვს.', 'en' => 'We plan power and thermal headroom under load.', 'ru' => 'Учитываем запас мощности и температурный режим.'],
                    ['ka' => 'ტესტი ჩაბარებამდე', 'en' => 'Pre-handover tests', 'ru' => 'Тест перед сдачей'],
                    ['ka' => 'ვამოწმებთ მეხსიერებას, დისკს, დატვირთვასა და სტაბილურობას.', 'en' => 'Memory, storage and load stability are tested.', 'ru' => 'Тестируем память, накопитель и стабильность под нагрузкой.'],
                ),
                'faqs' => $this->basicFaq(
                    ['ka' => 'ჩემი ნაწილებით აწყობა შეიძლება?', 'en' => 'Can you assemble parts I already own?', 'ru' => 'Можно собрать из моих комплектующих?'],
                    ['ka' => 'დიახ, თუ ნაწილები თავსებადია; რისკები და გარანტიის პასუხისმგებლობა წინასწარ ზუსტდება.', 'en' => 'Yes, if compatible; component risks and warranty responsibility are agreed upfront.', 'ru' => 'Да, если детали совместимы; заранее согласуем риски и гарантию.'],
                    ['ka' => 'რატომ მჭირდება დატვირთვის ტესტი?', 'en' => 'Why run a load test?', 'ru' => 'Зачем нужен стресс-тест?'],
                    ['ka' => 'დატვირთვისას ჩანს გაგრილების, კვებისა და სტაბილურობის შესაძლო პრობლემა.', 'en' => 'It can reveal thermal, power and stability problems before handover.', 'ru' => 'Он помогает выявить проблемы температур, питания и стабильности.'],
                ),
            ],
            'computer-cleaning-maintenance' => [
                'name' => ['ka' => 'კომპიუტერის პროფილაქტიკა', 'en' => 'PC cleaning and maintenance', 'ru' => 'Чистка и обслуживание ПК'],
                'title' => ['ka' => 'კომპიუტერის გაწმენდა თბილისში', 'en' => 'PC cleaning and maintenance in Tbilisi', 'ru' => 'Чистка компьютеров в Тбилиси'],
                'intro' => [
                    'ka' => 'თბილისში კომპიუტერისა და ლეპტოპის პროფილაქტიკური გაწმენდა გადახურების, ხმაურისა და არასტაბილური მუშაობის მიზეზების დასადგენად. სამუშაოს მოცულობა განსხვავდება კორპუსის, გაგრილებისა და მოწყობილობის მდგომარეობის მიხედვით.',
                    'en' => 'Desktop and laptop maintenance in Tbilisi for dust, high temperatures, fan noise and unstable performance. Scope depends on the device construction and cooling condition.',
                    'ru' => 'Профилактика ПК и ноутбуков в Тбилиси при пыли, перегреве, шуме вентиляторов и нестабильной работе. Объём зависит от конструкции и состояния устройства.',
                ],
                'technical' => [
                    'ka' => 'ვზომავთ ტემპერატურას და ვამოწმებთ ვენტილატორებს, ჰაერის ნაკადს, რადიატორსა და მტვერს. საჭიროების შემთხვევაში შეთანხმებით ვცვლით თერმოპასტას ან თერმობალიშებს; ლეპტოპის დაშლა და დაზიანებული სამაგრები ცალკე ფასდება.',
                    'en' => 'We check operating temperatures, fans, airflow, heatsinks and dust buildup. Thermal paste or pads are replaced only when appropriate and agreed; delicate laptop disassembly is assessed separately.',
                    'ru' => 'Проверяем температуры, вентиляторы, поток воздуха, радиаторы и пыль. При необходимости согласуем замену термопасты или термопрокладок; сложную разборку ноутбука оцениваем отдельно.',
                ],
                'scope' => [
                    'ka' => 'გვითხარით მოწყობილობის მოდელი, ხმაურის ან გადახურების ნიშნები, ბოლოს როდის გაიწმინდა და სად გჭირდებათ მომსახურება. წმენდა არ ცვლის გაუმართავი ვენტილატორის ან სხვა დაზიანებული კომპონენტის შეკეთებას.',
                    'en' => 'Share the model, temperature or noise symptoms, last cleaning date and Tbilisi location. Cleaning cannot substitute for a failed fan or damaged component repair.',
                    'ru' => 'Сообщите модель, признаки перегрева или шума, дату последней чистки и адрес. Чистка не заменяет ремонт неисправного вентилятора или других деталей.',
                ],
                'seo' => [
                    'ka' => 'კომპიუტერის გაწმენდა და პროფილაქტიკა თბილისში: მტვერი, გაგრილება, თერმოპასტა საჭიროების მიხედვით და ტემპერატურის ტესტი.',
                    'en' => 'PC and laptop cleaning in Tbilisi: dust removal, cooling checks, thermal paste when needed and temperature testing.',
                    'ru' => 'Чистка ПК и ноутбуков в Тбилиси: удаление пыли, проверка охлаждения, термопаста при необходимости и тест температур.',
                ],
                'cta' => ['ka' => 'მოითხოვეთ პროფილაქტიკა', 'en' => 'Request device maintenance', 'ru' => 'Заказать обслуживание ПК'],
                'request' => [
                    'ka' => 'მოგვწერეთ მოდელი და გადახურების ან ხმაურის ნიშნები; მომსახურების მოცულობას დაზუსტებით შევათანხმებთ.',
                    'en' => 'Send the device model and heat or noise symptoms to agree on the maintenance scope.',
                    'ru' => 'Укажите модель и симптомы перегрева или шума, чтобы согласовать работы.',
                ],
                'benefits' => $this->steps(
                    ['ka' => 'წინასწარი დიაგნოსტიკა', 'en' => 'Initial diagnostics', 'ru' => 'Предварительная диагностика'],
                    ['ka' => 'ვზომავთ ტემპერატურებსა და ვაკვირდებით გაგრილების მუშაობას.', 'en' => 'We check temperature and cooling behavior.', 'ru' => 'Проверяем температуры и работу охлаждения.'],
                    ['ka' => 'მიზნობრივი პროფილაქტიკა', 'en' => 'Targeted cleaning', 'ru' => 'Целевая чистка'],
                    ['ka' => 'ვწმენდთ მტვერს და საჭიროებისას განვაახლებთ თერმოინტერფეისს.', 'en' => 'We remove dust and renew thermal interface only if warranted.', 'ru' => 'Удаляем пыль, обновляем термоинтерфейс при необходимости.'],
                    ['ka' => 'შემდგომი შემოწმება', 'en' => 'After-service check', 'ru' => 'Проверка после работ'],
                    ['ka' => 'შედარებით ვამოწმებთ ტემპერატურებსა და ვენტილატორების ხმაურს.', 'en' => 'We recheck temperatures and fan noise.', 'ru' => 'Повторно проверяем температуры и шум вентиляторов.'],
                ),
                'faqs' => $this->basicFaq(
                    ['ka' => 'ყოველთვის საჭიროა თერმოპასტის შეცვლა?', 'en' => 'Does thermal paste always need replacing?', 'ru' => 'Всегда ли нужна замена термопасты?'],
                    ['ka' => 'არა. მისი შეცვლა დამოკიდებულია დაშლის საჭიროებასა და თერმული ინტერფეისის მდგომარეობაზე.', 'en' => 'No. It depends on disassembly and the condition of the thermal interface.', 'ru' => 'Нет. Всё зависит от разборки и состояния термоинтерфейса.'],
                    ['ka' => 'გაწმენდა გადახურებას აუცილებლად მოაგვარებს?', 'en' => 'Will cleaning always fix overheating?', 'ru' => 'Чистка гарантирует устранение перегрева?'],
                    ['ka' => 'არა. შესაძლოა პრობლემა იყოს ვენტილატორში, სენსორში ან სხვა კომპონენტში; საჭიროებისას დამატებითი დიაგნოსტიკაა საჭირო.', 'en' => 'Not always. A fan, sensor or another component may need separate diagnostics.', 'ru' => 'Не всегда. Может потребоваться диагностика вентилятора, датчика или другого компонента.'],
                ),
            ],
            'rack-assembly-cable-management' => [
                'name' => ['ka' => 'სერვერული რეკის აწყობა', 'en' => 'Network rack assembly', 'ru' => 'Сборка сетевого шкафа'],
                'title' => ['ka' => 'სერვერული რეკის აწყობა თბილისში', 'en' => 'Network rack assembly in Tbilisi', 'ru' => 'Сборка сетевого шкафа в Тбилиси'],
                'intro' => [
                    'ka' => 'თბილისში სერვერული და ქსელური რეკების დაგეგმვა, აწყობა და მოწესრიგება ოფისებისთვის, მაღაზიებისთვისა და უსაფრთხოების სისტემების ინფრასტრუქტურისთვის. სამუშაოს დაწყებამდე ვითვლით მოწყობილობებსა და მომავალ გაფართოებას.',
                    'en' => 'Network and server rack planning and assembly in Tbilisi for offices, retail sites and CCTV infrastructure. We inventory equipment and future expansion before choosing the cabinet.',
                    'ru' => 'Проектирование и сборка серверных и сетевых шкафов в Тбилиси для офисов, магазинов и видеонаблюдения. До выбора шкафа учитываем оборудование и запас для расширения.',
                ],
                'technical' => [
                    'ka' => 'ვგეგმავთ 19-დუიმიან რეკში U-სივრცეს, სიღრმეს, პაჩ-პანელებს, სვიჩებს, NVR-ს, UPS-ს, კვების განაწილებასა და ვენტილაციას. კაბელები ინომრება, ეწყობა ჰორიზონტალური/ვერტიკალური მენეჯმენტი და მოწმდება მომსახურებისთვის წვდომა.',
                    'en' => 'We size 19-inch rack U-space and depth around patch panels, switches, NVR, UPS, power distribution and ventilation. Cables are labelled and routed for safe service access and maintenance.',
                    'ru' => 'Подбираем U-высоту и глубину 19-дюймового шкафа под патч-панели, коммутаторы, NVR, UPS, питание и вентиляцию. Кабели маркируем и укладываем с доступом для обслуживания.',
                ],
                'scope' => [
                    'ka' => 'ზუსტი შეთავაზებისთვის საჭიროა აღჭურვილობის სია, ქსელური წერტილების რაოდენობა, კაბელების სიგრძე, რეკის ადგილმდებარეობა და კვების ხელმისაწვდომობა. დენის ხაზებისა და დამიწების მონტაჟი კომპეტენტურ ელექტრიკოსთან თანხმდება.',
                    'en' => 'For a quote send the device list, outlet count, cable routes, cabinet location and power availability. New mains circuits and earthing require qualified electrical work.',
                    'ru' => 'Для расчёта нужны список устройств, число линий, трассы кабелей, место шкафа и доступное питание. Новые силовые линии и заземление согласуются с электриком.',
                ],
                'seo' => [
                    'ka' => 'სერვერული და ქსელური რეკის აწყობა თბილისში: 19″ კარადა, პაჩ-პანელები, UPS, NVR, კაბელ-მენეჯმენტი და მარკირება.',
                    'en' => 'Network rack installation in Tbilisi: 19-inch cabinets, patch panels, UPS, NVR, labelled cables and serviceable layout.',
                    'ru' => 'Сборка сетевых шкафов в Тбилиси: 19″ стойки, патч-панели, UPS, NVR, маркировка и укладка кабелей.',
                ],
                'cta' => ['ka' => 'მოითხოვეთ რეკის პროექტი', 'en' => 'Request a rack layout', 'ru' => 'Заказать проект шкафа'],
                'request' => [
                    'ka' => 'გამოგვიგზავნეთ რეკის მდებარეობა და მოწყობილობების სია; შევაფასებთ ზომასა და სამუშაოს მოცულობას.',
                    'en' => 'Send the cabinet location and equipment list so we can estimate the enclosure and installation scope.',
                    'ru' => 'Сообщите место установки и список оборудования для расчёта шкафа и работ.',
                ],
                'benefits' => $this->steps(
                    ['ka' => 'სწორი ზომები', 'en' => 'Correct dimensions', 'ru' => 'Подходящие размеры'],
                    ['ka' => 'ვამოწმებთ სიმაღლეს, სიღრმესა და გაფართოების სივრცეს.', 'en' => 'We check height, depth and expansion headroom.', 'ru' => 'Проверяем высоту, глубину и резерв пространства.'],
                    ['ka' => 'მარკირებული ხაზები', 'en' => 'Labelled cabling', 'ru' => 'Маркированные линии'],
                    ['ka' => 'პაჩ-პანელები და ქსელის კაბელები იდენტიფიცირებადია.', 'en' => 'Patch panels and cables remain identifiable.', 'ru' => 'Патч-панели и кабели остаются понятными для обслуживания.'],
                    ['ka' => 'განლაგება მომსახურებისთვის', 'en' => 'Serviceable layout', 'ru' => 'Доступ для обслуживания'],
                    ['ka' => 'ვითვალისწინებთ ვენტილაციას, UPS-სა და მოწყობილობაზე წვდომას.', 'en' => 'We plan ventilation, UPS and equipment access.', 'ru' => 'Планируем вентиляцию, UPS и доступ к устройствам.'],
                ),
                'faqs' => $this->basicFaq(
                    ['ka' => 'როგორი სიღრმის რეკი მჭირდება?', 'en' => 'Which rack depth do I need?', 'ru' => 'Какая глубина шкафа нужна?'],
                    ['ka' => 'მოწყობილობის სიღრმისა და კაბელების მოსახვევის რადიუსის მიხედვით ვარჩევთ.', 'en' => 'It depends on device depth and cable bend clearance.', 'ru' => 'Зависит от глубины оборудования и пространства для изгиба кабелей.'],
                    ['ka' => 'არსებული რეკის მოწესრიგებაც შეიძლება?', 'en' => 'Can you reorganize an existing rack?', 'ru' => 'Можно привести в порядок существующий шкаф?'],
                    ['ka' => 'დიახ, წინასწარი ინვენტარიზაციისა და შესაძლო გათიშვის დროის შეთანხმებით.', 'en' => 'Yes, after inventory and agreement on any required downtime.', 'ru' => 'Да, после инвентаризации и согласования возможного простоя.'],
                ),
            ],
            'pos-system-installation' => [
                'name' => ['ka' => 'POS სისტემის გამართვა', 'en' => 'POS system setup', 'ru' => 'Настройка POS-систем'],
                'title' => ['ka' => 'POS სისტემის მონტაჟი თბილისში', 'en' => 'POS system setup in Tbilisi', 'ru' => 'Установка POS-систем в Тбилиси'],
                'intro' => [
                    'ka' => 'თბილისში POS-ის სამუშაო ადგილის გამართვა მაღაზიებისთვის, კაფეებისა და მომსახურების ობიექტებისთვის: კომპიუტერი, ეკრანი, პრინტერი, სკანერი და ქსელი. პროგრამის არჩევანი და ფისკალური ინტეგრაცია ცალკე განისაზღვრება.',
                    'en' => 'POS workstation installation in Tbilisi for shops, cafes and service counters, including PC, display, printer, scanner and networking. Software selection and fiscal integration are scoped separately.',
                    'ru' => 'Настройка POS-рабочего места в Тбилиси для магазинов, кафе и сферы услуг: компьютер, экран, принтер, сканер и сеть. ПО и фискальная интеграция обсуждаются отдельно.',
                ],
                'technical' => [
                    'ka' => 'ვამოწმებთ USB/LAN/COM ინტერფეისებს, პრინტერის დრაივერებს, IP მისამართებს, სარეზერვო კვებასა და მოწყობილობების თავსებადობას. ტესტირდება საცდელი ჩეკი ან არასაფისკალო ბეჭდვა სისტემის შესაძლებლობების მიხედვით.',
                    'en' => 'We verify USB/LAN/COM interfaces, printer drivers, IP configuration, backup power and device compatibility. Test printing or a test transaction follows the vendor system capabilities.',
                    'ru' => 'Проверяем USB/LAN/COM, драйверы принтера, IP-настройки, резервное питание и совместимость устройств. Тест печати или операции зависит от возможностей выбранной системы.',
                ],
                'scope' => [
                    'ka' => 'გამოგვიგზავნეთ POS პროგრამის დასახელება, მოწყობილობის მოდელები, სალაროების რაოდენობა და არსებული ქსელის სქემა. ანგარიშსწორების/ფისკალური სერვისის აქტივაცია ხდება შესაბამისი მიმწოდებლის წესებით; არ ვპირდებით უნივერსალურ თავსებადობას.',
                    'en' => 'Share the POS software, device models, counter count and existing network. Payment and fiscal activation follow the relevant provider requirements; universal compatibility is not assumed.',
                    'ru' => 'Укажите POS-программу, модели устройств, число касс и текущую сеть. Подключение платежей и фискальных сервисов зависит от правил поставщика; совместимость проверяется.',
                ],
                'seo' => [
                    'ka' => 'POS სისტემის მონტაჟი და გამართვა თბილისში: სალარო, პრინტერი, სკანერი, ქსელი და მოწყობილობების თავსებადობის ტესტირება.',
                    'en' => 'POS installation in Tbilisi: till workstation, receipt printer, scanner, network and compatible software setup.',
                    'ru' => 'Установка POS в Тбилиси: кассовое место, чековый принтер, сканер, сеть и проверка совместимости ПО.',
                ],
                'cta' => ['ka' => 'POS სამუშაო ადგილის გამართვა', 'en' => 'Request POS installation', 'ru' => 'Заказать установку POS'],
                'request' => [
                    'ka' => 'მოგვწერეთ POS პროგრამა, მოდელები და სალაროების რაოდენობა — დავაზუსტებთ ინტეგრაციასა და სამუშაოს მოცულობას.',
                    'en' => 'Send software name, hardware models and number of tills for an integration and installation quote.',
                    'ru' => 'Укажите ПО, модели оборудования и количество касс для расчёта работ.',
                ],
                'benefits' => $this->steps(
                    ['ka' => 'თავსებადობის შემოწმება', 'en' => 'Compatibility check', 'ru' => 'Проверка совместимости'],
                    ['ka' => 'POS, პრინტერი და სკანერი მოწმდება პროგრამის მოთხოვნებთან.', 'en' => 'POS, printers and scanners are checked against software needs.', 'ru' => 'POS, принтеры и сканеры проверяются с учётом требований ПО.'],
                    ['ka' => 'სტაბილური ქსელი', 'en' => 'Reliable connectivity', 'ru' => 'Стабильная сеть'],
                    ['ka' => 'ვგეგმავთ IP მისამართებსა და საჭირო Ethernet/Wi‑Fi კავშირს.', 'en' => 'We configure IP addressing and suitable Ethernet/Wi‑Fi connectivity.', 'ru' => 'Настраиваем IP-адреса и необходимую связь Ethernet/Wi‑Fi.'],
                    ['ka' => 'საცდელი შემოწმება', 'en' => 'Functional test', 'ru' => 'Функциональный тест'],
                    ['ka' => 'საბოლოო სამუშაო რეჟიმი მოწმდება მიმწოდებლის ფუნქციების ფარგლებში.', 'en' => 'We verify supported workflows before handover.', 'ru' => 'Проверяем поддерживаемые сценарии до передачи рабочего места.'],
                ),
                'faqs' => $this->basicFaq(
                    ['ka' => 'ნებისმიერ სალარო პროგრამასთან იმუშავებს?', 'en' => 'Will it work with any POS software?', 'ru' => 'Работает с любой POS-программой?'],
                    ['ka' => 'თავსებადობა დამოკიდებულია პრინტერის, სკანერის, პროგრამისა და მიმწოდებლის ინტეგრაციის მხარდაჭერაზე.', 'en' => 'It depends on printer/scanner drivers and the software vendor integration.', 'ru' => 'Зависит от драйверов, устройств и интеграции поставщика ПО.'],
                    ['ka' => 'სალაროს გადატანისას მონაცემებს შეინახავთ?', 'en' => 'Can you preserve data during migration?', 'ru' => 'Сохраните данные при переносе кассы?'],
                    ['ka' => 'მონაცემთა მიგრაციის შესაძლებლობას პროგრამის მომწოდებელთან და სარეზერვო ასლის ხელმისაწვდომობით ვადგენთ.', 'en' => 'We assess backup and migration options with the POS software provider.', 'ru' => 'Возможность переноса данных определяем по резервной копии и правилам поставщика ПО.'],
                ),
            ],
            'patch-panel-network-outlet-installation' => [
                'name' => ['ka' => 'პაჩ-პანელის და RJ45 როზეტის მონტაჟი', 'en' => 'Patch panel and RJ45 outlet installation', 'ru' => 'Монтаж патч-панелей и RJ45'],
                'title' => ['ka' => 'პაჩ-პანელის და RJ45 მონტაჟი თბილისში', 'en' => 'Patch panel and RJ45 installation in Tbilisi', 'ru' => 'Монтаж патч-панелей и RJ45 в Тбилиси'],
                'intro' => [
                    'ka' => 'თბილისში LAN ქსელის ტერმინაცია და მოწესრიგება ოფისებისთვის, კერძო სახლებისა და ვიდეოსამეთვალყურეო ინფრასტრუქტურისთვის: პაჩ-პანელები, Keystones, RJ45 როზეტები და პაჩ-კორდები.',
                    'en' => 'LAN termination in Tbilisi for offices, homes and CCTV infrastructure: patch panels, keystones, RJ45 wall outlets and patch cords.',
                    'ru' => 'Терминация LAN в Тбилиси для офисов, домов и видеонаблюдения: патч-панели, кейстоуны, RJ45-розетки и патч-корды.',
                ],
                'technical' => [
                    'ka' => 'CAT5e/CAT6 კაბელის კატეგორიას ვუთავსებთ შესაბამის კონექტორსა და Keystone-ს, ვიცავთ წყვილების განლაგებას და ორივე ბოლოზე ერთ სქემას (T568A ან T568B). დასრულებულ ხაზებს ვანიშნავთ ნომრებს და ვამოწმებთ გამტარობასა და წყვილების სისწორეს.',
                    'en' => 'We match CAT5e/CAT6 cable to compatible connectors and keystones, maintain pair termination using T568A or T568B consistently and label both ends. We test wiremap and continuity after termination.',
                    'ru' => 'Подбираем коннекторы под CAT5e/CAT6, соблюдаем раскладку пар T568A или T568B на обоих концах и маркируем линии. После монтажа проверяем wiremap и целостность.',
                ],
                'scope' => [
                    'ka' => 'შეთავაზებისთვის დაგვჭირდება პორტების რაოდენობა, კაბელის ტიპი, პაჩ-პანელის U-სივრცე, როზეტის მდებარეობა და არსებული ხაზების მდგომარეობა. საბაზისო ტესტერი და კატეგორიის სრულფასოვანი სერტიფიცირება სხვადასხვა მომსახურებაა.',
                    'en' => 'Send port count, cable category, rack U-space, outlet positions and existing line condition. Basic continuity testing and full standards certification are different service scopes.',
                    'ru' => 'Для расчёта укажите число портов, категорию кабеля, место в шкафу, расположение розеток и состояние линий. Обычный тест линий и сертификация — разные услуги.',
                ],
                'seo' => [
                    'ka' => 'RJ45 როზეტისა და პაჩ-პანელის მონტაჟი თბილისში: CAT6 ტერმინაცია, Keystone, მარკირება და ქსელის ხაზების ტესტი.',
                    'en' => 'RJ45 outlet and patch panel installation in Tbilisi: CAT6 termination, keystones, labelling and wiremap testing.',
                    'ru' => 'Монтаж RJ45 и патч-панелей в Тбилиси: CAT6, кейстоуны, маркировка и проверка линий.',
                ],
                'cta' => ['ka' => 'მოითხოვეთ RJ45 მონტაჟის შეთავაზება', 'en' => 'Request LAN termination', 'ru' => 'Заказать терминацию LAN'],
                'request' => [
                    'ka' => 'გამოგვიგზავნეთ პორტების რაოდენობა, კაბელის კატეგორია და რეკის/როზეტების ფოტო — დაზუსტდება საჭირო მასალები.',
                    'en' => 'Share port count, cable category and rack/outlet photos so materials can be scoped.',
                    'ru' => 'Сообщите число портов, тип кабеля и приложите фото шкафа и розеток для оценки материалов.',
                ],
                'benefits' => $this->steps(
                    ['ka' => 'სწორი ტერმინაცია', 'en' => 'Correct termination', 'ru' => 'Правильная терминация'],
                    ['ka' => 'კაბელის კატეგორიის შესაბამისი Keystone და პაჩ-პანელი.', 'en' => 'Keystone and patch panel selected for cable category.', 'ru' => 'Кейстоуны и патч-панели под категорию кабеля.'],
                    ['ka' => 'სქემა და მარკირება', 'en' => 'Wiremap and labels', 'ru' => 'Схема и маркировка'],
                    ['ka' => 'T568A/B ერთიანი სქემა და ნომრები ორივე ბოლოზე.', 'en' => 'Consistent T568A/B and IDs at both ends.', 'ru' => 'Единая схема T568A/B и номера на обоих концах.'],
                    ['ka' => 'ხაზის ტესტირება', 'en' => 'Line testing', 'ru' => 'Тест линий'],
                    ['ka' => 'ვამოწმებთ წყვილების კავშირს, წყვეტას და არასწორ გადაჯვარედინებას.', 'en' => 'We check continuity and pair miswiring.', 'ru' => 'Проверяем целостность и неправильную разводку пар.'],
                ),
                'faqs' => $this->basicFaq(
                    ['ka' => 'CAT6-ს CAT5e Keystone მოერგება?', 'en' => 'Can CAT5e keystones certify a CAT6 link?', 'ru' => 'Подойдут ли CAT5e кейстоуны для CAT6?'],
                    ['ka' => 'ფიზიკური თავსებადობა არ ნიშნავს CAT6 კატეგორიის შესაბამისობას. საჭირო კატეგორიის კომპონენტებს წინასწარ ვარჩევთ.', 'en' => 'Physical fit does not make a CAT6-rated channel; components need the appropriate category.', 'ru' => 'Физическая совместимость не означает соответствие CAT6; выбираем нужную категорию компонентов.'],
                    ['ka' => 'ქსელის სრული სერტიფიცირება შედის?', 'en' => 'Does it include certification?', 'ru' => 'Входит ли сертификация сети?'],
                    ['ka' => 'საბაზისო გამტარობის ტესტი და სპეციალიზებული სერტიფიცირება განსხვავდება; საჭიროებისას ცალკე შეთანხმდება.', 'en' => 'Basic wiremap tests differ from specialist certification, which is scoped separately.', 'ru' => 'Базовый тест и специализированная сертификация — разные услуги, согласуются отдельно.'],
                ),
            ],
        ];
    }

    /**
     * @return array<int, array{title:array<string,string>,description:array<string,string>}>
     */
    private function steps(array ...$values): array
    {
        return [
            ['title' => $values[0], 'description' => $values[1]],
            ['title' => $values[2], 'description' => $values[3]],
            ['title' => $values[4], 'description' => $values[5]],
        ];
    }

    /** @return array<int, array{question:array<string,string>,answer:array<string,string>}> */
    private function basicFaq(array ...$values): array
    {
        return [
            ['question' => $values[0], 'answer' => $values[1]],
            ['question' => $values[2], 'answer' => $values[3]],
        ];
    }
}
