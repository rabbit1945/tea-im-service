<?php


namespace app\api\dao\message;
use app\api\dao\BaseDao;
use app\model\MessageListModel;

class MessageDao extends BaseDao
{

    public function setModel(): string
    {
        // TODO: Implement setModel() method.
        return MessageListModel::class;

    }


}
