<?php

return [
    'merchant_id' => env('BAS_MERCHANT_ID', 'demo_merchant'),
    'api_key' => env('BAS_API_KEY', 'demo_api_key'),
    'secret_key' => env('BAS_SECRET_KEY', 'demo_secret_key'),
    'base_url' => env('BAS_BASE_URL', 'https://basgate.apidog.io'),
    'webhook_url' => env('BAS_WEBHOOK_URL', env('APP_URL') . '/api/v1/payments/bas/webhook'),
    'redirect_url' => env('BAS_REDIRECT_URL', env('APP_URL') . '/api/v1/payments/bas/callback'),
];
