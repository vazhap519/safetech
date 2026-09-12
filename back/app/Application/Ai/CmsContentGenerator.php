<?php

namespace App\Application\Ai;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use JsonException;
use RuntimeException;

final class CmsContentGenerator
{
    private const TARGETS_PER_REQUEST = 24;

    /**
     * Generate safe content updates for a Filament form.
     *
     * The form state is converted into an explicit list of editable paths.
     * Structured Outputs may return values for those paths only, and every
     * requested path must be present before success is reported.
     *
     * @param  array<string, mixed>  $currentState
     * @return array<string, mixed>
     */
    public function generate(string $profile, string $facts, array $currentState, bool $overwrite = false): array
    {
        $facts = trim($facts);

        if ($facts === '') {
            throw new RuntimeException('AI-სთვის საწყისი ინფორმაცია ცარიელია.');
        }

        $apiKey = trim((string) config('services.openai.api_key'));

        if ($apiKey === '') {
            throw new RuntimeException('OpenAI API key is not configured.');
        }

        $targets = $this->targetPaths($profile, $currentState, $overwrite);

        if ($targets === []) {
            return [];
        }

        $model = trim((string) config('services.openai.model', 'gpt-5.6-luna')) ?: 'gpt-5.6-luna';
        $updates = [];
        $workingState = $currentState;

        foreach (array_chunk($targets, self::TARGETS_PER_REQUEST) as $targetBatch) {
            $patches = $this->requestPatches(
                $apiKey,
                $model,
                $profile,
                $facts,
                $workingState,
                $overwrite,
                $targetBatch,
            );

            $missing = array_values(array_diff($targetBatch, array_keys($patches)));

            if ($missing !== []) {
                $retryPatches = $this->requestPatches(
                    $apiKey,
                    $model,
                    $profile,
                    $facts,
                    $this->mergePatchValues($workingState, $patches),
                    $overwrite,
                    $missing,
                    retry: true,
                );
                $patches = array_replace($patches, $retryPatches);
                $missing = array_values(array_diff($targetBatch, array_keys($patches)));
            }

            if ($missing !== []) {
                throw new RuntimeException($this->missingFieldsMessage($missing));
            }

            $updates = $this->mergePatchValues($updates, $patches);
            $workingState = $this->mergePatchValues($workingState, $patches);
        }

        $sanitized = $this->sanitizeUpdates($profile, $updates, $currentState, $overwrite);
        $merged = $this->mergeIntoState($currentState, $sanitized);
        $missingAfterSanitizing = array_values(array_filter(
            $targets,
            fn (string $path): bool => ! $this->isGeneratedValueComplete($profile, $path, data_get($merged, $path)),
        ));

        if ($missingAfterSanitizing !== []) {
            throw new RuntimeException($this->missingFieldsMessage($missingAfterSanitizing));
        }

        return $sanitized;
    }

    /**
     * @param  array<string, mixed>  $state
     * @param  array<int, string>  $targets
     * @return array<string, string|array<mixed>>
     */
    private function requestPatches(
        string $apiKey,
        string $model,
        string $profile,
        string $facts,
        array $state,
        bool $overwrite,
        array $targets,
        bool $retry = false,
    ): array {
        $targetJson = json_encode($targets, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $stateJson = json_encode(
            $this->safeContext($state),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT,
        );
        $attemptNote = $retry
            ? "\nThis is a completeness retry. Return every listed target exactly once; none may be omitted."
            : '';

        $payload = [
            'model' => $model,
            'instructions' => $this->instructions($profile, $overwrite),
            'input' => [[
                'role' => 'user',
                'content' => "EDITOR FACTS:\n{$facts}\n\n".
                    "EXACT TARGET PATHS TO FILL:\n{$targetJson}\n\n".
                    "CURRENT FORM STATE (context only):\n{$stateJson}{$attemptNote}",
            ]],
            'reasoning' => ['effort' => 'none'],
            'store' => false,
            'max_output_tokens' => max(2000, min(20000, (int) config('services.openai.max_output_tokens', 12000))),
            'text' => [
                'format' => [
                    'type' => 'json_schema',
                    'name' => 'cms_content_patches',
                    'strict' => true,
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'patches' => [
                                'type' => 'array',
                                'minItems' => count($targets),
                                'maxItems' => count($targets),
                                'items' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'path' => [
                                            'type' => 'string',
                                            'enum' => array_values($targets),
                                        ],
                                        'value_json' => ['type' => 'string'],
                                    ],
                                    'required' => ['path', 'value_json'],
                                    'additionalProperties' => false,
                                ],
                            ],
                        ],
                        'required' => ['patches'],
                        'additionalProperties' => false,
                    ],
                ],
            ],
        ];

        $response = $this->http($apiKey)
            ->post('https://api.openai.com/v1/responses', $payload);

        if ($response->failed()) {
            $message = trim((string) data_get($response->json(), 'error.message'));
            throw new RuntimeException($message !== '' ? $message : 'OpenAI content generation failed.');
        }

        $responseData = $response->json();
        $status = (string) ($responseData['status'] ?? '');

        if ($status === 'incomplete') {
            $reason = trim((string) data_get($responseData, 'incomplete_details.reason'));
            throw new RuntimeException(
                $reason !== ''
                    ? "OpenAI-ის პასუხი არასრულია: {$reason}. თავიდან სცადეთ."
                    : 'OpenAI-ის პასუხი არასრულია. თავიდან სცადეთ.',
            );
        }

        if ($status !== '' && $status !== 'completed') {
            throw new RuntimeException("OpenAI generation ended with status: {$status}.");
        }

        $decoded = $this->decodeJsonObject($this->extractText($responseData));
        $rows = $decoded['patches'] ?? null;

        if (! is_array($rows)) {
            throw new RuntimeException('AI-მ ვალიდური შევსების მონაცემები ვერ დააბრუნა.');
        }

        $allowedTargets = array_fill_keys($targets, true);
        $patches = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $path = (string) ($row['path'] ?? '');
            $valueJson = $row['value_json'] ?? null;

            if (! isset($allowedTargets[$path]) || ! is_string($valueJson)) {
                continue;
            }

            try {
                $value = json_decode($valueJson, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                continue;
            }

            if (! $this->isGeneratedValueComplete($profile, $path, $value)) {
                continue;
            }

            $patches[$path] = $value;
        }

        return $patches;
    }

    private function instructions(string $profile, bool $overwrite): string
    {
        $profileRules = match ($profile) {
            'project' => <<<'RULES'
PROJECT profile:
- Build a professional case study from the supplied facts.
- meta/scope/specs arrays use {"value":"...","label":"...","translations":{"en":{"value":"...","label":"..."},"ru":{"value":"...","label":"..."}}}.
- challenges/solutions arrays use {"title":"...","description":"...","translations":{"en":{"title":"...","description":"..."},"ru":{"title":"...","description":"..."}}}.
- process arrays use the same title/description translation shape.
- results arrays use {"value":"...","title":"...","description":"...","translations":{"en":{"value":"...","title":"...","description":"..."},"ru":{"value":"...","title":"...","description":"..."}}}.
- Prefer 3-5 useful items per generated section. Do not add equipment, models, quantities or specifications absent from EDITOR FACTS.
RULES,
            'service' => <<<'RULES'
SERVICE profile:
- Produce conversion-focused, technically credible service copy in KA/EN/RU.
- benefits/solutions arrays use {"title":"...","description":"..."}; process uses {"title":"...","description":"..."}.
- translations.entries is the legacy locale map for service repeaters. Each item must be {"key":"benefit.0.title","ka":"...","en":"...","ru":"..."}; cover every generated repeater title/description using its zero-based index.
- Tags/keywords/highlights/industries are JSON arrays of plain strings.
- If overview is targeted, its value must be a JSON-encoded string containing valid structured JSON suitable for the existing Overview JSON field.
- Never invent prices, discounts, warranty periods, response times, package prices, brands, technical limits or availability.
RULES,
            'page' => <<<'RULES'
PAGE profile:
- Generate title, excerpt, body content, SEO title/description/keywords and all existing KA/EN/RU translation paths.
- Preserve legal meaning for privacy/terms pages. Never invent legal promises, registration facts or contact details.
RULES,
            'about' => <<<'RULES'
ABOUT profile:
- Fill every targeted about_page_translations path in KA/EN/RU with consistent company positioning, story, capabilities, process and CTA copy.
- Do not invent years in business, team size, certifications, partner status, client counts or guarantees.
RULES,
            'faq' => <<<'RULES'
FAQ profile:
- Generate concise customer question/answer copy and every existing KA/EN/RU translation path.
- Do not invent prices, guarantees, service areas or technical claims.
RULES,
            'local-seo' => <<<'RULES'
LOCAL SEO profile:
- Create useful, unique local service copy in Georgian, English and Russian; avoid doorway-page wording and keyword stuffing.
- benefits items use {"title":"...","description":"...","translations":{"en":{"title":"...","description":"..."},"ru":{"title":"...","description":"..."}}}.
- faq items use {"question":"...","answer":"...","translations":{"en":{"question":"...","answer":"..."},"ru":{"question":"...","answer":"..."}}}.
- Keyword targets are JSON arrays of natural search phrases in the target language.
- Location and service facts must match EDITOR FACTS/current state exactly.
RULES,
            'category' => <<<'RULES'
CATEGORY profile:
- Generate category name, useful intro, SEO copy, keywords and FAQ in KA/EN/RU.
- faq and translations.faq.{locale} are arrays of {"question":"...","answer":"..."}.
- Keyword targets are JSON arrays of plain strings in the relevant language.
RULES,
            'seo-page' => <<<'RULES'
SEO PAGE profile:
- Generate title, description, Open Graph title/description and editorial keyword topics for every targeted KA/EN/RU path.
- Keyword targets are JSON arrays of plain strings. Match the current page's real search intent and do not invent business facts.
RULES,
            'settings' => <<<'RULES'
SETTINGS profile:
- Fill only targeted editorial copy and translation values already present in the current form.
- Never change phone numbers, email addresses, URLs, social links, addresses, coordinates, analytics values, IDs, booleans or operational settings.
RULES,
            'team-member' => <<<'RULES'
TEAM MEMBER profile:
- Fill the person's name, role and biography translation targets in natural KA/EN/RU.
- Names, roles, experience and qualifications must come from EDITOR FACTS/current state. Never invent a person or credential.
RULES,
            'testimonial' => <<<'RULES'
TESTIMONIAL profile:
- Translate the supplied real review, author, role and company faithfully into every targeted KA/EN/RU field.
- Do not create a fake review, author, company or outcome and do not strengthen the reviewer's claims.
RULES,
            'partner' => <<<'RULES'
PARTNER profile:
- Partner/brand names must remain exact. Fill a category only when the editor supplied it.
- This form has no localized marketing copy; never fabricate partner relationships or certifications.
RULES,
            default => <<<'RULES'
GENERIC profile:
- Fill only the explicitly targeted editorial text fields and translations already present in the form.
- Never change identifiers, media, contact details, prices, dates, booleans or operational settings.
RULES,
        };

        $overwriteRule = $overwrite
            ? 'Rewrite every listed editorial target when useful, while preserving all factual meaning.'
            : 'Every listed target is currently empty and must receive a useful value. Keep all non-targeted existing copy unchanged.';

        return <<<PROMPT
You are SafeTech Georgia's CMS copywriter, SEO specialist, sales copywriter, translator and QA editor.

Turn EDITOR FACTS into production-ready website content in Georgian (KA), English (EN) and Russian (RU).

NON-NEGOTIABLE RULES:
- Return exactly one patch for every path in EXACT TARGET PATHS. Never omit a path and never return a path outside that list.
- Each patch has {"path":"exact.path","value_json":"..."}. value_json is a JSON-encoded non-empty string or non-empty array, not ordinary unescaped prose.
- Example: text uses "value_json":"\"ქართული ტექსტი\"" and tags use "value_json":"[\"ერთი\",\"ორი\"]".
- Never invent a product model, quantity, price, warranty, SLA, location, certification, client result, statistic, date, person or company claim.
- Numbers and product names supplied by the editor must remain exact in every language.
- If a non-factual marketing field needs wording, use neutral truthful wording without adding unsupported claims.
- Georgian must be natural and professional; English and Russian must carry precisely the same factual meaning.
- SEO copy must be useful and natural. Do not use hashtags, fake reviews, fake urgency or unverifiable superlatives.
- Do not alter any field that is not present in EXACT TARGET PATHS.
- {$overwriteRule}

{$profileRules}
PROMPT;
    }

    /** @param array<string, mixed> $state
     * @return array<int, string>
     */
    private function targetPaths(string $profile, array $state, bool $overwrite): array
    {
        if ($profile === 'settings') {
            return $this->siteSettingTargetPaths($state, $overwrite);
        }

        $targets = [];

        foreach ($this->allowedRoots($profile) as $root) {
            if (! array_key_exists($root, $state)) {
                continue;
            }

            $this->collectTargetPaths($profile, $state[$root], $root, $overwrite, $targets);
        }

        return array_values(array_unique($targets));
    }

    /** @param array<string, mixed> $state
     * @return array<int, string>
     */
    private function siteSettingTargetPaths(array $state, bool $overwrite): array
    {
        $paths = ['managed_page_translations'];

        $paths = [...$paths, ...match ((string) ($state['key'] ?? '')) {
            'translations' => ['value.entries'],
            'branding' => ['value.site_name', 'value.tagline'],
            'seo' => ['value.site_name', 'value.site_description', 'value.default_keywords'],
            'socials' => ['value.share_title_ka', 'value.share_title_en', 'value.share_title_ru'],
            'contact' => ['value.whatsapp_message'],
            default => [],
        }];

        $targets = [];
        $missing = new \stdClass;

        foreach ($paths as $path) {
            $value = data_get($state, $path, $missing);
            if ($value === $missing) {
                continue;
            }

            $this->collectTargetPaths('settings', $value, $path, $overwrite, $targets);
        }

        return array_values(array_unique($targets));
    }

    /** @param array<int, string> $targets */
    private function collectTargetPaths(string $profile, mixed $value, string $path, bool $overwrite, array &$targets): void
    {
        // Translation entry keys route copy to the frontend. Existing keys
        // must never become rewrite targets.
        if (preg_match('/^translations\.entries\.\d+\.key$/', $path) === 1) {
            return;
        }

        if ($this->pathIsBlocked($profile, $path)) {
            return;
        }

        if (is_array($value)) {
            if ($value === []) {
                if ($this->isGeneratableCollectionPath($profile, $path)) {
                    $targets[] = $path;
                }

                return;
            }

            foreach ($value as $key => $item) {
                $this->collectTargetPaths($profile, $item, "{$path}.{$key}", $overwrite, $targets);
            }

            return;
        }

        if (! is_string($value) && $value !== null) {
            return;
        }

        if ($overwrite || trim((string) $value) === '') {
            $targets[] = $path;
        }
    }

    /** @return array<int, string> */
    private function allowedRoots(string $profile): array
    {
        return match ($profile) {
            'project' => ['name', 'title', 'description', 'seo_description', 'image_alt', 'technology', 'city', 'object_type', 'meta', 'scope', 'specs', 'challenges', 'solutions', 'process', 'results', 'seo', 'translations'],
            'service' => ['name', 'eyebrow', 'title', 'description', 'seo_description', 'keywords', 'highlights', 'industries', 'benefits', 'solutions', 'process', 'overview', 'warranty', 'sla', 'lead_form', 'translations'],
            'page' => ['title', 'excerpt', 'content', 'seo_title', 'seo_description', 'keywords', 'translations'],
            'about' => ['about_page_translations'],
            'faq' => ['question', 'answer', 'title', 'description', 'translations'],
            'local-seo' => ['location_name', 'eyebrow', 'title', 'excerpt', 'content', 'benefits', 'faq', 'cta_title', 'cta_text', 'primary_keyword', 'keywords', 'seo_title', 'seo_description', 'translations'],
            'category' => ['name', 'seo_title', 'seo_description', 'seo_keywords', 'intro_text', 'faq', 'translations'],
            'seo-page' => ['title', 'description', 'keywords', 'translations'],
            'settings' => ['value', 'managed_page_translations', 'translations', 'footer', 'hero', 'cta'],
            'team-member' => ['first_name', 'last_name', 'position', 'bio', 'translations'],
            'testimonial' => ['quote', 'author', 'role', 'company', 'translations'],
            'partner' => ['name', 'category'],
            default => [],
        };
    }

    private function isGeneratableCollectionPath(string $profile, string $path): bool
    {
        $allowed = match ($profile) {
            'project' => ['meta', 'scope', 'specs', 'challenges', 'solutions', 'process', 'results', 'seo.keywords'],
            'service' => ['keywords', 'highlights', 'industries', 'benefits', 'solutions', 'process', 'translations.entries'],
            'page' => ['keywords'],
            'local-seo' => ['benefits', 'faq', 'keywords', 'translations.keywords.en', 'translations.keywords.ru'],
            'category' => ['seo_keywords', 'faq', 'translations.keywords.ka', 'translations.keywords.en', 'translations.keywords.ru', 'translations.faq.ka', 'translations.faq.en', 'translations.faq.ru'],
            'seo-page' => ['keywords', 'translations.keywords.ka', 'translations.keywords.en', 'translations.keywords.ru'],
            'settings' => ['value.default_keywords'],
            default => [],
        };

        return in_array($path, $allowed, true);
    }

    private function isGeneratedValueComplete(string $profile, string $path, mixed $value): bool
    {
        if (is_string($value)) {
            return trim($value) !== '';
        }

        if (! is_array($value) || $value === []) {
            return false;
        }

        $requiredFields = match (true) {
            $profile === 'local-seo' && $path === 'benefits' => ['title', 'description', 'translations.en.title', 'translations.en.description', 'translations.ru.title', 'translations.ru.description'],
            $profile === 'local-seo' && $path === 'faq' => ['question', 'answer', 'translations.en.question', 'translations.en.answer', 'translations.ru.question', 'translations.ru.answer'],
            $profile === 'project' && in_array($path, ['meta', 'scope', 'specs'], true) => ['value', 'label', 'translations.en.value', 'translations.en.label', 'translations.ru.value', 'translations.ru.label'],
            $profile === 'project' && in_array($path, ['challenges', 'solutions', 'process'], true) => ['title', 'description', 'translations.en.title', 'translations.en.description', 'translations.ru.title', 'translations.ru.description'],
            $profile === 'project' && $path === 'results' => ['value', 'title', 'description', 'translations.en.value', 'translations.en.title', 'translations.en.description', 'translations.ru.value', 'translations.ru.title', 'translations.ru.description'],
            $profile === 'service' && in_array($path, ['benefits', 'solutions', 'process'], true) => ['title', 'description'],
            $profile === 'service' && $path === 'translations.entries' => ['key', 'ka', 'en', 'ru'],
            $profile === 'category' && ($path === 'faq' || str_starts_with($path, 'translations.faq.')) => ['question', 'answer'],
            default => [],
        };

        if ($requiredFields === []) {
            return collect($value)->contains(fn (mixed $item): bool => $this->containsNonEmptyValue($item));
        }

        foreach ($value as $item) {
            if (! is_array($item)) {
                return false;
            }

            foreach ($requiredFields as $requiredField) {
                $requiredValue = data_get($item, $requiredField);
                if (! is_string($requiredValue) || trim($requiredValue) === '') {
                    return false;
                }
            }
        }

        return true;
    }

    private function containsNonEmptyValue(mixed $value): bool
    {
        if (is_string($value)) {
            return trim($value) !== '';
        }

        if (! is_array($value)) {
            return false;
        }

        foreach ($value as $item) {
            if ($this->containsNonEmptyValue($item)) {
                return true;
            }
        }

        return false;
    }

    /** @param array<string, string|array<mixed>> $patches
     * @return array<string, mixed>
     */
    private function mergePatchValues(array $state, array $patches): array
    {
        foreach ($patches as $path => $value) {
            data_set($state, $path, $value);
        }

        return $state;
    }

    /** @param array<int, string> $missing */
    private function missingFieldsMessage(array $missing): string
    {
        $preview = implode(', ', array_slice($missing, 0, 6));
        $suffix = count($missing) > 6 ? '…' : '';

        return "AI-მ ყველა მოთხოვნილი ველი სრულად ვერ შეავსო ({$preview}{$suffix}). ცვლილებები არ გამოყენებულა — თავიდან სცადეთ უფრო ზუსტი ფაქტებით.";
    }

    /** @param array<string, mixed> $state
     * @return array<string, mixed>
     */
    private function safeContext(array $state): array
    {
        $blocked = ['password', 'token', 'secret', 'api_key', 'apiKey', 'schema', 'media', 'cover', 'services', 'gallery', 'published_at', 'created_at', 'updated_at'];

        $walk = function (mixed $value, string $path = '') use (&$walk, $blocked): mixed {
            if (is_array($value)) {
                $out = [];
                foreach ($value as $key => $item) {
                    $keyString = (string) $key;
                    $next = $path === '' ? $keyString : "{$path}.{$keyString}";
                    if ($this->pathContainsAny($next, $blocked)) {
                        continue;
                    }
                    $out[$key] = $walk($item, $next);
                }

                return $out;
            }

            if (is_string($value) && mb_strlen($value) > 6000) {
                return mb_substr($value, 0, 6000);
            }

            return $value;
        };

        return $walk($state);
    }

    /** @param array<string, mixed> $updates
     * @param  array<string, mixed>  $currentState
     * @return array<string, mixed>
     */
    private function sanitizeUpdates(string $profile, array $updates, array $currentState, bool $overwrite): array
    {
        $allowedRoots = $this->allowedRoots($profile);

        $filter = function (mixed $value, string $path = '') use (&$filter, $profile, $allowedRoots, $currentState, $overwrite): mixed {
            $root = Str::before($path, '.');
            if ($path !== '' && ! in_array($root, $allowedRoots, true)) {
                return null;
            }

            if ($path !== '' && $this->pathIsBlocked($profile, $path)) {
                return null;
            }

            if (is_array($value)) {
                $out = [];
                foreach ($value as $key => $item) {
                    $next = $path === '' ? (string) $key : "{$path}.{$key}";
                    $clean = $filter($item, $next);
                    if ($clean !== null) {
                        $out[$key] = $clean;
                    }
                }

                return $out;
            }

            if (! is_string($value) && ! is_null($value)) {
                return null;
            }

            $text = trim((string) $value);
            if ($text === '') {
                return null;
            }

            if (! $overwrite) {
                $existing = data_get($currentState, $path);
                if (is_string($existing) && trim($existing) !== '') {
                    return null;
                }
            }

            return mb_substr($text, 0, 12000);
        };

        return $filter($updates) ?: [];
    }

    private function pathIsBlocked(string $profile, string $path): bool
    {
        if ($profile === 'settings' && Str::before($path, '.') === 'managed_page_translations') {
            return false;
        }

        if ($profile === 'settings' && $path === 'value.whatsapp_message') {
            return false;
        }

        if ($profile === 'service' && preg_match('/^translations\.entries\.\d+\.key$/', $path) === 1) {
            return false;
        }

        if (Str::before($path, '.') === 'image_alt' || str_contains(Str::lower($path), 'imagealt') || str_contains(Str::lower($path), 'image_alt')) {
            return false;
        }

        if ($profile === 'service' && str_contains($path, 'lead_form')) {
            $leaf = Str::afterLast($path, '.');
            if (in_array($leaf, ['key', 'type', 'required', 'min', 'max', 'step', 'default', 'value'], true)) {
                return true;
            }
        }

        return $this->pathContainsAny($path, [
            'slug', 'url', 'email', 'phone', 'whatsapp', 'telegram', 'facebook', 'instagram', 'linkedin', 'youtube',
            'password', 'token', 'secret', 'api', 'key', 'id', 'icon', 'accent', 'media', 'cover', 'image', 'file',
            'published', 'noindex', 'sort', 'order', 'date', 'time', 'currency', 'price', 'cost', 'amount', 'lat', 'lng',
            'latitude', 'longitude', 'schema', 'featured', 'recommended', 'enabled', 'quantity', 'model', 'href',
        ]);
    }

    /** @param array<string, mixed> $updates */
    public function mergeIntoState(array $currentState, array $updates): array
    {
        foreach ($this->flatten($updates) as $path => $value) {
            data_set($currentState, $path, $value);
        }

        return $currentState;
    }

    /** @return array<string, mixed> */
    private function flatten(array $array, string $prefix = ''): array
    {
        $flat = [];

        foreach ($array as $key => $value) {
            $path = $prefix === '' ? (string) $key : "{$prefix}.{$key}";

            if (is_array($value) && ! array_is_list($value)) {
                $flat += $this->flatten($value, $path);

                continue;
            }

            $flat[$path] = $value;
        }

        return $flat;
    }

    private function pathContainsAny(string $path, array $tokens): bool
    {
        $segments = preg_split('/[._\-\s]+/', Str::lower($path)) ?: [];

        foreach ($tokens as $token) {
            $token = Str::lower((string) $token);
            if ($token !== '' && in_array($token, $segments, true)) {
                return true;
            }
        }

        return false;
    }

    private function http(string $apiKey): PendingRequest
    {
        return Http::withToken($apiKey)
            ->acceptJson()
            ->asJson()
            ->connectTimeout(10)
            ->timeout(120)
            ->retry(2, 300, throw: false);
    }

    /** @param array<string, mixed> $response */
    private function extractText(array $response): string
    {
        foreach ($response['output'] ?? [] as $item) {
            if (! is_array($item) || ($item['type'] ?? null) !== 'message') {
                continue;
            }

            foreach ($item['content'] ?? [] as $part) {
                if (is_array($part) && ($part['type'] ?? null) === 'output_text') {
                    $text = trim((string) ($part['text'] ?? ''));
                    if ($text !== '') {
                        return $text;
                    }
                }
            }
        }

        return '';
    }

    /** @return array<string, mixed> */
    private function decodeJsonObject(string $text): array
    {
        $decoded = json_decode(trim($text), true);

        if (is_array($decoded)) {
            return $decoded;
        }

        throw new RuntimeException('AI response is not valid JSON.');
    }
}
