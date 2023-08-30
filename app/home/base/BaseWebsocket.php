<?php


namespace app\home\base;

use Swoole\Server;
use think\Event;
use think\swoole\Websocket;
use think\swoole\websocket\Room;

class BaseWebsocket extends Websocket
{
    public function __construct(\think\App $app, Server $server, Room $room, Event $event)
    {
        parent::__construct($app, $server, $room, $event);
    }

}
