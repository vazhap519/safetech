<?php

namespace Tests\Unit;

use App\Support\SafeHtml;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SafeHtmlTest extends TestCase
{
    #[Test]
    public function it_keeps_content_formatting_and_removes_executable_markup(): void
    {
        $html = '<p onclick="alert(1)">Useful <strong>content</strong>.</p>'
            .'<script>alert(1)</script>'
            .'<a href="javascript:alert(1)">Unsafe link</a>';

        $sanitized = app(SafeHtml::class)->sanitize($html);

        $this->assertStringContainsString('<strong>content</strong>', $sanitized);
        $this->assertStringNotContainsString('onclick', $sanitized);
        $this->assertStringNotContainsString('<script', $sanitized);
        $this->assertStringNotContainsString('javascript:', $sanitized);
    }
}
