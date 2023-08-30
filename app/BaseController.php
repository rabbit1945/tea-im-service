<?php
declare (strict_types = 1);

namespace app;

use app\common\utils\JwToken;
use think\App;
use think\exception\ValidateException;
use think\facade\Request;
use think\Validate;


/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = ['CheckLogin'];
    /**
     * 用户ID
     * @var
     */
    public static $user_id;




    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {

        $this->app     = $app;
        $this->request = $this->app->request;


        // 控制器初始化
        $this->initialize();

    }



    // 初始化
    protected function initialize()
    {
        static::getUser();

    }

    /**
     * 获取用户信息
     */
    protected static function getUser() {
        $token = explode(' ', Request::header('authorization'));
        $verify = JwToken::verifyToken(!empty($token[1]) ? $token[1] : '');
        if ($verify) return static::$user_id = $verify['user_id'];
    }


    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }






}
