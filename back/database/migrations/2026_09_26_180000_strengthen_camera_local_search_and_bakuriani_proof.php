<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const CAMERA_SERVICE_SLUG = 'security-camera-installation';

    private const BAKURIANI_PROJECT_SLUG = '6-kameriani-tvt-full-color-videosametvalyureo-sistema';

    public function up(): void
    {
        $this->strengthenCameraService();
        $this->strengthenLocalCameraSearchIntent();
        $this->correctBakurianiProjectProof();
    }

    public function down(): void
    {
        // Content correction: preserve verified facts and later CMS edits.
    }

    private function strengthenCameraService(): void
    {
        if (! Schema::hasTable('services')) {
            return;
        }

        $service = DB::table('services')->where('slug', self::CAMERA_SERVICE_SLUG)->first();
        if (! $service) {
            return;
        }

        $copy = [
            'ka' => [
                'name' => 'უსაფრთხოების კამერების დაყენება და მონტაჟი',
                'title' => 'უსაფრთხოების კამერების დაყენება და მონტაჟი — IP, PoE, NVR/DVR და მობილური წვდომა',
                'seoTitle' => 'კამერების დაყენება და მონტაჟი | CCTV | SafeTech',
                'seoDescription' => 'უსაფრთხოების კამერების დაყენება და პროფესიონალური მონტაჟი: IP/PoE კამერები, NVR/DVR, 24/7 ჩანაწერი, Full Color, UPS და ტელეფონიდან ნახვა.',
                'keywords' => ['კამერების დაყენება', 'კამერების მონტაჟი', 'უსაფრთხოების კამერები', 'ვიდეოსამეთვალყურეობა', 'CCTV'],
            ],
            'en' => [
                'name' => 'Security Camera Installation and Setup',
                'title' => 'Security Camera Installation and Setup — IP, PoE, NVR/DVR and Mobile Access',
                'seoTitle' => 'Security Camera Installation and Setup | SafeTech',
                'seoDescription' => 'Professional security camera installation and setup: IP/PoE cameras, NVR/DVR, 24/7 recording, Full Color options, UPS and mobile viewing.',
                'keywords' => ['security camera installation', 'CCTV installation', 'IP camera setup', 'PoE cameras', 'video surveillance'],
            ],
            'ru' => [
                'name' => 'Установка и монтаж камер видеонаблюдения',
                'title' => 'Установка и монтаж камер видеонаблюдения — IP, PoE, NVR/DVR и доступ с телефона',
                'seoTitle' => 'Установка и монтаж камер видеонаблюдения | SafeTech',
                'seoDescription' => 'Профессиональная установка и монтаж камер: IP/PoE, NVR/DVR, запись 24/7, Full Color, ИБП и просмотр с телефона.',
                'keywords' => ['установка камер', 'монтаж камер', 'камеры видеонаблюдения', 'монтаж CCTV', 'IP-камеры'],
            ],
        ];

        $translations = $this->decode($service->translations ?? null);
        $translations['fields'] ??= [];
        foreach (['name', 'title', 'seoTitle', 'seoDescription'] as $field) {
            $translations['fields'][$field] = $this->localizedField($copy, $field);
        }
        $translations['keywords'] = $this->localizedField($copy, 'keywords');

        $seo = $this->decode($service->seo ?? null);
        $seo['title'] = $copy['ka']['seoTitle'];
        $seo['description'] = $copy['ka']['seoDescription'];
        $seo['keywords'] = $this->mergeKeywords($seo['keywords'] ?? [], $copy['ka']['keywords']);
        $seo['noindex'] = false;
        $seo['schema_type'] = 'Service';

        DB::table('services')->where('id', $service->id)->update([
            'name' => $copy['ka']['name'],
            'title' => $copy['ka']['title'],
            'seo_description' => $copy['ka']['seoDescription'],
            'keywords' => $this->json($this->mergeKeywords($service->keywords ?? [], $copy['ka']['keywords'])),
            'seo' => $this->json($seo),
            'translations' => $this->json($translations),
            'updated_at' => now(),
        ]);
    }

    private function strengthenLocalCameraSearchIntent(): void
    {
        if (! Schema::hasTable('services') || ! Schema::hasTable('local_service_landings')) {
            return;
        }

        $serviceId = DB::table('services')->where('slug', self::CAMERA_SERVICE_SLUG)->value('id');
        if (! $serviceId) {
            return;
        }

        $cities = [
            'tbilisi' => ['ka' => 'თბილისში', 'en' => 'Tbilisi', 'ru' => 'Тбилиси'],
            'bakuriani' => ['ka' => 'ბაკურიანში', 'en' => 'Bakuriani', 'ru' => 'Бакуриани'],
            'surami' => ['ka' => 'სურამში', 'en' => 'Surami', 'ru' => 'Сурами'],
            'borjomi' => ['ka' => 'ბორჯომში', 'en' => 'Borjomi', 'ru' => 'Боржоми'],
            'khashuri' => ['ka' => 'ხაშურში', 'en' => 'Khashuri', 'ru' => 'Хашури'],
            'abastumani' => ['ka' => 'აბასთუმანში', 'en' => 'Abastumani', 'ru' => 'Абастумани'],
        ];

        foreach ($cities as $slug => $city) {
            $landing = DB::table('local_service_landings')
                ->where('service_id', $serviceId)
                ->where('location_slug', $slug)
                ->first();

            if (! $landing) {
                continue;
            }

            $copy = $this->localCameraCopy($city);
            $translations = $this->decode($landing->translations ?? null);
            $translations['fields'] ??= [];
            foreach (['title', 'primaryKeyword', 'seoTitle', 'seoDescription', 'ogTitle', 'ogDescription'] as $field) {
                $translations['fields'][$field] = $this->localizedField($copy, $field);
            }

            foreach (['ka', 'en', 'ru'] as $locale) {
                $existing = data_get($translations, "keywords.{$locale}", []);
                data_set(
                    $translations,
                    "keywords.{$locale}",
                    $this->mergeKeywords($existing, $copy[$locale]['keywords']),
                );
            }

            DB::table('local_service_landings')->where('id', $landing->id)->update([
                'title' => $copy['ka']['title'],
                'primary_keyword' => $copy['ka']['primaryKeyword'],
                'keywords' => $this->json($this->mergeKeywords($landing->keywords ?? [], $copy['ka']['keywords'])),
                'seo_title' => $copy['ka']['seoTitle'],
                'seo_description' => $copy['ka']['seoDescription'],
                'translations' => $this->json($translations),
                'updated_at' => now(),
            ]);
        }
    }

    private function correctBakurianiProjectProof(): void
    {
        if (! Schema::hasTable('projects')) {
            return;
        }

        $project = DB::table('projects')->where('slug', self::BAKURIANI_PROJECT_SLUG)->first();
        if (! $project) {
            return;
        }

        $copy = [
            'ka' => [
                'name' => '6 კამერის მონტაჟი ბაკურიანის კოტეჯში',
                'title' => 'კოტეჯის კამერების მონტაჟი ბაკურიანში — 6× TVT 4MP Full Color',
                'description' => 'ბაკურიანის კოტეჯში დამონტაჟდა 6 ცალი TVT 4MP Full Color გარე კამერა, 8-პორტიანი PoE NVR, UPS და მონიტორი. სისტემა უზრუნველყოფს 24/7 ლოკალურ ჩანაწერს, ფერად გამოსახულებას დაბალი განათებისას და მობილურიდან დისტანციურ ნახვას.',
                'city' => 'ბაკურიანი',
                'objectType' => 'კოტეჯი',
                'seoTitle' => 'კოტეჯის კამერების მონტაჟი ბაკურიანში | SafeTech',
                'seoDescription' => 'ბაკურიანის კოტეჯში შესრულებული CCTV პროექტი: 6× TVT 4MP Full Color კამერა, 8-პორტიანი PoE NVR, UPS, მონიტორი, 24/7 ჩანაწერი და მობილური წვდომა.',
                'imageAlt' => 'ბაკურიანის კოტეჯის 6 ცალი TVT 4MP Full Color კამერის სისტემა',
            ],
            'en' => [
                'name' => 'Six-Camera CCTV Installation at a Bakuriani Cottage',
                'title' => 'Bakuriani Cottage CCTV Installation — 6× TVT 4MP Full Color',
                'description' => 'A Bakuriani cottage was equipped with six outdoor TVT 4MP Full Color cameras, an 8-port PoE NVR, UPS and monitor. The system provides 24/7 local recording, full-color low-light footage and remote mobile viewing.',
                'city' => 'Bakuriani',
                'objectType' => 'Cottage',
                'seoTitle' => 'Cottage CCTV Installation in Bakuriani | SafeTech',
                'seoDescription' => 'Completed CCTV project at a Bakuriani cottage: 6× TVT 4MP Full Color cameras, 8-port PoE NVR, UPS, monitor, 24/7 recording and mobile access.',
                'imageAlt' => 'Six TVT 4MP Full Color cameras installed at a Bakuriani cottage',
            ],
            'ru' => [
                'name' => 'Монтаж 6 камер в коттедже в Бакуриани',
                'title' => 'Видеонаблюдение для коттеджа в Бакуриани — 6× TVT 4MP Full Color',
                'description' => 'В коттедже в Бакуриани установлены шесть уличных камер TVT 4MP Full Color, 8-портовый PoE NVR, ИБП и монитор. Система обеспечивает локальную запись 24/7, цветное изображение при слабом освещении и удалённый просмотр со смартфона.',
                'city' => 'Бакуриани',
                'objectType' => 'Коттедж',
                'seoTitle' => 'Монтаж камер в коттедже в Бакуриани | SafeTech',
                'seoDescription' => 'Проект CCTV в коттедже в Бакуриани: 6× TVT 4MP Full Color, 8-портовый PoE NVR, ИБП, монитор, запись 24/7 и доступ со смартфона.',
                'imageAlt' => 'Шесть камер TVT 4MP Full Color в коттедже в Бакуриани',
            ],
        ];

        $translations = $this->decode($project->translations ?? null);
        $translations['fields'] ??= [];
        foreach (['name', 'title', 'description', 'city', 'objectType', 'seoTitle', 'seoDescription', 'imageAlt'] as $field) {
            $translations['fields'][$field] = $this->localizedField($copy, $field);
        }
        $translations['fields']['ogTitle'] = $this->localizedField($copy, 'seoTitle');
        $translations['fields']['ogDescription'] = $this->localizedField($copy, 'seoDescription');

        $seo = $this->decode($project->seo ?? null);
        $seo['title'] = $copy['ka']['seoTitle'];
        $seo['description'] = $copy['ka']['seoDescription'];
        $seo['keywords'] = [
            'კამერების მონტაჟი ბაკურიანში',
            'კამერების დაყენება ბაკურიანში',
            'კოტეჯის უსაფრთხოება',
            'TVT 4MP Full Color',
            'PoE NVR',
        ];
        $seo['noindex'] = false;
        $seo['schema_type'] = 'Article';

        DB::table('projects')->where('id', $project->id)->update([
            'name' => $copy['ka']['name'],
            'title' => $copy['ka']['title'],
            'description' => $copy['ka']['description'],
            'city' => $copy['ka']['city'],
            'object_type' => $copy['ka']['objectType'],
            'equipment' => $this->json([
                ['name' => 'TVT 4MP Full Color გარე კამერა', 'quantity' => '6'],
                ['name' => '8-პორტიანი PoE NVR', 'quantity' => '1'],
                ['name' => 'UPS', 'quantity' => '1'],
                ['name' => 'მონიტორი', 'quantity' => '1'],
            ]),
            'seo_description' => $copy['ka']['seoDescription'],
            'image_alt' => $copy['ka']['imageAlt'],
            'seo' => $this->json($seo),
            'translations' => $this->json($translations),
            'updated_at' => now(),
        ]);

        $this->attachProjectToBakuriani($project->id);
    }

    private function attachProjectToBakuriani(int $projectId): void
    {
        if (! Schema::hasTable('services')
            || ! Schema::hasTable('local_service_landings')
            || ! Schema::hasTable('local_service_landing_project')) {
            return;
        }

        $wrongLandingIds = DB::table('local_service_landings')
            ->where('location_slug', '<>', 'bakuriani')
            ->pluck('id');

        if ($wrongLandingIds->isNotEmpty()) {
            DB::table('local_service_landing_project')
                ->where('project_id', $projectId)
                ->whereIn('landing_id', $wrongLandingIds)
                ->delete();
        }

        $landingId = DB::table('local_service_landings')
            ->join('services', 'services.id', '=', 'local_service_landings.service_id')
            ->where('services.slug', self::CAMERA_SERVICE_SLUG)
            ->where('local_service_landings.location_slug', 'bakuriani')
            ->value('local_service_landings.id');

        if (! $landingId) {
            return;
        }

        $exists = DB::table('local_service_landing_project')
            ->where('landing_id', $landingId)
            ->where('project_id', $projectId)
            ->exists();

        if (! $exists) {
            DB::table('local_service_landing_project')->insert([
                'landing_id' => $landingId,
                'project_id' => $projectId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /** @return array<string, array<string, mixed>> */
    private function localCameraCopy(array $city): array
    {
        return [
            'ka' => [
                'title' => "უსაფრთხოების კამერების დაყენება და მონტაჟი {$city['ka']}",
                'primaryKeyword' => "კამერების დაყენება {$city['ka']}",
                'seoTitle' => "კამერების დაყენება და მონტაჟი {$city['ka']} | SafeTech",
                'seoDescription' => "კამერების დაყენება და პროფესიონალური მონტაჟი {$city['ka']}: IP/PoE, NVR/DVR, Full Color, 24/7 ჩანაწერი, UPS და ტელეფონიდან ნახვა.",
                'ogTitle' => "კამერების დაყენება და მონტაჟი {$city['ka']} | SafeTech",
                'ogDescription' => "კამერების დაყენება და პროფესიონალური მონტაჟი {$city['ka']}: IP/PoE, NVR/DVR, Full Color, 24/7 ჩანაწერი, UPS და ტელეფონიდან ნახვა.",
                'keywords' => ["კამერების დაყენება {$city['ka']}", "კამერების მონტაჟი {$city['ka']}", "უსაფრთხოების კამერები {$city['ka']}", "CCTV {$city['ka']}"],
            ],
            'en' => [
                'title' => "Security Camera Installation and Setup in {$city['en']}",
                'primaryKeyword' => "security camera installation {$city['en']}",
                'seoTitle' => "Security Camera Installation in {$city['en']} | SafeTech",
                'seoDescription' => "Security camera installation in {$city['en']}: IP/PoE, NVR/DVR, Full Color options, 24/7 local recording, UPS and mobile viewing.",
                'ogTitle' => "Security Camera Installation in {$city['en']} | SafeTech",
                'ogDescription' => "Security camera installation in {$city['en']}: IP/PoE, NVR/DVR, Full Color options, 24/7 local recording, UPS and mobile viewing.",
                'keywords' => ["security camera installation {$city['en']}", "CCTV installation {$city['en']}", "camera setup {$city['en']}"],
            ],
            'ru' => [
                'title' => "Установка и монтаж камер видеонаблюдения в {$city['ru']}",
                'primaryKeyword' => "установка камер в {$city['ru']}",
                'seoTitle' => "Установка и монтаж камер в {$city['ru']} | SafeTech",
                'seoDescription' => "Установка и монтаж камер в {$city['ru']}: IP/PoE, NVR/DVR, Full Color, локальная запись 24/7, ИБП и просмотр с телефона.",
                'ogTitle' => "Установка и монтаж камер в {$city['ru']} | SafeTech",
                'ogDescription' => "Установка и монтаж камер в {$city['ru']}: IP/PoE, NVR/DVR, Full Color, локальная запись 24/7, ИБП и просмотр с телефона.",
                'keywords' => ["установка камер {$city['ru']}", "монтаж камер {$city['ru']}", "видеонаблюдение {$city['ru']}"],
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function decode(mixed $value): array
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        return is_array($value) ? $value : [];
    }

    /** @return array<int, string> */
    private function mergeKeywords(mixed $existing, array $required): array
    {
        $existing = $this->decodeList($existing);

        return collect([...$required, ...$existing])
            ->filter(fn (mixed $keyword): bool => is_string($keyword) && trim($keyword) !== '')
            ->map(fn (string $keyword): string => trim($keyword))
            ->unique(fn (string $keyword): string => mb_strtolower($keyword))
            ->values()
            ->all();
    }

    /** @return array<int, mixed> */
    private function decodeList(mixed $value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : [];
        }

        return is_array($value) ? array_values($value) : [];
    }

    /** @return array<string, mixed> */
    private function localizedField(array $copy, string $field): array
    {
        return [
            'ka' => $copy['ka'][$field],
            'en' => $copy['en'][$field],
            'ru' => $copy['ru'][$field],
        ];
    }

    private function json(array $value): string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
};
