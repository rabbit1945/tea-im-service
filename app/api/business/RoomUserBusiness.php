<?php


namespace app\api\business;
use app\api\dao\user\RoomUserDao;
use app\api\dao\room\RoomDao;
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
     * 获取当前用户的房间列表
     * @param $user_id
     * @return mixed
     */
    public function getRoomList($user_id): mixed
    {
       return $this->dao->getRoomList($user_id);
    }




    /**
     * 查询房间信息
     * @param $id
     * @return array
     */
    public function find($id): array
    {
        $roomDao =  $this->app->make(RoomDao::class);
        $roomInfo = $roomDao->find([
            [
                'id','=',$id
            ]
        ]);

        return [
            'room_id' => $roomInfo['id'],
            'roomName' => $roomInfo['name'],

        ];

    }


    /**
     * 用户列表
     * @param $room_id
     * @param int $pages
     * @param int $size
     * @param string $user_ids
     * @return mixed
     */
    public function list($room_id, int $pages= 1, int $size = 20, string $user_ids = '')
    {

        $list =$this->dao->roomUserList($room_id,$pages,$size,$user_ids);
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
