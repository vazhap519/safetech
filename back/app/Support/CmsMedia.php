<?php

namespace App\Support;

final class CmsMedia
{
    public const MAX_FILE_SIZE_KB = 20 * 1024;

    /** @var array<int, string> */
    public const IMAGE_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/avif',
    ];

    private function __construct() {}
}
