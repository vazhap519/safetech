<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tests\TestCase;

class LivewireUploadProbeSignatureTest extends TestCase
{
    public function test_upload_probe_returns_a_valid_relative_livewire_signature(): void
    {
        $nonce = Str::uuid()->toString();
        $signature = hash_hmac('sha256', $nonce, (string) config('app.key'));

        $response = $this->withHeaders([
            'X-SafeTech-Upload-Probe-Nonce' => $nonce,
            'X-SafeTech-Upload-Probe-Signature' => $signature,
        ])->getJson('/_safetech/upload-probe');

        $response->assertOk();

        $signedUrl = (string) $response->json('livewire_upload_url');
        $this->assertNotSame('', $signedUrl);
        $this->assertStringContainsString('/livewire/upload-file', $signedUrl);

        $signedRequest = Request::create($signedUrl, 'POST');

        $this->assertTrue(
            $signedRequest->hasValidRelativeSignature(),
            'The upload probe must use the same relative signature that Livewire 3.8.7 validates.',
        );
    }
}
