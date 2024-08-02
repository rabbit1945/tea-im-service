<?php
declare (strict_types = 1);

namespace app\api\listener;

use app\common\utils\JwToken;
use app\service\WebSocketService;
use Swoole\Server;
use think\Container;
use think\swoole\Websocket;


class WebsocketClose  extends WebSocketService
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
     * @param $reactorId
     */
    public function handle($reactorId)
    {
        if ($reactorId == -1) {
            $this->server->close($this->websocket->getSender());

        }

    }

}
