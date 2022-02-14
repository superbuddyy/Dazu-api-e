<?php

return [
    'pagination' => [
        'per_page' => 24,
    ],
    'offer' => [
        'expire_time' => 168 // hours
    ],
    'admin' => [
        'email' => env('ADMIN_EMAIL', 'admin@dazu.app')
    ],
    'company_info' => [
        'name' => 'Dazu.pl',
        'email' => 'info@dazu.app',
        'nip' => '123123123',
    ],
    'recaptcha' => [
        'secret' => env('CAPTCHA_SECRET', '6LdeEN4UAAAAAFiL15UVm-Kg023wTRgPJBdXVFey'),
    ],
    'frontend_url' => env('FRONT_URL'),
    'email' => [
        'from_address' => env('MAIL_FROM_ADDRESS'),
        'from_name' => env('MAIL_FROM_NAME'),
    ],
];
