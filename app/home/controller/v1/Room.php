<?php
namespace app\home\controller\v1;
use app\BaseController;
use app\common\utils\ImJson;
use app\home\business\UserBusiness;
use think\App;
use app\home\business\RoomUserBusiness;
use think\facade\Request;

class Room extends BaseController
{

    private static $business;

    public function __construct(App $app,UserBusiness $business)
    {
        parent::__construct($app);
        static::$business = $business;

    }

    /**
     * 聊天室中的基本信息
     */
    public function roomInfo() {
        $user_id =static::$user_id;
        // 用户信息
        $user = static::$business->find($user_id);
        $groupUserBusiness = $this->app->make(RoomUserBusiness::class);

        $groupUser = $groupUserBusiness->find($user_id);

        if (!$user) ImJson::output(20006);

        ImJson::output(10000, '成功',[
            'userInfo' => [
                'user_id'   => $user->id,
                'nick_name' => $user->nick_name,
                'photo'=> $user->photo,
                'is_online' => $user->is_online,
            ],
            'roomInfo' => $groupUser,
            // 'userList' => $list,
        ]);

    }


    /**
     * 获取聊天室中用户的列表
     */
    public function roomUserList() {
        $user_id =static::$user_id;
        $pages = Request::post('pages') ? Request::post('pages') : 1;
        $size = Request::post('size') ? Request::post('size') : 20;

        // 聊天室名称
        $groupUserBusiness = $this->app->make(RoomUserBusiness::class);
        $groupUser = $groupUserBusiness->find($user_id);
        // 用户列表
        $room_id = $groupUser['room_id'];
        $list = $groupUserBusiness->list($room_id,$pages,$size);
        $count = $groupUserBusiness->count($room_id);
        if (!$list) ImJson::output(20006);
        ImJson::output(10000, '成功',[
           'userList' => $list,
           'total'    => $count

         ]);

    }

    /**
     * 添加用户登录日志
     */
    public function userLoginLogs()
    {
        $user_id =static::$user_id;
        $groupUserBusiness = static::$business->addUserLoginLogs($user_id);

        if ($groupUserBusiness)  ImJson::output(10000, '成功');

        ImJson::output(20001, '失败',[],["name","添加用户日志"]);

    }
}
