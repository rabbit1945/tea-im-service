<?php


namespace app\home\controller\v1;


use app\BaseController;
use app\common\utils\ImJson;
use app\home\business\UserBusiness;
use think\App;

class User extends BaseController
{

    private static $business;

    public function __construct(App $app,UserBusiness $business)
    {
        parent::__construct($app);
        static::$business = $business;

    }

    /**
     * 登出
     */

    public function logOut() {
        $user_id = static::$user_id;
        if (static::$business->logOut($user_id)) ImJson::output('10000','退出成功');
    }


}
