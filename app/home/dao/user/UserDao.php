<?php


namespace app\home\dao\user;
use app\model\UserModel;
use app\home\dao\BaseDao;

class UserDao extends BaseDao
{

    public function setModel(): string
    {
        // TODO: Implement setModel() method.
        return UserModel::class;

    }

    /**
     * 用户详情
     * @param $user_id
     * @param string $status
     * @return mixed
     */
    public function userInfo($user_id, string $status = '1',$field = 'id,nick_name,photo')
    {
        return $this->getModel()->field($field)->where('id','=',$user_id)->whereIn('status',$status)->find();
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
