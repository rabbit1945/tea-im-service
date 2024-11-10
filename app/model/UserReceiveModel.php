<?php
namespace app\model;

use think\Model;
use think\model\relation\HasMany;
use think\model\relation\HasOne;

class UserReceiveModel extends Model
{

    protected $name = 'user_receive';

    /**
     *  一条消息对应一个用户
     * @return HasOne
     */
    public function offlineToUserRelated(): HasOne
    {
        return $this->hasOne(UserModel::class,'id','msg_to');
    }

    /**
     * @return HasOne
     */
    public function userReceive(): HasOne
    {
        return $this->hasOne(MessageListModel::class,'seq','seq');
    }


}
