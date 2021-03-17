<?php

return [
    'pagination' => [
        'per_page' => 24,
    ],
    'offer' => [
        'expire_time' => 168 // hours
    ],
    'admin' => [
        'email' => env('ADMIN_EMAIL', 'admin@dazu.pl')
    ],
    'company_info' => [
        'name' => 'Dazu sp. z o.o.',
        'email' => 'info@dazu.app',
        'nip' => '123123123',
    ],
    'recaptcha' => [
        'secret' => env('CAPTCHA_SECRET', '6LdeEN4UAAAAAFiL15UVm-Kg023wTRgPJBdXVFey'),
    ],
    'frontend_url' => env('FRONT_URL')
];
