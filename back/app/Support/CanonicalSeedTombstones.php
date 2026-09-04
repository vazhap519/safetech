<?php

namespace App\Support;

use App\Models\CategoryForService;
use App\Models\Faq;
use App\Models\SeedDeletionTombstone;
use App\Models\SeoPage;
use App\Models\Service;
use App\Models\SiteSetting;
use Database\Seeders\ConsultationCopySeeder;
use Database\Seeders\PageContentSeeder;
use Database\Seeders\PrivacyPageSeeder;
use Database\Seeders\SeoPageSeeder;
use Database\Seeders\ServiceCatalogSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Keeps canonical seed data from being silently restored after an admin
 * deletes it. Only identifiers which are shipped by this application are
 * eligible, so normal CMS records are never tombstoned.
 */
final class CanonicalSeedTombstones
{
    public const CATEGORY = 'service-category';

    public const FAQ = 'faq';

    public const SEO_PAGE = 'seo-page';

    public const SERVICE = 'service';

    public const SITE_SETTING = 'site-setting';

    public const TRANSLATION_ENTRY = 'translation-entry';

    /** @var array<int, string>|null */
    private static ?array $categorySlugs = null;

    /** @var array<int, string>|null */
    private static ?array $contactFaqKeys = null;

    /** @var array<int, string>|null */
    private static ?array $serviceFaqContexts = null;

    /** @var array<int, string>|null */
    private static ?array $serviceSlugs = null;

    /** @var array<int, string>|null */
    private static ?array $seoPageKeys = null;

    /** @var array<int, string>|null */
    private static ?array $translationEntryKeys = null;

    /**
     * Register listeners in one place so deletion semantics apply to Filament,
     * API, and CLI model deletes alike.
     */
    public static function register(): void
    {
        CategoryForService::deleted(fn (CategoryForService $category) => self::rememberCategory($category));
        CategoryForService::created(fn (CategoryForService $category) => self::forgetCategory($category));

        Service::deleted(function (Service $service): void {
            self::rememberService($service);
            self::removeOrphanedCanonicalServiceFaqs($service);
        });
        Service::created(fn (Service $service) => self::forgetService($service));

        Faq::deleted(fn (Faq $faq) => self::rememberFaq($faq));
        Faq::created(fn (Faq $faq) => self::forgetFaq($faq));

        SiteSetting::updating(fn (SiteSetting $setting) => self::trackTranslationEntryChanges($setting));
        SiteSetting::deleted(fn (SiteSetting $setting) => self::rememberSiteSetting($setting));
        SiteSetting::created(fn (SiteSetting $setting) => self::forgetSiteSetting($setting));

        SeoPage::deleted(fn (SeoPage $page) => self::rememberSeoPage($page));
        SeoPage::created(fn (SeoPage $page) => self::forgetSeoPage($page));
    }

    public static function categoryWasDeleted(string $slug): bool
    {
        return self::isCanonicalCategory($slug)
            && self::exists(self::CATEGORY, $slug);
    }

    public static function serviceWasDeleted(string $slug): bool
    {
        return self::isCanonicalService($slug)
            && self::exists(self::SERVICE, $slug);
    }

    public static function faqWasDeleted(string $key): bool
    {
        return self::isCanonicalFaqKey($key)
            && self::exists(self::FAQ, $key);
    }

    public static function siteSettingWasDeleted(string $key): bool
    {
        return self::isCanonicalSiteSetting($key)
            && self::exists(self::SITE_SETTING, $key);
    }

    public static function seoPageWasDeleted(string $key): bool
    {
        return self::isCanonicalSeoPage($key)
            && self::exists(self::SEO_PAGE, $key);
    }

    public static function translationEntryWasDeleted(string $key): bool
    {
        return self::isCanonicalTranslationEntry($key)
            && self::exists(self::TRANSLATION_ENTRY, $key);
    }

    private static function rememberCategory(CategoryForService $category): void
    {
        $slug = trim((string) $category->slug);

        if (self::isCanonicalCategory($slug)) {
            self::remember(self::CATEGORY, $slug);
        }
    }

    private static function forgetCategory(CategoryForService $category): void
    {
        $slug = trim((string) $category->slug);

        if (self::isCanonicalCategory($slug)) {
            self::forget(self::CATEGORY, $slug);
        }
    }

    private static function rememberService(Service $service): void
    {
        $slug = trim((string) $service->slug);

        if (self::isCanonicalService($slug)) {
            self::remember(self::SERVICE, $slug);
        }
    }

    private static function forgetService(Service $service): void
    {
        $slug = trim((string) $service->slug);

        if (self::isCanonicalService($slug)) {
            self::forget(self::SERVICE, $slug);
        }
    }

    private static function rememberFaq(Faq $faq): void
    {
        $key = self::faqKey($faq);

        if ($key !== null) {
            self::remember(self::FAQ, $key);
        }
    }

    private static function forgetFaq(Faq $faq): void
    {
        $key = self::faqKey($faq);

        if ($key !== null) {
            self::forget(self::FAQ, $key);
        }
    }

    private static function rememberSiteSetting(SiteSetting $setting): void
    {
        $key = trim((string) $setting->key);

        if (self::isCanonicalSiteSetting($key)) {
            self::remember(self::SITE_SETTING, $key);
        }
    }

    private static function forgetSiteSetting(SiteSetting $setting): void
    {
        $key = trim((string) $setting->key);

        if (self::isCanonicalSiteSetting($key)) {
            self::forget(self::SITE_SETTING, $key);
        }
    }

    private static function rememberSeoPage(SeoPage $page): void
    {
        $key = trim((string) $page->key);

        if (self::isCanonicalSeoPage($key)) {
            self::remember(self::SEO_PAGE, $key);
        }
    }

    private static function trackTranslationEntryChanges(SiteSetting $setting): void
    {
        if (
            trim((string) $setting->key) !== 'translations'
            || ! $setting->isDirty('value')
        ) {
            return;
        }

        $before = self::translationEntryMap($setting->getRawOriginal('value'));
        $after = self::translationEntryMap($setting->value);

        foreach (array_keys($before) as $key) {
            if (
                ! array_key_exists($key, $after)
                && self::isCanonicalTranslationEntry($key)
            ) {
                self::remember(self::TRANSLATION_ENTRY, $key);
            }
        }

        foreach (array_keys($after) as $key) {
            if (self::isCanonicalTranslationEntry($key)) {
                self::forget(self::TRANSLATION_ENTRY, $key);
            }
        }
    }

    private static function forgetSeoPage(SeoPage $page): void
    {
        $key = trim((string) $page->key);

        if (self::isCanonicalSeoPage($key)) {
            self::forget(self::SEO_PAGE, $key);
        }
    }

    private static function removeOrphanedCanonicalServiceFaqs(Service $service): void
    {
        $slug = trim((string) $service->slug);

        if (! self::isCanonicalService($slug)) {
            return;
        }

        $contexts = array_values(array_filter(
            self::serviceFaqContexts(),
            static fn (string $context): bool => str_starts_with($context, "service:{$slug}:"),
        ));

        if ($contexts === []) {
            return;
        }

        // The FK deliberately uses nullOnDelete. Remove only the canonical FAQ
        // rows once the service has actually been deleted, so they cannot leak
        // into the site's global FAQ list as orphaned records.
        Faq::query()
            ->whereNull('service_id')
            ->whereIn('context', $contexts)
            ->get()
            ->each(static fn (Faq $faq): bool => $faq->delete());
    }

    private static function faqKey(Faq $faq): ?string
    {
        $context = trim((string) $faq->context);

        if (in_array($context, self::serviceFaqContexts(), true)) {
            return $context;
        }

        if ($context !== 'contact') {
            return null;
        }

        $key = PageContentSeeder::contactFaqTombstoneKey((int) $faq->sort_order);

        return in_array($key, self::contactFaqKeys(), true) ? $key : null;
    }

    private static function isCanonicalCategory(string $slug): bool
    {
        return in_array($slug, self::categorySlugs(), true);
    }

    private static function isCanonicalService(string $slug): bool
    {
        return in_array($slug, self::serviceSlugs(), true);
    }

    private static function isCanonicalFaqKey(string $key): bool
    {
        return in_array($key, self::serviceFaqContexts(), true)
            || in_array($key, self::contactFaqKeys(), true);
    }

    private static function isCanonicalSiteSetting(string $key): bool
    {
        return in_array($key, [
            'branding',
            'contact',
            'socials',
            'seo',
            'integrations',
            'translations',
        ], true);
    }

    private static function isCanonicalSeoPage(string $key): bool
    {
        return in_array($key, self::seoPageKeys(), true);
    }

    private static function isCanonicalTranslationEntry(string $key): bool
    {
        return in_array($key, self::translationEntryKeys(), true);
    }

    /** @return array<int, string> */
    private static function categorySlugs(): array
    {
        return self::$categorySlugs ??= ServiceCatalogSeeder::canonicalCategorySlugs();
    }

    /** @return array<int, string> */
    private static function serviceSlugs(): array
    {
        return self::$serviceSlugs ??= ServiceCatalogSeeder::canonicalServiceSlugs();
    }

    /** @return array<int, string> */
    private static function serviceFaqContexts(): array
    {
        return self::$serviceFaqContexts ??= ServiceCatalogSeeder::canonicalFaqContexts();
    }

    /** @return array<int, string> */
    private static function contactFaqKeys(): array
    {
        return self::$contactFaqKeys ??= PageContentSeeder::canonicalContactFaqTombstoneKeys();
    }

    /** @return array<int, string> */
    private static function seoPageKeys(): array
    {
        return self::$seoPageKeys ??= array_values(array_unique([
            ...SeoPageSeeder::canonicalKeys(),
            ...PrivacyPageSeeder::canonicalKeys(),
        ]));
    }

    /** @return array<int, string> */
    private static function translationEntryKeys(): array
    {
        return self::$translationEntryKeys ??= array_values(array_unique([
            ...PageContentSeeder::canonicalTranslationKeys(),
            ...ConsultationCopySeeder::canonicalTranslationKeys(),
            ...PrivacyPageSeeder::canonicalTranslationKeys(),
            ...ServiceCatalogSeeder::canonicalTranslationKeys(),
        ]));
    }

    /** @return array<string, array{ka:string,en:string,ru:string}> */
    private static function translationEntryMap(mixed $value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : [];
        }

        return MultilingualContent::mapFrom(is_array($value) ? $value : []);
    }

    private static function exists(string $type, string $key): bool
    {
        return self::tableExists()
            && SeedDeletionTombstone::query()
                ->where('type', $type)
                ->where('key', $key)
                ->exists();
    }

    private static function remember(string $type, string $key): void
    {
        if (! self::tableExists()) {
            return;
        }

        SeedDeletionTombstone::query()->updateOrCreate([
            'type' => $type,
            'key' => $key,
        ]);
    }

    private static function forget(string $type, string $key): void
    {
        if (! self::tableExists()) {
            return;
        }

        SeedDeletionTombstone::query()
            ->where('type', $type)
            ->where('key', $key)
            ->delete();
    }

    private static function tableExists(): bool
    {
        return Schema::hasTable((new SeedDeletionTombstone)->getTable());
    }
}
