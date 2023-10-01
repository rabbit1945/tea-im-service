<?php
namespace app;

use app\common\utils\ImJson;
use ParseError;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\ClassNotFoundException;
use think\exception\ErrorException;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\InvalidArgumentException;
use think\exception\RouteNotFoundException;
use think\exception\ValidateException;
use think\Response;
use Throwable;

/**
 * 应用异常处理类
 */
class ExceptionJsonHandle extends ExceptionHandle
{
    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];


    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request   $request
     * @param Throwable $e
     * @return Response
     */
    #app\ExceptionHandle.php
    public function render($request, Throwable $e): Response
    {
        // app_debug模式下按原thinkphp6异常模式处理异常
//        if (env('app_debug')) {
//            return parent::render($request, $e);
//        }
        // 使用了错误的数据类型 或 缺失参数  || 语法错误  || 请求异常
        if ($e instanceof HttpResponseException || $e instanceof ParseError || ($e instanceof HttpException && $request->isAjax())) {

             return  ImJson::output(500,'',[$e->getMessage()],[],500);

        }
        //参数验证错误
        if ($e instanceof ValidateException) {

            return  ImJson::output(422,'',[$e->getError()],[],422);
        }

        // 方法（控制器、路由、http请求）、资源（多媒体文件，如视频、文件）未匹配到，
        // 一旦在定义的路由规则中匹配不到，它就会直接去匹配控制器，但是因为在控制器中做了版本控制v1,v2这样的，所以它是无法获取对应控制器的
        // 所以都会直接走了HttpException的错误
        // 感觉好像也无所谓，反正是做api接口的，只不过这样就不好准确的提示信息了
        // 到底这个请求时控制器找不到呢？还是方法找不到？还是请求类型（get,post）不对？
        if(($e instanceof ClassNotFoundException || $e instanceof RouteNotFoundException)
            || ($e instanceof HttpException && $e->getStatusCode()==404)){
            return ImJson::output(404,'',[$e->getMessage()]);
        }
        // 使用了错误的数据类型 或 缺失参数
        if ($e instanceof InvalidArgumentException || $e instanceof ErrorException) {

            return  ImJson::output(20003,'',[$e->getMessage()],[],500);

        }


    }

}
