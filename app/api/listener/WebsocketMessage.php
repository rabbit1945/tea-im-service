<?php
declare (strict_types = 1);

namespace app\api\listener;

use app\common\utils\JwToken;
use app\service\WebSocketService;
use Swoole\Server;
use think\Container;

use think\swoole\Websocket;

class WebsocketMessage extends WebSocketService
{


    /**
     * @return false|mixed
     */


    public function __construct(Server $server,Websocket $websocket,JwToken $jwToken)
    {
        parent::__construct( $server, $websocket,$jwToken);
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

}
