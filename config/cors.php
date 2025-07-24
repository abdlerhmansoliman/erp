<?php




return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'], // ← كل أنواع الطلبات: GET, POST, PUT...

    'allowed_origins' => ['http://localhost:5173'], // ← اسم المضيف + البورت تبع Vite

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // ← كل الهيدرز مسموحة

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // ← ضروري لو بتستخدم login/session/cookies


];
