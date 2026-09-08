<?php

return [
    'driver'   => env('SESSION_DRIVER', 'file'),
    'lifetime' => (int) env('SESSION_LIFETIME', 120),
    'secure'   => env('SESSION_SECURE', true),
    'path'     => '/',
    'domain'   => null,
    'samesite' => 'Strict',
    'httponly' => true,
    'save_path' => dirname(__DIR__) . '/storage/sessions',
];
