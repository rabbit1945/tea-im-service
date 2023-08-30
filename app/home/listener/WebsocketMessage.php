<?php
declare (strict_types = 1);

namespace app\home\listener;

use think\Container;

use think\swoole\Websocket;

class WebsocketMessage
{
    Public $websocket = null;


    /**
     * @var false|mixed
     */


    public function __construct(Container $container)
    {
        $this->websocket = $container->make(Websocket::class);
    }

    /**
     * 事件监听处理
     * @param $data
     */
    public function handle($data)
    {
        $this->message($data);

    }

    public function message($data)
    {
//        var_dump($data);


    }

    public function getUserState()
    {
        echo "用户状态";

    }
}
