<?php
return [
    // 存储列表
    'storage' => [
        // 腾讯云
        'cos' => [
            'secret_id'  => env('cos.secret_id', ''),
            'secret_key' => env('cos.secret_key', ''),
            'scheme'     => 'http',
            'region'     => env('cos.region', ''), // 地域
        ]
    ]
];