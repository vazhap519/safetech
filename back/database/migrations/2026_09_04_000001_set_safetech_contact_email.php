<?php

use App\Models\SiteSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const CONTACT_EMAIL = 'info@safetech.ge';

    private const PREVIOUS_ADDRESS = 'safetechgeorgia@gmail.com';

    public function up(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        SiteSetting::query()
            ->where('key', 'contact')
            ->orderBy('id')
            ->each(function (SiteSetting $setting): void {
                $value = is_array($setting->value) ? $setting->value : [];
                $changed = false;

                foreach (['email', 'lead_email'] as $key) {
                    $current = strtolower(trim((string) ($value[$key] ?? '')));

                    if ($current !== '' && $current !== self::PREVIOUS_ADDRESS) {
                        continue;
                    }

                    $value[$key] = self::CONTACT_EMAIL;
                    $changed = true;
                }

                if ($changed) {
                    $setting->forceFill(['value' => $value])->save();
                }
            });
    }

    public function down(): void
    {
        // The address is user-managed content. Do not undo a deliberate public
        // contact update when rolling a schema migration back.
    }
};
