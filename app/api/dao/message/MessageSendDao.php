<?php


namespace app\api\dao\message;
use app\api\dao\BaseDao;
use app\model\UserSendModel;

class MessageSendDao extends BaseDao
{

    public function setModel(): string
    {
        // TODO: Implement setModel() method.
        return UserSendModel::class;

    }




}
