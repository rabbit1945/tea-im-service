<?php
declare (strict_types = 1);

namespace app\home\listener;

use app\service\WebSocketService;
use Swoole\Server;
use think\Container;
use think\swoole\Websocket;


class WebsocketClose  extends WebSocketService
{


    /**
     * @var false|mixed
     */

    public function __construct(Server $server,Websocket $websocket,Container $container)
    {
        parent::__construct( $server, $websocket, $container);
    }

    /**
     * 事件监听处理
     * @param $reactorId
     */
    public function handle($reactorId)
    {
        echo "关闭连接:".$reactorId."userid:".$this->getUserId();
        if ($reactorId == -1) {
            $this->server->close($this->websocket->getSender());

        }

    }

}
