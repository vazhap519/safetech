<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('services') || ! Schema::hasTable('local_service_landings')) {
            return;
        }

        $serviceId = DB::table('services')
            ->where('slug', 'security-camera-installation')
            ->value('id');

        if (! $serviceId) {
            return;
        }

        foreach ($this->locations() as $index => $location) {
            $now = now();
            $existing = DB::table('local_service_landings')
                ->where('service_id', $serviceId)
                ->where('location_slug', $location['slug'])
                ->first();

            $payload = [
                'location_name' => $location['name'],
                'title' => $location['title'],
                'eyebrow' => $location['eyebrow'],
                'excerpt' => $location['excerpt'],
                'content' => $location['content'],
                'cta_title' => $location['cta_title'],
                'cta_text' => $location['cta_text'],
                'benefits' => json_encode($location['benefits'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'faq' => json_encode($location['faq'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'primary_keyword' => $location['primary_keyword'],
                'keywords' => json_encode($location['keywords'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'seo_title' => $location['seo_title'],
                'seo_description' => $location['seo_description'],
                'translations' => json_encode($location['translations'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'is_published' => true,
                'noindex' => false,
                'published_at' => $existing?->published_at ?: $now,
                'sort_order' => 10 + $index,
                'updated_at' => $now,
            ];

            if ($existing) {
                DB::table('local_service_landings')->where('id', $existing->id)->update($payload);
                $landingId = $existing->id;
            } else {
                $landingId = DB::table('local_service_landings')->insertGetId([
                    'service_id' => $serviceId,
                    'location_slug' => $location['slug'],
                    'created_at' => $now,
                    ...$payload,
                ]);
            }

            $this->attachMatchingProjects($landingId, $location['project_terms']);
        }
    }

    public function down(): void
    {
        // Content migration: keep published landing pages and administrator edits intact.
    }

    private function attachMatchingProjects(int $landingId, array $terms): void
    {
        if (! Schema::hasTable('projects') || ! Schema::hasTable('local_service_landing_project')) {
            return;
        }

        $query = DB::table('projects')->where('is_published', true);
        $query->where(function ($projectQuery) use ($terms): void {
            foreach ($terms as $term) {
                $needle = '%'.mb_strtolower($term).'%';
                $projectQuery->orWhereRaw('LOWER(title) LIKE ?', [$needle]);
                if (Schema::hasColumn('projects', 'description')) {
                    $projectQuery->orWhereRaw('LOWER(description) LIKE ?', [$needle]);
                }
            }
        });

        foreach ($query->orderByDesc('published_at')->limit(3)->pluck('id') as $projectId) {
            DB::table('local_service_landing_project')->updateOrInsert(
                ['landing_id' => $landingId, 'project_id' => $projectId],
                ['created_at' => now(), 'updated_at' => now()],
            );
        }
    }

    private function locations(): array
    {
        return [
            $this->location(
                'tbilisi', 'თბილისი',
                'უსაფრთხოების კამერების მონტაჟი თბილისში',
                'CCTV და ვიდეოსამეთვალყურეობა თბილისში',
                'უსაფრთხოების კამერების პროფესიონალური მონტაჟი თბილისში — ბინისთვის, კერძო სახლისთვის, ოფისისთვის, მაღაზიისთვის, საწყობისა და სხვა ბიზნეს ობიექტებისთვის.',
                "თბილისში კამერების სისტემა სწორად უნდა დაიგეგმოს ობიექტის ზომის, შესასვლელების, განათების, ქსელისა და ჩანაწერის საჭირო პერიოდის მიხედვით. SafeTech გთავაზობთ ობიექტის შეფასებას, კამერების ხედვის წერტილების დაგეგმვას, CAT6/PoE კაბელირებას, IP ან ანალოგური კამერების მონტაჟს, NVR/DVR-ის გამართვას და ტელეფონიდან უსაფრთხო დისტანციურ წვდომას.\n\nვმუშაობთ საცხოვრებელ და ბიზნეს ობიექტებზე. სისტემის შერჩევისას ვითვალისწინებთ ღამის ხედვას, Full Color ტექნოლოგიას, ადამიანის/მოძრაობის დეტექციას, HDD-ის საჭირო მოცულობას, UPS-ს და ინტერნეტის გათიშვის შემთხვევაშიც ლოკალური ჩანაწერის გაგრძელებას.\n\nმონტაჟის შემდეგ ვამოწმებთ თითოეულ კამერას, ჩანაწერს, ქსელსა და მობილურ აპლიკაციას და მომხმარებელს ვუხსნით სისტემის ძირითად მართვას.",
                'მიიღეთ კამერების მონტაჟის შეთავაზება თბილისში',
                'გამოგვიგზავნეთ ობიექტის ტიპი, მისამართი და კამერების სავარაუდო რაოდენობა — დაგეხმარებით სწორ კონფიგურაციასა და ღირებულების განსაზღვრაში.',
                'კამერების მონტაჟი თბილისში',
                ['კამერების მონტაჟი თბილისში', 'ვიდეოკამერების მონტაჟი თბილისი', 'უსაფრთხოების კამერები თბილისი', 'CCTV თბილისი', 'IP კამერები თბილისი'],
                'კამერების მონტაჟი თბილისში | ვიდეოსამეთვალყურეობა | SafeTech',
                'უსაფრთხოების კამერების მონტაჟი თბილისში: IP/PoE კამერები, NVR/DVR, Full Color, 24/7 ჩანაწერი, მობილურიდან ნახვა, HDD და UPS. მიიღეთ შეთავაზება SafeTech-ისგან.',
                ['თბილისი', 'Tbilisi']
            ),
            $this->location(
                'khashuri', 'ხაშური',
                'უსაფრთხოების კამერების მონტაჟი ხაშურში',
                'SafeTech — კამერების მონტაჟი ხაშურსა და მიმდებარე ტერიტორიაზე',
                'კამერების მონტაჟი ხაშურში სახლისთვის, მაღაზიისთვის, ოფისისთვის, საწყობისთვის, ფერმისა და სხვა ობიექტებისთვის — სრული მონტაჟით და მობილურიდან კონტროლით.',
                "ხაშურში და მიმდებარე დასახლებებში ვამონტაჟებთ ვიდეოსამეთვალყურეო სისტემებს როგორც ახალი ობიექტებისთვის, ისე არსებული სისტემის განახლებისას. ვგეგმავთ კამერების მდებარეობას, კაბელის მარშრუტს, NVR/DVR-ს, HDD-ის მოცულობას და საჭიროების შემთხვევაში სარეზერვო კვებას.\n\nშესაძლებელია Full Color ღამის ხედვა, PoE კამერები, მოძრაობისა და ადამიანის დეტექცია და მსოფლიოს ნებისმიერი წერტილიდან მობილურით Live View/Playback. ინტერნეტის გათიშვისას სწორად აწყობილი NVR სისტემა ლოკალურად აგრძელებს ჩანაწერს.\n\nადგილობრივი მომსახურება საშუალებას გვაძლევს სწრაფად შევაფასოთ ობიექტი და შევარჩიოთ რეალური საჭიროების შესაბამისი სისტემა ზედმეტი მოწყობილობების გარეშე.",
                'გჭირდებათ კამერები ხაშურში?',
                'მოგვწერეთ რამდენი კამერა გჭირდებათ და რა ტიპის ობიექტია — შეგიდგენთ შესაბამის ტექნიკურ ვარიანტს.',
                'კამერების მონტაჟი ხაშურში',
                ['კამერების მონტაჟი ხაშურში', 'ვიდეოკამერები ხაშური', 'უსაფრთხოების კამერები ხაშური', 'CCTV ხაშური'],
                'კამერების მონტაჟი ხაშურში | SafeTech Georgia',
                'კამერების პროფესიონალური მონტაჟი ხაშურში: IP/PoE, Full Color, NVR, HDD, UPS, 24/7 ჩანაწერი და ტელეფონიდან ნახვა.',
                ['ხაშური', 'Khashuri']
            ),
            $this->location(
                'bakuriani', 'ბაკურიანი',
                'უსაფრთხოების კამერების მონტაჟი ბაკურიანში',
                'ვიდეოსამეთვალყურეობა კოტეჯებისთვის, სასტუმროებისა და კომერციული ობიექტებისთვის',
                'კამერების მონტაჟი ბაკურიანში კოტეჯებისთვის, აპარტამენტებისთვის, სასტუმროებისთვის და ბიზნეს ობიექტებისთვის — საიმედო გარე კამერებით და დისტანციური კონტროლით.',
                "ბაკურიანში კამერების სისტემის დაგეგმვისას განსაკუთრებით მნიშვნელოვანია გარე პირობები, დაბალი ტემპერატურა, ღამის ხედვა, ეზოსა და პარკინგის კონტროლი და ობიექტის დისტანციურად მართვის შესაძლებლობა.\n\nვაყენებთ IP/PoE და Full Color კამერებს, NVR-ს, შესაბამის HDD-სა და UPS-ს. სისტემა შეიძლება მუშაობდეს 24/7 ჩანაწერით, ხოლო მფლობელს ჰქონდეს Live View და ჩანაწერების ნახვა ტელეფონიდან მაშინაც, როდესაც თავად ბაკურიანში არ იმყოფება.\n\nკოტეჯებისა და სასტუმროებისთვის კამერების რაოდენობასა და ხედვის კუთხეებს ვარჩევთ ისე, რომ კონტროლდებოდეს შესასვლელი, ეზო, პარკინგი და სხვა მნიშვნელოვანი ზონები ზედმეტი ბრმა წერტილების გარეშე.",
                'მიიღეთ შეთავაზება ბაკურიანის ობიექტისთვის',
                'გამოგვიგზავნეთ კოტეჯის, სასტუმროს ან სხვა ობიექტის ფოტო/გეგმა და დაგიგეგმავთ კამერების ოპტიმალურ განლაგებას.',
                'კამერების მონტაჟი ბაკურიანში',
                ['კამერების მონტაჟი ბაკურიანში', 'კამერები კოტეჯისთვის ბაკურიანი', 'ვიდეოსამეთვალყურეობა ბაკურიანი', 'CCTV Bakuriani'],
                'კამერების მონტაჟი ბაკურიანში | კოტეჯი და სასტუმრო | SafeTech',
                'უსაფრთხოების კამერების მონტაჟი ბაკურიანში კოტეჯის, სასტუმროსა და ბიზნესისთვის: Full Color, PoE, NVR, UPS, 24/7 ჩანაწერი და დისტანციური ნახვა.',
                ['ბაკურიანი', 'Bakuriani']
            ),
            $this->location(
                'borjomi', 'ბორჯომი',
                'უსაფრთხოების კამერების მონტაჟი ბორჯომში',
                'კამერები სახლების, სასტუმროებისა და ბიზნეს ობიექტებისთვის',
                'ბორჯომში ვიდეოსამეთვალყურეობის სისტემის დაგეგმვა და მონტაჟი — კამერები, NVR/DVR, კაბელირება, ჩანაწერი და მობილურიდან დისტანციური კონტროლი.',
                "ბორჯომში ვამონტაჟებთ უსაფრთხოების კამერებს კერძო სახლებში, სასტუმროებში, საოჯახო სასტუმროებში, მაღაზიებსა და სხვა ბიზნეს ობიექტებზე. მონტაჟამდე ვაფასებთ სად არის საჭირო დეტალური იდენტიფიკაცია და სად — ფართო ხედვა.\n\nსისტემაში შეიძლება შევიდეს PoE ან ანალოგური კამერები, Full Color ღამის ხედვა, NVR/DVR, HDD, UPS და ინტერნეტით დისტანციური ნახვა. ჩანაწერის შენახვის პერიოდი წინასწარ ითვლება კამერების რაოდენობის, რეზოლუციისა და ბიტრეიტის მიხედვით.\n\nსაბოლოო ჩაბარებისას მოწმდება კამერების ხედვა დღის და დაბალი განათების პირობებში, ჩანაწერის არქივი და მომხმარებლის ტელეფონზე აპლიკაცია.",
                'კამერების სისტემა ბორჯომში — მიიღეთ კონსულტაცია',
                'მოგვწერეთ ობიექტის მდებარეობა და ტიპი. დაგეხმარებით კამერების რაოდენობისა და სისტემის კონფიგურაციის სწორად შერჩევაში.',
                'კამერების მონტაჟი ბორჯომში',
                ['კამერების მონტაჟი ბორჯომში', 'ვიდეოკამერები ბორჯომი', 'CCTV ბორჯომი', 'უსაფრთხოების კამერები ბორჯომი'],
                'კამერების მონტაჟი ბორჯომში | SafeTech Georgia',
                'კამერების მონტაჟი ბორჯომში სახლის, სასტუმროსა და ბიზნესისთვის — PoE, Full Color, NVR/DVR, HDD, UPS და მობილურიდან ნახვა.',
                ['ბორჯომი', 'Borjomi']
            ),
            $this->location(
                'surami', 'სურამი',
                'უსაფრთხოების კამერების მონტაჟი სურამში',
                'ვიდეოკამერები სახლისთვის, აგარაკისთვის და ბიზნესისთვის',
                'სურამში კამერების პროფესიონალური მონტაჟი სახლისთვის, აგარაკისთვის, მაღაზიისთვის, კვების ობიექტისთვის და სხვა ბიზნესისთვის — სრული გამართვით.',
                "სურამში ვგეგმავთ და ვამონტაჟებთ უსაფრთხოების კამერების სისტემებს საცხოვრებელი და კომერციული ობიექტებისთვის. სწორად შერჩეული კამერის ტიპი და მდებარეობა მნიშვნელოვანია ეზოს, შესასვლელის, პარკინგისა და სალაროს ზონის ეფექტურად გასაკონტროლებლად.\n\nშესაძლებელია IP/PoE კამერები, Full Color ღამის ხედვა, NVR, HDD, UPS, მოძრაობის ან ადამიანის დეტექცია და მობილურიდან დისტანციური ნახვა. აგარაკისა და სეზონური ობიექტებისთვის დისტანციური კონტროლი განსაკუთრებით პრაქტიკულია.\n\nმონტაჟისას ყურადღებას ვაქცევთ კაბელის დაცვას, კამერის სწორ სიმაღლესა და კუთხეს, ქსელის სტაბილურობას და ჩანაწერის საჭირო ხანგრძლივობას.",
                'მიიღეთ კამერების მონტაჟის შეთავაზება სურამში',
                'დაგვიკავშირდით და მოგვწერეთ ობიექტის ტიპი და სავარაუდო კამერების რაოდენობა — დაგეხმარებით ოპტიმალური სისტემის შერჩევაში.',
                'კამერების მონტაჟი სურამში',
                ['კამერების მონტაჟი სურამში', 'ვიდეოკამერები სურამი', 'კამერები აგარაკისთვის სურამი', 'CCTV სურამი'],
                'კამერების მონტაჟი სურამში | SafeTech Georgia',
                'უსაფრთხოების კამერების მონტაჟი სურამში: სახლი, აგარაკი და ბიზნესი — PoE, Full Color, NVR, HDD, UPS და ტელეფონიდან კონტროლი.',
                ['სურამი', 'Surami']
            ),
        ];
    }

    private function location(
        string $slug,
        string $name,
        string $title,
        string $eyebrow,
        string $excerpt,
        string $content,
        string $ctaTitle,
        string $ctaText,
        string $primaryKeyword,
        array $keywords,
        string $seoTitle,
        string $seoDescription,
        array $projectTerms,
    ): array {
        $commonBenefits = [
            ['title' => 'სწორი ხედვის წერტილები', 'description' => 'კამერების განლაგება ობიექტის რეალური რისკებისა და საჭირო ხედვის მიხედვით.'],
            ['title' => '24/7 ჩანაწერი', 'description' => 'NVR/DVR და HDD-ის გამოთვლა საჭირო არქივის ხანგრძლივობის მიხედვით.'],
            ['title' => 'მობილურიდან კონტროლი', 'description' => 'Live View და ჩანაწერების უსაფრთხო დისტანციური ნახვა თავსებადი აპლიკაციით.'],
        ];
        $faq = [
            ['question' => "რა ღირს კამერების მონტაჟი {$name}-ში?", 'answer' => 'საბოლოო ფასი დამოკიდებულია კამერების რაოდენობაზე, ტიპზე, NVR/DVR-ზე, HDD-ზე, კაბელის სიგრძეზე, UPS-ზე და მონტაჟის სირთულეზე. ზუსტი შეთავაზებისთვის საჭიროა ობიექტის მოთხოვნების შეფასება.'],
            ['question' => 'ტელეფონიდან კამერების ნახვა შეიძლება?', 'answer' => 'დიახ. თავსებადი სისტემისთვის ვამართავთ მობილურ აპლიკაციას, Live View-სა და ჩანაწერების დისტანციურ ნახვას.'],
            ['question' => 'ინტერნეტი თუ გაითიშა, კამერები ჩაიწერს?', 'answer' => 'თუ სისტემა NVR/DVR-ზე ლოკალურად იწერს, ინტერნეტის გათიშვა ჩანაწერს არ აჩერებს. დისტანციური ნახვა აღდგება ინტერნეტის დაბრუნების შემდეგ.'],
            ['question' => 'ღამით ფერადი გამოსახულება შესაძლებელია?', 'answer' => 'დიახ. შესაბამის პირობებში შესაძლებელია Full Color კამერების გამოყენება, რომლებიც დაბალი განათებისას ფერად და დეტალურ გამოსახულებას იძლევა.'],
        ];

        return [
            'slug' => $slug,
            'name' => $name,
            'title' => $title,
            'eyebrow' => $eyebrow,
            'excerpt' => $excerpt,
            'content' => $content,
            'cta_title' => $ctaTitle,
            'cta_text' => $ctaText,
            'benefits' => $commonBenefits,
            'faq' => $faq,
            'primary_keyword' => $primaryKeyword,
            'keywords' => $keywords,
            'seo_title' => $seoTitle,
            'seo_description' => $seoDescription,
            'project_terms' => $projectTerms,
            'translations' => [
                'fields' => [
                    'locationName' => ['ka' => $name, 'en' => $this->englishName($slug), 'ru' => $this->russianName($slug)],
                    'title' => ['ka' => $title, 'en' => "Security camera installation in {$this->englishName($slug)}", 'ru' => "Монтаж камер видеонаблюдения в {$this->russianName($slug)}"],
                    'eyebrow' => ['ka' => $eyebrow, 'en' => "CCTV installation in {$this->englishName($slug)}", 'ru' => "Видеонаблюдение — {$this->russianName($slug)}"],
                    'excerpt' => ['ka' => $excerpt, 'en' => "Professional CCTV installation, recording and mobile viewing for homes and businesses in {$this->englishName($slug)}.", 'ru' => "Профессиональный монтаж видеонаблюдения, запись и просмотр с телефона для домов и бизнеса — {$this->russianName($slug)}."],
                    'content' => ['ka' => $content],
                    'ctaTitle' => ['ka' => $ctaTitle, 'en' => "Get a CCTV quote in {$this->englishName($slug)}", 'ru' => "Получить расчет видеонаблюдения — {$this->russianName($slug)}"],
                    'ctaText' => ['ka' => $ctaText],
                    'primaryKeyword' => ['ka' => $primaryKeyword, 'en' => "CCTV installation {$this->englishName($slug)}", 'ru' => "монтаж камер {$this->russianName($slug)}"],
                    'seoTitle' => ['ka' => $seoTitle, 'en' => "CCTV Installation in {$this->englishName($slug)} | SafeTech", 'ru' => "Монтаж видеонаблюдения — {$this->russianName($slug)} | SafeTech"],
                    'seoDescription' => ['ka' => $seoDescription, 'en' => "Professional CCTV installation in {$this->englishName($slug)}: IP/PoE cameras, NVR/DVR, recording, UPS and mobile viewing.", 'ru' => "Монтаж видеонаблюдения — {$this->russianName($slug)}: IP/PoE камеры, NVR/DVR, архив, UPS и просмотр с телефона."],
                ],
                'keywords' => [
                    'ka' => $keywords,
                    'en' => ["CCTV installation {$this->englishName($slug)}", "security cameras {$this->englishName($slug)}"],
                    'ru' => ["монтаж камер {$this->russianName($slug)}", "видеонаблюдение {$this->russianName($slug)}"],
                ],
            ],
        ];
    }

    private function englishName(string $slug): string
    {
        return ['tbilisi' => 'Tbilisi', 'khashuri' => 'Khashuri', 'bakuriani' => 'Bakuriani', 'borjomi' => 'Borjomi', 'surami' => 'Surami'][$slug];
    }

    private function russianName(string $slug): string
    {
        return ['tbilisi' => 'Тбилиси', 'khashuri' => 'Хашури', 'bakuriani' => 'Бакуриани', 'borjomi' => 'Боржоми', 'surami' => 'Сурами'][$slug];
    }
};
