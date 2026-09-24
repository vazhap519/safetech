<?php

namespace Tests\Feature;

use App\Models\LocalServiceLanding;
use Database\Seeders\LegacyLocalLandingItemsSeeder;
use Database\Seeders\SystemContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegacyLocalCctvFaqSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_five_local_cctv_price_questions_use_correct_georgian_city_case(): void
    {
        $this->seed(SystemContentSeeder::class);

        foreach ($this->cityCases() as $slug => [$old, $correct]) {
            $landing = $this->cctvLanding($slug);

            $this->assertSame($correct, $landing->faq[0]['question']);

            // Reproduce the existing live CMS data written by the old migration.
            $faq = $landing->faq;
            $faq[0]['question'] = $old;
            $landing->faq = $faq;
            $landing->save();
        }

        $this->seed(LegacyLocalLandingItemsSeeder::class);

        foreach ($this->cityCases() as $slug => [, $correct]) {
            $landing = $this->cctvLanding($slug);
            $this->assertSame($correct, $landing->faq[0]['question']);
            $this->assertNotEmpty(data_get($landing->faq, '0.translations.en.question'));
            $this->assertNotEmpty(data_get($landing->faq, '0.translations.ru.question'));
        }

        // Idempotency: repeated production deploys do not re-edit published copy.
        $before = $this->cctvLanding('tbilisi')->faq;
        $this->seed(LegacyLocalLandingItemsSeeder::class);
        $this->assertSame($before, $this->cctvLanding('tbilisi')->faq);
    }

    public function test_it_preserves_custom_cctv_question_translation_and_noindex(): void
    {
        $this->seed(SystemContentSeeder::class);
        $landing = $this->cctvLanding('tbilisi');
        $originalTitle = $landing->title;
        $faq = $landing->faq;
        $faq[0]['question'] = 'რა ღირს კამერების მონტაჟი თბილისი-ში?';
        $faq[0]['translations']['en']['question'] = 'Custom editor-approved English question';
        $faq[] = ['question' => 'რა ღირს ჩემი ინდივიდუალური პროექტი?', 'answer' => 'ფასი ფასდება შეთანხმებით.'];
        $landing->faq = $faq;
        $landing->noindex = true;
        $landing->save();

        $this->seed(LegacyLocalLandingItemsSeeder::class);
        $landing->refresh();

        $this->assertSame('რა ღირს კამერების მონტაჟი თბილისში?', $landing->faq[0]['question']);
        $this->assertSame('Custom editor-approved English question',
            data_get($landing->faq, '0.translations.en.question'));
        $this->assertSame('რა ღირს ჩემი ინდივიდუალური პროექტი?', $landing->faq[4]['question']);
        $this->assertSame($originalTitle, $landing->title);
        $this->assertTrue($landing->noindex);
    }

    private function cctvLanding(string $slug): LocalServiceLanding
    {
        return LocalServiceLanding::query()
            ->where('location_slug', $slug)
            ->whereHas('service', fn ($query) => $query->where('slug', 'security-camera-installation'))
            ->sole();
    }

    private function cityCases(): array
    {
        return [
            'tbilisi' => ['რა ღირს კამერების მონტაჟი თბილისი-ში?', 'რა ღირს კამერების მონტაჟი თბილისში?'],
            'khashuri' => ['რა ღირს კამერების მონტაჟი ხაშური-ში?', 'რა ღირს კამერების მონტაჟი ხაშურში?'],
            'bakuriani' => ['რა ღირს კამერების მონტაჟი ბაკურიანი-ში?', 'რა ღირს კამერების მონტაჟი ბაკურიანში?'],
            'borjomi' => ['რა ღირს კამერების მონტაჟი ბორჯომი-ში?', 'რა ღირს კამერების მონტაჟი ბორჯომში?'],
            'surami' => ['რა ღირს კამერების მონტაჟი სურამი-ში?', 'რა ღირს კამერების მონტაჟი სურამში?'],
        ];
    }
}
