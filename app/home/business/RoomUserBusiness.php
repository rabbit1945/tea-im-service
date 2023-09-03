<?php


namespace app\home\business;
use app\home\dao\user\RoomUserDao;
use app\home\dao\room\RoomDao;
use think\App;
class RoomUserBusiness
{
    protected $app;
    protected $dao;

    public function __construct(App $app,RoomUserDao $dao) {
        $this->dao = $dao;
        $this->app = $app;
    }


    /**
     * @param $uid
     * @return array
     */
    public function find($uid): array
    {
        $info = $this->dao->find([
            [
             "user_id",'=',$uid
            ]
        ],'room_id');
        $data = [];
        if ($info) {
            $roomDao =  $this->app->make(RoomDao::class);
            $roomList = $roomDao->find([
                [
                    'id','=',$info['room_id']
                ]
            ]);
            $data = [
                'room_id' => $info['room_id'],
                'roomName' => $roomList['name'],

            ];
        }

        return $data;

    }


    /**
     * 用户列表
     * @param $room_id
     * @return mixed
     */
    public function list($room_id,$pages= 1,$size = 20)
    {

        $list =$this->dao->roomUserList($room_id,$pages,$size);
        foreach ($list as $key => $val) {

            $list[$key]['photo']     = $val['photo']?$val['photo']:'/static/images/微信头像.jpeg';
            $list[$key]['is_online'] = !empty($val['is_online']) ?$val['is_online']:"offline";
        }

        return [
            "list" => $list
        ];

    }

    /**
     * 获取房间内的用户是否在线
     * @param $room_id
     * @return mixed
     */
    public function getRoomUserIsOnlineCount($room_id)
    {


        return $this->dao->getRoomUserIsOnlineCount($room_id);



    }
}
