<?php

use App\Support\CmsMedia;

return [
    'temporary_file_upload' => [
        'disk' => 'local',
        'rules' => [
            'required',
            'file',
            'mimetypes:'.implode(',', CmsMedia::IMAGE_MIME_TYPES),
            'max:'.CmsMedia::MAX_FILE_SIZE_KB,
        ],
        'directory' => 'livewire-tmp',
        'middleware' => 'throttle:60,1',
        'preview_mimes' => [
            'jpg',
            'jpeg',
            'png',
            'webp',
            'avif',
        ],
        'max_upload_time' => 10,
        'cleanup' => true,
    ],
];
