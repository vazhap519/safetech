<?php

namespace Tests\Feature;

use App\Filament\Resources\PageResource\Pages\CreatePage;
use App\Filament\Resources\PageResource\Pages\EditPage;
use App\Filament\Resources\PageResource\Pages\ListPages;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPageCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('cms.admin.email', 'qa-admin@safetech.test');

        $admin = User::query()->create([
            'name' => 'QA Admin',
            'email' => 'qa-admin@safetech.test',
            'password' => 'SafeTechQaPass123!',
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin);
    }

    public function test_admin_can_create_edit_list_and_delete_a_page(): void
    {
        Livewire::test(CreatePage::class)
            ->fillForm([
                'title' => 'QA release candidate page',
                'slug' => 'qa-release-candidate-page',
                'content' => 'This page exists only inside the automated QA database.',
                'is_published' => true,
                'noindex' => true,
                'sort_order' => 999,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $page = Page::query()->where('slug', 'qa-release-candidate-page')->sole();

        $this->assertTrue($page->is_published);
        $this->assertTrue($page->noindex);

        Livewire::test(ListPages::class)
            ->assertCanSeeTableRecords([$page]);

        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->fillForm([
                'title' => 'QA release candidate page updated',
                'slug' => 'qa-release-candidate-page',
                'content' => 'Updated automated QA content.',
                'is_published' => false,
                'noindex' => true,
                'sort_order' => 998,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $page->refresh();
        $this->assertSame('QA release candidate page updated', $page->title);
        $this->assertFalse($page->is_published);

        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->callAction('delete');

        $this->assertDatabaseMissing('pages', ['id' => $page->getKey()]);
    }
}
