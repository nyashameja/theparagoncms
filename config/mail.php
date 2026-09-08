<?php

return [
    'mailer'      => env('MAIL_MAILER', 'smtp'),
    'host'        => env('MAIL_HOST', 'localhost'),
    'port'        => env('MAIL_PORT', 587),
    'username'    => env('MAIL_USERNAME', ''),
    'password'    => env('MAIL_PASSWORD', ''),
    'encryption'  => env('MAIL_ENCRYPTION', 'tls'),
    'from'        => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@theparagondesign.com'),
        'name'    => env('MAIL_FROM_NAME', 'The Paragon .Design'),
    ],
    'retry_attempts' => 3,
    'retry_delay'    => 60,
];
