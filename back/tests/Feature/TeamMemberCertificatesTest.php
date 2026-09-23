<?php

namespace Tests\Feature;

use App\Models\TeamMember;
use App\Support\CmsMedia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TeamMemberCertificatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_content_exposes_an_ordered_certificate_gallery_and_refreshes_its_cache(): void
    {
        Storage::fake('public');

        $member = TeamMember::query()->create([
            'first_name' => 'Nino',
            'last_name' => 'Kiknadze',
            'position' => 'Security systems engineer',
            'bio' => 'Certified SafeTech specialist.',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->getJson('/api/content')
            ->assertOk()
            ->assertJsonPath('data.team.0.certificates', []);

        $first = $member
            ->addMedia(UploadedFile::fake()->image('network-certificate.png', 1200, 900))
            ->withCustomProperties(['alt' => 'Network engineering certificate'])
            ->toMediaCollection('certificates');
        $second = $member
            ->addMedia(UploadedFile::fake()->image('security-certificate.jpg', 900, 1200))
            ->toMediaCollection('certificates');

        $response = $this->getJson('/api/content')->assertOk();

        $response
            ->assertJsonCount(2, 'data.team.0.certificates')
            ->assertJsonPath('data.team.0.certificates.0.id', $first->id)
            ->assertJsonPath('data.team.0.certificates.0.alt', 'Network engineering certificate')
            ->assertJsonPath('data.team.0.certificates.1.id', $second->id)
            ->assertJsonPath('data.team.0.certificates.1.alt', 'Nino Kiknadze — certificate 2');

        foreach ($response->json('data.team.0.certificates') as $certificate) {
            $this->assertStringContainsString('/storage/', $certificate['src']);
            $this->assertStringContainsString('/storage/', $certificate['thumbnail']);
        }

        $first->delete();

        $this->getJson('/api/content')
            ->assertOk()
            ->assertJsonCount(1, 'data.team.0.certificates')
            ->assertJsonPath('data.team.0.certificates.0.id', $second->id);
    }

    public function test_certificate_collection_is_public_multi_image_only(): void
    {
        $collection = collect((new TeamMember)->getRegisteredMediaCollections())
            ->firstWhere('name', 'certificates');

        $this->assertNotNull($collection);
        $this->assertSame('public', $collection->diskName);
        $this->assertFalse($collection->singleFile);
        $this->assertSame(CmsMedia::IMAGE_MIME_TYPES, $collection->acceptsMimeTypes);
        $this->assertNotContains('application/pdf', $collection->acceptsMimeTypes);
    }
}
