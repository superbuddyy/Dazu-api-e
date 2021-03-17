<?php

return [
    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID', 'AbzVsKsUCUVudsQScymhpyDW58WcWpVGhivoKr4cTVioLBumEcEc5ZqGgJd80pVxSBAUD4fdjtkJz7By'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET', 'EMZiDhMmsLeVUfyMWPm5DPWdvVqhG6BfEwp0hTI1hyKCcAbIiwWUpaxQsGL4eWUT1qJfuaHDfC3qpOdH'),
        'return_url' => env('PAYPAL_RETURN_URL', env('APP_URL') . '/api/payments/callback'),
        'cancel_url' => env('PAYPAL_CANCEL_URL', env('FRONT_URL') . '?payment-status=fail'),
    ],
];
