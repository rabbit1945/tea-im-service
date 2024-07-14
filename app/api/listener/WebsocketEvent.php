<?php
declare (strict_types = 1);

namespace app\api\listener;
use app\api\business\MessageBusiness;
use app\api\business\MessageSendBusiness;
use app\api\business\UploadBusiness;
use app\api\business\UserBusiness;
use app\common\utils\ImJson;
use app\common\utils\JwToken;
use app\common\utils\Upload;
use app\job\SendMessage;
use app\service\AiService;
use app\service\JsonService;
use app\service\RedisService;
use app\service\WebSocketService;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException as BindingResolutionExceptionAlias;
use Swoole\Server;
use think\App;
use think\facade\Config;
use think\facade\Log;
use think\swoole\Websocket;
use app\service\rabbitMq\EmitMq;
class WebsocketEvent  extends WebSocketService
{
    public array|JsonService $jsonToArray = [];


    /**
     * @var Upload
     */
    private Upload $upload;

    /**
     * @var App
     */
    private App $app;

    /**
     * @param App $app
     * @param Upload $upload
     * @param Websocket $websocket
     * @param JsonService $jsonService
     */
    public function __construct(Server $server,JwToken $jwToken,App $app,Upload $upload,Websocket $websocket, JsonService $jsonService)
    {
        parent::__construct($server, $websocket,$jwToken);
        $this->app = $app;
        $this->jsonToArray = $jsonService;
        $this->upload = $upload;
    }

    /**
     * 事件监听处理
     * @param $event
     */
    public function handle($event): void
    {
        $func = $event['type'];
        $this->$func($event);
    }

    /**
     * @throws Exception
     */
    public function robot($contactList, $sendUser, $msg)
    {
        $content = "";
        foreach ($contactList as $val) {
            $search_name = '@'.$val['nick_name'];
            $content =  str_replace($search_name,"",$msg);
        }
        if (!$content) return false;
        $sendBus = app()->make(MessageSendBusiness::class);
        $userBuss = app()->make(UserBusiness::class);
       // $sendMessage = app()->make(SendMessage::class);
        $aiService = app()->make(AiService::class);
        $aiService->setModel( 'app\service\ai\QianFan');
        $data =  $aiService->randomSend($content,$sendUser['user_id']);
        Log::write(date('Y-m-d H:i:s').'_机器人_'.json_encode($data,JSON_UNESCAPED_UNICODE),'info');
        $result = isset($data['result']) ?'@'.$sendUser['nick_name'].' '.$data['result']: '@'.$sendUser['nick_name'].'请稍后重试';
        if (empty($result)) return false;
        foreach ($contactList as $val) {
            if ($val['is_robot'] === 1){
                if ($data) {
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
                        $sendMsg = $this->sendMsg($getContext);
                        if (!$sendMsg) return false;
                        $this->websocket->to($room)->emit('roomCallback',
                            $getContext
                        );

                    }


                }
            }
        }

    }

    /**
     * 向房间内的用户发送消息
     * @param $event
     * @return false|void
     * @throws Exception
     */
    public function room($event)
    {
        Log::write(date('Y-m-d H:i:s').'_event_'.json_encode($event),'info');
        $sendContext = $event['data'][0];
        $user_id = $sendContext['user_id'];
        $msg = $sendContext['msg'];
        $contactList =  $sendContext['contactList'] ?? [];
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
        Log::write(date('Y-m-d H:i:s').'_getContext_'.json_encode($sendContext),'info');
        $room = (string)$sendContext['room_id'];
        $sendBus = app()->make(MessageSendBusiness::class);
        $sendContext['file_name'] = $sendContext['file_name'] ?? "";
        $sendContext['file_size'] = $sendContext['file_size'] ?? "";
        $sendContext['md5'] = $sendContext['md5'] ?? "";
        $sendContext['original_file_name'] = $sendContext['original_file_name'] ?? "";
        $getContext = $sendBus->getContext($sendContext,$this->websocket->getSender());
        $sendMsg = $this->sendMsg($getContext);
        Log::write(date('Y-m-d H:i:s').'_发送到rabbitmq:'.$sendMsg,'info');

        if (!$sendMsg) return false;
//        app()->make(SendMessage::class)->send($getContext);
        $send = $this->websocket->to($room)->emit('roomCallback',
            $getContext
        );
        if ($send) {
            if ($contactList) {
                $this->robot($contactList,$sendContext,$msg);
            }

        }
    }

    /**
     * @param $event
     * @return true
     */
    public function aiQuestionnaire($event): bool
    {
        $sendContext = $event['data'][0];

        // 正则表达式尝试匹配JSON对象或数组
        $jsonPattern = '/\{(?:[^{}]|(?R))*\}/';

        // 使用preg_match_all来查找所有匹配的JSON对象或数组
        preg_match_all($jsonPattern, $sendContext, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        $extractedJsons = [];
        foreach ($matches as $match) {
            // 提取匹配的JSON字符串并尝试解码
            $jsonStr = $match[0][0];
            if (empty($jsonStr)) continue;
            $decodedJson = json_decode($jsonStr, true);

            // 检查是否成功解码
            if (json_last_error() === JSON_ERROR_NONE) {
                $extractedJsons = $decodedJson;
            } else {
                echo "解析错误: " . json_last_error_msg() . " - JSON字符串: " . $jsonStr . "\n";
                $extractedJsons = json_decode(str_replace("，",",",$jsonStr),true);

            }
        }

        $extractedJsons = $extractedJsons ? JsonService::jsonDecode($extractedJsons): [];
        // 显示所有成功提取并解析的JSON数据
        Log::write(date('Y-m-d H:i:s').'_成功提取并解析的JSON数据_'.json_encode($extractedJsons,JSON_UNESCAPED_UNICODE),'info');

        $send = $this->websocket->emit('aiQuestionnaireCallback',
            $extractedJsons
        );
        if (!$send) return false;

    }

    /**
     * @param $getContext
     * @return bool
     * @throws BindingResolutionExceptionAlias
     */
    protected function sendMsg($getContext): bool
    {
        return $this->app->make(EmitMq::class)->sendMsg($getContext, 'messageExchange','MessageJobQueue','message.info');
    }


    /**
     * 大文件分片上传
     * @param $event
     * @return bool
     */
    public function chunkFile($event): bool
    {

        $callbackEvent = 'chunkFileCallback';  // 回调名称
        $sendContext = $event['data'][0];
        if (!$sendContext) return  $this->setSender($callbackEvent,ImJson::outData(20003));
        $seq = $sendContext['seq']; // 序列号
        $md5 = $sendContext['identifier'];  // md5
        $user_id = $sendContext['user_id']; // 用户id
        $chunkNumber = $sendContext['chunkNumber']+1; // 当前分片数量
        $newFileName = $sendContext['newFileName'];
        // 分块名称
        $chunkFileName = $chunkNumber.'_'.$newFileName;
        // 创建目录
        $dir ='files/'.$user_id."/".$seq."/";
        $file = $sendContext['file'];    // 二进制数据流

        // 缓存已经上传的文件数量
        $incr = $this->incr($sendContext['seq'],'chunkNumber');
        Log::write(date('Y-m-d H:i:s').'_chunkFile_分片数量'.json_encode($incr),'info');

        // 上传分块
        $this->upload->setModel( 'app\common\utils\upload\src\local\Upload');
        $uploadPart = $this->upload->uploadPart([
            "uploadId" => $seq, // 对象id
            "partId"   => $chunkNumber, // 分块编号
            "dir"      => $dir, // 分块地址
            "key"      => $chunkFileName, // 名称
            "file"     => $file,  // 数据
            "totalChunks" => $sendContext['totalChunks']
        ]);
        Log::write(date('Y-m-d H:i:s').'$uploadPart::'.json_encode($uploadPart),'info');
        if (!$uploadPart) return $this->setSender($callbackEvent,ImJson::outData(20003));

        return $this->sendChunk($sendContext,$callbackEvent,$uploadPart);

    }

    /**
     * 发送分块信息
     * @param $sendContext
     * @param $callbackEvent
     * @param $uploadPart
     * @return bool
     */
    protected function sendChunk($sendContext,$callbackEvent,$uploadPart): bool
    {
        $room_id = (string)$sendContext['room_id'];
        $filename = $sendContext['filename']; // 文件名称
        $totalChunks = $sendContext['totalChunks']; // 分片总数量
        $totalSize = $sendContext['totalSize']; // 总 size
        $uploadProgress = $sendContext['uploadProgress']; // 上传进度
        $chunkSize = $sendContext['chunkSize'];  // 分块size
        $chunkNumber =  $uploadPart['partId'];

        return $this->websocket->to($room_id)->emit($callbackEvent,
            ImJson::outData(10000,'成功',[
                'filename'  => $filename,
                'totalSize' => $totalSize,
                'identifier' => $sendContext['identifier'],
                'chunkPath' => $uploadPart['path'],
                'totalChunks' => $totalChunks,
                'chunkNumber' => $chunkNumber,
                "uploadStatus" => $uploadPart['uploadStatus'],
                "uploadProgress" => $uploadProgress,
                "chunkSize"  => $chunkSize,
                "newFileName" => $sendContext['newFileName'],
                "user_id"  => $sendContext['user_id'],
                "seq"      => $sendContext['seq']
            ])
        );

    }

    /**
     * redis 加一
     * @param string|int $key
     * @param string $table
     * @return mixed
     */
    protected function incr(string|int $key, string $table): mixed
    {

        $redisService = $this->app->make( RedisService::class);
        $key = $redisService->getKey($key,$table);
        $num = 0;
        if ($redisService->exists($key)) {
            $num = $redisService->get($key);
        }
        $redisService->set($key,$num);
        return $redisService->incr($key);

    }
    /**
     * 合并大文件
     * @param $event
     * @return bool|void
     */
    public function mergeFile($event)
    {
        $callbackEvent = "mergeFileCallback";
        $sendContext = $event['data'][0];
        if (!$sendContext) return  $this->setSender($callbackEvent,ImJson::outData(20003));
        $room_id = (string)$sendContext['room_id'];
        $totalChunks = (int)$sendContext['totalChunks'];
        $seq = $sendContext['seq'];
        $md5 = $sendContext['identifier'];  // md5
        $user_id = $sendContext['user_id'];
        $mergeNumber = (int)$sendContext['mergeNumber'];
        $totalSize = (int)$sendContext['totalSize'];
        $newFileName =  $sendContext['newFileName']; // 文件名称
        $chunkSize = (int)$sendContext['chunkSize'];  // 每一块的大小
        $chunkDir = Config::get('filesystem.disks.public.root').'/files/'.$user_id."/".$seq."/";
        $mergePath =  Config::get('filesystem.disks.public.root').'/files/'.$user_id."/".$newFileName; // 合并文件
        // 缓存key
        if (!$out = @fopen($mergePath, "wb")) {
            return true;
        }

        if (flock($out, LOCK_EX) ) {
            if ($mergeNumber <= 0) {
                $mergeNumber = $mergeNumber +1;
            }
            Log::write(date('Y-m-d H:i:s').'_mergeFile_参数'.json_encode($sendContext),'info');
            // 分块文件
            for ($i=$mergeNumber; $i<=$totalChunks; $i++) {
                $val = $chunkDir.$i."_".$newFileName;
                $data = [
                    "seq"          => $seq,
                    "chunk_number" => $totalChunks,
                    "mergeNumber" =>  $i,
                    'identifier'  =>$md5,
                    "newFileName"  => $newFileName,
                    'totalSize'    => $totalSize,
                    "totalChunks"   => $totalChunks
                ];

                if (!$in = @fopen($val, "rb")) {
                    Log::write(date('Y-m-d H:i:s').'_权限_'.json_encode($val),'info');
                    $mergeFileStatus = 2; // 文件没有权限
                    $data["mergeFileStatus"] = $mergeFileStatus;
                    $this->websocket->to($room_id)->emit($callbackEvent,
                        ImJson::outData(20404,'',$data )
                    );
                    break;
                }
                Log::write(date('Y-m-d H:i:s').'_分块_'.json_encode($val),'info');
                while ($buff = fread($in, $chunkSize)) {
                    fwrite($out, $buff);
                }
                @fclose($in);
                $uploadStatus = $this->upload::SENDING;
                if ($totalChunks ==  $i) {
                    $uploadStatus =  $this->upload::SEND_SUCCESS;

                }
                @unlink($val); //删除分片
                // 缓存
                $this->incr($seq,'mergeNumber');
                $data["uploadStatus"] = $uploadStatus;
                $this->websocket->to($room_id)->emit($callbackEvent,
                    ImJson::outData(10000,'成功', $data)
                );

            }
            flock($out, LOCK_UN); // 释放锁

        }
        @fclose($out);

    }


    /**
     * 修改消息
     * @param $event
     * @return bool
     */
    public function updateMsgStatus($event): bool
    {
        $callbackEvent = "updateMsgStatusCallback";
        $sendContext = $event['data'][0];
        if (!$sendContext) return  $this->setSender($callbackEvent,ImJson::outData(20003));
        $redisService = $this->app->make(RedisService::class);
        $totalChunks = (int)$sendContext['totalChunks'] ?? 0; // 分片总数量
        $chunkNumber = $sendContext['chunkNumber'] ?? 0;// 当前分片数量
        $mergeNumber =   $sendContext['mergeNumber'] ?? 0;// 合并分片数量
        $totalSize    = $sendContext['totalSize'];
        $uploadStatus = $sendContext['uploadStatus'];
        $newFileName =  $sendContext['newFileName']; // 文件名称
        $room_id = $sendContext['room_id'];
        $md5 = $sendContext['identifier'];  // md5
        $seq =  $sendContext['seq'];  // seq
        $chunkKey = $redisService->getKey($seq,'chunkNumber');
        $chunkNumber     = (int)$redisService->get($chunkKey) ?? $chunkNumber;
        $mergeKey =  $redisService->getKey($seq,'mergeNumber');
        $mergeNumber     = (int)$redisService->get($mergeKey) ?? $mergeNumber;

        $where = [
            "seq"       => $seq,
        ];
        $data = [
            "room_id"      => $room_id,
            "identifier"   => $md5,
            "newFileName"  => $newFileName,
            "uploadStatus" => $uploadStatus,
            "totalChunks"  => $totalChunks,
            "chunkNumber"  => $chunkNumber,
            "mergeNumber"  => $mergeNumber,
            "totalSize"    => $totalSize,
            "seq"          => $seq
        ];
        $updateData = [
            "upload_status" => $uploadStatus,
            "chunk_number"  => $chunkNumber,
            "merge_number"  => $mergeNumber
        ];

        $messageBusiness = $this->app->make(MessageBusiness::class);
        $info = $messageBusiness->find($where);
        if (!$info) return  $this->setSender($callbackEvent,ImJson::outData(20001,'失败',$data));

        if ($info['content_type'] == 2 && $uploadStatus ==$this->upload::SEND_SUCCESS) {
            $thumb = $this->app->make(UploadBusiness::class)->createThumb($info['file_path'],explode('.',$newFileName)) ?? "";
            $updateData['thumb_path'] = $thumb;
            $data['thumb_path']       = $thumb;
        }

        $save = $messageBusiness->save($where,$updateData);

        if (!$save) return  $this->setSender($callbackEvent,ImJson::outData(20001,'失败',$data));
        // 删除
        if ($chunkNumber && $redisService->exists($chunkKey)) {
            $redisService->del($chunkKey);
        }
        if ($mergeNumber && $redisService->exists($mergeKey)) {
            $redisService->del($mergeKey);
        }
        return $this->websocket->to($room_id)->emit($callbackEvent,ImJson::outData(10000,'成功',$data));
    }

    /**
     * 撤回消息
     * @param $event
     * @return bool
     */
    public function revokeMsg($event): bool
    {
        $callbackEvent = "revokeMsgCallback";
        $sendContext = $event['data'][0];
        if (!$sendContext) return  $this->setSender($callbackEvent,ImJson::outData(20003));
        $seq = (int)$sendContext["seq"] ?? 0;
        $room_id = $sendContext["room_id"] ?? 0;
        $token =  $sendContext["token"] ?? "";
        $index  =  $sendContext["index"] ?? 0;
        if (!$seq || !$room_id || !$token || !$index) return  $this->setSender($callbackEvent,ImJson::outData(20003));
        if (!$this->socketVerifyToken($token)) return $this->setSender($callbackEvent,ImJson::outData(20003));
        $user_id = $this->getUserId();
        $is_revoke = 1;
        $where = [
            "seq" => $seq
        ];
        $data  = [
            "is_revoke" => $is_revoke
        ];
        $messageBusiness = $this->app->make(MessageBusiness::class);
        $save = $messageBusiness->save($where,$data);
        if (!$save) return  $this->setSender($callbackEvent,ImJson::outData(20006));
        $redis = $this->app->make(RedisService::class);
        $key = $redis->getPrefix()."message:$room_id:".$user_id;
        if ($redis->exists($key)) {
            $list = $redis->ZREVRANGEBYSCORE($key,$seq,$seq);
            if ($list) {
                $jsonToArray = $this->jsonToArray->jsonDecode($list[0]);
                $jsonToArray['is_revoke'] = $is_revoke;
                Log::write(date('Y-m-d H:i:s').'_jsonToArrayRevokeMsg_'.$this->jsonToArray->jsonEncode($jsonToArray),'info');
                if ($redis->ZREMRANGEBYSCORE($key,$seq,$seq)) {
                    $redis->zadd($key,$seq,$this->jsonToArray->jsonEncode($jsonToArray));
                }
            }

        }

        return $this->websocket->to($room_id)->emit($callbackEvent,ImJson::outData(10000,'成功',[
            "seq" => $seq,
            "is_revoke" => 1,
            "index" => $index
        ]));

    }

    protected function setSender($event,$data): bool
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
