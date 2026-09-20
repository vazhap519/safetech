<?php

namespace Database\Seeders;

use App\Models\LocalServiceLanding;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * The September 2026 canonical 30 Local pages were first defined in data
 * migrations, which run BEFORE ServiceCatalogSeeder on clean installations.
 * Restore those exact authored records only if the Local table is EMPTY.
 * Production installations with existing CMS pages are never rewritten.
 */
final class LegacyLocalLandingBaselineSeeder extends Seeder
{
    public function run(): void
    {
        if (LocalServiceLanding::query()->exists()) {
            return;
        }

        $migrationFiles = [
            '2026_09_05_163000_strengthen_cctv_local_seo_landings.php',
            '2026_09_05_173000_expand_priority_local_seo_landings.php',
        ];

        foreach ($migrationFiles as $migrationFile) {
            $migration = require database_path('migrations/'.$migrationFile);
            $migration->up();
        }

        // The original migrations matched project titles only by city string;
        // that is insufficient proof of the actual service delivered. No Local
        // page existed before this fresh-install bootstrap, so all newly
        // created auto-links are safe to withhold pending human verification.
        $ids = LocalServiceLanding::query()->pluck('id');
        if ($ids->isNotEmpty()) {
            DB::table('local_service_landing_project')
                ->whereIn('landing_id', $ids)
                ->delete();
        }
    }
}
