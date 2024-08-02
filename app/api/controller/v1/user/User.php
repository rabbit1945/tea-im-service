<?php


namespace app\api\controller\v1\user;
use app\api\business\UserBusiness;
use app\BaseController;
use app\common\utils\ImJson;
use think\App;
use think\Response;

class User extends BaseController
{

    private static $business;
    protected $middleware = ['CheckLogin'];

    public function __construct(App $app,UserBusiness $business)
    {
        parent::__construct($app);
        static::$business = $business;

    }


    /**
     * 添加用户登录日志
     */
    public function userLoginLogs(): Response
    {
        $user_id = static::$user_id;
        $groupUserBusiness = static::$business->addUserLoginLogs($user_id);

        if ($groupUserBusiness) return ImJson::output(10000, '成功');

        return ImJson::output(20001, '失败', [], ["name", "添加用户日志"]);

    }

    /**
     * 登出
     */
    public function loginOut()
    {
        $user_id = static::$user_id;
        if (static::$business->logOut($user_id)) return ImJson::output(10000,'退出成功');
    }


}
