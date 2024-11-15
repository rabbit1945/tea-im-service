<?php

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    // 默认缓存驱动
    'default' => env('cache.driver', 'redis'),

    // 缓存连接方式配置
    'stores'  => [
        'file' => [
            // 驱动方式
            'type'       => 'File',
            // 缓存保存目录
            'path'       => '../runtime/file/',
            // 缓存前缀
            'prefix'     => '',
            // 缓存有效期 0表示永久缓存
            'expire'     => 0,
            // 缓存标签前缀
            'tag_prefix' => 'tag:',
            // 序列化机制 例如 ['serialize', 'unserialize']
            'serialize'  => [],
        ],
        // 更多的缓存连接
        'redis'   =>  [
            // 驱动方式
            'type'   => 'redis',
            // 服务器地址
            'host'       => env('redis.host', '127.0.0.1'),
            //密码
            'password'   => env('redis.password', ''),
            //端口
            'port'       => env('redis.port', 6379),
            //数据库
            'select'     => 0,
            //超时时间
            'timeout'    => 0,
            //缓存有效期 （默认为0 表示永久缓存）
            'expire'     => 0,

            //缓存前缀（默认为空）
            'prefix'     => 'api_',

            'tag_prefix' => 'tag:',

            //缓存序列化和反序列化方法
            'serialize'  => [],
        ],

    ],
];
