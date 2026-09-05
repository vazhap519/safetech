<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $cities = [
            'tbilisi' => ['name' => 'თბილისი', 'locative' => 'თბილისში'],
            'khashuri' => ['name' => 'ხაშური', 'locative' => 'ხაშურში'],
            'bakuriani' => ['name' => 'ბაკურიანი', 'locative' => 'ბაკურიანში'],
            'borjomi' => ['name' => 'ბორჯომი', 'locative' => 'ბორჯომში'],
            'surami' => ['name' => 'სურამი', 'locative' => 'სურამში'],
        ];

        $services = [
            'network-cable-installation' => [
                'label' => 'ქსელის კაბელის გაყვანა',
                'short' => 'CAT6 ქსელის გაყვანა',
                'description' => 'CAT5e/CAT6 ქსელის დაგეგმვა, კაბელის პროფესიონალური გაყვანა, RJ45 წერტილები, მარკირება და ხაზების ტესტირება.',
                'benefits' => [
                    ['title' => 'სწორი მარშრუტი', 'description' => 'ქსელის მარშრუტი იგეგმება ელექტრო ხაზების, სიგრძეებისა და სამომავლო გაფართოების გათვალისწინებით.'],
                    ['title' => 'მარკირება და ტესტირება', 'description' => 'ხაზები მარკირდება ორივე ბოლოში და მონტაჟის შემდეგ მოწმდება ტესტერით.'],
                    ['title' => 'ბიზნესისა და სახლის ქსელი', 'description' => 'ვაწყობთ სტაბილურ LAN ინფრასტრუქტურას კამერებისთვის, კომპიუტერებისთვის, POS-სა და Wi‑Fi Access Point-ებისთვის.'],
                ],
                'faqs' => [
                    ['question' => 'CAT6 კაბელის გაყვანის ფასი როგორ ითვლება?', 'answer' => 'ღირებულება დამოკიდებულია კაბელის მეტრაჟზე, მარშრუტზე, გოფრა/არხზე, RJ45 წერტილების რაოდენობაზე და სამუშაო გარემოზე.'],
                    ['question' => 'დამონტაჟებული ხაზები იტესტება?', 'answer' => 'დიახ. ხაზები მოწმდება კონტაქტებზე და გამტარობაზე, ხოლო საჭიროების შემთხვევაში კეთდება დამატებითი ქსელური დიაგნოსტიკა.'],
                    ['question' => 'რემონტამდე ქსელის დაგეგმვაც შეგიძლიათ?', 'answer' => 'დიახ. შესაძლებელია კაბელების მარშრუტებისა და ქსელის წერტილების წინასწარ დაგეგმვა, სანამ კედლები და ჭერი საბოლოოდ დაიხურება.'],
                ],
            ],
            'router-wifi-configuration' => [
                'label' => 'Wi‑Fi და MikroTik-ის გამართვა',
                'short' => 'Wi‑Fi / MikroTik',
                'description' => 'როუტერის, Wi‑Fi დაფარვის, MikroTik-ის, VLAN, VPN, DHCP, Firewall და უსაფრთხო ინტერნეტის პროფესიონალური კონფიგურაცია.',
                'benefits' => [
                    ['title' => 'სტაბილური Wi‑Fi', 'description' => 'Access Point/Mesh განლაგება იგეგმება ფართობის, კედლებისა და სართულების მიხედვით.'],
                    ['title' => 'უსაფრთხო კონფიგურაცია', 'description' => 'ვამართავთ ადმინისტრატორის წვდომას, Wi‑Fi დაცვას, DHCP-სა და Firewall წესებს.'],
                    ['title' => 'MikroTik და ბიზნეს ქსელი', 'description' => 'საჭიროებისას ვაწყობთ VLAN, VPN, Port Forwarding და მრავალქსელიან კონფიგურაციას.'],
                ],
                'faqs' => [
                    ['question' => 'რატომ არის Wi‑Fi სუსტი ზოგ ოთახში?', 'answer' => 'მიზეზი ხშირად არის კედლები, სართულები, არასწორი როუტერის მდებარეობა ან რადიოჩარევა. ობიექტის მიხედვით შეიძლება საჭირო იყოს Access Point ან Mesh.'],
                    ['question' => 'MikroTik-ის სრულ კონფიგურაციას აკეთებთ?', 'answer' => 'დიახ. ვამართავთ WAN, DHCP, NAT, VLAN, VPN, Firewall, Port Forwarding და სხვა საჭირო ფუნქციებს.'],
                    ['question' => 'პროვაიდერის შეცვლის შემდეგ ქსელს თავიდან გამართავთ?', 'answer' => 'დიახ. ვასწორებთ ახალ WAN პარამეტრებს და ვამოწმებთ ლოკალურ ქსელს, სერვერებს, კომპიუტერებს, POS-სა და სხვა დაკავშირებულ მოწყობილობებს.'],
                ],
            ],
            'business-it-support' => [
                'label' => 'IT მხარდაჭერა ბიზნესისთვის',
                'short' => 'IT Support',
                'description' => 'ოფისების, მაღაზიების, სასტუმროებისა და სხვა ბიზნესების კომპიუტერების, პრინტერების, ქსელის, POS-ისა და Windows სისტემების დისტანციური და ადგილზე მხარდაჭერა.',
                'benefits' => [
                    ['title' => 'ერთი პასუხისმგებელი IT პარტნიორი', 'description' => 'კომპიუტერი, პრინტერი, ქსელი, Wi‑Fi და სხვა ყოველდღიური IT საკითხები ერთ სივრცეში გვარდება.'],
                    ['title' => 'დისტანციური და ადგილზე დახმარება', 'description' => 'პრობლემის ტიპის მიხედვით მხარდაჭერა შესაძლებელია დისტანციურად ან ტექნიკოსის ვიზიტით.'],
                    ['title' => 'ერთჯერადი ან აბონენტური მომსახურება', 'description' => 'შესაძლებელია როგორც კონკრეტული პრობლემის მოგვარება, ისე პერიოდული IT მხარდაჭერის შეთანხმება.'],
                ],
                'faqs' => [
                    ['question' => 'IT მხარდაჭერა მხოლოდ კომპანიებისთვისაა?', 'answer' => 'ძირითადი სერვისი გათვლილია ბიზნესზე, თუმცა კონკრეტული ტექნიკური სამუშაოები შესაძლებელია სხვა ტიპის ობიექტებზეც შეთანხმებით.'],
                    ['question' => 'აბონენტური IT მომსახურება შესაძლებელია?', 'answer' => 'დიახ. გეგმა ფორმირდება კომპიუტერების, მომხმარებლების, ქსელური მოწყობილობებისა და საჭირო რეაგირების დროის მიხედვით.'],
                    ['question' => 'დისტანციურად რა პრობლემებს აგვარებთ?', 'answer' => 'Windows, პროგრამები, მომხმარებლის პარამეტრები, პრინტერების ნაწილი, ქსელური კონფიგურაცია და სხვა საკითხები, რომლებიც ფიზიკურ ჩარევას არ საჭიროებს.'],
                ],
            ],
            'barrier-gate-installation' => [
                'label' => 'შლაგბაუმის მონტაჟი და LPR',
                'short' => 'შლაგბაუმი / LPR',
                'description' => 'ავტომატური შლაგბაუმის მონტაჟი, ფოტოსენსორი, Loop Detector, პულტი, GSM, ნომრის ამოცნობა (LPR) და დაშვების სისტემასთან ინტეგრაცია.',
                'benefits' => [
                    ['title' => 'უსაფრთხო ავტომატიკა', 'description' => 'ფოტოსენსორი და/ან ინდუქციური მარყუჟი გამოიყენება ავტომობილისა და დაბრკოლების უსაფრთხოდ დასაფიქსირებლად.'],
                    ['title' => 'LPR ნომრის ამოცნობა', 'description' => 'თავსებადი კამერით შესაძლებელია დაშვებული ნომრების მიხედვით ავტომატური გახსნა.'],
                    ['title' => 'პულტი, GSM ან დაშვების კონტროლი', 'description' => 'მართვის მეთოდი შეირჩევა ობიექტის გამოყენების სცენარის მიხედვით.'],
                ],
                'faqs' => [
                    ['question' => 'შლაგბაუმი ნომრის ამოცნობით ავტომატურად გაიღება?', 'answer' => 'დიახ. თავსებადი LPR კამერა და კონტროლის სქემა საშუალებას იძლევა whitelist-ში დამატებული ნომრის ამოცნობისას შლაგბაუმი ავტომატურად გაიხსნას.'],
                    ['question' => 'Loop Detector და Photocell ორივე საჭიროა?', 'answer' => 'დამოკიდებულია ობიექტზე. Loop Detector მანქანის არსებობას გზის ზედაპირში ჩადგმული მარყუჟით ადგენს, Photocell კი დამატებით აკონტროლებს დაბრკოლებას სხივის ზონაში.'],
                    ['question' => 'ტელეფონით გახსნა შეიძლება?', 'answer' => 'დიახ. შესაძლებელია GSM კონტროლერი, აპლიკაცია ან სხვა დაშვების სისტემასთან ინტეგრაცია.'],
                ],
            ],
            'intercom-access-control-installation' => [
                'label' => 'დომოფონი და დაშვების კონტროლი',
                'short' => 'Access Control',
                'description' => 'ვიდეოდომოფონის, ელექტრო საკეტის, Exit ღილაკის, ბარათის, PIN-ის, ბიომეტრიის, კარის კონტაქტისა და სარეზერვო კვების მონტაჟი და გამართვა.',
                'benefits' => [
                    ['title' => 'კარის უსაფრთხო მართვა', 'description' => 'საკეტი და კონტროლერი შეირჩევა კარის ტიპის, უსაფრთხოების მოთხოვნისა და კვების სქემის მიხედვით.'],
                    ['title' => 'ბარათი, PIN და ბიომეტრია', 'description' => 'დაშვების მეთოდები შესაძლებელია გაერთიანდეს ერთ სისტემაში.'],
                    ['title' => 'დომოფონი და მობილური აპი', 'description' => 'თავსებადი IP სისტემებით შესაძლებელია ზარის მიღება და კარის გახსნა მობილურიდან.'],
                ],
                'faqs' => [
                    ['question' => 'Fail-safe და fail-secure საკეტს შორის რა განსხვავებაა?', 'answer' => 'Fail-safe კვების დაკარგვისას იხსნება, fail-secure კი ჩაკეტილ მდგომარეობას ინარჩუნებს. სწორი ვარიანტი შეირჩევა კარის დანიშნულებისა და უსაფრთხოების მოთხოვნის მიხედვით.'],
                    ['question' => 'დენის გათიშვისას სისტემა იმუშავებს?', 'answer' => 'სარეზერვო კვების დამატების შემთხვევაში კონტროლერი, საკეტი და საჭირო ქსელური მოწყობილობები განსაზღვრული დროით გააგრძელებს მუშაობას.'],
                    ['question' => 'ბარათით და PIN-ით ერთდროულად შეიძლება დაშვება?', 'answer' => 'დიახ. თავსებადი კონტროლერის შემთხვევაში შესაძლებელია ბარათი, PIN, ბიომეტრია ან მათი კომბინაცია.'],
                ],
            ],
        ];

        foreach ($services as $serviceSlug => $serviceData) {
            $serviceId = DB::table('services')->where('slug', $serviceSlug)->value('id');
            if (! $serviceId) {
                continue;
            }

            foreach ($cities as $index => $city) {
                $cityName = $city['name'];
                $locative = $city['locative'];
                $primaryKeyword = $serviceData['short'].' '.$locative;

                $title = match ($serviceSlug) {
                    'network-cable-installation' => "ქსელის კაბელის გაყვანა {$locative} — CAT6, RJ45 და ტესტირება",
                    'router-wifi-configuration' => "Wi‑Fi და MikroTik-ის გამართვა {$locative}",
                    'business-it-support' => "IT მხარდაჭერა {$locative} — ბიზნესისთვის ადგილზე და დისტანციურად",
                    'barrier-gate-installation' => "შლაგბაუმის მონტაჟი {$locative} — LPR, GSM და უსაფრთხოების სენსორები",
                    default => "დომოფონის და დაშვების კონტროლის მონტაჟი {$locative}",
                };

                $localContext = match ($cityName) {
                    'თბილისი' => 'ვემსახურებით ოფისებს, მაღაზიებს, საცხოვრებელ და კომერციულ ობიექტებს სხვადასხვა უბანში. სამუშაოს დაგეგმვისას ყურადღებას ვაქცევთ დატვირთვას, კაბელების მარშრუტებსა და შემდგომ მომსახურებას.',
                    'ხაშური' => 'ადგილობრივ ობიექტებზე შესაძლებელია სწრაფი ტექნიკური შეფასება, მონტაჟის დაგეგმვა და შემდგომი მხარდაჭერა. გადაწყვეტილება ერგება როგორც კერძო სახლს, ისე მაღაზიას, ოფისსა და საწარმოს.',
                    'ბაკურიანი' => 'კოტეჯების, სასტუმროებისა და სეზონურად დატვირთული ობიექტებისთვის მნიშვნელოვანია სტაბილური მუშაობა, სარეზერვო სცენარები და დისტანციური მართვა. სისტემას სწორედ ამ პირობების გათვალისწინებით ვგეგმავთ.',
                    'ბორჯომი' => 'ვმუშაობთ საცხოვრებელ, სასტუმრო და ბიზნეს ობიექტებზე. წინასწარ ვაფასებთ ინფრასტრუქტურას, საჭირო მოწყობილობებსა და სამუშაოს მოცულობას.',
                    default => 'კერძო სახლებისა და მცირე ბიზნესისთვის ვაწყობთ პრაქტიკულ, გამართულ და შემდგომ გაფართოებაზე გათვლილ ტექნიკურ გადაწყვეტებს.',
                };

                $excerpt = $serviceData['description'].' SafeTech მუშაობს '.$locative.' და საქართველოს რეგიონებში წინასწარი შეთანხმებით.';
                $content = $excerpt."\n\n".$localContext."\n\n".'სამუშაო იწყება მოთხოვნების დაზუსტებითა და ობიექტის პირობების შეფასებით. ამის შემდეგ განისაზღვრება მოწყობილობები, სამუშაოს მოცულობა, მონტაჟის ეტაპები და საბოლოო ღირებულება. მიზანია არა მხოლოდ მონტაჟი, არამედ გამართული და მომსახურებადი სისტემა.';
                $seoTitle = $title.' | SafeTech';
                $seoDescription = $serviceData['description'].' მომსახურება '.$locative.'. კონსულტაცია, დაგეგმვა, მონტაჟი და გამართვა — SafeTech.';
                $keywords = [$primaryKeyword, $serviceData['label'].' '.$cityName, 'SafeTech '.$cityName];

                DB::table('local_service_landings')->updateOrInsert(
                    ['service_id' => $serviceId, 'location_slug' => array_search($city, $cities, true) ?: ''],
                    []
                );

                $locationSlug = array_keys($cities)[$index];
                DB::table('local_service_landings')->updateOrInsert(
                    ['service_id' => $serviceId, 'location_slug' => $locationSlug],
                    [
                        'location_name' => $cityName,
                        'eyebrow' => $serviceData['label'].' — '.$cityName,
                        'title' => $title,
                        'excerpt' => $excerpt,
                        'content' => $content,
                        'cta_title' => 'მიიღეთ შეთავაზება '.$locative,
                        'cta_text' => 'მოგვწერეთ ობიექტის ტიპი, მდებარეობა და რა სისტემის მოწყობა ან გამართვა გჭირდებათ. დაგეხმარებით ტექნიკური გადაწყვეტისა და საორიენტაციო ბიუჯეტის განსაზღვრაში.',
                        'benefits' => json_encode($serviceData['benefits'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'faq' => json_encode($serviceData['faqs'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'primary_keyword' => $primaryKeyword,
                        'keywords' => json_encode($keywords, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'seo_title' => $seoTitle,
                        'seo_description' => $seoDescription,
                        'is_published' => true,
                        'noindex' => false,
                        'published_at' => now(),
                        'sort_order' => $index + 1,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ],
                );

                $landingId = DB::table('local_service_landings')
                    ->where('service_id', $serviceId)
                    ->where('location_slug', $locationSlug)
                    ->value('id');

                if ($landingId) {
                    $projectIds = DB::table('projects')
                        ->where('is_published', true)
                        ->where(function ($query) use ($cityName) {
                            $query->where('title', 'like', "%{$cityName}%")
                                ->orWhere('description', 'like', "%{$cityName}%");
                        })
                        ->orderByDesc('published_at')
                        ->limit(3)
                        ->pluck('id');

                    foreach ($projectIds as $projectId) {
                        DB::table('local_service_landing_project')->updateOrInsert(
                            ['landing_id' => $landingId, 'project_id' => $projectId],
                            ['updated_at' => now(), 'created_at' => now()],
                        );
                    }
                }
            }
        }
    }

    public function down(): void
    {
        // Content migration intentionally preserved on rollback.
    }
};
