<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

final class DeploymentInfo
{
    private const COMMIT_FILE = 'deployment/commit';

    public static function commit(): ?string
    {
        if (Storage::disk('local')->exists(self::COMMIT_FILE)) {
            $storedCommit = self::validCommit(
                (string) Storage::disk('local')->get(self::COMMIT_FILE),
            );

            if ($storedCommit !== null) {
                return $storedCommit;
            }
        }

        return self::gitCommit();
    }

    public static function writeCommit(string $commit): void
    {
        $commit = self::validCommit($commit);

        if ($commit === null) {
            return;
        }

        Storage::disk('local')->put(self::COMMIT_FILE, $commit."\n");
    }

    private static function gitCommit(): ?string
    {
        $gitDirectory = dirname(base_path()).'/.git';

        if (is_file($gitDirectory)) {
            $pointer = trim((string) file_get_contents($gitDirectory));

            if (str_starts_with($pointer, 'gitdir:')) {
                $gitDirectory = trim(substr($pointer, strlen('gitdir:')));
            }
        }

        $headPath = $gitDirectory.'/HEAD';

        if (! is_file($headPath)) {
            return null;
        }

        $head = trim((string) file_get_contents($headPath));
        $detachedCommit = self::validCommit($head);

        if ($detachedCommit !== null) {
            return $detachedCommit;
        }

        if (! str_starts_with($head, 'ref:')) {
            return null;
        }

        $reference = trim(substr($head, strlen('ref:')));
        $looseReference = $gitDirectory.'/'.$reference;

        if (is_file($looseReference)) {
            return self::validCommit((string) file_get_contents($looseReference));
        }

        $packedReferences = $gitDirectory.'/packed-refs';

        if (! is_file($packedReferences)) {
            return null;
        }

        foreach (file($packedReferences, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            if (str_starts_with($line, '#') || str_starts_with($line, '^')) {
                continue;
            }

            [$commit, $packedReference] = array_pad(preg_split('/\s+/', trim($line), 2) ?: [], 2, null);

            if ($packedReference === $reference && is_string($commit)) {
                return self::validCommit($commit);
            }
        }

        return null;
    }

    private static function validCommit(string $commit): ?string
    {
        $commit = strtolower(trim($commit));

        return preg_match('/^[0-9a-f]{40}$/', $commit) === 1 ? $commit : null;
    }

    private function __construct() {}
}
