<?php
namespace app\home\controller\v1;
use app\BaseController;
use app\common\utils\ImJson;
use app\common\utils\Json;
use app\home\business\UserBusiness;
use think\facade\Cache;
use think\App;
use app\service\JsonService;
use app\home\business\RoomUserBusiness;


class Index extends BaseController
{

    private static $business;

    public function __construct(App $app,UserBusiness $business)
    {
        parent::__construct($app);
        static::$business = $business;

    }


    public function index()
    {

        return view();

    }

    /**
     * 聊天室中的基本信息
     */
    public function roomInfo() {
        $user_id =static::$user_id;
        // 用户信息
        $user = static::$business->find($user_id);
        // 聊天室名称
        $groupUserBusiness = $this->app->make(RoomUserBusiness::class);
        $groupUser = $groupUserBusiness->find($user_id);

        // 用户列表
        $room_id = $groupUser['room_id'];
        $list = $groupUserBusiness->list($room_id);
        return ImJson::output(10000, '成功',[
            'userInfo' => [
                'id'   => $user->id,
                'nick_name' => $user->nick_name,
                'photo'=> $user->photo ? $user->photo : '/static/images/微信头像.jpeg'
            ],
            'room' => $groupUser,
            'user_list' => $list,
        ]);

    }
}
