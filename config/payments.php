<?php

return [
    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID', 'AQYhsmBUBp8_MBVeUdYpYvXfP7MU_sH6AUSWWuzdwlp48ZNqXYIOTIw73j1jjwErSjuwYlssrStQbYCx'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET', 'EFO6B-q6FgQbBw1gqacUiZ6QXg6yZhrDQ46VaFvEXDYRUTb-6B3C_MqI6L1UrUOifAOTXt05K9-OzU6F'),
        'return_url' => env('PAYPAL_RETURN_URL', env('APP_URL') . '/api/payments/callback'),
        'cancel_url' => env('PAYPAL_CANCEL_URL', env('FRONT_URL') . '?payment-status=fail'),
    ],
];
