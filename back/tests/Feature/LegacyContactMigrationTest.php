<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LegacyContactMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_contacts_are_copied_to_the_canonical_lead_table_once(): void
    {
        DB::table('contacts')->insert([
            'id' => 41,
            'name' => 'Legacy Client',
            'phone' => '+995555123456',
            'message' => 'Legacy request',
            'is_read' => true,
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        $migration = require database_path('migrations/2026_07_20_000001_migrate_legacy_contacts_to_contact_leads.php');
        $migration->up();
        $migration->up();

        $this->assertDatabaseCount('contact_leads', 1);
        $this->assertDatabaseHas('contact_leads', [
            'name' => 'Legacy Client',
            'phone' => '+995555123456',
            'source' => 'legacy-contact',
            'status' => 'contacted',
        ]);
    }
}
