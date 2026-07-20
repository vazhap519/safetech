<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('contacts') || ! Schema::hasTable('contact_leads')) {
            return;
        }

        DB::table('contacts')
            ->orderBy('id')
            ->chunkById(200, function ($contacts): void {
                foreach ($contacts as $contact) {
                    $fingerprint = hash('sha256', "legacy-contact:{$contact->id}");

                    DB::table('contact_leads')->updateOrInsert(
                        [
                            'source' => 'legacy-contact',
                            'ip_hash' => $fingerprint,
                        ],
                        [
                            'name' => $contact->name,
                            'phone' => $contact->phone,
                            'message' => $contact->message,
                            'status' => $contact->is_read ? 'contacted' : 'new',
                            'details' => json_encode([
                                [
                                    'key' => 'legacy_contact_id',
                                    'label' => 'ძველი მოთხოვნის ID',
                                    'value' => (string) $contact->id,
                                ],
                            ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                            'created_at' => $contact->created_at,
                            'updated_at' => $contact->updated_at,
                        ],
                    );
                }
            });
    }

    public function down(): void
    {
        if (Schema::hasTable('contact_leads')) {
            DB::table('contact_leads')->where('source', 'legacy-contact')->delete();
        }
    }
};
