<?php
namespace app\model;

use think\Model;
use think\model\relation\HasMany;
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

    /**
     * 一个用户多个房间
     * @return HasMany
     */
    public function room():HasMany
    {
        return $this->hasMany(RoomModel::class,'id','room_id');
    }

}
