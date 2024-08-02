<?php


namespace app\api\dao\user;
use app\model\ThirdPartyLoginUserModel;
use app\api\dao\BaseDao;

class ThirdPartyLoginUserDao extends BaseDao
{

    public function setModel(): string
    {
        // TODO: Implement setModel() method.
        return ThirdPartyLoginUserModel::class;

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
