<?php


namespace app\home\business;
use app\home\dao\user\RoomUserDao;
use app\model\UserModel;
use think\App;
use app\model\RoomModel;
use app\model\RoomUserModel;
use think\facade\Request;
class RoomUserBusiness
{
    protected $app;
    protected static $model;

    public function __construct(App $app,RoomUserModel $model) {
        static::$model = $model;
        $this->app = $app;
    }


    /**
     * @param $uid
     * @return array
     */
    public function find($uid): array
    {
        $info = static::$model->where('user_id','=',$uid)->find();
        $data = [];
        if ($info) {
            $roomModel =  $this->app->make(RoomModel::class);
            $roomName = $roomModel->where('id','=',$info->room_id)->value('name');
            $data = [
                'room_id' => $info->room_id,
                'roomName' => $roomName
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

        $list =$this->app->make(RoomUserDao::class)->roomUserList($room_id,$pages,$size);
        foreach ($list as $key => $val) {

            $list[$key]['photo']     = $val['photo']?$val['photo']:'/static/images/微信头像.jpeg';
            $list[$key]['is_online'] = !empty($val['is_online']) ?$val['is_online']:"offline";

        }
        return $list;

    }

    /**
     * 用户列表总数
     * @param $room_id
     * @return mixed
     */
    public function count($room_id)
    {

        return static::$model->field('id,user_id,is_on_line,is_master,types')
                             ->where('room_id','=',$room_id)
                             ->count();

    }







}
