<?php
namespace app\home\controller\v1;

use app\common\utils\ImJson;
use app\service\JsonService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use think\App;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Db;
use think\facade\Log;
use think\facade\Request;
use \app\home\business\UserBusiness;
use \app\service\login\AuthLogin;
use think\Response;


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

           return ImJson::output(10000, '',$createUser,['name'=>'登录']);
        }


       return ImJson::output(20001, '',[],['name' => '登录']);
    }

    /**
     * gitee 登录
     * @return Response
     */
    public function authLogin(): Response
    {
        $oauthToken = Request::post('oauthToken');
        if (!$oauthToken) return ImJson::output(20006, '',[]);
        $createUser = static::$business->oauthLogin($oauthToken);
        if ($createUser) return ImJson::output(10000, '',$createUser,['name'=>'登录']);
        return ImJson::output(20001, '',[],['name' => '登录']);
    }


    /**
     * gitee 授权
     * @throws GuzzleException
     */
    public function getAuth(): \think\Response
    {
        $origin =  Request::get('origin');
        $oauthToken =  Request::get('oauthToken');
        if (empty($origin)) {
            return ImJson::output(20006, '',[]);
        }

        if ($oauthToken) {
            // 获取token
            $getAccessToken = [];
            if (Cache::has($oauthToken)){
                $getAccessToken =  Cache::get($oauthToken);
            }

            if ($getAccessToken) {
                return ImJson::output(10000,'',["url" => "","oauthToken"=>$oauthToken,"origin"=>$origin],['name' => '回调']);
            }

        }

        $login = app()->make(AuthLogin::class,[$origin]);
        $url = $login->getUserInfo()->authorization();
        return ImJson::output(10000,'',["url" => $url],['name' => '回调']);

    }

    /**
     * 第三方登录回调
     * @throws GuzzleException
     */
    public function callback(): Response
    {
        $parm = Request::param();
        if (!$parm) return ImJson::output(422,'',[],[],422);
        $jsonService = app()->make(JsonService::class);
        Log::write(date('Y-m-d H:i:s').'_'.$jsonService->jsonEncode($parm),'info');
        $code = $parm['code'] ?? "";
        $origin = $parm['origin'] ?? "";
        if (!$code || !$origin) return ImJson::output(422,'',[],[],422);
        // 获取token
        $getAccessToken =  static::$business->getAccessToken($origin,$code);
        if (!isset($getAccessToken['access_token']))  return ImJson::output(401,'',[],[],401);
        //  获取基本信息
        $accessToken = $getAccessToken['access_token'];
        $data =  static::$business->getAuthUserInfo($origin,$accessToken);
        $thirdPartyData = [];

        switch ($origin)
        {
            case 'gitee':
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
                break;
            case 'github':

                break;


        }
        if (empty($thirdPartyData))  return ImJson::output(401,'',$getAccessToken,[],401);
        // 创建第三方登录
        $createThirdPartyLogin = static::$business->CreateThirdPartyLogin($thirdPartyData,$origin);
        if (!$createThirdPartyLogin) return ImJson::output(20001, $createThirdPartyLogin,[],['name' => '第三方注册']);
        return static::$business->authRedirect($accessToken);
    }


//    public function test()
//    {
//       $list =  Db::table('webim_user_send')->select();
//       foreach ($list as $val) {
//           Db::name('msg_list')->save([
//               "seq"=>$val['seq'],
//               "msg_content"=>$val['msg_content'],
//
//           ]);
//       }
//    }






}
