<?php
declare (strict_types = 1);

namespace app\home\listener;
use app\common\utils\JwToken;
use app\home\dao\user\UserDao;
use Mockery\Exception;
use think\facade\Cache;
use think\facade\Config;
use app\model\UserLogsModel;
use app\home\business\UserBusiness;
/**
 * 用户登录一系列动作处理
 * Class UserLogin
 * @package app\home\listener
 */
class UserLogin
{

    public $user;

    public function __construct(UserLogsModel $user)
    {
        $this->user = $user;
    }


    /**
     * 事件监听处理
     * @param $data
     * @return array|false
     * @throws \Exception
     */
    public function handle($data)
    {

        try {

            // 获取token
            $user_id = (string)$data['id'];

            $getToken = JwToken::getAccessToken($user_id,time()+86400*64);
            if (empty($getToken))  return false;

            // 设置缓存
            if (!$this->setCache($user_id,$data)) return false;
            // 缓存token
            if (!app()->make(UserBusiness::class)->setCacheToken('login_token_'.$user_id,$getToken)) return false;
            // 更改用户状态
            $update = app()->make(UserDao::class)->update($user_id,['is_online' => 'online']);
            if (!$update) return false;

            return [
                "token" => $getToken
            ];
        } catch (Exception $exception) {
            return false;
        }
    }



    /**
     * 设置缓存
     * @throws \Exception
     */
    public function setCache($key,$find): bool
    {
        if (!Cache::has($key)) {
            //  设置缓存数据
           return Cache::set(
                "{$key}",
                [
                    'user_id' => $find->id,
                    'nick_name' => $find->nick_name,
                    'sex' => $find->sex,
                    'photo' => $find->photo
                ],
                new \DateTime(date('Y-m-d h:i:s', Config::get('jwt.exp')))
            );

        }

        return true;

    }


}
