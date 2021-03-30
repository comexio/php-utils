<?php

return [
    'default' => 'test',
    'channels' => [
        'test' => [
            'driver' => 'single',
            'path' => storage_path('logs/lumen.log'),
            'days' => 14,
        ],
    ],
];
