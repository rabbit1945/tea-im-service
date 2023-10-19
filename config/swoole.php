<?php

use think\swoole\websocket\socketio\Handler;
use think\swoole\websocket\socketio\Parser;

return [
    'server'     => [
        'host'      => env('SWOOLE_HOST', '192.168.1.12'), // 监听地址
        'port'      => env('SWOOLE_PORT', 9502), // 监听端口
        'mode'      => SWOOLE_PROCESS, // 运行模式 默认为SWOOLE_PROCESS
        'sock_type' => SWOOLE_SOCK_TCP| SWOOLE_SSL, // sock type 默认为SWOOLE_SOCK_TCP，SWOOLE_SSL 支持 https

        // 参数
        'options'   => [
            'pid_file'              => runtime_path() . 'swoole.pid',
            'log_file'              => runtime_path() . 'swoole.log',
            'daemonize'             => true,  // 开启守护进程
            // Normally this value should be 1~4 times larger according to your cpu cores.
            'reactor_num'           => swoole_cpu_num()*4,// 线程数
            'worker_num'            => swoole_cpu_num()*4,// 进程数
            'task_worker_num'       => swoole_cpu_num(),// 任务进程
            'enable_static_handler' => true,
            'document_root'         => root_path('public'),
            'package_max_length'    => 3 * 1024 * 1024, // 3M  最大数据包 默认 2M
            'buffer_output_size'    => 32 * 1024 * 1024, // 32M 配置发送输出缓存区内存尺寸【默认值：2M】
            'socket_buffer_size'    => 128* 1024 * 1024,  // 128M 配置客户端连接的缓存区长度。【默认值：2M】
            'heartbeat_idle_time' => 6,
            'heartbeat_check_interval' => 3,
            'compression_min_length' => 20,
            'open_eof_split' => true,
            'package_eof' => "\r\n",
            //配置SSL证书和密钥路径
            'ssl_cert_file' => "../.docker/nginx/conf.d/cert/scs1695721843916_xiaogongtx.com_server.crt",
            'ssl_key_file'  => "../.docker/nginx/conf.d/cert/scs1695721843916_xiaogongtx.com_server.key",


        ],
    ],
    'websocket'  => [
        'enable'        => true,
        'handler'       => Handler::class,
        'parser'        => Parser::class,
        'ping_interval' => 1000,
        'ping_timeout'  => 2000,
        'room'          => [
            'type'  => 'redis',
            'table' => [
                'room_rows'   => 4096,
                'room_size'   => 2048,
                'client_rows' => 8192,
                'client_size' => 2048,
            ],
            'redis' => [
                'host'          => '192.168.1.11',
                'password'   => '123456',
                'port'          => 6379,
                'max_active'    => 3,
                'max_wait_time' => 5,
            ],
        ],
        'listen'      => [
            'open'    => app\api\listener\WebsocketOpen::class, // 判断协议 是 http轮询或ws
            'connect' =>  app\api\listener\WebsocketConnect::class,//连接成功
            'message' =>  app\api\listener\WebsocketMessage::class,// 处理消息
            'event'   => app\api\listener\WebsocketEvent::class, //重要的事件监听类 处理消息
            'disconnect' => app\api\listener\WebsocketDisconnect::class, // 断开连接
            'close'   => app\api\listener\WebsocketClose::class, // 关闭连接
        ],
        'subscribe'     => [],
    ],
    'rpc'        => [
        'server' => [
            'enable'   => false,
            'port'     => 9000,
            'services' => [
            ],
        ],
        'client' => [
        ],
    ],
    'hot_update' => [
        'enable'  => env('APP_DEBUG', false),
        'name'    => ['*.php'],
        'include' => [app_path()],
        'exclude' => [],
    ],
    //连接池
    'pool'       => [
        'db'    => [
            'enable'        => true,
            'max_active'    => 30,
            'max_wait_time' => 5,
        ],
        'cache' => [
            'enable'        => true,
            'max_active'    => 30,
            'max_wait_time' => 5,
        ],
        //自定义连接池
    ],
    //队列
    'queue'      => [
        'enable'  => false,
        'workers' => [],
    ],
    // 协程
    'coroutine'  => [
        'enable' => true,
        'flags'  => SWOOLE_HOOK_ALL,
    ],
    'tables'     => [],
    //每个worker里需要预加载以共用的实例
    'concretes'  => [],
    //重置器
    'resetters'  => [],
    //每次请求前需要清空的实例
    'instances'  => [],
    //每次请求前需要重新执行的服务
    'services'   => [],
];
