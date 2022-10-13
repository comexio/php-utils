<?php

return [
    'default' => 'test',
    'channels' => [
        'test' => [
            'driver' => 'single',
            'path' => storage_path('logs/lumen.log'),
            'days' => 14,
        ],
        'json_formatter' => [
            'driver' => 'single',
            'path' => storage_path('logs/json_lumen.log'),
            'formatter' => \Logcomex\PhpUtils\Loggers\LoggerFormatterJson::class,
            'days' => 14,
        ],
    ],
];
