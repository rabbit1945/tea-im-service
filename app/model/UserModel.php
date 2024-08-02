<?php
namespace app\model;

use think\Model;
use think\model\relation\HasMany;

class UserModel extends Model
{

    protected $name = 'user';

    /**
     * @return HasMany
     */
    public function roomUser(): HasMany
    {
        return $this->hasMany(RoomUserModel::class,'user_id');
    }

}
