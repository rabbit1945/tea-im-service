<?php


namespace app\home\business;
use app\common\utils\Ip;
use app\common\utils\JwToken;
use app\home\dao\user\UserDao;
use app\home\dao\user\UserLogsDao;
use app\model\RoomModel;
use app\model\UserModel;
use app\validate\UserValidate;
use think\Event;
use think\exception\ValidateException;
use app\model\RoomUserModel;
use think\facade\Cache;

class UserBusiness
{
    protected static $model;
    /** @var Event */
    protected $event;
    public function __construct(UserModel $model, Event $event) {
        static::$model = $model;

        $this->event  = $event;
    }

    /**
     * 创建用户
     * @param $nick_name
     * @param $login_name
     * @param $password
     * @param $confirm_password
     * @return array|string
     */
    public function createUser($nick_name,$login_name,$password,$confirm_password){

        try {
            // 检测字段
            $data = [
                'nick_name' => $nick_name,
                'login_name'=> $login_name,
                'password'  => $password,
                'confirm_password' => $confirm_password,
            ];
            // 验证
//            $validate = app()->make(UserValidate::class);
            validate(UserValidate::class)
                ->scene('create')
                ->check($data);
            // 添加用户
            static::$model->transaction(function () use ( $nick_name,$login_name,$password){
                static::$model->save(
                    [
                        'nick_name' => $nick_name,
                        'login_name'=> $login_name,
                        'password'  => md5($password),
                    ]
                );

                $roomUserModel = new RoomUserModel();
                $roomModel = new RoomModel();
                $groupId = $roomModel->value('id');

                $roomUserModel->save(
                    [
                        'user_id' => static::$model->id,
                        'room_id'=> $groupId,

                    ]
                );
            });

            return True;

        } catch (ValidateException $e) {
            // 验证失败 输出错误信息

            return $e->getError();
        }


    }


    /**
     * 登录
     * @param $login_name
     * @param $password
     * @return mixed
     * @throws \Exception
     */
    public function login($login_name,$password) {

        $data = [
            'login_name'=> $login_name,
            'password'  => $password,
        ];

        validate(UserValidate::class)
            ->scene('edit')
            ->check($data);

        $find = static::$model
            ->where('login_name', '=', $login_name)
            ->where('password','=',md5($password))
            ->where('status','=',1)
            ->find();

        if (!$find) return false;
        // 登录之后一系列动作
        $result = $this->event->trigger("UserLogin",$find);
        if (!$result) return false;

        $find->token = $result[0]['token'] ?? "";
        return $find;
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
            return False;
        }

        return True;
    }

    /**
     * 获取用户的信息
     */
    public function find($id) {

        $find = static::$model->field('id,nick_name,photo,is_online')->where('id','=',$id)->order('is_online desc')->find();
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
}
