<?php


namespace app\api\business;
use app\api\dao\message\MessageReceiveDao;
use app\api\dao\user\RoomUserDao;
use app\api\dao\user\UserDao;
use app\common\utils\SensitiveWord;
use app\service\JsonService;
use app\service\RedisService;
use Exception;
use think\App;
use think\Event;

class MessageReceiveBusiness extends Business
{
    /**
     * @var App
     */
    protected App $app;
    /**
     * @var MessageReceiveDao
     */
    protected MessageReceiveDao $dao;

    protected RedisService $redis;

    protected Event $event;

    private JsonService $json;

    public function __construct(App $app,MessageReceiveDao $dao,Event $event,RedisService $redis,JsonService $json) {
        parent::__construct($app);
        $this->dao = $dao;
        $this->app = $app;
        $this->event = $event;
        $this->redis = $redis;
        $this->json = $json;
    }


    /**
     * 获取消息员
     * @param $room_id
     * @param $user_id
     * @param int $page
     * @param int $limit
     * @return array|true
     */
    public function getMessage($room_id, $user_id, int $page = 1, int $limit =20): bool|array
    {
        $getKey =  $this->redis->getKey("$room_id:$user_id:$page:$limit",'isMsgNull');
        if ($this->redis->exists($getKey)) return [];
        $getPrefix = $this->redis->getPrefix();
        $key = "message:$room_id:".$user_id;
        $start = ($page -1) * 20;
        $end   = $start+$limit-1;
//        $list = $this->getMsgCacheList($key,$start,$end);
//        if ($list) return $list;
        $where = [
            ['UserReceiveModel.room_id','=',$room_id],
            ['UserReceiveModel.msg_to','=',$user_id]
        ];

        $messageList = $this->dao->historyMessageList($where,$user_id,$page,$limit);
        if (!$messageList) {
            $this->redis->set($getKey,true);
            $this->redis->pexpire($getKey,1000);
            return [];
        }
        $list = [];
        $key =  trim($getPrefix."message:$room_id:".$user_id);
        foreach ($messageList as $val) {
            $list[] = $this->getMsg($val);
            $jsonEncode = $this->json->jsonEncode($val);
            $this->redis->zadd($key,$val['seq'],$jsonEncode);
        }
        return $list;

    }




    /**
     * 统计消息的总数
     * @param $room_id
     * @param $user_id
     * @param null $delivered // null 全部  0 离线消息 1 收到的消息
     * @return mixed
     */
    public function receiveCount($room_id, $user_id,$delivered = NULL): mixed
    {
        $where = [
            ['room_id','=',$room_id],
            ['msg_to','=',$user_id],
        ];
        if ($delivered !== NULL) {
            $where[]= ['delivered','=',$delivered];
        }

        return $this->dao->count($where);
    }


    /***
     * 保存某个用户收到哪些信息。使用场景离线消息。
     */
    public function addReceive($data) {
        try {
            if (empty($data)) return false;
            // 查看房间内的用户是否在线 在线  消息收到 ，离线 未收到
            $room_id =  $data['room_id'];
            $userList = $this->app->make(RoomUserDao::class)->roomUserList($room_id,1,1000);
            if (empty($userList)) return false;
            $redis = $this->redis;
            $json = $this->json;
            $receiveData = [];
            foreach ($userList as $val) {
                $isOnline = $val['is_online'];
                $data["msg_to"] = $val['user_id'];
                $data["delivered"] = $isOnline == 'online' ? 1 : 0;
                $data["nick_name"] =  $val['nick_name'];
                $receiveData[] = $data;
            }
            $list = $this->dao->saveAll($receiveData)->toArray();
            if ($list) {
//                $uploadBusiness = $this->app->make(UploadBusiness::class);
                // 用户维度进行有序集合
                foreach ($list as $val) {
                    $key = $redis->getPrefix() . "message:$room_id:" . $val['msg_to'];
//                    $val['file_path'] =  $uploadBusiness->getObjectUrl($val['msg_to'].'/'.$val['file_name']);
                    $jsonEncode = $json->jsonEncode($val);
                    $redis->zadd($key, $val['seq'], $jsonEncode);
                }

            }
            return $list;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param  $user_id
     * @param array $data
     * @return bool
     */
    public function updateDeliveredStatus($user_id,array $data): bool
    {
        if (!$user_id || empty($data)) return false;
        if (!$this->dao->update($user_id,$data,'msg_to'))  return false;
        return true;
    }

    /**
     * @param $val
     * @return array
     */
    protected function getMsg( $val): array
    {
        $userDo = $this->app->make(UserDao::class);
        $sensitiveWord = $this->app->make(SensitiveWord::class);
        $user_info = $userDo->userInfo($val['msg_form']);
        $val['photo'] = '/static/images/微信头像.jpeg';

//        $val['photo'] = !empty($user_info['photo']) ?$user_info['photo']: '/static/images/微信头像.jpeg';
        $val['send_time'] = date('Y-m-d H:i:s', floor($val['send_time'] / 1000));
        $val['msg_content'] = $sensitiveWord->addWords(false)->filter(urldecode($val['msg_content']), '*', 2);
        $val['nick_name'] = $user_info['nick_name'];
//        $uploadBusiness = $this->app->make(UploadBusiness::class);
//        $key = $val['msg_to'].'/'.$val['file_name'];
//
//        $val['file_path'] = $uploadBusiness->getObjectUrl($key);
        return $val;
    }




}
