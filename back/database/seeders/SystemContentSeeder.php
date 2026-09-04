<?php

namespace Database\Seeders;

use App\Models\SiteSetting;

final class SystemContentSeeder extends ContentSeeder
{
    public function run(): void
    {
        $this->seedSystemContent();

        $existingContact = SiteSetting::query()
            ->where('key', 'contact')
            ->first();
        $adminManagedContact = is_array($existingContact?->value)
            ? $existingContact->value
            : null;

        $this->call(PageContentSeeder::class);

        if ($adminManagedContact !== null) {
            $contact = SiteSetting::query()->where('key', 'contact')->first();

            if ($contact) {
                $seededContact = is_array($contact->value) ? $contact->value : [];
                $mergedContact = array_replace($seededContact, $adminManagedContact);

                // A legacy blank/Gmail address is not a meaningful CMS
                // override. Keep the public SafeTech address produced by the
                // current system content, while preserving any other address
                // the administrator intentionally configured.
                foreach (['email', 'lead_email'] as $key) {
                    $adminValue = trim((string) ($adminManagedContact[$key] ?? ''));

                    if ($adminValue === '' || strcasecmp($adminValue, 'safetechgeorgia@gmail.com') === 0) {
                        $seededValue = trim((string) ($seededContact[$key] ?? ''));

                        $mergedContact[$key] = $seededValue !== ''
                            && strcasecmp($seededValue, 'safetechgeorgia@gmail.com') !== 0
                            ? $seededValue
                            : 'info@safetech.ge';
                    }
                }

                $contact->forceFill([
                    'value' => $mergedContact,
                ])->save();
            }
        }

        $this->call(ConsultationCopySeeder::class);
        $this->call(PrivacyPageSeeder::class);
        $this->call(ServiceCatalogSeeder::class);
    }
}
