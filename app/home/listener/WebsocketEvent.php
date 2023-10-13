<?php
declare (strict_types = 1);

namespace app\home\listener;
use app\common\utils\IdRedisGenerator;
use app\common\utils\SensitiveWord;
use app\home\business\MessageReceiveBusiness;
use app\home\business\MessageSendBusiness;
use app\home\business\RoomUserBusiness;
use app\home\business\UserBusiness;
use app\job\SendMessage;
use app\service\AiService;
use app\service\JsonService;
use think\Container;
use think\facade\App;
use think\facade\Log;
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

    public function robot($contactList,$sendUser,$msg)
    {
        echo "=============机器人============";
        $aiService = app()->make(AiService::class);
        $json = app()->make(JsonService::class);

        $content = "";
        foreach ($contactList as $val) {
            $search_name = '@'.$val['nick_name'];
            $content =  str_replace($search_name,"",$msg);
        }
        if (!$content) return false;
        $sendBus = app()->make(MessageSendBusiness::class);
        $userBuss = app()->make(UserBusiness::class);
        $sendMessage = app()->make(SendMessage::class);
        foreach ($contactList as $val) {

            if ($val['is_robot'] === 1){

                $msgData = [
                    "user_id"=> (string)$sendUser['user_id'],
                    "messages"=> [
                        [
                            "role" => 'user',
                            "content"=> $content
                        ]
                    ]
                ];
                $jsonData =  $aiService->run($json->jsonEncode($msgData));
                if ($jsonData) {
                    $data = $json->jsonDecode($jsonData);
                    $result = isset($data['result']) ?'@'.$sendUser['nick_name'].' '.$data['result']: '@'.$sendUser['nick_name'].'请稍后重试';
                    if (empty($result)) return false;
                    $val['userLogo'] = $val['photo'];
                    $val['msg'] =  $result;
                    $val['content_type'] = 0;
                    $userInfo = $userBuss->find($sendUser['user_id'])->toArray();
                    if (!$userInfo) return false;
                    $userInfo["user_id"] = $sendUser['user_id'];
                    $val['contactList'] = [$userInfo];
                    $getContext = $sendBus->getContext($val);
                    if ($getContext) {
                        // @消息内容
                        $getContext['contactUserMsg'] =  [
                            "user_id" => $sendUser['user_id'],
                            "nick_name" => $sendUser['nick_name'],
                            "msg" => $sendUser['msg'],
                        ];
                        $room = (string)$val['room_id'];
                        Log::write(date('Y-m-d H:i:s').'_机器人_'.$room,'info');
                        $send = $this->websocket->to($room)->emit('roomCallback',
                            $getContext
                        );
                        if ($send) {
                            $sendMessage->send($getContext);

                        }

                    }


                }
            }
        }

    }

    /**
     * 向房间内的用户发送消息
     * @param $event
     */
    public function room($event)
    {
        $sendContext = $event['data'][0];
        $msg = $sendContext['msg'];
        $contactList = $sendContext['contactList'];
        if (!empty($contactList)) {
            $content = "";
            foreach ($contactList as $val) {
                $search_name = '@'.$val['nick_name'];
                $content =  str_replace($search_name,"",$msg);
            }
            if (!$content) return false;
        } else {
            if (empty($msg)) return false;
        }

        $room = (string)$sendContext['room_id'];
        $sendBus = app()->make(MessageSendBusiness::class);
        $getContext = $sendBus->getContext($sendContext,$this->websocket->getSender());

        $send = $this->websocket->to($room)->emit('roomCallback',
            $getContext
        );
        if ($send) {
            app()->make(SendMessage::class)->send($getContext);
            $msg = $sendContext['msg'];
            Log::write(date('Y-m-d H:i:s').'_机器人_'.json_encode($sendContext),'info');
            $this->robot($sendContext['contactList'],$sendContext,$msg);
        }
    }


    public function __call($name,$arguments)
    {
        $this->websocket->emit('error',['code'=>'404','msg'=>'方法不存在:'.$name]);
    }
}
