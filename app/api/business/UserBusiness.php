<?php


namespace app\api\business;
use app\common\utils\Ip;
use app\common\utils\JwToken;
use app\api\dao\user\UserDao;
use app\api\dao\user\UserLogsDao;
use app\api\dao\user\ThirdPartyLoginUserDao;
use app\model\RoomModel;
use app\model\UserModel;
use app\service\login\AuthLogin;
use app\validate\UserValidate;
use GuzzleHttp\Exception\GuzzleException;
use think\Event;
use think\exception\ValidateException;
use app\model\RoomUserModel;
use think\facade\Cache;
use think\facade\Log;
use think\facade\Request;
use think\Response;

class UserBusiness
{
    protected static $model;
    protected $dao;
    /** @var Event */
    protected $event;
    public function __construct(UserModel $model, UserDao $dao, Event $event) {
        static::$model = $model;
        $this->event  = $event;
        $this->dao = $dao;
    }

    /**
     * 创建用户
     * @param $nick_name
     * @param $login_name
     * @param string $password
     * @param string $confirm_password
     * @return array|string
     */
    public function createUser($nick_name, $login_name, string $password = "", string $confirm_password = "", $thirdPartyData = []) {

        try {
            // 检测字段
            $data = [
                'nick_name' => $nick_name,
                'login_name'=> $login_name,
            ];

            // 检测是否第三登录
            if (empty($thirdPartyData)) {
                $data['password'] = $password;
                $data['confirm_password'] = $confirm_password;
                $scene = "create";
            } else {
                $scene = "thirdPartyLogin";
            }

            // 验证
            validate(UserValidate::class)
                ->scene($scene)
                ->check($data);
            // 添加用户
            static::$model->transaction(function () use ( $nick_name,$login_name,$password,$thirdPartyData) {
                // 创建用户
                static::$model->save(
                    [
                        'nick_name' => $nick_name,
                        'login_name'=> $login_name,
                        'password'  => !empty($password) ? md5($password):"",
                    ]
                );
                // 用户加入聊天室
                $roomUserModel = app()->make(RoomUserModel::class);
                $roomModel = app()->make(RoomModel::class);
                $groupId = $roomModel->value('id');
                $roomUserModel->save(
                    [
                        'user_id' => static::$model->id,
                        'room_id'=> $groupId,

                    ]
                );

                // 添加第三方登录的信息
                if ($thirdPartyData) {
                    $thirdPartyData["user_id"] =  static::$model->id;
                    $this->saveThirdPartyUser($thirdPartyData);
                }

            });

            return True;

        } catch (ValidateException $e) {
            // 验证失败 输出错误信息

            return $e->getError();
        }


    }

    /**
     * 添加第三方登录账号
     * @param $data
     * @param string $origin
     * @return array|string|true
     */
    public function CreateThirdPartyLogin($data, string $origin = "gitee"): bool|array|string
    {
        $login_name = $data['login_name'];
        $nick_name =  $data['nick_name'];

        // 查看第三方登录
        $thirdPartyLoginUserDao = app()->make(thirdPartyLoginUserDao::class);
        $thirdPartyLoginUserFind = $thirdPartyLoginUserDao->find([
            "login_name" => $login_name,
            "origin"  => $origin
        ]);

        if (!empty($thirdPartyLoginUserFind)) {
            $updateOrAdd =  $this->saveThirdPartyUser($data,$thirdPartyLoginUserFind['id']);

        } else {
            // 查看是否已有此用户
            $userInfo =  $this->dao->find([
                "login_name" => $login_name
            ],'id,nick_name,login_name,status,create_time');
            if ($userInfo) {
                // 用户名加后缀
                $login_name = $login_name ."_".$userInfo['create_time'];
            }
            // 创建用户
            $updateOrAdd =  $this->createUser($nick_name,$login_name,"","",$data);
        }

        if (!$updateOrAdd) return  false;

        return $updateOrAdd;

    }

    /**
     * 创建修改第三方用户信息
     * @param $thirdPartyData
     * @param $id
     * @return false|mixed
     */
    public function saveThirdPartyUser($thirdPartyData,$id = NULL): mixed
    {
        $thirdPartyLoginUserDao = app()->make(thirdPartyLoginUserDao::class);
        if (empty($id))  return $thirdPartyLoginUserDao->create($thirdPartyData);
        return $thirdPartyLoginUserDao->update($id,$thirdPartyData);

    }

    public function oauthLogin($oauthToken){
        $thirdPartyLoginUserDao = app()->make(thirdPartyLoginUserDao::class);
        $thirdPartyLoginUserFind = $thirdPartyLoginUserDao->find([
            "access_token" => $oauthToken
        ]);
        if ($thirdPartyLoginUserFind) {
            $find = static::$model
                ->where(['id' =>$thirdPartyLoginUserFind['user_id']])
                ->find();
            return $this->extracted($find);
        }

        return false;


    }

    /**
     * 获取token
     * @param $origin
     * @param string $code
     * @return mixed|Response
     * @throws GuzzleException
     */
    public function getAccessToken($origin, string $code): mixed
    {
        $login = app()->make(AuthLogin::class,[$origin]);
        $getAccessToken = $login->getUserInfo()->getAccessToken($code);
        if (isset($getAccessToken['error']))  return false;
        //  设置缓存
        $accessToken = $getAccessToken['access_token'] ?? "";
        if ($accessToken) {
            Cache::set($accessToken,$getAccessToken,86000);
        }
        return $getAccessToken;

    }


    /**
     * 获取 AuthUserInfo
     * @throws GuzzleException
     */
    public function getAuthUserInfo($origin,$accessToken)
    {
        if(empty($accessToken) && empty($type)) return false;
        $login = app()->make(AuthLogin::class,[$origin]);
        return $login->getUserInfo()->getUserInfo($accessToken);
    }

    /**
     * 授权重定向
     * @param $accessToken
     * @return Response
     */
    public function authRedirect($accessToken): Response
    {
        // 重定向
        $url = Request::domain();
        $strHttps = strstr($url,'https');
        if (!$strHttps) $url = str_replace("http","https",$url);

        return Response::create($url, 'redirect',302)
            ->cookie("oauthToken",$accessToken)
            ->cookie("isAuthLogin",true);
    }



    /**
     * 登录
     * @param $login_name
     * @param $password
     * @return mixed
     * @throws \Exception
     */
    public function login($login_name,$password): mixed
    {
        $data = [
            'login_name'=> $login_name,
            'password'  => $password,
        ];
        $scene = 'edit';
        validate(UserValidate::class)
            ->scene($scene)
            ->check($data);
        $where = [
            ['login_name', '=', $login_name],
            ['status','=',1],
            ['password','=',md5($password)]
        ];
        $find = static::$model
            ->where($where)
            ->find();
        return $this->extracted($find);
    }

    /**
     * 判断账号是否存在
     * @param $login_name
     * @return false
     */

    public function isLoginName($login_name): bool
    {
        $count = static::$model->where('login_name', '=', $login_name)->count();
        if ($count > 0) {
            return true;
        }

        return false;
    }

    /**
     * 获取用户的信息
     */
    public function find($id) {

        $find = static::$model->field('id,nick_name,photo,is_online,is_robot')->where('id','=',$id)->order('is_online desc')->find();
        if (!$find) return false;
        $find->photo = $find->photo ?$find->photo:'/static/images/微信头像.jpeg';
        return $find;
    }

    /**
     * 添加用户登录日志
     * @param $user_id
     */
    public function addUserLoginLogs($user_id): bool
    {
        //  检测缓存是否过有效期
        $key = 'getAddLoginLogs_'.$user_id; //获取登录用户的日志信息
        if (Cache::has($key)) {
            return true;
        }
        $ip = Ip::getIp();

        if ($ip) {
          $data = [
              "user_id" => $user_id,
              "ip"      => $ip,
          ];
          if (app()->make(UserLogsDao::class)->addUserLogs($data)) {
              Cache::set($key,$data,60);
              return true;
          }
       }
        return false;

    }

    /**
     * 登出
     * @param $user_id
     * @return bool
     */
    public function logOut($user_id): bool
    {
        //  判断用户是否存在
        $isUser = app()->make(UserDao::class)->isUser($user_id);
        if (!$isUser) return false;
        // 重新生成token
        $time = time()+60;
        $restToken = JwToken::getAccessToken($user_id,$time);
        if (empty($restToken)) return false;
        $key = 'login_token_'.$user_id;
        if (!$this->setCacheToken($key,$restToken)) return false;
        // 更改用户状态
        $update = app()->make(UserDao::class)->update($user_id,['is_online' => 'offline']);
        if (!$update) return false;

        return true;
    }

    /**
     * 缓存token
     */
    public function setCacheToken($key,$val,$exp = 0): bool
    {
        if (Cache::has($key)) {
            Cache::delete($key);
        }
        // 缓存token
        return Cache::set($key, $val,$exp);

    }

    public function count(array $where)
    {
        if (empty($where)) return false;
        return app()->make(UserDao::class)->count($where);

    }

    /**
     * @param $find
     * @return false|mixed
     */
    public function extracted($find): mixed
    {
        if (!$find) return false;
        $find = $find->toArray();
        // 登录之后一系列动作
        $result = $this->event->trigger("UserLogin", $find);
        if (!$result[0]) return false;
        $find['is_online'] = $result[0]['is_online'] ?? "";
        $find['token'] = $result[0]['token'] ?? "";
        //获取token
        return [
            'user_id'    => $find['id'],
            'nick_name'  => $find['nick_name'],
            'photo'      => $find['photo'],
            'sex'        => $find['sex'],
            'is_online'  => $find['is_online'],
            'token'      => $find['token'],
        ];
    }
}
