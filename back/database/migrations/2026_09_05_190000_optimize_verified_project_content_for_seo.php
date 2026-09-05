<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $projects = [
            [
                'match' => 'კერძო სახლის სუსტი დენების ინფრასტრუქტურა',
                'title' => 'კერძო სახლის სუსტი დენების ინფრასტრუქტურა — CAT6, PoE და IP CCTV',
                'description' => 'კერძო სახლისთვის მოწყობილი ცენტრალიზებული სუსტი დენების ინფრასტრუქტურა: CAT6 Ethernet ქსელი, RJ45 წერტილები, PoE კვება/მონაცემები და IP ვიდეომეთვალყურეობის მზადყოფნა ერთიან საკაბელო არქიტექტურაში.',
                'seo_description' => 'კერძო სახლის სუსტი დენების პროექტი — CAT6 Ethernet, RJ45, PoE და IP CCTV ინფრასტრუქტურა. იხილეთ SafeTech-ის შესრულებული ქსელური და უსაფრთხოების გადაწყვეტა.',
                'image_alt' => 'კერძო სახლის CAT6, PoE და IP CCTV სუსტი დენების ინფრასტრუქტურა',
                'keywords' => ['სუსტი დენების მონტაჟი', 'CAT6 ქსელი', 'PoE', 'IP CCTV', 'RJ45', 'კერძო სახლის ქსელი'],
            ],
            [
                'match' => '6 კამერიანი TVT Full Color ვიდეოსამეთვალყურეო სისტემა',
                'title' => '6 კამერიანი TVT 4MP Full Color ვიდეოსამეთვალყურეო სისტემა',
                'description' => '6 ცალი TVT 4MP Full Color კამერით მოწყობილი ვიდეოსამეთვალყურეო სისტემა, 8-პორტიანი PoE NVR-ით და UPS სარეზერვო კვებით. გადაწყვეტა უზრუნველყოფს მაღალი გარჩევადობის ფერად ღამის ხედვასა და ცენტრალიზებულ ჩანაწერს.',
                'seo_description' => '6 კამერიანი TVT 4MP Full Color CCTV პროექტი — 8-Port PoE NVR, UPS და ფერადი ღამის ხედვა. SafeTech-ის რეალური ვიდეოსამეთვალყურეო სისტემის შესრულება.',
                'image_alt' => 'TVT 4MP Full Color 6 კამერიანი ვიდეოსამეთვალყურეო სისტემა PoE NVR-ით და UPS-ით',
                'keywords' => ['TVT 4MP კამერები', 'Full Color კამერები', '6 კამერიანი სისტემა', 'PoE NVR', 'CCTV მონტაჟი', 'UPS კამერებისთვის'],
            ],
        ];

        foreach ($projects as $data) {
            $project = DB::table('projects')
                ->where('title', $data['match'])
                ->orWhere('name', $data['match'])
                ->first();

            if (! $project) {
                continue;
            }

            $seo = json_decode((string) ($project->seo ?? '{}'), true);
            if (! is_array($seo)) {
                $seo = [];
            }

            $seo['title'] = $data['title'].' | SafeTech';
            $seo['description'] = $data['seo_description'];
            $seo['keywords'] = $data['keywords'];
            $seo['noindex'] = false;

            DB::table('projects')->where('id', $project->id)->update([
                'title' => $data['title'],
                'description' => $data['description'],
                'seo_description' => $data['seo_description'],
                'image_alt' => $data['image_alt'],
                'seo' => json_encode($seo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Intentionally left empty to avoid overwriting later CMS edits.
    }
};
