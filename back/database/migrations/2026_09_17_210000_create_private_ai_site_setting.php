<?php

use App\Models\SiteSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $setting = SiteSetting::query()->firstOrCreate(
            ['key' => 'ai'],
            [
                'group' => 'general',
                'value' => ['system_prompt' => ''],
                'is_public' => false,
            ],
        );

        if ($setting->is_public) {
            $setting->forceFill(['is_public' => false])->save();
        }
    }

    public function down(): void
    {
        SiteSetting::query()->where('key', 'ai')->delete();
    }
};
