<?php
declare (strict_types = 1);

namespace app\api\listener;

use app\common\utils\JwToken;
use app\service\WebSocketService;
use Swoole\Server;
use think\Container;
use think\swoole\Websocket;


class WebsocketConnect extends WebSocketService
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
