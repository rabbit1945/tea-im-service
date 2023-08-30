<?php
declare (strict_types = 1);

namespace app\home\listener;
use app\common\utils\IdRedisGenerator;
use app\job\SendMessage;
use think\Container;
use think\facade\App;
use think\swoole\Websocket;

class WebsocketEvent
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
     * @param $event
     */
    public function handle($event)
    {
        $func = $event['type'];
        $this->$func($event);
    }

    /**
     * 向房间内的用户发送消息
     * @param $event
     */
    public function room($event)
    {
        $idGenerator =  app()->make(IdRedisGenerator::class);
        $idGenerator->generator('1',strtotime('2023-08-25')*1000);
        $seq = $idGenerator->getSequence();
        $room = (string)$event['data'][0]['room_id'];
        $time = floor(microtime(true) * 1000)| 0;
        $data =   [
            'room_id'   => $room,
            'seq' => $seq,
            'user_id'   => $event['data'][0]['user_id'], // user
            'nick_name' => $event['data'][0]['nick_name'], // 名称
            'userLogo' => $event['data'][0]['userLogo'], // img
            'sender' => $this->websocket->getSender(), //客户端
            'msg' => trim($event['data'][0]['msg']), // 消息
            'send_timestamp'=> $time,
            'send_time' => date("Y-m-d H:i:s",time())   // 发送时间
        ];

        $send = $this->websocket->to($room)->emit('roomCallback',
            $data
        );

        if ($send) {

           app()->make(SendMessage::class)->send($data);
        }
    }

    /**
     * 判断用户的状态
     * @param $event
     */
    public function sendUserStatus($event) {
        $array = [];
        $room = (string)$event['data'][0]['room_id'];

        $data =   [
            'room_id'   => $room,
            'user_id'   => $event['data'][0]['user_id'], // user
            'userStatus'=> $event['data'][0]['userStatus'],
        ];
        $array[] = $data;


        $this->websocket->to($room)->emit('pushUserStatus',
            $data
        );


    }


    public function __call($name,$arguments)
    {
        $this->websocket->emit('error',['code'=>'30001','msg'=>'方法不存在:'.$name]);
    }
}
