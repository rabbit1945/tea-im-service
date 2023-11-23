<?php
declare (strict_types = 1);

namespace app\api\listener;
use app\api\business\UserBusiness;
use app\api\dao\user\UserDao;
use app\common\utils\JwToken;
use app\model\UserLogsModel;
use DateTime;
use Exception;
use think\facade\Cache;
use think\facade\Config;

/**
 * 用户登录一系列动作处理
 * Class UserLogin
 * @package app\api\listener
 */
class UserLogin
{

    public UserLogsModel $user;

    protected int|float $exp = 86400 * 64;

    public function __construct(UserLogsModel $user)
    {
        $this->user = $user;
    }

    /**
     * @return float|int
     */
    public function getExp(): float|int
    {
        return $this->exp;
    }

    /**
     * @param float|int $exp
     */
    public function setExp(float|int $exp): void
    {
        $this->exp = $exp;
    }


    /**
     * 事件监听处理
     * @param $data
     * @return string|array|bool
     * @throws Exception
     */
    public function handle($data): string|array|bool
    {
        try {
            // 获取token
            $user_id = (string)$data['id'];
            $getExp = $this->getExp();
            $key = 'loginToken:' . $user_id;
            $getToken = Cache::get($key) ?? JwToken::getAccessToken($user_id, time() + $getExp);
            if (empty($getToken)) return false;
            // 更改用户状态
            $update = app()->make(UserDao::class)->update($user_id, ['is_online' => 'online']);
            if (!$update) return false;
            // 设置缓存
            if (!$this->setCache($user_id, $data)) return false;
            $userBusiness = app()->make(UserBusiness::class);
            // 缓存token
            if (!Cache::has($key)) {
                if (!$userBusiness->setCacheToken($key, $getToken, $getExp - 1000)) return false;
            }

            $userBusiness->addUserLoginLogs($user_id);
            return [
                "is_online" => "online",
                "token" => $getToken
            ];
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }


    /**
     * 设置缓存
     * @throws Exception
     */
    public function setCache($key,$find): bool
    {
        if (!Cache::has($key)) {
            //  设置缓存数据
           return Cache::set(
                "{$key}",
                [
                    'user_id' => $find['id'],
                    'nick_name' => $find['nick_name'],
                    'sex' => $find['sex'],
                    'photo' => $find['photo']
                ],
               new DateTime(date('Y-m-d h:i:s', Config::get('jwt.exp')))
            );

        }

        return true;

    }


}
