<?php


namespace app\api\base;

use Swoole\Server;
use think\App;
use think\Event;
use think\swoole\Websocket;
use think\swoole\websocket\Room;

class BaseWebsocket extends Websocket
{
    public function __construct(App $app, Server $server, Room $room, Event $event)
    {
        parent::__construct($app, $server, $room, $event);
    }

}
