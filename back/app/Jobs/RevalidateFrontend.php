<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class RevalidateFrontend implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [5, 30, 120];

    public function __construct(
        public string $frontendUrl,
        public string $secret,
        public ?string $tag = null,
        public ?string $path = null,
    ) {}

    public function handle(): void
    {
        try {
            $response = Http::connectTimeout(1)
                ->timeout(3)
                ->withHeaders(['x-secret' => $this->secret])
                ->post("{$this->frontendUrl}/api/revalidate", array_filter([
                    'tag' => $this->tag,
                    'path' => $this->path,
                ], fn ($value) => $value !== null && $value !== ''));

            if (! $response->successful()) {
                Log::warning('Frontend cache revalidation request failed.', [
                    'status' => $response->status(),
                    'tag' => $this->tag,
                    'path' => $this->path,
                ]);

                $response->throw();
            }
        } catch (Throwable $exception) {
            Log::warning('Frontend cache revalidation could not be delivered.', [
                'tag' => $this->tag,
                'path' => $this->path,
                'exception' => $exception::class,
            ]);

            throw $exception;
        }
    }
}
