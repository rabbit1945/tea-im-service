<?php


namespace app\home\dao\user;
use app\model\RoomUserModel;
use app\model\UserModel;
use app\home\dao\BaseDao;
use PhpParser\Node\Stmt\Echo_;

class RoomUserDao extends BaseDao
{

    public function setModel(): string
    {
        // TODO: Implement setModel() method.
        return RoomUserModel::class;

    }

    /**
     * 房间用户列表
     * @param $room_id
     * @param int $pages
     * @param int $size
     * @param string $nickName
     * @return mixed
     */
    public function roomUserList($room_id, int $pages= 1, int $size = 20, string $nickName = '')
    {

        $roomUserModel = $this->getModel();
        return $roomUserModel
            ->field('nick_name,photo,is_online,is_robot')
            ->hasWhere('user')
            ->when($nickName,function ($query,$nickName) {
                // 满足条件后执行
                $query->where('nick_name', 'LIKE','%' . $nickName);
            })
            ->where('status','=',1)
            ->where('room_id','=',$room_id)
            ->order('is_online desc')
            ->page($pages,$size)
            ->select();
    }


    public function getRoomUserIsOnlineCount($room_id)
    {
        $roomUserModel = $this->getModel();
        return $roomUserModel
            ->hasWhere('user')
            ->where('status','=',1)
            ->where('room_id','=',$room_id)
            ->where('is_online','=','online')
            ->count();

    }

    /**
     * 用户详情
     * @param $user_id
     * @param string $status
     * @return mixed
     */
    public function userInfo($user_id, string $status = '1')
    {
        return $this->getModel()->where('id','=',$user_id)->whereIn('status',$status)->find();
    }

    /**
     * 判断是否有这个用户
     * @param $user_id
     * @return bool
     */
    public function isUser($user_id): bool
    {
        if ($this->getModel()->where('id','=',$user_id)->count() > 0)  return true;
        return false;
    }





}
