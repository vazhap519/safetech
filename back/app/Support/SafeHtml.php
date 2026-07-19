<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

final class SafeHtml
{
    private HtmlSanitizer $sanitizer;

    public function __construct()
    {
        $config = (new HtmlSanitizerConfig())
            ->allowSafeElements()
            ->allowRelativeLinks()
            ->allowRelativeMedias()
            ->withMaxInputLength(1_000_000);

        $this->sanitizer = new HtmlSanitizer($config);
    }

    public function sanitize(mixed $html): string
    {
        return is_string($html) ? $this->sanitizer->sanitize($html) : '';
    }
}
