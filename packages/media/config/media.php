<?php

declare(strict_types=1);

return [
    'disk' => 'public',
    'max_upload_size' => 10240,
    'image_variants' => [
        'thumbnail' => [150, 150],
        'medium' => [600, 600],
        'large' => [1200, 1200],
    ],
    'webp_quality' => 85,
];
