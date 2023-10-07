<?php
namespace app\home\controller\v1;

use app\common\utils\ImJson;
use app\service\JsonService;
use GuzzleHttp\Exception\GuzzleException;
use think\App;
use think\facade\Config;
use think\facade\Log;
use think\facade\Request;
use \app\home\business\UserBusiness;
use \app\service\login\Login as otherLogin;



class Login
{


    public static $business;


    public function __construct( App $app , UserBusiness $business)
    {

        static::$business = $business;
    }

    /**
     * 创建用户
     */

    public function register(): \think\Response
    {

        $nickName  =  Request::param('nick_name');
        $registerName = Request::post('login');

        if (static::$business->isLoginName($registerName))  ImJson::output(10001);
        $password   = Request::post('password');
        $confirmPassword = Request::post('confirm_password');
        $registerUser = static::$business->createUser($nickName,$registerName,$password,$confirmPassword);
        if ($registerUser !== true)  return ImJson::output(20001, $registerUser,[],['name' => '注册']);

        return ImJson::output(10000, '',[],['name'=>'注册']);

    }


    /**
     * 登录
     * @throws \Exception
     */
    public function login(): \think\Response
    {

        $loginName = Request::post('login');
        $password   = Request::post('password');
        $createUser = static::$business->login($loginName,$password);
        if ($createUser){
            //获取token
            $data = [
                'user_id'    => $createUser['id'],
                'nick_name'  => $createUser['nick_name'],
                'photo'      => $createUser['photo'],
                'sex'        => $createUser['sex'],
                'is_online'  => $createUser['is_online'],
                'token'      => $createUser['token'],
            ];


           return ImJson::output(10000, '',$data,['name'=>'登录']);
        }


       return ImJson::output(20001, '',[],['name' => '登录']);
    }

    /**
     * gitee 登录
     */
    public function giteeLogin(): \think\Response
    {
        $login = app()->make(otherLogin::class);
        $url = $login->getUserInfo('Gitee')->authorization();
        return ImJson::output(10000,'',["url" => $url],['name' => '回调']);

    }



    /**
     * 第三方登录回调
     * @throws GuzzleException
     */
    public function callback(): \think\Response
    {
        $parm = Request::param();
        $jsonService = app()->make(JsonService::class);
        Log::write(date('Y-m-d H:i:s').'_'.$jsonService->jsonEncode($parm),'info');
        $login = app()->make(otherLogin::class);
        $getAccessToken = $login->getUserInfo('Gitee')->getAccessToken($parm['code']);
        Log::write(date('Y-m-d H:i:s').'_'.$jsonService->jsonEncode($getAccessToken),'info');
        if (!isset($getAccessToken['access_token']))  return ImJson::output(401,'',$getAccessToken,[],401);
        $accessToken = $getAccessToken['access_token'];
        $data = $login->getUserInfo('Gitee')->getUserInfo($accessToken);
        $origin = $parm['origin'];
        $thirdPartyData = [];
        if ($origin == "gitee") {
            $thirdPartyData = [
                "third_party_id" =>$data['id'],
                "login_name" => $data['login'],
                "nick_name"  => $data['name'],
                "email"      => $data['email'] ?? "",
                "access_token" => $accessToken,
                "refresh_token" => $getAccessToken['refresh_token'],
                "create_token_time" => $getAccessToken['created_at'],
                "expires_in" => $getAccessToken['expires_in'],
                "createdAt"  => $data['created_at'],
                "updatedAt"  => $data['updated_at'],
                "origin"     => $origin
            ];
        }
        if (empty($thirdPartyData))  return ImJson::output(401,'',$getAccessToken,[],401);
        // 创建第三方登录
        $createThirdPartyLogin = static::$business->CreateThirdPartyLogin($thirdPartyData,$origin);
        if (!$createThirdPartyLogin) return ImJson::output(20001, $createThirdPartyLogin,[],['name' => '第三方注册']);
        //获取token
        $user = [
            'user_id'    => $createThirdPartyLogin['id'],
            'nick_name'  => $createThirdPartyLogin['nick_name'],
            'photo'      => $createThirdPartyLogin['photo'],
            'sex'        => $createThirdPartyLogin['sex'],
            'is_online'  => $createThirdPartyLogin['is_online'],
            'token'      => $createThirdPartyLogin['token'],
        ];
        return ImJson::output(10000,'',$user,['name' => '登录']);
    }





}
