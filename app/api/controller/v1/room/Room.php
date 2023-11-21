<?php
namespace app\api\controller\v1\room;
use app\api\business\RoomBusiness;
use app\api\business\RoomUserBusiness;
use app\api\business\UserBusiness;
use app\BaseController;
use app\common\utils\ImJson;
use think\App;
use think\facade\Request;
use think\Response;

class Room extends BaseController
{

    private RoomBusiness $business;
    private RoomUserBusiness $roomUserBusiness;

    public function __construct(App $app,RoomBusiness $business,RoomUserBusiness $roomUserBusiness)
    {
        parent::__construct($app);
        $this->business = $business;
        $this->roomUserBusiness = $roomUserBusiness;

    }

    public function index()
    {

        $user_id = static::$user_id;
        $list = $this->roomUserBusiness->getRoomList($user_id);
        if (!$list) return ImJson::output(20006);
        return ImJson::output(10000, '成功',[
            'list' => $list
        ]);

    }

    /**
     * 聊天室中的基本信息
     */
    public function show(): Response
    {
        $id = Request::param('id');
        if (!$id) return ImJson::output(20006);
        $groupUserBusiness = $this->roomUserBusiness;
        // 获取房间信息
        $groupUser = $groupUserBusiness->find($id);
        $user_id = static::$user_id;
        // 用户信息
        $user = $this->app->make(UserBusiness::class)->find($user_id);
        //统计在线人数
        $countOnline = $groupUserBusiness->getRoomUserIsOnlineCount($groupUser['room_id']);
        $groupUser['countOnline'] = $countOnline;

        if (!$user) ImJson::output(20006);

        return ImJson::output(10000, '成功',[
            'userInfo' => [
                'user_id'   => $user->id,
                'nick_name' => $user->nick_name,
                'photo'=> $user->photo,
                'is_online' => $user->is_online,
            ],
            'roomInfo' => $groupUser,
        ]);

    }


    /**
     * 获取聊天室中用户的列表
     */
    public function roomUserList(): Response
    {
        $pages = Request::param('pages', 1);
        $size = Request::param('size', 20);
        $nickName = Request::param('nickName', "");
        $room_id = Request::param('room_id', "");
        if (!$room_id) return ImJson::output(20006);
        // 聊天室名称
        $groupUserBusiness = $this->roomUserBusiness;

        // 用户列表
        $list = $groupUserBusiness->list($room_id,$pages,$size,$nickName);
        if (!$list) return ImJson::output(20006);
        return ImJson::output(10000, '成功',[
           'userList' => $list['list']
         ]);
    }

}
