<?php

namespace Tests\Feature;

use App\Models\LocalServiceLanding;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalServiceLandingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_a_localized_commercial_landing_with_real_projects(): void
    {
        $service = Service::query()->create([
            'slug' => 'security-camera-installation',
            'name' => 'უსაფრთხოების კამერების მონტაჟი',
            'title' => 'ვიდეო მეთვალყურეობის სისტემები',
            'description' => 'კამერების მონტაჟი და გამართვა.',
            'seo_description' => 'კამერების პროფესიონალური მონტაჟი.',
            'is_published' => true,
        ]);
        $project = Project::query()->create([
            'slug' => 'tbilisi-cctv-project',
            'name' => 'თბილისის CCTV პროექტი',
            'title' => '10 კამერის მონტაჟი თბილისში',
            'description' => 'რეალურ ობიექტზე შესრულებული კამერების პროექტი.',
            'excerpt' => '10 კამერის პროექტი.',
            'seo_description' => 'CCTV პროექტი თბილისში.',
            'is_published' => true,
        ]);
        $landing = LocalServiceLanding::query()->create([
            'service_id' => $service->id,
            'location_slug' => 'tbilisi',
            'location_name' => 'თბილისი',
            'title' => 'უსაფრთხოების კამერების მონტაჟი თბილისში',
            'excerpt' => 'დაგეგმვა, მონტაჟი და დისტანციური წვდომა.',
            'content' => 'SafeTech ახორციელებს ვიდეო მეთვალყურეობის სისტემების დაგეგმვასა და მონტაჟს თბილისში კერძო და ბიზნეს ობიექტებისთვის.',
            'benefits' => [[
                'title' => 'სწორი დაგეგმვა',
                'description' => 'კამერების პოზიციები და არქივი ითვლება ობიექტის მიხედვით.',
                'translations' => [
                    'en' => [
                        'title' => 'Correct planning',
                        'description' => 'Camera positions and retention are planned for the property.',
                    ],
                ],
            ]],
            'faq' => [[
                'question' => 'თბილისში ადგილზე მოდიხართ?',
                'answer' => 'დიახ, ობიექტის შეფასება შესაძლებელია შეთანხმებით.',
                'translations' => [
                    'en' => [
                        'question' => 'Do you visit properties in Tbilisi?',
                        'answer' => 'Yes, on-site assessment is available by arrangement.',
                    ],
                ],
            ]],
            'primary_keyword' => 'უსაფრთხოების კამერების მონტაჟი თბილისში',
            'keywords' => ['CCTV თბილისი', 'კამერების მონტაჟი'],
            'seo_title' => 'უსაფრთხოების კამერების მონტაჟი თბილისში | SafeTech',
            'seo_description' => 'ვიდეო მეთვალყურეობის დაგეგმვა, მონტაჟი და გამართვა თბილისში.',
            'translations' => [
                'fields' => [
                    'locationName' => ['en' => 'Tbilisi'],
                    'title' => ['en' => 'Security camera installation in Tbilisi'],
                    'excerpt' => ['en' => 'Planning, installation and remote access.'],
                    'content' => ['en' => 'SafeTech plans and installs video surveillance systems for homes and businesses in Tbilisi.'],
                    'seoTitle' => ['en' => 'Security Camera Installation in Tbilisi | SafeTech'],
                    'seoDescription' => ['en' => 'Professional CCTV planning, installation and setup in Tbilisi.'],
                ],
            ],
            'is_published' => true,
            'noindex' => false,
        ]);
        $landing->projects()->attach($project);

        $this->getJson('/api/local-service-landings/security-camera-installation/tbilisi?locale=en')
            ->assertOk()
            ->assertJsonPath('data.locationName', 'Tbilisi')
            ->assertJsonPath('data.title', 'Security camera installation in Tbilisi')
            ->assertJsonPath('data.benefits.0.title', 'Correct planning')
            ->assertJsonPath('data.faqs.0.question', 'Do you visit properties in Tbilisi?')
            ->assertJsonPath('data.projects.0.slug', 'tbilisi-cctv-project')
            ->assertJsonPath('data.service.slug', 'security-camera-installation')
            ->assertJsonPath('data.seo.noindex', false);
    }

    public function test_unpublished_local_landings_are_not_public(): void
    {
        $service = Service::query()->create([
            'slug' => 'network-cable-installation',
            'name' => 'ქსელის კაბელის გაყვანა',
            'title' => 'ქსელის კაბელის გაყვანა',
            'description' => 'CAT6 ქსელის მონტაჟი.',
            'seo_description' => 'CAT6 ქსელის მონტაჟი.',
            'is_published' => true,
        ]);

        LocalServiceLanding::query()->create([
            'service_id' => $service->id,
            'location_slug' => 'tbilisi',
            'location_name' => 'თბილისი',
            'title' => 'ქსელის კაბელის გაყვანა თბილისში',
            'content' => 'ჯერ არ გამოქვეყნებული გვერდი.',
            'is_published' => false,
        ]);

        $this->getJson('/api/local-service-landings/network-cable-installation/tbilisi')
            ->assertNotFound();
    }
}
