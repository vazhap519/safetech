<?php

namespace Database\Seeders;

use App\Models\SeoPage;
use Illuminate\Database\Seeder;

class SeoPageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = $this->pages();
        $keys = array_column($pages, 'key');

        SeoPage::query()->whereNotIn('key', $keys)->delete();

        foreach ($pages as $page) {
            SeoPage::query()->updateOrCreate(
                ['key' => $page['key']],
                $page,
            );
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function pages(): array
    {
        return [
            $this->page(
                'home',
                '/',
                ['IT ინფრასტრუქტურა და უსაფრთხოების სისტემები ბიზნესისთვის', 'IT Infrastructure and Security Systems for Business', 'IT-инфраструктура и системы безопасности для бизнеса'],
                ['SafeTech ქმნის პროფესიონალურ IT და უსაფრთხოების გადაწყვეტილებებს საქართველოში.', 'SafeTech delivers professional IT and security solutions in Georgia.', 'SafeTech внедряет профессиональные IT-решения и системы безопасности в Грузии.'],
                'WebSite',
            ),
            $this->page(
                'about',
                '/about',
                ['SafeTech-ის გუნდი და გამოცდილება', 'SafeTech Team and Experience', 'Команда и опыт SafeTech'],
                ['გაიცანით SafeTech-ის გუნდი, გამოცდილება და მუშაობის მიდგომა.', 'Meet the SafeTech team, experience, and approach.', 'Познакомьтесь с командой, опытом и подходом SafeTech.'],
                'AboutPage',
            ),
            $this->page(
                'services',
                '/services',
                ['IT და უსაფრთხოების სერვისები', 'IT and Security Services', 'IT-услуги и системы безопасности'],
                ['ვიდეოსამეთვალყურეობა, დაშვების კონტროლი, ქსელები, სერვერები და IT მხარდაჭერა.', 'CCTV, access control, networking, servers, and IT support.', 'Видеонаблюдение, контроль доступа, сети, серверы и IT-поддержка.'],
                'CollectionPage',
            ),
            $this->page(
                'projects',
                '/projects',
                ['განხორციელებული პროექტები', 'Completed Projects', 'Реализованные проекты'],
                ['SafeTech-ის მიერ განხორციელებული IT და უსაფრთხოების პროექტები.', 'IT and security projects delivered by SafeTech.', 'IT-проекты и проекты безопасности, реализованные SafeTech.'],
                'CollectionPage',
            ),
            $this->page(
                'contact',
                '/contact',
                ['კონტაქტი და კონსულტაცია', 'Contact and Consultation', 'Контакты и консультация'],
                ['დაუკავშირდით SafeTech-ს ტექნიკური კონსულტაციისთვის.', 'Contact SafeTech for a technical consultation.', 'Свяжитесь с SafeTech для технической консультации.'],
                'ContactPage',
            ),
        ];
    }

    /** @param array{0:string,1:string,2:string} $titles @param array{0:string,1:string,2:string} $descriptions */
    private function page(
        string $key,
        string $slug,
        array $titles,
        array $descriptions,
        string $schemaType,
    ): array {
        [$titleKa, $titleEn, $titleRu] = $titles;
        [$descriptionKa, $descriptionEn, $descriptionRu] = $descriptions;

        return [
            'key' => $key,
            'slug' => $slug,
            'title' => $titleKa,
            'description' => $descriptionKa,
            'keywords' => [],
            'og_title' => $titleKa,
            'og_description' => $descriptionKa,
            'canonical' => null,
            'noindex' => false,
            'schema_type' => $schemaType,
            'schema' => null,
            'translations' => [
                'fields' => [
                    'title' => ['ka' => $titleKa, 'en' => $titleEn, 'ru' => $titleRu],
                    'description' => ['ka' => $descriptionKa, 'en' => $descriptionEn, 'ru' => $descriptionRu],
                    'og_title' => ['ka' => $titleKa, 'en' => $titleEn, 'ru' => $titleRu],
                    'og_description' => ['ka' => $descriptionKa, 'en' => $descriptionEn, 'ru' => $descriptionRu],
                ],
                'keywords' => ['ka' => [], 'en' => [], 'ru' => []],
            ],
        ];
    }
}
