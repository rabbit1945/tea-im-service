<?php

declare (strict_types = 1);

namespace app\home\listener;

use think\Container;
use think\swoole\Websocket;

class WebsocketDisconnect
{
    public $websocket = null;


    /**
     * @var false|mixed
     */


    public function __construct(Container $container)
    {
        $this->websocket = $container->make(Websocket::class);
    }

    /**
     * 事件监听处理
     *
     */
    public function handle($error)
    {
        $this->disConnect($error);

    }

    public function disConnect($error)
    {
        echo "断开连接,当前客户端:".$error;

    }


}
