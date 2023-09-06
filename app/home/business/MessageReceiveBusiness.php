<?php


namespace app\home\business;
use app\common\utils\Upload;
use app\home\dao\message\MessageReceiveDao;
use app\home\dao\user\RoomUserDao;
use app\home\dao\user\UserDao;
use app\service\JsonService;
use app\service\RedisService;
use think\App;
use think\facade\Config;

class MessageReceiveBusiness
{
    protected $app;
    protected $dao;
    protected static $redis;

    public function __construct(App $app,MessageReceiveDao $dao) {
        $this->dao = $dao;
        $this->app = $app;
    }

    /**
     * 获取离线消息
     * @param $room_id
     * @param $user_id
     */
    public function getOffLineMessage($room_id,$user_id)
    {

        $redis = $this->app->make(RedisService::class);
        $json = $this->app->make(JsonService::class);
        // 获取离线缓存消息
    //    $room_msg_key = "room_$room_id"."_".$user_id;
    //    if ( $redis->exists($room_msg_key)) {
    //        $cacheList = $redis->zrange($room_msg_key,0,-1);

    //        if (!empty($cacheList)) {
    //            $list = [];
    //            foreach ($cacheList as $val) {
    //                $list[] = $json->jsonDecode($val);
    //            }
    //            return $list;
    //        }
    //    }

        $where = [
            ['room_id','=',$room_id],
            ['msg_to','=',$user_id],
            ['delivered','=',0]

        ];

        $list =  $this->dao->receiveList($where,$user_id);

        $userDo = $this->app->make(UserDao::class);

        foreach ($list as $key=>$val) {
            $user_info =  $userDo->userInfo($val['msg_form']);
            $list[$key]['nick_name'] = $user_info['nick_name'];
            $list[$key]['photo'] = $user_info['photo']?$user_info['photo']:'/static/images/微信头像.jpeg';

            $list[$key]['send_time'] = date('Y-m-d H:i:s',floor($val['send_time']/1000));

        }
        if (empty($list)) return false;
        return $list;
    }

    /**
     * 获取收到的消息或在线消息
     * @param $room_id
     * @param $user_id
     * @param $msg_id
     *
     */
    public function getOnlineMessage($room_id, $user_id, $page =1,$limit =20)
    {
        $where = [
            ['room_id','=',$room_id],
            ['msg_to','=',$user_id],
            ['delivered','=',1]

        ];
        $list = $this->dao->historyMessageList($where,$user_id,$page,$limit);
        $userDo = $this->app->make(UserDao::class);
        foreach ($list as $key=>$val) {
           $user_info =  $userDo->userInfo($val['msg_form']);
            $list[$key]['nick_name'] = $user_info['nick_name'];
            $list[$key]['photo'] = $user_info['photo']?$user_info['photo']:'/static/images/微信头像.jpeg';
            $list[$key]['send_time'] = date('Y-m-d H:i:s',floor($val['send_time']/1000));


        }
//        $array_msg_id = array_column($list,'msg_id');
//        array_multisort($array_msg_id,SORT_ASC,$list);

        return $list;

    }

    /**
     * 统计消息的总数
     * @param $room_id
     * @param $user_id
     * @param null $delivered // null 全部  0 离线消息 1 收到的消息
     * @return mixed
     */
    public function receiveCount($room_id, $user_id,$delivered = NULL)
    {
        $where = [
            ['room_id','=',$room_id],
            ['msg_to','=',$user_id],
        ];
        if ($delivered !== NULL) {
            $where[]= ['delivered','=',$delivered];
        }

        return $this->dao->count((array)$where);
    }

    /***
     * 保存某个用户收到哪些信息。使用场景离线消息。
     */
    public function addReceive($data){
        try {
            if (empty($data)) return false;
            // 查看房间内的用户是否在线 在线  消息收到 ，离线 未收到
            $room_id =  $data['room_id'];
            $userList = $this->app->make(RoomUserDao::class)->roomUserList($room_id,1,1000);
            if (empty($userList)) return false;
            $redis = $this->app->make(RedisService::class);
            $json = $this->app->make(JsonService::class);

            $receiveData = [];
            foreach ($userList as $val) {
                $isOnline = $val['is_online'];
                $receiveData[] = [
                    "room_id" => $room_id,
                    "msg_form" => $data['msg_form'],
                    "msg_to"   => $val['user_id'],
                    "nick_name" => $val['nick_name'],
                    "msg_content" => $data['msg_content'],
                    "send_time" => $data['send_time'],
                    "msg_type" => 2,
                    "delivered" => $isOnline == 'online' ? 1 : 0,
                    "seq" => $data['seq'],
                    "content_type" => $data['content_type'],
                ];

            }


            $list = $this->dao->saveAll($receiveData)->toArray();

            if ($list) {
                // 用户维度进行有序集合
                foreach ($list as $val) {
                    // redis 存离线消息
                    if  ($val['delivered'] === 0) {

                        $key = "room_$room_id"."_".$val['msg_to'];
                        $jsonEncode = $json->jsonEncode($val);
                        $redis->zadd($key,$val['id'],$jsonEncode);
                    }

                }

            }

            return $list;
        } catch (\Exception $e) {
            echo $e;
        }


    }

    /**
     * @param  $user_id
     */
    public function updateDeliveredStatus($user_id,array $data,$room_msg_key): bool
    {
        if (!$user_id || empty($data)) return false;
        $redis = $this->app->make(RedisService::class);

        if ($this->dao->update($user_id,$data,'msg_to')) {
            if ($redis->exists($room_msg_key) == false) return true;
            if ($redis->del($room_msg_key) >0) return true;

        }

        return false;

    }


    /**
     * 上传音频
     */
    public function uploadAudio($dir,$file,$name)
    {
        $upload = $this->app->make(Upload::class);
        $path = $upload->fileUpload($dir,$file,$name);

        return $path;

    }









}
