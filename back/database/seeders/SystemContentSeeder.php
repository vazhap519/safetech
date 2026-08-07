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

                $contact->forceFill([
                    'value' => array_replace($seededContact, $adminManagedContact),
                ])->save();
            }
        }

        $this->call(ConsultationCopySeeder::class);
        $this->call(PrivacyPageSeeder::class);
        $this->call(ServiceCatalogSeeder::class);
    }
}
