<?php
declare (strict_types = 1);

namespace app\middleware;

use app\common\utils\ImJson;
use app\common\utils\JwToken;
use Closure;
use think\facade\Cache;
use think\facade\Request;

class CheckLogin
{
    /**
     * 用户的key
     * @var string
     */
    protected  $user_key = "home_api_user";

    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(\think\Request $request, Closure $next)
    {
        $this->isLogin();
        return $next($request);
    }

    /**
     * 判断用户是否登录
     * @return
     */
    public function isLogin()
    {
        try {
            $token = explode(' ', Request::header('authorization'));
            $tokens = !empty($token[1]) ? $token[1] : '';

            if (!$tokens) return true;
            $verify = JwToken::verifyToken($tokens);
            // 判断token是否一致
            $cacheToken = Cache::get('login_token_'.$verify['user_id']);

            if ($tokens != $cacheToken ) ImJson::output('20001',"token检验失败1");


            if (empty($verify))  ImJson::output('20001',"token检验失败2");


        } catch (\Exception $e) {
            ImJson::output('20001',"token检验失败3");
        }


    }
}
