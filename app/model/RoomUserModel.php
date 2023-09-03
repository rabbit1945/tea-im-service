<?php
namespace app\model;

use think\Model;
use think\model\relation\HasOne;

class RoomUserModel extends Model
{


    protected $name = 'room_user';

    /**
     *
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(UserModel::class,'id','user_id');
    }

}
