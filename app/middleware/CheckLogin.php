<?php
declare (strict_types = 1);

namespace app\middleware;

use app\common\utils\ImJson;
use app\common\utils\JwToken;
use Closure;
use Exception;
use think\facade\Cache;
use think\facade\Request;

class CheckLogin
{
    /**
     * 用户的key
     * @var string
     */
    protected  $user_key = "api_api_user";

    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(\think\Request $request, Closure $next)
    {
        $isLogin = $this->isLogin();

        if ($isLogin['code'] === 10000) {
            return $next($request);
        }
        return ImJson::output($isLogin['code'],$isLogin['msg'],$isLogin['data'],$isLogin['vars'],$isLogin['httpCode']);

    }

    /**
     * 判断用户是否登录
     * @return
     */
    public function isLogin()
    {
       $data =  [
            'code' => 10000,
            'msg'  => "",
            'data' => [],
            'vars' => [],
            'httpCode' => 200
        ];
        try {
            $token = explode(' ', Request::header('authorization'));
            $tokens = !empty($token[1]) ? $token[1] : '';
            if (!$tokens) {
                $data['code'] = 20401;
                $data['httpCode'] = 401;
                return $data;
            }
            $verify = JwToken::verifyToken($tokens);
            // 判断token是否一致
            $cacheToken = Cache::get('loginToken:'.$verify['user_id']);

            if ($tokens !== $cacheToken){
                $data['code'] = 20401;
                $data['httpCode'] = 401;
                return $data;
            }

            if (empty($verify)) {
                $data['code'] = 20401;
                $data['httpCode'] = 401;
                return $data;
            }


        } catch (Exception $e) {
            $data['code'] = 20500;
            $data['data'] = [$e->getMessage()];
            $data['httpCode'] = 500;
            return $data;

        }

        return $data;


    }
}
