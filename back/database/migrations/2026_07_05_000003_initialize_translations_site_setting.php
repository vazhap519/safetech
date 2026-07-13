<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('site_settings')->updateOrInsert(
            ['key' => 'translations'],
            [
                'group' => 'general',
                'value' => json_encode(['entries' => []], JSON_UNESCAPED_UNICODE),
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
    }

    public function down(): void
    {
        DB::table('site_settings')->where('key', 'translations')->delete();
    }
};
