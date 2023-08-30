<?php
namespace app\home\controller\v1;

use app\common\utils\ImJson;
use think\App;
use think\facade\Request;
use \app\home\business\UserBusiness;
use app\common\utils\IdRedisGenerator;



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

    public function register()
    {

        $nickName  =  Request::param('nick_name');
        $registerName = Request::post('login');

        if (!static::$business->isLoginName($registerName))  ImJson::output(10001);
        $password   = Request::post('password');
        $confirmPassword = Request::post('confirm_password');
        $registerUser = static::$business->createUser($nickName,$registerName,$password,$confirmPassword);
        if ($registerUser !== true)  ImJson::output(20001, $registerUser,[],['name' => '注册']);

        ImJson::output(10000, '',[],['name'=>'注册']);

    }


    /**
     * 登录
     * @throws \Exception
     */
    public function login()
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


            ImJson::output(10000, '',$data,['name'=>'登录']);
        }


        ImJson::output(20001, '',[],['name' => '登录']);
    }





}
