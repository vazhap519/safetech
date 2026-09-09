<?php

namespace App\Application\Ai;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

final class CmsContentGenerator
{
    /**
     * Generate safe content updates for a Filament form.
     *
     * AI is allowed to write marketing/editorial copy and translations only.
     * Identifiers, media, prices, contact data, publishing state and technical
     * facts that were not supplied by the editor are never accepted.
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

        $model = trim((string) config('services.openai.model', 'gpt-5.6')) ?: 'gpt-5.6';
        $safeState = $this->safeContext($currentState);

        $payload = [
            'model' => $model,
            'instructions' => $this->instructions($profile, $overwrite),
            'input' => [[
                'role' => 'user',
                'content' => "EDITOR FACTS:\n{$facts}\n\nCURRENT FORM STATE (context only):\n".
                    json_encode($safeState, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),
            ]],
            'reasoning' => ['effort' => 'none'],
            'store' => false,
            'max_output_tokens' => 7000,
        ];

        $response = $this->http($apiKey)
            ->post('https://api.openai.com/v1/responses', $payload);

        if ($response->failed()) {
            $message = trim((string) data_get($response->json(), 'error.message'));
            throw new RuntimeException($message !== '' ? $message : 'OpenAI content generation failed.');
        }

        $text = $this->extractText($response->json());
        $decoded = $this->decodeJsonObject($text);
        $updates = data_get($decoded, 'updates', $decoded);

        if (! is_array($updates)) {
            throw new RuntimeException('AI-მ ვალიდური შევსების მონაცემები ვერ დააბრუნა.');
        }

        return $this->sanitizeUpdates($profile, $updates, $currentState, $overwrite);
    }

    private function instructions(string $profile, bool $overwrite): string
    {
        $profileRules = match ($profile) {
            'project' => <<<'RULES'
PROJECT profile:
- Build a professional case study from the supplied facts.
- You may generate: name, title, description, seo_description, image_alt, technology, city, object_type, KA/EN/RU translations, meta, scope, specs, challenges, solutions, process and results.
- meta/scope/specs items use {value,label,translations:{en:{value,label},ru:{value,label}}}.
- challenges/solutions items use {icon,title,description,featured,translations:{en:{title,description},ru:{title,description}}}.
- process items use {title,description,translations:{en:{title,description},ru:{title,description}}}.
- results items use {value,title,description,accent,translations:{en:{value,title,description},ru:{value,title,description}}}.
- Prefer 3-5 useful items per section. Do not add equipment/models/specifications not present in EDITOR FACTS.
RULES,
            'service' => <<<'RULES'
SERVICE profile:
- Produce conversion-focused, technically credible service copy in KA/EN/RU.
- You may generate main copy, SEO copy, keywords, highlights, industries, brands only when brands are explicitly supplied, benefits, solutions, process, warranty/SLA wording only when the editor supplied warranty/SLA facts, and textual lead-form labels/placeholders/options.
- Never invent prices, discounts, warranty periods, response times, package prices, brands, technical limits or availability.
RULES,
            'page' => <<<'RULES'
PAGE profile:
- Generate title, excerpt, body content, SEO title/description/keywords and KA/EN/RU translations.
- Preserve legal meaning for privacy/terms pages. Never invent legal promises, company registration facts or contact details.
RULES,
            'about' => <<<'RULES'
ABOUT profile:
- Fill the existing about_page_translations fields in KA/EN/RU with consistent company positioning, story, capabilities, process and CTA copy.
- Use only facts provided by the editor/current form. Do not invent years in business, team size, certifications, partner status, client counts or guarantees.
RULES,
            'faq' => <<<'RULES'
FAQ profile:
- Generate concise customer question/answer copy and KA/EN/RU translations from the supplied facts.
- Do not invent prices, guarantees, service areas or technical claims.
RULES,
            'seo' => <<<'RULES'
SEO profile:
- Generate search-intent aligned title, heading/copy, meta description, keywords and KA/EN/RU translations using only supplied facts.
- Avoid keyword stuffing and doorway-page copy. Make local text genuinely useful and distinct.
RULES,
            'settings' => <<<'RULES'
SETTINGS/CONTACT profile:
- Fill only editorial text and translation fields already present in the current form.
- Never change phone numbers, email addresses, URLs, social links, addresses, coordinates, API values, IDs, booleans or operational settings.
RULES,
            default => <<<'RULES'
GENERIC profile:
- Fill only editorial text fields and translations already present in the current form.
- Never change identifiers, media, contact details, prices, dates, booleans or operational settings.
RULES,
        };

        $overwriteRule = $overwrite
            ? 'You MAY rewrite existing editorial text when it improves quality, but must preserve all factual meaning.'
            : 'Prefer filling empty editorial fields. Keep good existing copy unchanged unless a translation is missing.';

        return <<<PROMPT
You are SafeTech Georgia's CMS copywriter, SEO specialist, SMM strategist, sales copywriter, translator and QA editor.

Your job is to turn EDITOR FACTS into production-ready website content in Georgian (KA), English (EN) and Russian (RU).

NON-NEGOTIABLE QA RULES:
- Never invent a technical fact, product model, quantity, price, warranty, SLA, location, certification, client result, statistic, date or company claim.
- Numbers and product names from EDITOR FACTS must remain exact across all languages.
- If a fact was not supplied and cannot safely be inferred as generic wording, omit it.
- Georgian should read naturally and professionally, not like literal machine translation.
- English and Russian must carry the same factual meaning as Georgian.
- SEO titles should normally stay concise; meta descriptions should be compelling and natural, not stuffed with keywords.
- No hashtags in website copy.
- No fake reviews, fake urgency or unverifiable superlatives.
- Do not change slugs, URLs, IDs, category IDs, icons, accents, media, publishing flags, sort order, dates, emails, phone numbers, prices or schema JSON.
- {$overwriteRule}

{$profileRules}

OUTPUT FORMAT:
Return ONLY valid JSON, with no Markdown fences and no commentary.
Use this top-level shape:
{"updates": { ... }}
The updates object must mirror the form paths/structures you want to change. Do not include unsafe or irrelevant fields.
PROMPT;
    }

    /** @param array<string, mixed> $state
     * @return array<string, mixed>
     */
    private function safeContext(array $state): array
    {
        $blocked = [
            'password', 'token', 'secret', 'api_key', 'apiKey', 'schema', 'media', 'cover',
            'services', 'gallery', 'published_at', 'created_at', 'updated_at',
        ];

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

    /**
     * @param array<string, mixed> $updates
     * @param array<string, mixed> $currentState
     * @return array<string, mixed>
     */
    private function sanitizeUpdates(string $profile, array $updates, array $currentState, bool $overwrite): array
    {
        $allowedRoots = match ($profile) {
            'project' => ['name','title','description','seo_description','image_alt','technology','city','object_type','translations','meta','scope','specs','challenges','solutions','process','results'],
            'service' => ['name','eyebrow','title','description','seo_description','keywords','highlights','industries','brands','translations','benefits','solutions','process','warranty','sla','lead_form'],
            'page' => ['title','excerpt','content','seo_title','seo_description','keywords','translations'],
            'about' => ['about_page_translations'],
            'faq' => ['question','answer','title','description','translations'],
            'seo' => ['title','heading','description','content','seo_title','seo_description','keywords','translations','location_name'],
            'settings' => ['value','translations','contact','footer','hero','cta'],
            default => array_keys($currentState),
        };

        $blockedTokens = [
            'slug','url','email','phone','whatsapp','telegram','facebook','instagram','linkedin','youtube',
            'password','token','secret','api','key','id','icon','accent','media','cover','image_file','file',
            'published','noindex','sort','order','date','time','currency','price','cost','amount','lat','lng',
            'latitude','longitude','schema','featured','recommended','enabled','quantity','model',
        ];

        $filter = function (mixed $value, string $path = '') use (&$filter, $allowedRoots, $blockedTokens, $currentState, $overwrite): mixed {
            $root = Str::before($path, '.');
            if ($path !== '' && ! in_array($root, $allowedRoots, true)) {
                return null;
            }

            if ($path !== '' && $this->pathContainsAny($path, $blockedTokens)) {
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
        $normalized = Str::lower(str_replace(['-', ' '], '_', $path));

        foreach ($tokens as $token) {
            $token = Str::lower((string) $token);
            if ($token !== '' && str_contains($normalized, $token)) {
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
            ->timeout(90)
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
        $text = trim($text);
        $text = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $text) ?? $text;
        $decoded = json_decode($text, true);

        if (is_array($decoded)) {
            return $decoded;
        }

        $start = strpos($text, '{');
        $end = strrpos($text, '}');

        if ($start !== false && $end !== false && $end > $start) {
            $decoded = json_decode(substr($text, $start, $end - $start + 1), true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        throw new RuntimeException('AI response is not valid JSON.');
    }
}
