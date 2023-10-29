<?php
declare (strict_types = 1);

namespace app\api\listener;
use app\api\business\MessageBusiness;
use app\common\utils\ImJson;
use app\api\business\MessageSendBusiness;
use app\api\business\UserBusiness;
use app\job\SendMessage;
use app\service\AiService;
use app\service\JsonService;
use think\Container;
use think\facade\Config;
use think\facade\Log;
use think\swoole\Websocket;

class WebsocketEvent
{
    public $websocket = null;

    public $jsontoArray = [];


    /**
     * @var false|mixed
     */


    public function __construct(Container $container)
    {
        $this->websocket = $container->make(Websocket::class);

        $this->jsontoArray = app()->make(JsonService::class);

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
        Log::write(date('Y-m-d H:i:s').'_event_'.json_encode($event),'info');
        $sendContext = $event['data'][0];
        $msg = $sendContext['msg'];
        $sendContext["contactList"] = $sendContext['contactList'] ?? [];
        $contactList = $sendContext["contactList"];
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
        $sendContext['file_name'] = $sendContext['file_name'] ?? "";
        $sendContext['file_size'] = $sendContext['file_size'] ?? "";
        $sendContext['md5'] = $sendContext['md5'] ?? "";
        $sendContext['original_file_name'] = $sendContext['original_file_name'] ?? "";
        $getContext = $sendBus->getContext($sendContext,$this->websocket->getSender());

        $send = $this->websocket->to($room)->emit('roomCallback',
            $getContext
        );
        if ($send) {
            app()->make(SendMessage::class)->send($getContext);
            Log::write(date('Y-m-d H:i:s').'_机器人_'.json_encode($sendContext),'info');
            $this->robot($contactList,$sendContext,$msg);
        }
    }


    /**
     * @param $event
     * @return
     */
    public function chunkFile($event)
    {
        $CallbackEvent = 'chunkFileCallback';  // 回调名称
        $sendContext = $event['data'][0];
        if (!$sendContext) return  $this->setSender($CallbackEvent,ImJson::outData(20003));
        $md5 = $sendContext['identifier'];  // md5
        $room_id = (string)$sendContext['room_id'];
        $filename = $sendContext['filename']; // 文件名称
        $totalChunks =$sendContext['totalChunks']; // 分片总数量
        $chunkNumber = $sendContext['chunkNumber']+1; // 当前分片数量
        $totalSize = $sendContext['totalSize']; // 总 size
        $user_id = $sendContext['user_id']; // 用户id
        $uploadProgress = $sendContext['uploadProgress']; // 上传进度
        $chunkSize = $sendContext['chunkSize'];  // 分块size
        $file = $sendContext['file'];    // 二进制数据流
        $newFileName =  $sendContext['newFileName']; // 新的文件名称
        // 目录
        $dir ='files/'.$user_id."/".$md5."/";

        // 分块名称
        $chunk_filename = $chunkNumber.'_'.$newFileName;
        $proTem =  $dir.$chunk_filename;  //临时文件
        $messageBusiness = app()->make(MessageBusiness::class);
        if (!$messageBusiness->uploadFile($file,$proTem)) return  $this->setSender($CallbackEvent,ImJson::outData(20001));
        $upload_status = 0;
        if ($totalChunks == $chunkNumber) {
            $upload_status =  1;
        }
        return $this->websocket->to($room_id)->emit($CallbackEvent,
            [
                'filename'  => $filename,
                'totalSize' => $totalSize,
                'identifier'  =>$md5,
                'chunkPath' => $proTem,
                'totalChunks' => $totalChunks,
                'chunkNumber' => $chunkNumber,
                "uploadStatus" => $upload_status,
                "uploadProgress" => $uploadProgress,
                "chunkSize"  => $chunkSize,
                "newFileName" => $newFileName,
                "user_id"  => $user_id

            ]
        );

    }
    public function mergeFile($event)
    {
        $callbackEvent = "mergeFileCallback";
        $sendContext = $event['data'][0];
        if (!$sendContext) return  $this->setSender($callbackEvent,ImJson::outData(20003));
        $room_id = (string)$sendContext['room_id'];
        $totalChunks = (int)$sendContext['totalChunks'];
        $md5 = $sendContext['identifier'];  // md5
        $user_id = $sendContext['user_id'];
        $totalSize = (int)$sendContext['totalSize'];
        $newFileName =  $sendContext['newFileName']; // 文件名称
        $chunkSize = (int)$sendContext['chunkSize'];  // 每一块的大小
        $chunkDir = Config::get('filesystem.disks.public.root').'/files/'.$user_id."/".$md5."/";

        $mergePath =  Config::get('filesystem.disks.public.root').'/files/'.$user_id."/".$newFileName; // 合并文件
        if (!$out = @fopen($mergePath, "wb")) {
            return true;
        }
        if (flock($out, LOCK_EX) ) {
            // 分块文件
            for ($i=1; $i<=$totalChunks; $i++) {
                $val = $chunkDir.$i."_".$newFileName;

                if (!$in = @fopen($val, "rb")) {
                    Log::write(date('Y-m-d H:i:s').'_权限_'.json_encode($val),'info');
                    $mergeFileStatus = 2; // 文件没有权限
                    $this->websocket->to($room_id)->emit($callbackEvent,
                        [
                            "mergeNum" =>  $i,
                            "mergeFileStatus" => $mergeFileStatus,
                            'identifier'  =>$md5,
                            "newFileName"  => $newFileName,
                            'chunkNumber' => $i,
                            'totalSize' => $totalSize
                        ]
                    );
                    break;


                }
                Log::write(date('Y-m-d H:i:s').'_分块_'.json_encode($val),'info');
                while ($buff = fread($in, $chunkSize)) {
                    fwrite($out, $buff);
                }
                @fclose($in);
                $upload_status = 2;
                if ($totalChunks ==  $i) {
                    $upload_status =  3;
                }
                @unlink($val); //删除分片
                $this->websocket->to($room_id)->emit($callbackEvent,
                    [
                        "mergeNum" =>  $i,
                        "uploadStatus" => $upload_status,
                        'identifier'  =>$md5,
                        "newFileName"  => $newFileName,
                        "totalChunks"  => $totalChunks,
                        'chunkNumber' => $i,
                        'totalSize' => $totalSize

                    ]
                );
            }

            flock($out, LOCK_UN); // 释放锁


        }
        @fclose($out);


    }

    /**
     * 修改消息
     * @return
     */
    public function updateMsgStatus($event)
    {

        $callbackEvent = "updateMsgStatusCallback";
        $sendContext = $event['data'][0];
        if (!$sendContext) return  $this->setSender($callbackEvent,ImJson::outData(20003));
        $totalChunks = (int)$sendContext['totalChunks']; // 分片总数量
        $chunkNumber = $sendContext['chunkNumber'];// 当前分片数量
        $totalSize    = $sendContext['totalSize'];
        $uploadStatus = $sendContext['uploadStatus'];
        $newFileName =  $sendContext['newFileName']; // 文件名称
        $room_id = $sendContext['room_id'];
        $md5 = $sendContext['identifier'];  // md5

        $where = [
            "md5"       => $md5,
            "room_id"   => $room_id,
            "file_name" => $newFileName
        ];
        $updateData = [
            "upload_status" => $uploadStatus
        ];
        $data = [
            "room_id"      => $room_id,
            "identifier"   => $md5,
            "newFileName"  => $newFileName,
            "uploadStatus" => $uploadStatus,
            "totalChunks"  => $totalChunks,
            "chunkNumber"  => $chunkNumber,
            "totalSize"    => $totalSize
        ];
        if ($totalChunks > $chunkNumber)  return  $this->setSender($callbackEvent,ImJson::outData(20018,"",$data));
        $messageBusiness = app()->make(MessageBusiness::class);
        $save =  $messageBusiness->save($where,$updateData);
        if (!$save)  return  $this->setSender($callbackEvent,ImJson::outData(20017,"",$data));

        Log::write(date('Y-m-d H:i:s').'_$save_'.json_encode($save),'info');
        return $this->websocket->to($room_id)->emit($callbackEvent,ImJson::outData(10000,'成功',$data));

    }


    function unicodeToChn($str){
        $pattern = '/\\\\u([0-9a-f]{4})/i';
        $result = preg_replace_callback($pattern, function($matches){
            return iconv('UCS-2', 'UTF-8', hex2bin($matches[1]));
        }, $str);
        return $result;
    }




    public function setSender($event,$data)
    {
        $fd =  $this->websocket->getSender();
        return $this->websocket->setSender($fd)->emit($event,$data);
    }




    /**
     * @param $name
     * @param $arguments
     * @return void
     */
    public function __call($name,$arguments)
    {
        $this->websocket->emit('error',['code'=>'20404','msg'=>'方法不存在:'.$name]);
    }
}
