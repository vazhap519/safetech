<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $cities = [
            'tbilisi' => ['name' => 'თბილისი', 'locative' => 'თბილისში', 'context' => 'ოფისებს, მაღაზიებს, საცხოვრებელ და კომერციულ ობიექტებს სხვადასხვა უბანში ვემსახურებით.'],
            'khashuri' => ['name' => 'ხაშური', 'locative' => 'ხაშურში', 'context' => 'ადგილობრივ ობიექტებზე შესაძლებელია სწრაფი ტექნიკური შეფასება და შემდგომი მხარდაჭერა.'],
            'bakuriani' => ['name' => 'ბაკურიანი', 'locative' => 'ბაკურიანში', 'context' => 'კოტეჯებისა და სასტუმროებისთვის სისტემას სეზონური დატვირთვისა და დისტანციური მართვის გათვალისწინებით ვგეგმავთ.'],
            'borjomi' => ['name' => 'ბორჯომი', 'locative' => 'ბორჯომში', 'context' => 'ვმუშაობთ საცხოვრებელ, სასტუმრო და ბიზნეს ობიექტებზე და წინასწარ ვაფასებთ ინფრასტრუქტურას.'],
            'surami' => ['name' => 'სურამი', 'locative' => 'სურამში', 'context' => 'კერძო სახლებისა და მცირე ბიზნესისთვის ვაწყობთ პრაქტიკულ და გაფართოებაზე გათვლილ გადაწყვეტებს.'],
        ];

        $services = [
            'network-cable-installation' => [
                'label' => 'ქსელის კაბელის გაყვანა',
                'short' => 'CAT6 ქსელის გაყვანა',
                'description' => 'CAT5e/CAT6 ქსელის დაგეგმვა, კაბელის პროფესიონალური გაყვანა, RJ45 წერტილები, მარკირება და ხაზების ტესტირება.',
                'title' => fn (string $locative) => "ქსელის კაბელის გაყვანა {$locative} — CAT6, RJ45 და ტესტირება",
                'benefits' => [
                    ['title' => 'სწორი მარშრუტი', 'description' => 'მარშრუტი იგეგმება ელექტრო ხაზების, სიგრძეებისა და სამომავლო გაფართოების გათვალისწინებით.'],
                    ['title' => 'მარკირება და ტესტირება', 'description' => 'ხაზები მარკირდება ორივე ბოლოში და მონტაჟის შემდეგ მოწმდება ტესტერით.'],
                    ['title' => 'LAN ინფრასტრუქტურა', 'description' => 'ქსელი ეწყობა კამერებისთვის, კომპიუტერებისთვის, POS-სა და Wi‑Fi Access Point-ებისთვის.'],
                ],
                'faqs' => [
                    ['question' => 'CAT6 კაბელის გაყვანის ფასი როგორ ითვლება?', 'answer' => 'ფასი დამოკიდებულია მეტრაჟზე, მარშრუტზე, გოფრა/არხზე, RJ45 წერტილების რაოდენობასა და სამუშაო გარემოზე.'],
                    ['question' => 'დამონტაჟებული ხაზები იტესტება?', 'answer' => 'დიახ. ხაზები მოწმდება კონტაქტებზე და გამტარობაზე, საჭიროებისას კი კეთდება დამატებითი ქსელური დიაგნოსტიკა.'],
                    ['question' => 'რემონტამდე ქსელის დაგეგმვაც შეგიძლიათ?', 'answer' => 'დიახ. შესაძლებელია მარშრუტებისა და ქსელის წერტილების წინასწარ დაგეგმვა კედლებისა და ჭერის საბოლოო დახურვამდე.'],
                ],
            ],
            'router-wifi-configuration' => [
                'label' => 'Wi‑Fi და MikroTik-ის გამართვა',
                'short' => 'Wi‑Fi / MikroTik',
                'description' => 'როუტერის, Wi‑Fi დაფარვის, MikroTik-ის, VLAN, VPN, DHCP, Firewall და უსაფრთხო ინტერნეტის პროფესიონალური კონფიგურაცია.',
                'title' => fn (string $locative) => "Wi‑Fi და MikroTik-ის გამართვა {$locative}",
                'benefits' => [
                    ['title' => 'სტაბილური Wi‑Fi', 'description' => 'Access Point/Mesh განლაგება იგეგმება ფართობის, კედლებისა და სართულების მიხედვით.'],
                    ['title' => 'უსაფრთხო კონფიგურაცია', 'description' => 'ვამართავთ ადმინისტრატორის წვდომას, Wi‑Fi დაცვას, DHCP-სა და Firewall წესებს.'],
                    ['title' => 'MikroTik და ბიზნეს ქსელი', 'description' => 'საჭიროებისას ვაწყობთ VLAN, VPN, Port Forwarding და მრავალქსელიან კონფიგურაციას.'],
                ],
                'faqs' => [
                    ['question' => 'რატომ არის Wi‑Fi სუსტი ზოგ ოთახში?', 'answer' => 'მიზეზი შეიძლება იყოს კედლები, სართულები, არასწორი მდებარეობა ან რადიოჩარევა; საჭიროებისას ემატება Access Point ან Mesh.'],
                    ['question' => 'MikroTik-ის სრულ კონფიგურაციას აკეთებთ?', 'answer' => 'დიახ. ვამართავთ WAN, DHCP, NAT, VLAN, VPN, Firewall, Port Forwarding და სხვა საჭირო ფუნქციებს.'],
                    ['question' => 'პროვაიდერის შეცვლის შემდეგ ქსელს თავიდან გამართავთ?', 'answer' => 'დიახ. ვასწორებთ WAN პარამეტრებს და ვამოწმებთ ლოკალურ ქსელს, სერვერებს, კომპიუტერებს, POS-სა და სხვა მოწყობილობებს.'],
                ],
            ],
            'business-it-support' => [
                'label' => 'IT მხარდაჭერა ბიზნესისთვის',
                'short' => 'IT Support',
                'description' => 'ოფისების, მაღაზიების, სასტუმროებისა და სხვა ბიზნესების კომპიუტერების, პრინტერების, ქსელის, POS-ისა და Windows სისტემების დისტანციური და ადგილზე მხარდაჭერა.',
                'title' => fn (string $locative) => "IT მხარდაჭერა {$locative} — ბიზნესისთვის ადგილზე და დისტანციურად",
                'benefits' => [
                    ['title' => 'ერთი IT პარტნიორი', 'description' => 'კომპიუტერი, პრინტერი, ქსელი, Wi‑Fi და სხვა ყოველდღიური IT საკითხები ერთ სივრცეში გვარდება.'],
                    ['title' => 'დისტანციური და ადგილზე დახმარება', 'description' => 'პრობლემის ტიპის მიხედვით მხარდაჭერა შესაძლებელია დისტანციურად ან ტექნიკოსის ვიზიტით.'],
                    ['title' => 'ერთჯერადი ან აბონენტური მომსახურება', 'description' => 'შესაძლებელია კონკრეტული პრობლემის მოგვარება ან პერიოდული IT მხარდაჭერის შეთანხმება.'],
                ],
                'faqs' => [
                    ['question' => 'IT მხარდაჭერა მხოლოდ კომპანიებისთვისაა?', 'answer' => 'ძირითადი სერვისი ბიზნესზეა გათვლილი, თუმცა კონკრეტული ტექნიკური სამუშაოები სხვა ტიპის ობიექტებზეც შესაძლებელია შეთანხმებით.'],
                    ['question' => 'აბონენტური IT მომსახურება შესაძლებელია?', 'answer' => 'დიახ. გეგმა ფორმირდება მოწყობილობების, მომხმარებლებისა და საჭირო რეაგირების დროის მიხედვით.'],
                    ['question' => 'დისტანციურად რა პრობლემებს აგვარებთ?', 'answer' => 'Windows, პროგრამები, მომხმარებლის პარამეტრები, პრინტერების ნაწილი და ქსელური კონფიგურაცია ხშირად დისტანციურად გვარდება.'],
                ],
            ],
            'barrier-gate-installation' => [
                'label' => 'შლაგბაუმის მონტაჟი და LPR',
                'short' => 'შლაგბაუმი / LPR',
                'description' => 'ავტომატური შლაგბაუმის მონტაჟი, Photocell, Loop Detector, პულტი, GSM, ნომრის ამოცნობა (LPR) და დაშვების სისტემასთან ინტეგრაცია.',
                'title' => fn (string $locative) => "შლაგბაუმის მონტაჟი {$locative} — LPR, GSM და უსაფრთხოების სენსორები",
                'benefits' => [
                    ['title' => 'უსაფრთხო ავტომატიკა', 'description' => 'Photocell და/ან ინდუქციური მარყუჟი გამოიყენება დაბრკოლებისა და ავტომობილის დასაფიქსირებლად.'],
                    ['title' => 'LPR ნომრის ამოცნობა', 'description' => 'თავსებადი კამერით შესაძლებელია დაშვებული ნომრების მიხედვით ავტომატური გახსნა.'],
                    ['title' => 'პულტი, GSM ან Access Control', 'description' => 'მართვის მეთოდი შეირჩევა ობიექტის გამოყენების სცენარის მიხედვით.'],
                ],
                'faqs' => [
                    ['question' => 'შლაგბაუმი ნომრის ამოცნობით ავტომატურად გაიღება?', 'answer' => 'დიახ. თავსებადი LPR კამერა whitelist-ში დამატებული ნომრის ამოცნობისას შლაგბაუმის ავტომატურად გახსნას უზრუნველყოფს.'],
                    ['question' => 'Loop Detector და Photocell ორივე საჭიროა?', 'answer' => 'დამოკიდებულია ობიექტზე. Loop Detector მანქანას მარყუჟით ადგენს, Photocell კი დაბრკოლებას სხივის ზონაში აკონტროლებს.'],
                    ['question' => 'ტელეფონით გახსნა შეიძლება?', 'answer' => 'დიახ. შესაძლებელია GSM კონტროლერი, აპლიკაცია ან დაშვების სისტემასთან ინტეგრაცია.'],
                ],
            ],
            'intercom-access-control-installation' => [
                'label' => 'დომოფონი და დაშვების კონტროლი',
                'short' => 'Access Control',
                'description' => 'ვიდეოდომოფონის, ელექტრო საკეტის, Exit ღილაკის, ბარათის, PIN-ის, ბიომეტრიის, კარის კონტაქტისა და სარეზერვო კვების მონტაჟი და გამართვა.',
                'title' => fn (string $locative) => "დომოფონის და დაშვების კონტროლის მონტაჟი {$locative}",
                'benefits' => [
                    ['title' => 'კარის უსაფრთხო მართვა', 'description' => 'საკეტი და კონტროლერი შეირჩევა კარის ტიპისა და უსაფრთხოების მოთხოვნის მიხედვით.'],
                    ['title' => 'ბარათი, PIN და ბიომეტრია', 'description' => 'დაშვების რამდენიმე მეთოდი შესაძლებელია გაერთიანდეს ერთ სისტემაში.'],
                    ['title' => 'დომოფონი და მობილური აპი', 'description' => 'თავსებადი IP სისტემებით შესაძლებელია ზარის მიღება და კარის გახსნა მობილურიდან.'],
                ],
                'faqs' => [
                    ['question' => 'Fail-safe და fail-secure საკეტს შორის რა განსხვავებაა?', 'answer' => 'Fail-safe კვების დაკარგვისას იხსნება, fail-secure კი ჩაკეტილ მდგომარეობას ინარჩუნებს. ვარიანტი უსაფრთხოების მოთხოვნის მიხედვით შეირჩევა.'],
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

            foreach ($cities as $sort => $city) {
                $locationSlug = $sort;
                $cityName = $city['name'];
                $locative = $city['locative'];
                $title = $serviceData['title']($locative);
                $primaryKeyword = $serviceData['short'].' '.$locative;
                $excerpt = $serviceData['description'].' SafeTech მუშაობს '.$locative.' და საქართველოს რეგიონებში წინასწარი შეთანხმებით.';
                $content = $excerpt."\n\n".$city['context']."\n\nსამუშაო იწყება მოთხოვნების დაზუსტებითა და ობიექტის შეფასებით. შემდეგ განისაზღვრება მოწყობილობები, სამუშაოს მოცულობა, მონტაჟის ეტაპები და საბოლოო ღირებულება.";
                $keywords = [$primaryKeyword, $serviceData['label'].' '.$cityName, 'SafeTech '.$cityName];

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
                        'seo_title' => $title.' | SafeTech',
                        'seo_description' => $serviceData['description'].' მომსახურება '.$locative.'. კონსულტაცია, დაგეგმვა, მონტაჟი და გამართვა — SafeTech.',
                        'is_published' => true,
                        'noindex' => false,
                        'published_at' => now(),
                        'sort_order' => array_search($locationSlug, array_keys($cities), true) + 1,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ],
                );

                $landingId = DB::table('local_service_landings')->where('service_id', $serviceId)->where('location_slug', $locationSlug)->value('id');
                if (! $landingId) {
                    continue;
                }

                $projectIds = DB::table('projects')->where('is_published', true)
                    ->where(fn ($query) => $query->where('title', 'like', "%{$cityName}%")->orWhere('description', 'like', "%{$cityName}%"))
                    ->orderByDesc('published_at')->limit(3)->pluck('id');

                foreach ($projectIds as $projectId) {
                    DB::table('local_service_landing_project')->updateOrInsert(
                        ['landing_id' => $landingId, 'project_id' => $projectId],
                        ['updated_at' => now(), 'created_at' => now()],
                    );
                }
            }
        }
    }

    public function down(): void
    {
        // SEO content is intentionally preserved on rollback.
    }
};
