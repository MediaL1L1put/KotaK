<?php

return [
    'shop_id' => env('YOOKASSA_SHOP_ID', '1224152'),
    'secret_key' => env('YOOKASSA_SECRET_KEY', 'test_TQmJq6_24Ty0Z46F7Y6kejxPCZ7VnhHrJ8l8i0VO508'),
    'return_url' => env('YOOKASSA_RETURN_URL', 'http://localhost:8000/payment/success'),
    'webhook_url' => env('YOOKASSA_WEBHOOK_URL', 'http://localhost:8000/payment/webhook'),
    'currency' => 'RUB',
    'default_description' => 'Оплата на ледовом катке',
    
    // КРИТИЧЕСКОЕ ИСПРАВЛЕНИЕ: отключаем чек для тестового режима
    'receipt_required' => false,
    
    'vat_code' => 1,
    'tax_system_code' => 1,
    
    'timeout' => 30,
    'attempts' => 3,
    'delay' => 1,
    
    'logging' => [
        'enabled' => true,
        'path' => storage_path('logs/laravel.log'),
        'level' => 'debug',
    ],
    
    'test_mode' => true,
    'min_amount' => 1.00,
    'max_amount' => 10000.00,
    
    'payment_methods' => [],
    'auto_capture' => true,
    'save_payment_method' => false,
    
    'api_url' => 'https://api.yookassa.ru/v3/',
    
    'allowed_ips' => [
        '185.71.76.0/27',
        '185.71.77.0/27',
        '77.75.153.0/25',
        '77.75.156.11',
        '77.75.156.35',
        '77.75.154.128/25',
        '2a02:5180::/32',
    ],
];