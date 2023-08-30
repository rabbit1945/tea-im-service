<?php
namespace app;

use app\common\utils\ImJson;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\ErrorException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
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
        if (env('app_debug')) {
            return parent::render($request, $e);
        }
        if ($e instanceof HttpResponseException) {

             ImJson::output(20014,$e->getMessage(),[],[],500);
        } elseif ($e instanceof ValidateException) {

             ImJson::output(20014,$e->getError(),[],[],422);
        } elseif ($e instanceof HttpException && $request->isAjax()) {

             ImJson::output(20014,$e->getMessage());

        } elseif ($e instanceof HttpException){

             ImJson::output(20014,$e->getMessage());

        } elseif ($e instanceof ErrorException) {
            // 自定义json返回异常

             ImJson::output(20014,$e,[],[],500);

        }

    }

}
