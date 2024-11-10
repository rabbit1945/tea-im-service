<?php

declare (strict_types = 1);

namespace app\api\listener;

use app\common\utils\JwToken;
use app\service\WebSocketService;
use Swoole\Server;
use think\App;
use think\Container;
use think\swoole\Websocket;

class WebsocketDisconnect extends WebSocketService
{
    /**
     * @return void
     */


    public function __construct(Server $server,Websocket $websocket,JwToken $jwToken)
    {
        parent::__construct( $server, $websocket,$jwToken);
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
