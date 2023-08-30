<?php


namespace app\home\dao\message;
use app\home\dao\BaseDao;
use app\model\UserSendModel;

class MessageSendDao extends BaseDao
{

    public function setModel(): string
    {
        // TODO: Implement setModel() method.
        return UserSendModel::class;

    }




}
