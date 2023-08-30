<?php
declare (strict_types = 1);

namespace app\home\listener;

use app\service\WebSocketService;
use Swoole\Server;
use think\Container;
use think\swoole\Websocket;


class WebsocketConnect extends WebSocketService
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
     * @param $data
     */
    public function handle($data)
    {
        $this->connect($data);

    }

    public function connect($data){
        echo '连接成功，当前客户端:'.$this->websocket->getSender();



    }





}
