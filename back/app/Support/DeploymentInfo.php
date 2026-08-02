<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

final class DeploymentInfo
{
    private const COMMIT_FILE = 'deployment/commit';

    public static function commit(): ?string
    {
        if (! Storage::disk('local')->exists(self::COMMIT_FILE)) {
            return null;
        }

        $commit = trim((string) Storage::disk('local')->get(self::COMMIT_FILE));

        return preg_match('/^[0-9a-f]{40}$/', $commit) === 1 ? $commit : null;
    }

    public static function writeCommit(string $commit): void
    {
        $commit = trim($commit);

        if (preg_match('/^[0-9a-f]{40}$/', $commit) !== 1) {
            return;
        }

        Storage::disk('local')->put(self::COMMIT_FILE, $commit."\n");
    }

    private function __construct() {}
}
