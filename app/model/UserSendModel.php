<?php
namespace app\model;

use think\Model;
use think\model\relation\HasOne;

class UserSendModel extends Model
{

    protected $name = 'user_send';

    /**
     *
     * @return HasOne
     */
    public function userSend(): HasOne
    {
        return $this->hasOne(MessageListModel::class,'seq','seq');
    }

}
